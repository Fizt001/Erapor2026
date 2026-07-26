<?php

namespace App\Http\Controllers\Api\Kurikulum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mapel;
use App\Models\Kurikulum;
use App\Models\TahunAjaran;
use App\Models\Referensi;

class MapelController extends Controller
{
    public function index(Request $request)
    {
        $query = Mapel::with('kurikulum');

        if ($request->filled('kurikulum_id')) {
            $query->where('kurikulum_id', $request->kurikulum_id);
        }

        if ($request->filled('kelompok')) {
            $query->where('kelompok', $request->kelompok);
        }

        $mapels = $query->orderBy('kelompok')->orderBy('kode_mapel')->get();
            
        $kurikulums = Kurikulum::all();
        $kelompokMapel = Referensi::where('jenis', 'kelompok_mapel')->orderBy('kode')->get();
        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();

        return response()->json([
            'success' => true,
            'data' => $mapels,
            'kurikulums' => $kurikulums,
            'kelompok_mapel' => $kelompokMapel,
            'tahun_ajaran_aktif' => $tahunAjaranAktif
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kurikulum_id' => 'required|exists:kurikulums,id',
            'kode_mapel'   => 'required|string|max:50',
            'nama_mapel'   => 'required|string|max:255',
            'kelompok'     => 'required|string|max:50',
        ]);

        $mapel = Mapel::create([
            'kurikulum_id' => $request->kurikulum_id,
            'kode_mapel'   => $request->kode_mapel,
            'nama_mapel'   => $request->nama_mapel,
            'kelompok'     => $request->kelompok,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mata Pelajaran berhasil ditambahkan!',
            'data' => $mapel
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_mapel'   => 'required|string|max:50',
            'nama_mapel'   => 'required|string|max:255',
            'kurikulum_id' => 'required|exists:kurikulums,id',
            'kelompok'     => 'required|string|max:50',
        ]);

        $mapel = Mapel::findOrFail($id);
        $mapel->update($request->only('kode_mapel', 'nama_mapel', 'kurikulum_id', 'kelompok'));

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
