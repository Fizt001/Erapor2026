<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\WaliKelas;
use App\Models\TahunAjaran;
use App\Models\SumatifNilai;
use App\Models\PoinSiswa;
use App\Models\AbsensiSiswa;
use Illuminate\Support\Facades\DB;

class WalasDashboardStatsController extends Controller
{
    public function getStats(Request $request)
    {
        $user = Auth::user();
        
        $currentMonth = (int) date('n');

        // Cari tahun ajaran berdasarkan is_aktif
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();

        if (!$tahunAktif) {
            return response()->json(['success' => false, 'message' => 'Tidak ada tahun ajaran aktif']);
        }

        $walas = WaliKelas::where('guru_id', $user->id)
            ->whereHas('kelas', function($q) use ($tahunAktif) {
                $q->where('tahun_ajaran_id', $tahunAktif->id);
            })->first();

        if (!$walas) {
            return response()->json(['success' => false, 'message' => 'Anda bukan wali kelas aktif']);
        }

        $kelasId = $walas->kelas_id;

        // 1. Populasi Siswa (L/P)
        $siswaRaw = Siswa::where('kelas_id', $kelasId)->where('status_siswa', 'aktif')->get();
        $totalSiswa = $siswaRaw->count();
        $laki = $siswaRaw->where('jenis_kelamin', 'L')->count();
        $perempuan = $siswaRaw->where('jenis_kelamin', 'P')->count();

        $siswaIds = $siswaRaw->pluck('id')->toArray();

        // 2. Agregat Rata-rata Nilai (Sumatif) per Siswa untuk periode berjalan
        $rataRataQuery = SumatifNilai::select('siswa_id', DB::raw('AVG(na_value) as rata_rata'))
            ->whereIn('siswa_id', $siswaIds)
            ->whereNotNull('na_value')
            ->where('na_value', '>', 0)
            ->groupBy('siswa_id')
            ->get();

        $rataRataMap = $rataRataQuery->keyBy('siswa_id');

        // Top 10 Siswa
        $top10 = $rataRataQuery->sortByDesc('rata_rata')->take(10)->map(function($item) use ($siswaRaw) {
            $s = $siswaRaw->where('id', $item->siswa_id)->first();
            return [
                'id' => $item->siswa_id,
                'nama' => $s ? $s->name : 'Unknown',
                'nis' => $s ? $s->nis : '-',
                'rata_rata' => round($item->rata_rata, 1)
            ];
        })->values();

        // Rata-rata Kelas
        $rataRataKelas = $rataRataQuery->avg('rata_rata') ? round($rataRataQuery->avg('rata_rata'), 1) : 0;

        // 3. Siswa Butuh Penanganan (Kombinasi Poin & Absen)
        // Hitung total poin pengurang
        $poinRaw = PoinSiswa::select('siswa_id', DB::raw('SUM(skor_pengurang) as total_pengurang'))
            ->whereIn('siswa_id', $siswaIds)
            ->where('tahun_ajaran_id', $tahunAktif->id)
            ->groupBy('siswa_id')
            ->get()->keyBy('siswa_id');

        // Hitung total absen alpha
        // Asumsi di Erapor absensi dicatat bulanan, ambil total_a
        $absenRaw = AbsensiSiswa::select('siswa_id', DB::raw('SUM(total_a) as total_alpha'))
            ->whereIn('siswa_id', $siswaIds)
            ->where('tahun_ajaran', $tahunAktif->tahun)
            ->groupBy('siswa_id')
            ->get()->keyBy('siswa_id');

        $penanganan = [];
        foreach ($siswaRaw as $s) {
            $poin = isset($poinRaw[$s->id]) ? $poinRaw[$s->id]->total_pengurang : 0;
            $alpha = isset($absenRaw[$s->id]) ? $absenRaw[$s->id]->total_alpha : 0;
            $score = $poin + ($alpha * 10); // Alpha diberi bobot lebih

            if ($score > 0) {
                $penanganan[] = [
                    'id' => $s->id,
                    'nama' => $s->name,
                    'poin_pelanggaran' => (int) $poin,
                    'alpha' => (int) $alpha,
                    'skor_risiko' => $score
                ];
            }
        }
        usort($penanganan, function($a, $b) { return $b['skor_risiko'] <=> $a['skor_risiko']; });
        $butuhPenanganan = array_slice($penanganan, 0, 8); // Top 8 terbanyak

        // 4. Siswa Berprestasi Tiap Mapel (Tertinggi)
        $prestasiMapel = [];
        // Ambil mapel_id distinct dari SumatifNilai di kelas ini
        $mapelList = SumatifNilai::select('mapel_id')
            ->whereIn('siswa_id', $siswaIds)
            ->whereNotNull('na_value')
            ->where('na_value', '>', 0)
            ->distinct()
            ->with('mapel')
            ->get();

        foreach ($mapelList as $m) {
            // Cari siswa tertinggi di mapel ini
            $tertinggi = SumatifNilai::whereIn('siswa_id', $siswaIds)
                ->where('mapel_id', $m->mapel_id)
                ->whereNotNull('na_value')
                ->orderBy('na_value', 'desc')
                ->first();

            if ($tertinggi) {
                $s = $siswaRaw->where('id', $tertinggi->siswa_id)->first();
                $prestasiMapel[] = [
                    'mapel' => $m->mapel ? $m->mapel->nama_mapel : 'Mapel '.$m->mapel_id,
                    'siswa' => $s ? $s->name : 'Unknown',
                    'nilai' => $tertinggi->na_value
                ];
            }
        }
        
        // Sort by mapel name
        usort($prestasiMapel, function($a, $b) { return strcmp($a['mapel'], $b['mapel']); });

        // 5. Notifikasi / Peringatan Sistem (PenangananPelanggaran)
        $notifikasiRaw = \App\Models\PenangananPelanggaran::with(['siswa.user', 'guru'])
            ->whereIn('siswa_id', $siswaIds)
            ->where('tahun_ajaran_id', $tahunAktif->id)
            ->whereIn('kategori', ['Bimbingan Walas', 'SP1', 'SP2', 'SP3', 'Penanganan BK'])
            ->where('status', 'Proses')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        $notifikasi = $notifikasiRaw->map(function($notif) {
            return [
                'id' => $notif->id,
                'siswa' => $notif->siswa && $notif->siswa->user ? $notif->siswa->user->name : 'Unknown',
                'guru' => $notif->guru ? $notif->guru->name : 'Sistem',
                'deskripsi' => $notif->deskripsi_masalah,
                'waktu' => $notif->created_at->diffForHumans()
            ];
        });

        // 6. Data Grafik 4 Periode (Titimangsa aktif tahun ini)
        $titimangsas = \App\Models\Titimangsa::where('tahun_ajaran_id', $tahunAktif->id)
            ->orderBy('id', 'asc')
            ->get();
            
        $grafikSiswa = [];
        // Init struktur
        foreach ($siswaRaw as $s) {
            $grafikSiswa[$s->id] = [
                'id' => $s->id,
                'nama' => $s->name,
                'series' => []
            ];
            foreach ($titimangsas as $t) {
                $grafikSiswa[$s->id]['series'][$t->nama_periode] = 0;
            }
        }

        // Ambil semua sumatif di tahun aktif untuk siswa-siswa ini
        $allSumatif = SumatifNilai::whereIn('siswa_id', $siswaIds)
            ->where('tahun_ajaran_id', $tahunAktif->id)
            ->whereNotNull('na_value')
            ->get();
            
        // Group by siswa_id and titimangsa_id to calculate average
        $grouped = $allSumatif->groupBy(['siswa_id', 'titimangsa_id']);
        
        foreach ($grouped as $sId => $tData) {
            foreach ($tData as $tId => $nilais) {
                $t = $titimangsas->firstWhere('id', $tId);
                if ($t) {
                    $avg = round($nilais->avg('na_value'), 1);
                    $grafikSiswa[$sId]['series'][$t->nama_periode] = $avg;
                }
            }
        }
        
        $grafikSiswa = array_values($grafikSiswa); // Convert to array

        return response()->json([
            'success' => true,
            'data' => [
                'populasi' => [
                    'total' => $totalSiswa,
                    'laki' => $laki,
                    'perempuan' => $perempuan
                ],
                'rata_rata_kelas' => $rataRataKelas,
                'top_10' => $top10,
                'penanganan' => $butuhPenanganan,
                'prestasi_mapel' => $prestasiMapel,
                'notifikasi' => $notifikasi,
                'grafik_siswa' => $grafikSiswa,
                'periode_labels' => $titimangsas->pluck('nama_periode')
            ]
        ]);
    }
}
