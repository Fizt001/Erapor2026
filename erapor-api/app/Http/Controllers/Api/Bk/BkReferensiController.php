<?php

namespace App\Http\Controllers\Api\Bk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Referensi;

class BkReferensiController extends Controller
{
    public function index()
    {
        // Hanya memunculkan kategori_penanganan
        $data = Referensi::where('jenis', 'kategori_penanganan')->orderBy('id', 'desc')->get();
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|unique:referensis,kode',
            'nama' => 'required|string',
            'keterangan' => 'nullable|string'
        ]);

        $data = Referensi::create([
            'jenis' => 'kategori_penanganan', // Dikunci keras
            'kode' => $request->kode,
            'nama' => $request->nama,
            'keterangan' => $request->keterangan
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan.',
            'data' => $data
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode' => 'required|string|unique:referensis,kode,' . $id,
            'nama' => 'required|string',
            'keterangan' => 'nullable|string'
        ]);

        $referensi = Referensi::findOrFail($id);

        if ($referensi->jenis !== 'kategori_penanganan') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak berhak mengubah referensi ini.'
            ], 403);
        }

        $referensi->update([
            'kode' => $request->kode,
            'nama' => $request->nama,
            'keterangan' => $request->keterangan
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diubah.',
            'data' => $referensi
        ]);
    }

    public function destroy($id)
    {
        $referensi = Referensi::findOrFail($id);

        if ($referensi->jenis !== 'kategori_penanganan') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak berhak menghapus referensi ini.'
            ], 403);
        }

        $referensi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus.'
        ]);
    }
}
