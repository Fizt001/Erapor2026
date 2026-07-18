<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WaliKelas;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Cari tahun ajaran yang sedang aktif
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        
        $isWalas = false;
        if ($tahunAktif) {
            $cekWalas = WaliKelas::where('guru_id', $user->id)
                ->whereHas('kelas', function($query) use ($tahunAktif) {
                    $query->where('tahun_ajaran_id', $tahunAktif->id);
                })
                ->first();
                
            if ($cekWalas) {
                $isWalas = true;
            }
        }

        // Dapatkan Titimangsa/Periode yang aktif
        $periodeAktif = null;
        if ($tahunAktif) {
            $periodeAktif = \App\Models\Titimangsa::where('tahun_ajaran_id', $tahunAktif->id)
                                ->where('is_aktif', true)
                                ->first();
        }

        // Kalkulasi Statistik Guru
        $kelasIdsQuery = \App\Models\Pengampu::where('guru_id', $user->id)->select('kelas_id');
        
        $totalKelasDiajar = \App\Models\Pengampu::where('guru_id', $user->id)
            ->distinct('kelas_id')
            ->count('kelas_id');

        $totalSiswa = \App\Models\Siswa::whereIn('kelas_id', $kelasIdsQuery)->count();

        $mapelUmum = \App\Models\Pengampu::where('guru_id', $user->id)
            ->whereNotNull('struktur_kurikulum_id')
            ->distinct('struktur_kurikulum_id')
            ->count('struktur_kurikulum_id');

        $mapelKejuruan = \App\Models\Pengampu::where('guru_id', $user->id)
            ->whereNotNull('struktur_kejuruan_id')
            ->distinct('struktur_kejuruan_id')
            ->count('struktur_kejuruan_id');

        $totalMapelDiajar = $mapelUmum + $mapelKejuruan;

        // Data Grafik Nilai (Rata-rata Nilai Akhir per Kelas & Mapel yang Diampu)
        $grafikNilai = [];
        if ($periodeAktif) {
            $pengampus = \App\Models\Pengampu::with(['kelas', 'strukturKurikulum.mapel', 'strukturKejuruan.mapel'])
                ->where('guru_id', $user->id)
                ->get();
            
            foreach($pengampus as $p) {
                if ($p->kelas) {
                    $mapel = null;
                    if ($p->struktur_kurikulum_id && $p->strukturKurikulum) {
                        $mapel = $p->strukturKurikulum->mapel;
                    } else if ($p->struktur_kejuruan_id && $p->strukturKejuruan) {
                        $mapel = $p->strukturKejuruan->mapel;
                    }

                    if ($mapel) {
                        $avg = \App\Models\SumatifNilai::where('kelas_id', $p->kelas_id)
                            ->where('mapel_id', $mapel->id)
                            ->where('titimangsa_id', $periodeAktif->id)
                            ->avg('na_value');
                            
                        if ($avg !== null) {
                            $grafikNilai[] = [
                                'kelas' => $p->kelas->nama_kelas,
                                'mapel' => $mapel->nama_mapel,
                                'rata_rata' => round($avg, 1)
                            ];
                        }
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                ],
                'is_walas' => $isWalas,
                'akademik' => [
                    'tahun_ajaran' => $tahunAktif ? $tahunAktif->tahun : 'Belum Diset',
                    'periode' => $periodeAktif ? $periodeAktif->nama_periode : 'Belum Diset'
                ],
                'stats' => [
                    'total_kelas' => $totalKelasDiajar,
                    'total_mapel' => $totalMapelDiajar,
                    'total_siswa' => $totalSiswa
                ],
                'grafik_nilai' => $grafikNilai
            ]
        ]);
    }
}
