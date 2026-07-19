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
        
        $currentMonth = (int) date('n');

        // Cari tahun ajaran berdasarkan is_aktif
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

        // Dapatkan Titimangsa/Periode yang aktif berdasarkan bulan riil
        $periodeAktif = null;
        if ($tahunAktif) {
            $allTitimangsa = \App\Models\Titimangsa::where('tahun_ajaran_id', $tahunAktif->id)
                                ->orderBy('tanggal_cetak', 'asc')
                                ->get();
                                
            if ($allTitimangsa->count() > 0) {
                if ($currentMonth >= 7 && $currentMonth <= 9) {
                    $periodeAktif = $allTitimangsa->get(0);
                } elseif ($currentMonth >= 10 && $currentMonth <= 12) {
                    $periodeAktif = $allTitimangsa->get(1) ?? $allTitimangsa->last();
                } elseif ($currentMonth >= 1 && $currentMonth <= 3) {
                    $periodeAktif = $allTitimangsa->get(2) ?? $allTitimangsa->last();
                } else {
                    $periodeAktif = $allTitimangsa->get(3) ?? $allTitimangsa->last();
                }
            }
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

        $semuaPeriode = collect();
        $totalPertemuan = 0;
        if ($tahunAktif) {
            $semuaPeriode = \App\Models\Titimangsa::where('tahun_ajaran_id', $tahunAktif->id)->orderBy('id', 'asc')->get();
            
            $currentMonth = date('m');
            $isGanjil = in_array($currentMonth, ['07', '08', '09', '10', '11', '12']);
            
            $totalPertemuan = \App\Models\PertemuanGuru::where('guru_id', $user->id)
                ->whereHas('titimangsa', function($q) use ($tahunAktif) {
                    $q->where('tahun_ajaran_id', $tahunAktif->id);
                })
                ->whereRaw($isGanjil ? 'MONTH(tanggal) IN (7,8,9,10,11,12)' : 'MONTH(tanggal) IN (1,2,3,4,5,6)')
                ->count();
        }

        // Data Grafik Nilai (Rata-rata Nilai Akhir per Kelas & Mapel yang Diampu)
        $grafikNilai = [];
        $periodeLabels = $semuaPeriode->pluck('nama_periode')->toArray();

        if ($tahunAktif && $semuaPeriode->count() > 0) {
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
                        $seriesData = [];
                        $hasData = false;

                        foreach ($semuaPeriode as $periode) {
                            $avg = \App\Models\SumatifNilai::where('kelas_id', $p->kelas_id)
                                ->where('mapel_id', $mapel->id)
                                ->where('titimangsa_id', $periode->id)
                                ->avg('na_value');
                                
                            if ($avg !== null && $avg > 0) {
                                $seriesData[] = round($avg, 1);
                                $hasData = true;
                            } else {
                                $seriesData[] = null;
                            }
                        }

                        if ($hasData) {
                            $namaKelasLengkap = ($p->kelas->tingkat ? $p->kelas->tingkat . ' ' : '') . $p->kelas->nama_kelas;
                            $grafikNilai[] = [
                                'kelas' => $namaKelasLengkap,
                                'mapel' => $mapel->nama_mapel,
                                'series' => $seriesData
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
                    'total_siswa' => $totalSiswa,
                    'total_pertemuan' => $totalPertemuan
                ],
                'grafik_nilai' => $grafikNilai,
                'periode_labels' => $periodeLabels
            ]
        ]);
    }
}
