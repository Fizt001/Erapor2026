<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TahunAjaran;
use App\Models\Kurikulum;
use App\Models\Titimangsa;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\FormatifTp;
use App\Models\SumatifNilai;

class SumatifNilaiController extends Controller
{
    /**
     * Get Dependensi Filter & Data Matriks Nilai Sumatif
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // 1. Ambil opsi referensi (Sama seperti Formatif)
        $tahunAjarans = TahunAjaran::orderBy('id', 'desc')->get();
        $kurikulums = Kurikulum::all();

        $selectedTahunId = $request->tahun_ajaran_id ?? (TahunAjaran::where('is_aktif', true)->first()->id ?? null);
        $selectedKurikulumId = $request->kurikulum_id ?? ($kurikulums->first()->id ?? null);

        $periodes = Titimangsa::where('tahun_ajaran_id', $selectedTahunId)
                              ->where('kurikulum_id', $selectedKurikulumId)
                              ->get();
                              
        $selectedTitimangsaId = $request->titimangsa_id ?? ($periodes->where('is_aktif', true)->first()->id ?? null);
        $titimangsaData = $periodes->where('id', $selectedTitimangsaId)->first();
        $isTitimangsaAktif = $titimangsaData ? $titimangsaData->is_aktif : false;
        
        $namaPeriode = $titimangsaData ? strtolower($titimangsaData->nama_periode) : '';
        $isPsts = str_contains($namaPeriode, 'psts');

        // 2. Filter Rombel & Mapel berdasarkan tugas mengajar guru (Paralel Data)
        $selectedKelasId = $request->kelas_id;
        $selectedMapelId = $request->mapel_id;

        $kelases = Kelas::whereHas('pengampus', function($q) use ($user) {
            $q->where('guru_id', $user->id);
        })->get();

        $mapels = [];
        if ($selectedKelasId) {
            $mapels = Mapel::where(function($q) use ($user, $selectedKelasId) {
                $q->whereHas('strukturKurikulums.pengampus', function($sq) use ($user, $selectedKelasId) {
                    $sq->where('guru_id', $user->id)
                       ->where('kelas_id', $selectedKelasId);
                })
                ->orWhereHas('strukturKejuruans.pengampus', function($sq) use ($user, $selectedKelasId) {
                    $sq->where('guru_id', $user->id)
                       ->where('kelas_id', $selectedKelasId);
                });
            })->get();
        }

        // 3. Ambil data Matriks jika filter lengkap
        $siswas = collect();
        $existingNilai = collect();
        $pstsLaluNilai = collect();
        $jumlahTp = 1;
        $globalBobot = [
            'b_uh' => 60,
            'b_ujian' => 40,
            'b_praktek' => 50,
            'b_teori' => 50,
            'b_psts_lalu' => 0
        ];

        if ($selectedKelasId && $selectedTitimangsaId && $selectedMapelId) {
            // Ambil Siswa
            $siswas = Siswa::where('kelas_id', $selectedKelasId)->with('user')->get()->map(function($s) {
                return [
                    'id' => $s->id,
                    'nama' => $s->user->name ?? 'Tanpa Nama',
                    'nis' => $s->nis
                ];
            });

            // Ambil TP untuk menghitung pembagi NA Harian
            $jumlahTpDb = FormatifTp::whereHas('master', function($q) use ($selectedTitimangsaId, $selectedMapelId, $selectedKelasId, $user) {
                $q->where('titimangsa_id', $selectedTitimangsaId)
                  ->where('mapel_id', $selectedMapelId)
                  ->where('kelas_id', $selectedKelasId)
                  ->where('user_id', $user->id);
            })->count();
            
            $jumlahTp = $jumlahTpDb > 0 ? $jumlahTpDb : 1;

            // Ambil Nilai Sumatif
            $siswaIds = $siswas->pluck('id')->toArray();
            
            $existingNilai = SumatifNilai::whereIn('siswa_id', $siswaIds)
                                          ->where('titimangsa_id', $selectedTitimangsaId)
                                          ->where('mapel_id', $selectedMapelId)
                                          ->where('kelas_id', $selectedKelasId)
                                          ->get();

            // Cari Nilai PSTS Lalu jika ini adalah PSAS/PSAT (bukan PSTS)
            if (!$isPsts) {
                // Cari titimangsa PSTS di tahun ajaran & kurikulum yg sama
                $pstsTitimangsa = Titimangsa::where('tahun_ajaran_id', $selectedTahunId)
                                    ->where('kurikulum_id', $selectedKurikulumId)
                                    ->where('nama_periode', 'like', '%psts%')
                                    ->where('id', '<', $selectedTitimangsaId)
                                    ->orderBy('id', 'desc')
                                    ->first();

                if ($pstsTitimangsa) {
                    $pstsLaluNilai = SumatifNilai::whereIn('siswa_id', $siswaIds)
                                          ->where('titimangsa_id', $pstsTitimangsa->id)
                                          ->where('mapel_id', $selectedMapelId)
                                          ->where('kelas_id', $selectedKelasId)
                                          ->get();
                }
            }

            // Ekstrak Global Bobot dari record pertama yang ada
            if ($existingNilai->isNotEmpty()) {
                $firstRow = $existingNilai->first();
                $globalBobot = [
                    'b_uh' => $firstRow->b_uh ?? 60,
                    'b_ujian' => $firstRow->b_ujian ?? 40,
                    'b_praktek' => $firstRow->b_praktek ?? 50,
                    'b_teori' => $firstRow->b_teori ?? 50,
                    'b_psts_lalu' => $firstRow->b_psts_lalu ?? 0
                ];
            }
        }

        // Mapping PSTS lalu ke nilai agar mudah di frontend
        $mappedNilai = $existingNilai->map(function($n) use ($pstsLaluNilai) {
            $pstsLaluRow = $pstsLaluNilai->firstWhere('siswa_id', $n->siswa_id);
            // NA dari PSTS lalu dihitung berdasarkan na_value atau kalkulasi ulang
            $n->psts_lalu = $pstsLaluRow ? $pstsLaluRow->na_value : null; 
            return $n;
        });

        // Untuk siswa yg belum punya existingNilai, kita tetep perlu kirim psts_lalu nya
        $pstsDataOnly = [];
        foreach ($siswas as $s) {
            $pRow = $pstsLaluNilai->firstWhere('siswa_id', $s['id']);
            $pstsDataOnly[$s['id']] = $pRow ? $pRow->na_value : null;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'tahun_ajarans' => $tahunAjarans,
                'kurikulums' => $kurikulums,
                'periodes' => $periodes,
                'mapels' => $mapels,
                'kelases' => $kelases,
                'siswas' => $siswas,
                'nilai' => $mappedNilai,
                'psts_data' => $pstsDataOnly,
                'jumlah_tp' => $jumlahTp,
                'global_bobot' => $globalBobot,
                'selections' => [
                    'tahun_ajaran_id' => $selectedTahunId,
                    'kurikulum_id' => $selectedKurikulumId,
                    'titimangsa_id' => $selectedTitimangsaId,
                    'mapel_id' => $selectedMapelId,
                    'kelas_id' => $selectedKelasId,
                    'is_titimangsa_aktif' => $isTitimangsaAktif,
                    'is_psts' => $isPsts
                ]
            ]
        ]);
    }

    /**
     * Manual Bulk Save Nilai Sumatif
     */
    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran_id' => 'required',
            'titimangsa_id' => 'required',
            'mapel_id' => 'required',
            'kelas_id' => 'required',
            'data' => 'required|array'
        ]);

        $safeNum = function($val) {
            return ($val === '' || $val === null) ? null : floatval($val);
        };

        $maxUh = $request->max_uh ?? 1;
        
        foreach ($request->data as $row) {
            $avgUh = (($safeNum($row['uh1']) ?? 0) + ($safeNum($row['uh2']) ?? 0) + ($safeNum($row['uh3']) ?? 0) + ($safeNum($row['uh4']) ?? 0)) / $maxUh;
            $nUjian = (($safeNum($row['praktek']) ?? 0) * (($request->b_praktek ?? 50) / 100)) + 
                      (($safeNum($row['teori']) ?? 0) * (($request->b_teori ?? 50) / 100));
            
            $na = ($avgUh * (($request->b_uh ?? 60) / 100)) + 
                  ($nUjian * (($request->b_ujian ?? 40) / 100)) + 
                  (($safeNum($row['psts_lalu']) ?? 0) * (($request->b_psts_lalu ?? 0) / 100)) +
                  ($safeNum($row['literasi']) ?? 0);

            \Illuminate\Support\Facades\Log::info('DEBUG STORE', [
                'siswa_id' => $row['siswa_id'],
                'uh1' => $row['uh1'],
                'maxUh' => $maxUh,
                'avgUh' => $avgUh,
                'nUjian' => $nUjian,
                'b_uh' => $request->b_uh,
                'b_ujian' => $request->b_ujian,
                'na' => $na
            ]);

            SumatifNilai::updateOrCreate(
                [
                    'siswa_id' => $row['siswa_id'],
                    'titimangsa_id' => $request->titimangsa_id,
                    'mapel_id' => $request->mapel_id,
                    'kelas_id' => $request->kelas_id,
                ],
                [
                    'tahun_ajaran_id' => $request->tahun_ajaran_id,
                    'kurikulum_id' => $request->kurikulum_id,
                    
                    'uh1' => $safeNum($row['uh1']),
                    'uh2' => $safeNum($row['uh2']),
                    'uh3' => $safeNum($row['uh3']),
                    'uh4' => $safeNum($row['uh4']),
                    
                    'praktek' => $safeNum($row['praktek']),
                    'teori' => $safeNum($row['teori']),
                    'literasi' => $safeNum($row['literasi']) ?? 0,
                    
                    'na_value' => round($na, 1),
                    
                    'b_uh' => $request->b_uh ?? 60,
                    'b_ujian' => $request->b_ujian ?? 40,
                    'b_praktek' => $request->b_praktek ?? 50,
                    'b_teori' => $request->b_teori ?? 50,
                    'b_psts_lalu' => $request->b_psts_lalu ?? 0
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan secara manual'
        ]);
    }
}
