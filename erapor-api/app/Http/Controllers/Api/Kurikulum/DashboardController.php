<?php

namespace App\Http\Controllers\Api\Kurikulum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\Ekskul;
use App\Models\StrukturKurikulum;
use App\Models\StrukturKejuruan;
use App\Models\TahunAjaran;
use App\Models\Kkm;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Tahun Ajaran Aktif
        $taAktif = TahunAjaran::where('is_aktif', true)->first();

        // 2. Statistik Dasar
        $totalGuru = User::where('role', 'guru')->count();
        $totalMapel = Mapel::count();
        $totalKelas = Kelas::count();
        $totalEkskul = Ekskul::count();

        // 3. Tambahan Statistik Lanjutan
        $jpUmum = StrukturKurikulum::sum('jp');
        $jpKejuruan = StrukturKejuruan::sum('jp');
        $totalJP = $jpUmum + $jpKejuruan;

        // Rata-rata KKM Tahun Aktif
        $avgKkm = 0;
        $kkms = [];
        if ($taAktif) {
            $avgKkm = Kkm::where('tahun_ajaran_id', $taAktif->id)->avg('nilai');
            $kkms = Kkm::where('tahun_ajaran_id', $taAktif->id)->pluck('nilai', 'tingkat')->toArray();
        }

        // 4. Data untuk Chart (Distribusi Mapel)
        $totalMapelUmum = Mapel::where('kategori', 'umum')->count();
        $totalMapelKejuruan = Mapel::where('kategori', 'kejuruan')->count();

        // 5. Data untuk Chart (JP per Tingkat)
        $jpTingkatX = StrukturKurikulum::where('tingkat', 'X')->sum('jp') + StrukturKejuruan::where('tingkat', 'X')->sum('jp');
        $jpTingkatXI = StrukturKurikulum::where('tingkat', 'XI')->sum('jp') + StrukturKejuruan::where('tingkat', 'XI')->sum('jp');
        $jpTingkatXII = StrukturKurikulum::where('tingkat', 'XII')->sum('jp') + StrukturKejuruan::where('tingkat', 'XII')->sum('jp');

        // 6. ALERT & CHECKLIST
        // Kelas yang belum punya data di tabel wali_kelas 
        $kelasTanpaWalasCount = Kelas::whereDoesntHave('waliKelas')->count();
        
        // Mapel di struktur (Umum & Kejuruan) yang belum di-plot gurunya
        $mapelTanpaGuruUmumCount = StrukturKurikulum::whereDoesntHave('pengampus')->count();
        $mapelTanpaGuruKejuruanCount = StrukturKejuruan::whereDoesntHave('pengampus')->count(); 
        $mapelTanpaGuruCount = $mapelTanpaGuruUmumCount + $mapelTanpaGuruKejuruanCount;

        return response()->json([
            'success' => true,
            'data' => [
                'taAktif' => $taAktif,
                'totalGuru' => $totalGuru,
                'totalMapel' => $totalMapel,
                'totalKelas' => $totalKelas,
                'totalEkskul' => $totalEkskul,
                'totalJP' => $totalJP,
                'avgKkm' => $avgKkm ? round($avgKkm, 1) : 0,
                'kkms' => $kkms,
                'distribusiMapel' => [
                    'umum' => $totalMapelUmum,
                    'kejuruan' => $totalMapelKejuruan
                ],
                'jpPerTingkat' => [
                    'X' => $jpTingkatX,
                    'XI' => $jpTingkatXI,
                    'XII' => $jpTingkatXII
                ],
                'alerts' => [
                    'kelasTanpaWalas' => $kelasTanpaWalasCount,
                    'mapelTanpaGuru' => $mapelTanpaGuruCount
                ]
            ]
        ]);
    }
}
