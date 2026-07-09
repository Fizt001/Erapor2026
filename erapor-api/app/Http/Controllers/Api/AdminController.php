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
                'nama_kepsek' => '', 'nip_kepsek' => '',
                'nama_yayasan' => '', 'akreditasi' => '', 'logo_kiri' => null
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
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'logo_kiri' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'nama_yayasan' => 'nullable|string|max:255',
            'akreditasi' => 'nullable|string|max:50',
        ]);

        $sekolah = Sekolah::first();
        
        $data = $request->except(['logo', 'logo_kiri']);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            $logoDir = public_path('uploads/logo');
            if (!\Illuminate\Support\Facades\File::exists($logoDir)) {
                \Illuminate\Support\Facades\File::makeDirectory($logoDir, 0755, true);
            }
            
            $file->move($logoDir, $filename);
            
            // Delete old logo if exists
            if ($sekolah && $sekolah->logo && file_exists(public_path($sekolah->logo))) {
                @unlink(public_path($sekolah->logo));
            }
            
            $data['logo'] = 'uploads/logo/' . $filename;
        }

        if ($request->hasFile('logo_kiri')) {
            $fileKiri = $request->file('logo_kiri');
            $filenameKiri = time() . '_kiri_' . $fileKiri->getClientOriginalName();
            
            $logoDir = public_path('uploads/logo');
            if (!\Illuminate\Support\Facades\File::exists($logoDir)) {
                \Illuminate\Support\Facades\File::makeDirectory($logoDir, 0755, true);
            }
            
            $fileKiri->move($logoDir, $filenameKiri);
            
            // Delete old logo if exists
            if ($sekolah && $sekolah->logo_kiri && file_exists(public_path($sekolah->logo_kiri))) {
                @unlink(public_path($sekolah->logo_kiri));
            }
            
            $data['logo_kiri'] = 'uploads/logo/' . $filenameKiri;
        }

        if ($sekolah) {
            $sekolah->update($data);
        } else {
            $sekolah = Sekolah::create($data);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Profil Sekolah berhasil diperbarui',
            'data' => $sekolah
        ]);
    }
}
