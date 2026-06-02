<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\Ekskul;
use App\Models\TahunAjaran;
use App\Models\Sekolah;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $totalAdmin = User::where('role', 'admin')->count();
        $totalGuru = User::whereIn('role', ['guru', 'kurikulum', 'bk', 'kepsek'])->count();
        $totalSiswa = Siswa::count();
        
        $totalMapel = Mapel::count();
        $totalKelas = Kelas::count();
        $totalEkskul = Ekskul::count();
        
        $taAktif = TahunAjaran::where('is_aktif', true)->first();
        $sekolah = Sekolah::first();

        return response()->json([
            'success' => true,
            'data' => [
                'users' => [
                    'admin' => $totalAdmin,
                    'guru' => $totalGuru,
                    'siswa' => $totalSiswa,
                ],
                'master' => [
                    'mapel' => $totalMapel,
                    'kelas' => $totalKelas,
                    'ekskul' => $totalEkskul,
                ],
                'academic' => [
                    'tahun_ajaran' => $taAktif ? $taAktif->tahun . ' - ' . ucfirst($taAktif->semester) : 'Belum Diatur',
                ],
                'sekolah' => [
                    'nama' => $sekolah ? $sekolah->nama_sekolah : 'SMK Yatindo',
                    'npsn' => $sekolah ? $sekolah->npsn : '-',
                ]
            ]
        ]);
    }

    public function getSekolah(Request $request)
    {
        $sekolah = Sekolah::first();
        if (!$sekolah) {
            // Berikan template default jika kosong
            $sekolah = [
                'nama_sekolah' => '', 'npsn' => '', 'nss' => '', 'website' => '',
                'alamat' => '', 'kelurahan' => '', 'kecamatan' => '', 'kota' => '', 
                'provinsi' => '', 'kode_pos' => '', 'telepon' => '', 'email' => '',
                'nama_kepsek' => '', 'nip_kepsek' => ''
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $sekolah
        ]);
    }

    public function updateSekolah(Request $request)
    {
        $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'npsn' => 'nullable|string|max:50',
            'nss' => 'nullable|string|max:50',
            'website' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'kelurahan' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kota' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:20',
            'telepon' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'nama_kepsek' => 'nullable|string|max:255',
            'nip_kepsek' => 'nullable|string|max:100',
        ]);

        $sekolah = Sekolah::first();
        
        if ($sekolah) {
            $sekolah->update($request->all());
        } else {
            $sekolah = Sekolah::create($request->all());
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Profil Sekolah berhasil diperbarui',
            'data' => $sekolah
        ]);
    }
}
