<?php

namespace App\Http\Controllers\Api\Kurikulum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ekskul;

class EkskulController extends Controller
{
    public function index()
    {
        $ekskuls = Ekskul::latest()->get();
        return response()->json([
            'success' => true,
            'data' => $ekskuls
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_ekskul' => 'required|unique:ekskuls,nama_ekskul',
            'nama_pembina' => 'nullable|string'
        ]);

        $ekskul = Ekskul::create([
            'nama_ekskul' => $request->nama_ekskul,
            'nama_pembina' => $request->nama_pembina
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ekstrakurikuler berhasil ditambahkan!',
            'data' => $ekskul
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_ekskul' => 'required|unique:ekskuls,nama_ekskul,' . $id,
            'nama_pembina' => 'nullable|string'
        ]);

        $ekskul = Ekskul::findOrFail($id);
        $ekskul->update([
            'nama_ekskul' => $request->nama_ekskul,
            'nama_pembina' => $request->nama_pembina
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Ekstrakurikuler berhasil diperbarui!',
            'data' => $ekskul
        ]);
    }

    public function destroy($id)
    {
        $ekskul = Ekskul::findOrFail($id);
        $ekskul->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ekstrakurikuler berhasil dihapus!'
        ]);
    }
}
