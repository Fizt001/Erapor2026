<?php

namespace App\Http\Controllers\Api\Kepsek;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\SumatifNilai;
use Illuminate\Support\Facades\DB;

class KepsekDashboardController extends Controller
{
    public function index()
    {
        $taAktif = TahunAjaran::where('is_aktif', true)->first();

        // 1. Metrik Utama
        $totalGuru = User::where('role', 'guru')->count();
        $totalSiswa = Siswa::where('status_siswa', 'aktif')->count();
        $totalKelas = $taAktif ? Kelas::where('tahun_ajaran_id', $taAktif->id)->count() : 0;
        
        $kelasQuery = $taAktif ? Kelas::where('tahun_ajaran_id', $taAktif->id)->orderBy('nama_kelas')->get() : collect();
        $kelasIds = $kelasQuery->pluck('id')->toArray();

        // 2. Kelompokkan Kelas per Tingkat
        $kelasPerTingkat = [
            'X' => [],
            'XI' => [],
            'XII' => []
        ];

        foreach ($kelasQuery as $k) {
            if (isset($kelasPerTingkat[$k->tingkat])) {
                $kelasPerTingkat[$k->tingkat][] = [
                    'id' => $k->id,
                    'nama_kelas' => $k->nama_kelas
                ];
            }
        }

        // 3. Kalkulasi Ranking Top 3 untuk SEMUA kelas aktif (supaya frontend cepat ganti-ganti tanpa loading)
        $rataRataSiswa = SumatifNilai::select('siswa_id', DB::raw('AVG(na_value) as rata_rata'))
            ->whereHas('siswa', function($q) use ($kelasIds) {
                $q->whereIn('kelas_id', $kelasIds);
            })
            ->with(['siswa:id,kelas_id,user_id', 'siswa.user:id,name'])
            ->groupBy('siswa_id')
            ->get();

        // Group by kelas_id, sort, and take top 3
        $topRankingAll = [];
        
        $groupedByKelas = $rataRataSiswa->groupBy(function($item) {
            return $item->siswa->kelas_id ?? 0;
        });

        foreach ($groupedByKelas as $kelasId => $items) {
            $top3 = $items->sortByDesc('rata_rata')->take(3)->values()->map(function($item, $index) {
                return [
                    'peringkat' => $index + 1,
                    'siswa_id' => $item->siswa_id,
                    'nama' => optional($item->siswa->user)->name ?? 'Tanpa Nama',
                    'rata_rata' => round($item->rata_rata, 1)
                ];
            });
            $topRankingAll[$kelasId] = $top3;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'taAktif' => $taAktif ? $taAktif->tahun : '-',
                'totalGuru' => $totalGuru,
                'totalSiswa' => $totalSiswa,
                'totalKelas' => $totalKelas,
                'kelasPerTingkat' => $kelasPerTingkat,
                'topRankingAll' => $topRankingAll,
            ]
        ]);
    }
}
