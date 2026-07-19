<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use App\Models\AbsensiSiswa;
use App\Models\CatatanWaliKelas;
use App\Models\Kelas;
use App\Models\Kurikulum;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Titimangsa;
use App\Models\WaliKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalasRekapController extends Controller
{
    private function getWalasContext()
    {
        $user = Auth::user();

        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAktif) return null;

        $walas = WaliKelas::with(['kelas.kurikulum'])->where('guru_id', $user->id)
            ->whereHas('kelas', function($query) use ($tahunAktif) {
                $query->where('tahun_ajaran_id', $tahunAktif->id);
            })->first();
        if (!$walas) {
            return null;
        }

        $titimangsas = Titimangsa::where('tahun_ajaran_id', $tahunAktif->id)
            ->orderBy('id')->get();
            
        if ($titimangsas->isEmpty()) return null;

        return [
            'walas' => $walas,
            'kelas' => $walas->kelas,
            'tahun_ajaran' => $tahunAktif,
            'titimangsas' => $titimangsas,
        ];
    }

    public function index(Request $request)
    {
        $context = $this->getWalasContext();
        
        if (!$context) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki kelas perwalian pada tahun ajaran aktif.'
            ], 403);
        }

        $kelas = $context['kelas'];
        $tahunAjaranAktif = $context['tahun_ajaran'];
        $titimangsas = $context['titimangsas'];
        
        // Ambil semua siswa di kelas ini
        $siswas = Siswa::with('user')
            ->where('kelas_id', $kelas->id)
            ->get()
            ->sortBy(function($siswa) {
                return $siswa->name;
            })
            ->values();

        $masterKurikulum = Kurikulum::all();

        // Format data: Titimangsa -> list Siswa beserta nilai Absensi dan Catatannya
        $rekapData = [];

        foreach ($titimangsas as $titimangsa) {
            $siswaList = [];

            $namaPeriode = strtolower($titimangsa->nama_periode);
            $months = [];
            
            if (str_contains($namaPeriode, 'ganjil')) {
                if (str_contains($namaPeriode, 'sts') || str_contains($namaPeriode, 'tengah')) {
                    $months = [7, 8, 9];
                } else {
                    $months = [10, 11, 12]; // ASAS
                }
            } elseif (str_contains($namaPeriode, 'genap')) {
                if (str_contains($namaPeriode, 'sts') || str_contains($namaPeriode, 'tengah')) {
                    $months = [1, 2, 3];
                } else {
                    $months = [4, 5, 6]; // ASAT
                }
            } elseif (str_contains($namaPeriode, 'sas')) {
                $months = [10, 11, 12];
            } elseif (str_contains($namaPeriode, 'sat')) {
                $months = [4, 5, 6];
            } else {
                $bulanCetak = date('n', strtotime($titimangsa->tanggal_cetak));
                if ($bulanCetak >= 7 && $bulanCetak <= 9) {
                    $months = [7, 8, 9]; // ASTS Ganjil
                } elseif ($bulanCetak >= 10 && $bulanCetak <= 12) {
                    $months = [10, 11, 12]; // ASAS
                } elseif ($bulanCetak >= 1 && $bulanCetak <= 3) {
                    $months = [1, 2, 3]; // ASTS Genap
                } else {
                    $months = [4, 5, 6]; // ASAT
                }
            }

            // Determine start and end date for the months in this titimangsa
            $tahun = $tahunAjaranAktif->tahun; // e.g. "2026/2027" -> 2026
            $startYear = intval(explode('/', $tahun)[0]);
            
            // Get all dates where there was a meeting for this class ONCE per titimangsa
            $pertemuans = \App\Models\PertemuanGuru::where('kelas_id', $kelas->id)
                ->where(function($q) use ($months) {
                    foreach ($months as $m) {
                        $q->orWhereMonth('tanggal', $m);
                    }
                })
                ->whereYear('tanggal', in_array(min($months), [7,8,9,10,11,12]) ? $startYear : $startYear + 1)
                ->orderBy('tanggal')
                ->orderBy('jam_selesai', 'desc')
                ->get();

            // Get all absensi for these meetings ONCE per titimangsa
            $allAbsensi = \App\Models\AbsensiPertemuan::whereIn('pertemuan_id', $pertemuans->pluck('id'))
                ->get()
                ->groupBy('siswa_id');

            foreach ($siswas as $siswa) {
                
                $dailyStatus = [];
                $siswaAbsensi = isset($allAbsensi[$siswa->id]) ? $allAbsensi[$siswa->id]->keyBy('pertemuan_id') : collect();

                foreach ($pertemuans as $pert) {
                    if (!isset($dailyStatus[$pert->tanggal])) {
                        if (isset($siswaAbsensi[$pert->id])) {
                            $status = $siswaAbsensi[$pert->id]->status;
                            if (in_array($status, ['S', 'I', 'A'])) {
                                $dailyStatus[$pert->tanggal] = $status;
                            } else {
                                $dailyStatus[$pert->tanggal] = 'H';
                            }
                        } else {
                            // If no record, it's considered Hadir (H). We record it so earlier meetings don't override.
                            $dailyStatus[$pert->tanggal] = 'H';
                        }
                    }
                }
                
                $totalS = 0;
                $totalI = 0;
                $totalA = 0;

                foreach ($dailyStatus as $date => $status) {
                    if ($status === 'S') $totalS++;
                    if ($status === 'I') $totalI++;
                    if ($status === 'A') $totalA++;
                }

                // Cari Catatan Wali Kelas untuk siswa ini pada periode ini
                $catatan = CatatanWaliKelas::where('siswa_id', $siswa->id)
                    ->where('titimangsa_id', $titimangsa->id)
                    ->first();

                // BK Points (Base 100)
                $bkRecords = \App\Models\PoinSiswa::where('siswa_id', $siswa->id)
                    ->where('titimangsa_id', $titimangsa->id)
                    ->where(function ($q) {
                        $q->where('is_tambahan_walas', false)->orWhereNull('is_tambahan_walas');
                    })->get();
                
                $poinBk = 100;
                foreach ($bkRecords as $pr) {
                    $poinBk -= $pr->skor_pengurang ?? 0;
                    $poinBk += $pr->skor_penambah ?? 0;
                }

                // Walas Tambahan Poin
                $walasRecord = \App\Models\PoinSiswa::where('siswa_id', $siswa->id)
                    ->where('titimangsa_id', $titimangsa->id)
                    ->where('is_tambahan_walas', true)
                    ->first();
                
                $tambahanPoin = 0;
                $keterangan = '';
                if ($walasRecord) {
                    $tambahanPoin = ($walasRecord->skor_penambah ?? 0) - ($walasRecord->skor_pengurang ?? 0);
                    $keterangan = $walasRecord->catatan ?? '';
                }

                $poinFinal = $poinBk + $tambahanPoin;

                $siswaList[] = [
                    'id' => $siswa->id,
                    'nama_siswa' => $siswa->user ? $siswa->user->name : '',
                    'nisn' => $siswa->nis,
                    'tanggal_keluar' => $siswa->tanggal_keluar,
                    'absensi' => [
                        's' => $totalS,
                        'i' => $totalI,
                        'a' => $totalA,
                    ],
                    'catatan' => $catatan ? $catatan->catatan : '',
                    'rekomendasi_kenaikan' => $catatan ? $catatan->rekomendasi_kenaikan : 'belum_ditentukan',
                    'poin' => [
                        'bk' => $poinBk,
                        'tambahan' => $tambahanPoin,
                        'keterangan' => $keterangan,
                        'final' => $poinFinal
                    ]
                ];
            }

            $rekapData[] = [
                'id' => $titimangsa->id,
                'nama_periode' => $titimangsa->nama_periode,
                'is_aktif' => $titimangsa->is_aktif,
                'is_akhir_tahun' => (date('n', strtotime($titimangsa->tanggal_cetak)) >= 4 && date('n', strtotime($titimangsa->tanggal_cetak)) <= 6),
                'siswa' => $siswaList
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'kelas' => [
                    'id' => $kelas->id,
                    'tingkat' => $kelas->tingkat,
                    'nama_kelas' => $kelas->nama_kelas,
                    'kurikulum_id' => $kelas->kurikulum_id,
                    'kurikulum' => $kelas->kurikulum ? $kelas->kurikulum->nama_kurikulum : '-'
                ],
                'tahun_ajaran' => [
                    'id' => $tahunAjaranAktif->id,
                    'tahun' => $tahunAjaranAktif->tahun,
                    'semester' => $tahunAjaranAktif->semester
                ],
                'master_kurikulum' => $masterKurikulum,
                'titimangsas' => $rekapData
            ]
        ]);
    }

    public function storeCatatan(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'titimangsa_id' => 'required|exists:titimangsas,id',
            'catatan' => 'nullable|string',
            'rekomendasi_kenaikan' => 'nullable|in:naik,tinggal,lulus,belum_ditentukan'
        ]);

        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();

        $titimangsa = Titimangsa::find($request->titimangsa_id);
        if (!$titimangsa || !$titimangsa->is_aktif) {
            return response()->json([
                'success' => false,
                'message' => 'Periode titimangsa sudah ditutup.'
            ], 403);
        }

        CatatanWaliKelas::updateOrCreate(
            [
                'siswa_id' => $request->siswa_id,
                'titimangsa_id' => $request->titimangsa_id
            ],
            [
                'tahun_ajaran_id' => $tahunAjaranAktif->id,
                'catatan' => $request->catatan,
                'rekomendasi_kenaikan' => $request->rekomendasi_kenaikan ?? 'belum_ditentukan'
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Catatan Wali Kelas berhasil disimpan.'
        ]);
    }

    public function storePoinTambahan(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'titimangsa_id' => 'required|exists:titimangsas,id',
            'tambahan_poin' => 'nullable|numeric',
            'keterangan' => 'nullable|string'
        ]);

        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();

        $titimangsa = Titimangsa::find($request->titimangsa_id);
        if (!$titimangsa || !$titimangsa->is_aktif) {
            return response()->json([
                'success' => false,
                'message' => 'Periode titimangsa sudah ditutup.'
            ], 403);
        }

        $tambahanPoin = intval($request->tambahan_poin);
        $skorPenambah = $tambahanPoin > 0 ? $tambahanPoin : 0;
        $skorPengurang = $tambahanPoin < 0 ? abs($tambahanPoin) : 0;

        \App\Models\PoinSiswa::updateOrCreate(
            [
                'siswa_id' => $request->siswa_id,
                'titimangsa_id' => $request->titimangsa_id,
                'is_tambahan_walas' => true
            ],
            [
                'tahun_ajaran_id' => $tahunAjaranAktif->id,
                'skor_penambah' => $skorPenambah,
                'skor_pengurang' => $skorPengurang,
                'catatan' => $request->keterangan,
                'guru_id' => Auth::id(),
                'tanggal' => date('Y-m-d')
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Tambahan poin berhasil disimpan.'
        ]);
    }
}
