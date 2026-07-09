<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use App\Models\MutasiSiswa;
use App\Models\Siswa;
use App\Models\PenangananPelanggaran;
use App\Models\WaliKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalasMutasiController extends Controller
{
    private function getActiveKelas($userId)
    {
        $walas = WaliKelas::where('user_id', $userId)
            ->whereHas('tahunAjaran', function ($q) {
                $q->where('is_aktif', true);
            })->first();
        return $walas ? $walas->kelas_id : null;
    }

    public function index(Request $request)
    {
        $kelasId = $this->getActiveKelas($request->user()->id);
        if (!$kelasId) {
            return response()->json(['success' => false, 'message' => 'Anda bukan wali kelas aktif'], 403);
        }

        $mutasi = MutasiSiswa::with(['siswa', 'kelas_tujuan'])
            ->where('kelas_asal_id', $kelasId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $mutasi
        ]);
    }

    public function getKelasList()
    {
        $kelas = \App\Models\Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        return response()->json([
            'success' => true,
            'data' => $kelas
        ]);
    }

    public function store(Request $request)
    {
        $kelasId = $this->getActiveKelas($request->user()->id);
        if (!$kelasId) {
            return response()->json(['success' => false, 'message' => 'Anda bukan wali kelas aktif'], 403);
        }

        $validator = Validator::make($request->all(), [
            'siswa_id' => 'required|exists:siswa,id',
            'jenis_mutasi' => 'required|in:pindah_kelas,pindah_sekolah,keluar,dikeluarkan',
            'tanggal_mutasi' => 'required|date',
            'alasan' => 'required|string|max:500',
            'kelas_tujuan_id' => 'nullable|exists:kelas,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $siswa = Siswa::where('id', $request->siswa_id)->where('kelas_id', $kelasId)->first();
        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Siswa tidak ditemukan di kelas Anda'], 404);
        }

        // Cek apakah ada pengajuan mutasi pending untuk siswa ini
        $existing = MutasiSiswa::where('siswa_id', $siswa->id)->where('status_approval', 'pending')->first();
        if ($existing) {
            return response()->json(['success' => false, 'message' => 'Siswa ini sudah memiliki pengajuan mutasi yang belum diproses'], 400);
        }

        // Validasi dikeluarkan butuh riwayat SP BK
        if ($request->jenis_mutasi === 'dikeluarkan') {
            $hasSP = PenangananPelanggaran::where('siswa_id', $siswa->id)
                        ->where('tindak_lanjut', 'like', '%SP%')
                        ->exists();
            if (!$hasSP) {
                // If they don't have SP, we might still allow it if we only check ANY penanganan, 
                // but let's check any penanganan as fallback if SP is strictly formatted.
                $hasPenanganan = PenangananPelanggaran::where('siswa_id', $siswa->id)->exists();
                if (!$hasPenanganan) {
                    return response()->json(['success' => false, 'message' => 'Siswa tidak dapat dikeluarkan karena tidak memiliki riwayat penanganan BK/SP.'], 400);
                }
            }
        }

        $mutasi = MutasiSiswa::create([
            'siswa_id' => $siswa->id,
            'jenis_mutasi' => $request->jenis_mutasi,
            'tanggal_mutasi' => $request->tanggal_mutasi,
            'alasan' => $request->alasan,
            'kelas_asal_id' => $kelasId,
            'kelas_tujuan_id' => $request->jenis_mutasi === 'pindah_kelas' ? $request->kelas_tujuan_id : null,
            'status_approval' => 'pending',
            'diajukan_oleh' => $request->user()->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan mutasi berhasil dikirim ke Admin',
            'data' => $mutasi
        ]);
    }
}
