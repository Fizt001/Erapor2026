<?php

namespace App\Http\Controllers\Api\Kepsek;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\WaliKelas;
use App\Models\TahunAjaran;
use App\Models\SumatifNilai;
use App\Models\PoinSiswa;
use App\Models\AbsensiSiswa;
use Illuminate\Support\Facades\DB;

class KepsekWaliKelasController extends Controller
{
    public function getDashboardStats($kelas_id)
    {
        // Cari tahun ajaran berdasarkan is_aktif
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();

        if (!$tahunAktif) {
            return response()->json(['success' => false, 'message' => 'Tidak ada tahun ajaran aktif']);
        }

        $walas = WaliKelas::where('kelas_id', $kelas_id)->first();

        if (!$walas) {
            return response()->json(['success' => false, 'message' => 'Kelas ini belum memiliki wali kelas aktif']);
        }

        $kelasId = $walas->kelas_id;

        // 1. Populasi Siswa (L/P) — eager load user untuk fix $s->name bug
        $siswaRaw = Siswa::with('user')->where('kelas_id', $kelasId)->where('status_siswa', 'aktif')->get();
        $totalSiswa = $siswaRaw->count();

        $laki = $siswaRaw->where('jenis_kelamin', 'L')->count();
        $perempuan = $siswaRaw->where('jenis_kelamin', 'P')->count();

        if ($totalSiswa == 0) {
            return response()->json([
                'success' => true,
                'data' => [
                    'populasi' => ['total' => 0, 'laki' => 0, 'perempuan' => 0],
                    'rata_rata_kelas' => 0,
                    'top_10' => [],
                    'penanganan' => [],
                    'prestasi_mapel' => [],
                    'notifikasi' => [],
                    'grafik_siswa' => [],
                    'grafik_kelas' => [],
                    'periode_labels' => [],
                    'grafik_kkm' => [],
                    'kkm_set' => false
                ]
            ]);
        }

        $siswaIds = $siswaRaw->pluck('id')->toArray();
        $namaSiswaMap = [];
        foreach ($siswaRaw as $s) {
            $namaSiswaMap[$s->id] = $s->user ? $s->user->name : '-';
        }

        // 2. Rata-rata Kelas dan Top 10 Siswa
        $sumatifRataRata = SumatifNilai::select('siswa_id', DB::raw('AVG(na_value) as avg_nilai'))
            ->whereIn('siswa_id', $siswaIds)
            ->whereNotNull('na_value')
            ->where('na_value', '>', 0)
            ->groupBy('siswa_id')
            ->get();

        $rataRataTotal = $sumatifRataRata->avg('avg_nilai');
        $rataRataKelas = round($rataRataTotal, 1);

        $siswaRataRata = [];
        foreach ($sumatifRataRata as $sr) {
            $siswaRataRata[] = [
                'id' => $sr->siswa_id,
                'nama' => $namaSiswaMap[$sr->siswa_id] ?? '-',
                'rata_rata' => round($sr->avg_nilai, 1)
            ];
        }

        usort($siswaRataRata, function($a, $b) { return $b['rata_rata'] <=> $a['rata_rata']; });
        $top10 = array_slice($siswaRataRata, 0, 10);

        // 3. Siswa Butuh Penanganan
        $poinRaw = PoinSiswa::select('siswa_id', DB::raw('SUM(skor_pengurang) as total_pengurang'))
            ->whereIn('siswa_id', $siswaIds)
            ->where('tahun_ajaran_id', $tahunAktif->id)
            ->groupBy('siswa_id')
            ->get()->keyBy('siswa_id');

        $absenRaw = AbsensiSiswa::select('siswa_id', DB::raw('SUM(total_a) as total_alpha'))
            ->whereIn('siswa_id', $siswaIds)
            ->where('tahun_ajaran', $tahunAktif->tahun)
            ->groupBy('siswa_id')
            ->get()->keyBy('siswa_id');

        $penanganan = [];
        foreach ($siswaRaw as $s) {
            $poin  = isset($poinRaw[$s->id]) ? $poinRaw[$s->id]->total_pengurang : 0;
            $alpha = isset($absenRaw[$s->id]) ? $absenRaw[$s->id]->total_alpha : 0;
            $score = $poin + ($alpha * 10);

            if ($score > 0) {
                $penanganan[] = [
                    'id'                => $s->id,
                    'nama'              => $namaSiswaMap[$s->id] ?? '-',
                    'poin_pelanggaran'  => (int) $poin,
                    'alpha'             => (int) $alpha,
                    'skor_risiko'       => $score
                ];
            }
        }
        usort($penanganan, function($a, $b) { return $b['skor_risiko'] <=> $a['skor_risiko']; });
        $butuhPenanganan = array_slice($penanganan, 0, 8); // Top 8 terbanyak

        // 4. Siswa Berprestasi Tiap Mapel
        $prestasiMapel = [];
        $topPerMapel = SumatifNilai::whereIn('siswa_id', $siswaIds)
            ->whereNotNull('na_value')
            ->where('na_value', '>', 0)
            ->selectRaw('mapel_id, MAX(na_value) as nilai_max')
            ->groupBy('mapel_id')
            ->with('mapel')
            ->get();

        foreach ($topPerMapel as $row) {
            $topRecord = SumatifNilai::whereIn('siswa_id', $siswaIds)
                ->where('mapel_id', $row->mapel_id)
                ->where('na_value', $row->nilai_max)
                ->first();

            if ($topRecord) {
                $prestasiMapel[] = [
                    'mapel'  => optional($row->mapel)->nama_mapel ?? 'Mapel ' . $row->mapel_id,
                    'siswa'  => $namaSiswaMap[$topRecord->siswa_id] ?? 'Unknown',
                    'nilai'  => $topRecord->na_value
                ];
            }
        }
        usort($prestasiMapel, fn($a, $b) => strcmp($a['mapel'], $b['mapel']));

        // 5. Notifikasi / Peringatan Sistem
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

        // 6. Data Grafik 4 Periode
        $titimangsas = \App\Models\Titimangsa::where('tahun_ajaran_id', $tahunAktif->id)
            ->orderBy('id', 'asc')
            ->get();
            
        $grafikSiswa = [];
        foreach ($siswaRaw as $s) {
            $grafikSiswa[$s->id] = [
                'id' => $s->id,
                'nama' => $namaSiswaMap[$s->id] ?? '-',
                'series' => []
            ];
            foreach ($titimangsas as $t) {
                $grafikSiswa[$s->id]['series'][$t->nama_periode] = 0;
            }
        }

        $allSumatif = SumatifNilai::whereIn('siswa_id', $siswaIds)
            ->where('tahun_ajaran_id', $tahunAktif->id)
            ->whereNotNull('na_value')
            ->get();
            
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
        $grafikSiswa = array_values($grafikSiswa);

        $grafikKelas = [];
        foreach ($titimangsas as $t) {
            $grafikKelas[$t->nama_periode] = 0;
        }
        $groupedByTitimangsa = $allSumatif->groupBy('titimangsa_id');
        foreach ($groupedByTitimangsa as $tId => $nilais) {
            $t = $titimangsas->firstWhere('id', $tId);
            if ($t) {
                $grafikKelas[$t->nama_periode] = round($nilais->avg('na_value'), 1);
            }
        }

        // 7. Data KKM Evaluasi
        $kelasInfo = \App\Models\Kelas::find($kelasId);
        $kkmModel = \App\Models\Kkm::where('kurikulum_id', $kelasInfo->kurikulum_id)
            ->where('tingkat', $kelasInfo->tingkat)
            ->first();
        
        $kkmValue = $kkmModel ? $kkmModel->nilai : null;
        $kkmSet = !is_null($kkmValue);
        
        $grafikKkm = [];
        $count = 0;
        
        foreach ($titimangsas as $t) {
            if ($count >= 4) break;
            
            $periodName = $t->nama_periode;
            $hasData = false;
            $tuntas = 0;
            $belumTuntas = 0;
            $totalCount = 0;

            $totalNilai = $allSumatif->where('titimangsa_id', $t->id);
            $totalCount = $totalNilai->count();
            
            if ($totalCount > 0 && $kkmSet) {
                $hasData = true;
                $tuntas = $totalNilai->where('na_value', '>=', $kkmValue)->count();
                $belumTuntas = $totalNilai->where('na_value', '<', $kkmValue)->count();
            }
            
            $grafikKkm[] = [
                'periode' => $periodName,
                'aktif' => true,
                'has_data' => $hasData,
                'kkm_set' => $kkmSet,
                'tuntas' => $tuntas,
                'belum_tuntas' => $belumTuntas,
                'total' => $totalCount,
                'kkm_value' => $kkmValue
            ];
            
            $count++;
        }
        
        $defaultPeriods = ['Periode 1', 'Periode 2', 'Periode 3', 'Periode 4'];
        while ($count < 4) {
            $grafikKkm[] = [
                'periode' => $defaultPeriods[$count],
                'aktif' => false,
                'has_data' => false,
                'kkm_set' => $kkmSet,
                'tuntas' => 0,
                'belum_tuntas' => 0,
                'total' => 0,
                'kkm_value' => $kkmValue
            ];
            $count++;
        }

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
                'grafik_kelas' => $grafikKelas,
                'periode_labels' => $titimangsas->pluck('nama_periode'),
                'grafik_kkm' => $grafikKkm,
                'kkm_set' => $kkmSet
            ]
        ]);
    }
}
