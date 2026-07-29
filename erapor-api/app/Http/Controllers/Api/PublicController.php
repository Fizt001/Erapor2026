<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WaliKelas;
use App\Models\SumatifNilai;
use App\Models\Siswa;
use App\Models\Sekolah;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    public function stats()
    {
        // 1. Get active tahun ajaran
        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();

        // 2. Count Gurus
        $guruUmumCount = User::where('role', 'guru')->where('is_pengampu_umum', true)->count();
        $guruProduktifCount = User::where('role', 'guru')->where('is_pengampu_kejuruan', true)->count();

        // 3. Count Wali Kelas
        $walasCount = WaliKelas::count();

        // 4. Top Student (Peringkat 1 Umum)
        $topStudent = null;
        if ($tahunAjaranAktif) {
            $topQuery = SumatifNilai::select('siswa_id', DB::raw('SUM(na_value) as total_nilai'))
                ->where('tahun_ajaran_id', $tahunAjaranAktif->id)
                ->groupBy('siswa_id')
                ->orderByDesc('total_nilai')
                ->first();

            if ($topQuery) {
                $siswa = Siswa::with('kelas')->find($topQuery->siswa_id);
                if ($siswa) {
                    $topStudent = [
                        'nama' => $siswa->nama_lengkap,
                        'kelas' => $siswa->kelas ? $siswa->kelas->nama_kelas : '-',
                        'total_nilai' => $topQuery->total_nilai
                    ];
                }
            }
        }

        // 5. Data Sekolah
        $sekolah = Sekolah::first();

        // 6. Early Warning System (Akumulasi Nilai per Kelas)
        $earlyWarning = [
            '10' => [],
            '11' => [],
            '12' => []
        ];

        if ($tahunAjaranAktif) {
            $kelasList = \App\Models\Kelas::where('tahun_ajaran_id', $tahunAjaranAktif->id)->get();
            
            // Ambil semua KKM
            $kkmList = \App\Models\Kkm::all();
            
            // Ambil semua SumatifNilai untuk tahun ajaran aktif, dikelompokkan berdasarkan kelas
            $sumatifQuery = SumatifNilai::where('tahun_ajaran_id', $tahunAjaranAktif->id)
                                        ->whereNotNull('na_value')
                                        ->get()
                                        ->groupBy('kelas_id');
            
            foreach ($kelasList as $kelas) {
                // Cari KKM untuk kelas ini
                $kkm = $kkmList->where('kurikulum_id', $kelas->kurikulum_id)
                               ->where('tingkat', $kelas->tingkat)
                               ->first();
                
                $kkmValue = $kkm ? $kkm->nilai : null;
                $kkmSet = !is_null($kkmValue);
                
                $tuntas = 0;
                $belumTuntas = 0;
                $totalCount = 0;
                $hasData = false;
                
                if (isset($sumatifQuery[$kelas->id])) {
                    $nilais = $sumatifQuery[$kelas->id];
                    $totalCount = $nilais->count();
                    
                    if ($totalCount > 0 && $kkmSet) {
                        $hasData = true;
                        $tuntas = $nilais->where('na_value', '>=', $kkmValue)->count();
                        $belumTuntas = $nilais->where('na_value', '<', $kkmValue)->count();
                    }
                }
                
                $tingkatStr = (string)$kelas->tingkat;
                if (isset($earlyWarning[$tingkatStr])) {
                    $earlyWarning[$tingkatStr][] = [
                        'nama_kelas' => $kelas->nama_kelas,
                        'kkm_set' => $kkmSet,
                        'kkm_value' => $kkmValue,
                        'has_data' => $hasData,
                        'tuntas' => $tuntas,
                        'belum_tuntas' => $belumTuntas,
                        'total' => $totalCount
                    ];
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'sekolah' => $sekolah,
                'guru_umum' => $guruUmumCount,
                'guru_produktif' => $guruProduktifCount,
                'walas' => $walasCount,
                'top_student' => $topStudent,
                'early_warning' => $earlyWarning
            ]
        ]);
    }
}
