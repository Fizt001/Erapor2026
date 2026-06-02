<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kurikulum;
use Illuminate\Http\Request;

class AdminKurikulumController extends Controller
{
    public function index()
    {
        $data = Kurikulum::orderBy('created_at', 'desc')->get();
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kurikulum' => 'required|string|max:255',
            'singkatan' => 'required|string|max:50'
        ]);

        $kurikulum = Kurikulum::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Kurikulum berhasil ditambahkan',
            'data' => $kurikulum
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kurikulum' => 'required|string|max:255',
            'singkatan' => 'required|string|max:50'
        ]);

        $kurikulum = Kurikulum::findOrFail($id);
        $kurikulum->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Kurikulum berhasil diperbarui',
            'data' => $kurikulum
        ]);
    }

    public function destroy($id)
    {
        $kurikulum = Kurikulum::findOrFail($id);
        $kurikulum->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kurikulum berhasil dihapus'
        ]);
    }
}
