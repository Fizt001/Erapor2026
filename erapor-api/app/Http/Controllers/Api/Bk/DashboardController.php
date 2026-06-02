<?php

namespace App\Http\Controllers\Api\Bk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pelanggaran;
use App\Models\PoinSiswa;
use App\Models\Siswa;
use App\Models\PenangananPelanggaran;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Total Kasus Hari Ini
        $kasusHariIni = PoinSiswa::whereDate('created_at', Carbon::today())->count();

        // 2. Total Siswa dengan Poin Negatif (Pelanggar)
        $siswaBermasalah = PoinSiswa::where('skor_pengurang', '>', 0)
                                    ->distinct('siswa_id')
                                    ->count('siswa_id');

        // 3. Total Kasus Penanganan
        $totalPenanganan = PenangananPelanggaran::count();

        // 4. Pelanggaran Terbanyak Bulan Ini
        $topPelanggaran = PoinSiswa::with('pelanggaran')
            ->whereNotNull('pelanggaran_id')
            ->whereMonth('created_at', Carbon::now()->month)
            ->selectRaw('pelanggaran_id, count(*) as total')
            ->groupBy('pelanggaran_id')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'kasus_hari_ini' => $kasusHariIni,
                'siswa_bermasalah' => $siswaBermasalah,
                'total_penanganan' => $totalPenanganan,
                'top_pelanggaran' => $topPelanggaran
            ]
        ]);
    }
}
