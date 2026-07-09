<?php

namespace App\Http\Controllers\Api\Bk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PoinSiswa;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use App\Models\AbsensiSiswa;

class BkLaporanController extends Controller
{
    public function index(Request $request)
    {
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        
        if (!$tahunAktif) {
            return response()->json(['success' => false, 'message' => 'Tahun Ajaran aktif tidak ditemukan.'], 400);
        }

        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        $kelases = [];
        if ($tahunAktif) {
            $kelases = Kelas::where('tahun_ajaran_id', $tahunAktif->id)
                            ->withCount('siswas')
                            ->orderBy('tingkat')->orderBy('nama_kelas')->get();
        }
        $kelasId = $request->kelas_id;

        $baseQuery = PoinSiswa::where('tahun_ajaran_id', $tahunAktif->id)
            ->whereNotNull('pelanggaran_id');

        if ($kelasId) {
            $baseQuery->whereHas('siswa', function($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        // Statistik Bulanan (1-12)
        $monthlyStats = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyStats[] = (clone $baseQuery)->whereMonth('tanggal', $i)->count();
        }

        // Statistik Kategori Pelanggaran
        $ringan = (clone $baseQuery)->whereHas('pelanggaran', function($q) {
            $q->where('jenis', 'Ringan');
        })->count();

        $sedang = (clone $baseQuery)->whereHas('pelanggaran', function($q) {
            $q->where('jenis', 'Sedang');
        })->count();

        $berat = (clone $baseQuery)->whereHas('pelanggaran', function($q) {
            $q->where('jenis', 'Berat');
        })->count();

        // Statistik Absensi
        $absensiBase = AbsensiSiswa::where('tahun_ajaran', $tahunAktif->tahun);
        if ($kelasId) {
            $absensiBase->whereHas('siswa', function($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }
        $absensis = $absensiBase->get();
        
        $absensiStats = ['S' => 0, 'I' => 0, 'A' => 0];
        $absensiMonthly = array_fill(0, 12, 0);

        foreach ($absensis as $ab) {
            $m = (int)$ab->bulan;
            $mIndex = $m >= 1 && $m <= 12 ? $m - 1 : -1;
            
            for ($d = 1; $d <= 31; $d++) {
                $col = "tgl_$d";
                $val = $ab->$col;
                
                if (isset($absensiStats[$val])) {
                    $absensiStats[$val]++;
                    if ($mIndex !== -1) {
                        $absensiMonthly[$mIndex]++;
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'kelases' => $kelases,
                'monthly' => $monthlyStats,
                'categories' => [$ringan, $sedang, $berat],
                'absensi_monthly' => $absensiMonthly,
                'absensi_categories' => [$absensiStats['S'], $absensiStats['I'], $absensiStats['A']],
                'tahun_aktif' => $tahunAktif
            ]
        ]);
    }
}
