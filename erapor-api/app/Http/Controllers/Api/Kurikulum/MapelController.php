<?php

namespace App\Http\Controllers\Api\Kurikulum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mapel;
use App\Models\Kurikulum;
use App\Models\TahunAjaran;

class MapelController extends Controller
{
    public function index(Request $request)
    {
        $kategori = $request->query('kategori', 'umum');
        
        $query = Mapel::with('kurikulum')
            ->where('kategori', $kategori);

        if ($request->filled('kurikulum_id')) {
            $query->where('kurikulum_id', $request->kurikulum_id);
        }

        $mapels = $query->orderBy('kurikulum_id')
            ->latest()
            ->get();
            
        $kurikulums = Kurikulum::all();
        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();

        return response()->json([
            'success' => true,
            'data' => $mapels,
            'kategori' => $kategori,
            'kurikulums' => $kurikulums,
            'tahun_ajaran_aktif' => $tahunAjaranAktif
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kurikulum_id' => 'required|exists:kurikulums,id',
            'kode_mapel' => 'required|string|max:50',
            'nama_mapel' => 'required|string|max:255',
            'kategori' => 'required|string|max:50',
            'kelompok' => 'nullable|string|max:50',
        ]);

        $mapel = Mapel::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Mata Pelajaran ' . ucfirst($request->kategori) . ' berhasil ditambahkan!',
            'data' => $mapel
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_mapel' => 'required|string|max:50',
            'nama_mapel' => 'required|string|max:255',
            'kategori' => 'required|string|max:50',
            'kurikulum_id' => 'required|exists:kurikulums,id',
            'kelompok' => 'nullable|string|max:50',
        ]);

        $mapel = Mapel::findOrFail($id);
        $mapel->update($request->only('kode_mapel', 'nama_mapel', 'kategori', 'kurikulum_id', 'kelompok'));

        return response()->json([
            'success' => true,
            'message' => 'Data Mata Pelajaran berhasil diperbarui!',
            'data' => $mapel
        ]);
    }

    public function destroy($id)
    {
        $mapel = Mapel::findOrFail($id);
        $nama = $mapel->nama_mapel;
        $mapel->delete();

        return response()->json([
            'success' => true,
            'message' => "Mata Pelajaran $nama berhasil dihapus!"
        ]);
    }
}
