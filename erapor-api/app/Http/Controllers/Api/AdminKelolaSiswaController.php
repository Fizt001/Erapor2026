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
        $kelas = Kelas::with('tahunAjaran')->findOrFail($kelas_id);
        
        // Ambil semua kelas di tahun ajaran yang sama dan tingkat yang sama
        $semuaKelas = Kelas::where('tahun_ajaran_id', $kelas->tahun_ajaran_id)
            ->where('tingkat', $kelas->tingkat)
            ->orderBy('nama_kelas', 'asc')
            ->get(['id', 'nama_kelas', 'tingkat']);

        // Ambil user role 'siswa' yang BELUM ada di tabel siswa
        $usersAvailable = User::where('role', 'siswa')
            ->whereDoesntHave('siswa')
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'email']);

        // Ambil data siswa yang terdaftar di kelas ini
        $query = Siswa::with('user:id,name,email')->where('kelas_id', $kelas_id);
        
        if (request()->query('show_inactive') != '1') {
            $query->where('status_siswa', 'aktif');
        }

        $students = $query->orderBy('nis', 'asc')->get();

        return response()->json([
            'success' => true,
            'kelas' => $kelas,
            'semua_kelas' => $semuaKelas,
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

    // Koreksi NIS / Pindah Kelas
    public function update(Request $request, $siswa_id)
    {
        $request->validate([
            'nis' => 'required|unique:siswa,nis,' . $siswa_id,
            'kelas_id' => 'required|exists:kelas,id',
            'status_siswa' => 'nullable|in:aktif,lulus,pindah,keluar,dikeluarkan'
        ]);

        $siswa = Siswa::findOrFail($siswa_id);
        
        $newStatus = $request->status_siswa ?? $siswa->status_siswa;
        $tanggalKeluar = $siswa->tanggal_keluar;
        $alasanKeluar = $siswa->alasan_keluar;

        if ($newStatus === 'aktif') {
            $tanggalKeluar = null;
            $alasanKeluar = null;
        }

        $siswa->update([
            'nis' => $request->nis,
            'kelas_id' => $request->kelas_id,
            'status_siswa' => $newStatus,
            'tanggal_keluar' => $tanggalKeluar,
            'alasan_keluar' => $alasanKeluar,
        ]);

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
