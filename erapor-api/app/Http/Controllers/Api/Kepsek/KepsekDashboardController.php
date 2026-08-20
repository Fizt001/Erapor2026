<?php

namespace App\Http\Controllers\Api\Kepsek;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\SumatifNilai;
use App\Models\PenangananPelanggaran;
use App\Models\KasusGuru;
use App\Models\SupervisiGuru;
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

        // 4. Data untuk Grafik Analitik
        
        // 4a. Tren Kedisiplinan Siswa (Line Chart) - Jumlah Kasus per Bulan
        $trenPelanggaranSiswa = PenangananPelanggaran::select(
            DB::raw('MONTH(created_at) as bulan'),
            DB::raw('COUNT(*) as total')
        )
        ->whereYear('created_at', date('Y'))
        ->groupBy('bulan')
        ->orderBy('bulan')
        ->get();
        // Format ke bulan nama
        $bulanNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $trenPelanggaranSiswaFormatted = $trenPelanggaranSiswa->map(function($item) use ($bulanNames) {
            return [
                'bulan' => $bulanNames[$item->bulan - 1] ?? 'Unknown',
                'total' => $item->total
            ];
        });

        // 4b. Status Supervisi Guru (Doughnut Chart)
        $statusSupervisiGuru = SupervisiGuru::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();

        // 4c. Profil Kasus Guru (Bar Chart)
        $profilKasusGuru = KasusGuru::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();

        // 4d. Kategori Pelanggaran Siswa (Doughnut Chart)
        $kategoriPelanggaranSiswa = PenangananPelanggaran::select('kategori', DB::raw('COUNT(*) as total'))
            ->groupBy('kategori')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'taAktif' => $taAktif ? $taAktif->tahun : '-',
                'totalGuru' => $totalGuru,
                'totalSiswa' => $totalSiswa,
                'totalKelas' => $totalKelas,
                'kelasPerTingkat' => $kelasPerTingkat,
                'topRankingAll' => $topRankingAll,
                'analytics' => [
                    'trenPelanggaranSiswa' => $trenPelanggaranSiswaFormatted,
                    'statusSupervisiGuru' => $statusSupervisiGuru,
                    'profilKasusGuru' => $profilKasusGuru,
                    'kategoriPelanggaranSiswa' => $kategoriPelanggaranSiswa,
                ]
            ]
        ]);
    }
}
