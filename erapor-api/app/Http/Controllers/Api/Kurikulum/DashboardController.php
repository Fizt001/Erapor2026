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
use App\Models\PenangananPelanggaran;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Tahun Ajaran Aktif
        $taAktif = TahunAjaran::where('is_aktif', true)->first();

        // 2. Statistik Dasar
        $totalGuru = User::where('role', 'guru')->count();
        $totalMapel = Mapel::count();
        $totalKelas = $taAktif ? Kelas::where('tahun_ajaran_id', $taAktif->id)->count() : 0;
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
        $kelasTanpaWalasCount = 0;
        $mapelTanpaGuruCount = 0;

        if ($taAktif) {
            // Kelas di tahun aktif yang belum punya data di tabel wali_kelas 
            $kelasTanpaWalasCount = Kelas::where('tahun_ajaran_id', $taAktif->id)
                ->whereDoesntHave('waliKelas')
                ->count();
            
            // Mapel di struktur (Umum & Kejuruan) yang belum di-plot gurunya untuk tahun aktif
            $mapelTanpaGuruUmumCount = StrukturKurikulum::whereDoesntHave('pengampus', function($q) use ($taAktif) {
                $q->whereHas('kelas', function($q2) use ($taAktif) {
                    $q2->where('tahun_ajaran_id', $taAktif->id);
                });
            })->count();
            
            $mapelTanpaGuruKejuruanCount = StrukturKejuruan::whereDoesntHave('pengampus', function($q) use ($taAktif) {
                $q->whereHas('kelas', function($q2) use ($taAktif) {
                    $q2->where('tahun_ajaran_id', $taAktif->id);
                });
            })->count(); 
            
            $mapelTanpaGuruCount = $mapelTanpaGuruUmumCount + $mapelTanpaGuruKejuruanCount;
        }

        // 7. Notifikasi SP2 & SP3
        $notifikasi = [];
        if ($taAktif) {
            $notifikasi = PenangananPelanggaran::with(['siswa.user', 'siswa.kelas', 'guru'])
                ->where('tahun_ajaran_id', $taAktif->id)
                ->whereIn('kategori', ['SP2', 'SP3'])
                ->where(function($q) {
                    $q->where('status', 'Proses')
                      ->orWhere(function($q2) {
                          $q2->where('kategori', 'SP3')
                             ->where('status', 'Selesai')
                             ->where(function($q3) {
                                 $q3->whereNull('tindakan_penyelesaian')
                                    ->orWhere('tindakan_penyelesaian', 'not like', '%ACC Kurikulum%');
                             });
                      });
                })
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
        }

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
                ],
                'notifikasi' => $notifikasi
            ]
        ]);
    }
}
