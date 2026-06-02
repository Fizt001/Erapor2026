<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Referensi;
use Illuminate\Http\Request;

class AdminReferensiController extends Controller
{
    public function index(Request $request)
    {
        $query = Referensi::query();
        if ($request->has('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        return response()->json([
            'success' => true,
            'data' => $query->orderBy('kode')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required|string|max:50',
            'kode' => 'required|string|max:50',
            'nama' => 'required|string|max:100',
        ]);

        $referensi = Referensi::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'data' => $referensi
        ]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'jenis' => 'required|string|max:50',
            'kode' => 'required|string|max:50',
            'nama' => 'required|string|max:100',
        ]);

        $referensi = Referensi::findOrFail($id);
        $referensi->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diupdate',
            'data' => $referensi
        ]);
    }

    public function destroy(string $id)
    {
        $referensi = Referensi::findOrFail($id);
        $referensi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus'
        ]);
    }
}
