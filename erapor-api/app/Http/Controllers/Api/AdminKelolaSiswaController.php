<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;

class AdminKelolaSiswaController extends Controller
{
    // Mengambil data kelas, siswa di dalamnya, dan user kandidat
    public function index($kelas_id)
    {
        $kelas = Kelas::findOrFail($kelas_id);
        
        // Ambil user role 'siswa' yang BELUM ada di tabel siswa
        $usersAvailable = User::where('role', 'siswa')
            ->whereDoesntHave('siswa')
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'email']);

        // Ambil data siswa yang terdaftar di kelas ini
        $students = Siswa::with('user:id,name,email')
            ->where('kelas_id', $kelas_id)
            ->orderBy('nis', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'kelas' => $kelas,
            'users_available' => $usersAvailable,
            'students' => $students
        ]);
    }

    // Tarik User ke Kelas (Bulk)
    public function storeBulk(Request $request, $kelas_id)
    {
        $request->validate([
            'siswa' => 'required|array|min:1',
            'siswa.*.user_id' => 'required|exists:users,id',
            'siswa.*.nis' => 'required|string',
        ]);

        $kelas = Kelas::findOrFail($kelas_id);
        
        $successCount = 0;
        $failedUsers = [];

        foreach ($request->siswa as $s) {
            $exists = Siswa::where('user_id', $s['user_id'])->first();
            $nisExists = Siswa::where('nis', $s['nis'])->first();
            
            if ($exists || $nisExists) {
                $failedUsers[] = $s['user_id'];
                continue;
            }

            Siswa::create([
                'user_id' => $s['user_id'],
                'kelas_id' => $kelas_id,
                'nis' => $s['nis']
            ]);
            $successCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "$successCount siswa berhasil ditambahkan." . (count($failedUsers) > 0 ? " Beberapa siswa gagal (NIS ganda atau sudah punya kelas)." : ""),
        ]);
    }

    // Koreksi NIS
    public function update(Request $request, $siswa_id)
    {
        $request->validate([
            'nis' => 'required|unique:siswa,nis,' . $siswa_id
        ]);

        $siswa = Siswa::findOrFail($siswa_id);
        $siswa->update(['nis' => $request->nis]);

        $siswa->load('user:id,name,email');

        return response()->json([
            'success' => true,
            'message' => 'NIS berhasil diperbarui!',
            'data' => $siswa
        ]);
    }

    // Keluarkan dari Kelas (Hapus Record Siswa)
    public function destroy($siswa_id)
    {
        $siswa = Siswa::findOrFail($siswa_id);
        $siswa->delete();

        return response()->json([
            'success' => true,
            'message' => 'Siswa berhasil dikeluarkan dari kelas!'
        ]);
    }
}
