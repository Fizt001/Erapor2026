<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiswaBiodataController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $siswa = $user->siswa;
        
        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Data siswa tidak ditemukan.'], 404);
        }

        $siswaData = $siswa->toArray();
        $siswaData['nama_lengkap'] = $user->name;

        return response()->json([
            'success' => true,
            'data' => $siswaData
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $siswa = $user->siswa;
        
        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Data siswa tidak ditemukan.'], 404);
        }

        DB::beginTransaction();
        try {
            // Filter out 'nis' from request so it can't be updated by the student
            $updateData = $request->except(['nama_lengkap', 'nis', 'id', 'user_id', 'kelas_id']);
            $siswa->update($updateData);

            if ($request->has('nama_lengkap') && !empty($request->nama_lengkap)) {
                $user->update(['name' => $request->nama_lengkap]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Biodata berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan biodata: ' . $e->getMessage()
            ], 500);
        }
    }
}
