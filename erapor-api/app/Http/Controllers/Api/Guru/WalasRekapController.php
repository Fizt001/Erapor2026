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
        
        $walas = WaliKelas::with(['kelas.kurikulum'])->where('guru_id', $user->id)->first();
        if (!$walas) {
            return null;
        }

        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAktif) return null;

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
        $siswas = Siswa::with('user')->where('kelas_id', $kelas->id)->get()->sortBy(function($siswa) {
            return $siswa->name;
        })->values();

        $masterKurikulum = Kurikulum::all();

        // Format data: Titimangsa -> list Siswa beserta nilai Absensi dan Catatannya
        $rekapData = [];

        foreach ($titimangsas as $titimangsa) {
            $siswaList = [];

            foreach ($siswas as $siswa) {
                // Cari Absensi untuk siswa ini pada periode ini (agregrasi dari semua bulan di absensi_siswas)
                // karena absensi_siswas disimpan per bulan, kita jumlahkan total_s, total_i, total_a
                $absensi = AbsensiSiswa::where('siswa_id', $siswa->id)
                    ->where('tahun_ajaran', $tahunAjaranAktif->tahun)
                    ->where('periode', $titimangsa->nama_periode)
                    ->get();
                
                $totalS = 0;
                $totalI = 0;
                $totalA = 0;

                foreach ($absensi as $ab) {
                    for ($i = 1; $i <= 31; $i++) {
                        $col = 'tgl_' . $i;
                        if ($ab->$col === 'S') $totalS++;
                        if ($ab->$col === 'I') $totalI++;
                        if ($ab->$col === 'A') $totalA++;
                    }
                }

                // Cari Catatan Wali Kelas untuk siswa ini pada periode ini
                $catatan = CatatanWaliKelas::where('siswa_id', $siswa->id)
                    ->where('titimangsa_id', $titimangsa->id)
                    ->first();

                $siswaList[] = [
                    'id' => $siswa->id,
                    'nama_siswa' => $siswa->name,
                    'nisn' => $siswa->nisn,
                    'absensi' => [
                        's' => $totalS,
                        'i' => $totalI,
                        'a' => $totalA,
                    ],
                    'catatan' => $catatan ? $catatan->catatan : ''
                ];
            }

            $rekapData[] = [
                'id' => $titimangsa->id,
                'nama_periode' => $titimangsa->nama_periode,
                'is_aktif' => $titimangsa->is_aktif,
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
            'catatan' => 'nullable|string'
        ]);

        $tahunAjaranAktif = TahunAjaran::where('status', 'Aktif')->first();

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
                'catatan' => $request->catatan
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Catatan Wali Kelas berhasil disimpan.'
        ]);
    }
}
