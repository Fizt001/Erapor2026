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

        return response()->json([
            'success' => true,
            'data' => [
                'sekolah' => $sekolah,
                'guru_umum' => $guruUmumCount,
                'guru_produktif' => $guruProduktifCount,
                'walas' => $walasCount,
                'top_student' => $topStudent
            ]
        ]);
    }
}
