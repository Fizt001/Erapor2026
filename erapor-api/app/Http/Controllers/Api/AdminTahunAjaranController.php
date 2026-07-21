<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminTahunAjaranController extends Controller
{
    public function index()
    {
        $data = TahunAjaran::orderBy('created_at', 'desc')->get();
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|string|max:20',
            'is_aktif' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            if ($request->is_aktif) {
                // Nonaktifkan semua tahun ajaran lain
                TahunAjaran::where('is_aktif', true)->update(['is_aktif' => false]);
            }

            $tahunAjaran = TahunAjaran::create([
                'tahun' => $request->tahun,
                'is_aktif' => $request->is_aktif ?? false
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tahun Ajaran berhasil ditambahkan',
                'data' => $tahunAjaran
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan Tahun Ajaran: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tahun' => 'required|string|max:20',
            'is_aktif' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            $tahunAjaran = TahunAjaran::findOrFail($id);

            if ($request->is_aktif) {
                // Nonaktifkan semua tahun ajaran lain jika yang ini diaktifkan
                TahunAjaran::where('id', '!=', $id)->update(['is_aktif' => false]);
            }

            $tahunAjaran->update([
                'tahun' => $request->tahun,
                'is_aktif' => $request->is_aktif ?? false
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tahun Ajaran berhasil diperbarui',
                'data' => $tahunAjaran
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui Tahun Ajaran: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        
        if ($tahunAjaran->is_aktif) {
            return response()->json([
                'success' => false,
                'message' => 'Tahun Ajaran yang sedang aktif tidak dapat dihapus'
            ], 400);
        }

        $tahunAjaran->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tahun Ajaran berhasil dihapus'
        ]);
    }
}
