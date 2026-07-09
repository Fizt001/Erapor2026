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

        // 5. Notifikasi Kasus Penanganan Aktif (SP1, SP2, SP3, Bimbingan BK)
        $notifikasi = PenangananPelanggaran::with(['siswa.user', 'siswa.kelas', 'guru'])
            ->where('status', 'Proses')
            ->whereIn('kategori', ['SP1', 'SP2', 'SP3', 'Penanganan BK'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($notif) {
                return [
                    'id' => $notif->id,
                    'kategori' => $notif->kategori,
                    'siswa' => $notif->siswa && $notif->siswa->user ? $notif->siswa->user->name : 'Unknown',
                    'kelas' => $notif->siswa && $notif->siswa->kelas ? $notif->siswa->kelas->tingkat . ' ' . $notif->siswa->kelas->nama_kelas : '-',
                    'deskripsi' => $notif->deskripsi_masalah,
                    'waktu' => $notif->created_at->diffForHumans()
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'kasus_hari_ini' => $kasusHariIni,
                'siswa_bermasalah' => $siswaBermasalah,
                'total_penanganan' => $totalPenanganan,
                'top_pelanggaran' => $topPelanggaran,
                'notifikasi' => $notifikasi
            ]
        ]);
    }
}
