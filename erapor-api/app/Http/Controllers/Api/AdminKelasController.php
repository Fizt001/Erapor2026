<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Kejuruan;
use App\Models\Kurikulum;
use Illuminate\Http\Request;

class AdminKelasController extends Controller
{
    public function dependencies()
    {
        $kejuruans = Kejuruan::select('id', 'nama_konsentrasi', 'kode_konsentrasi')->get();
        $kurikulums = Kurikulum::select('id', 'nama_kurikulum', 'singkatan')->get();

        return response()->json([
            'success' => true,
            'kejuruans' => $kejuruans,
            'kurikulums' => $kurikulums
        ]);
    }

    public function index(Request $request)
    {
        $query = Kelas::with(['kejuruan', 'kurikulum']);

        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_kelas', 'like', "%{$search}%");
        }

        $kelas = $query->orderBy('tingkat')->orderBy('nama_kelas')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $kelas
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kejuruan_id' => 'required|exists:kejuruans,id',
            'kurikulum_id' => 'required|exists:kurikulums,id',
            'tingkat' => 'required|in:X,XI,XII',
            'nama_kelas' => 'required|string|max:255'
        ]);

        $kelas = Kelas::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil ditambahkan',
            'data' => $kelas
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kejuruan_id' => 'required|exists:kejuruans,id',
            'kurikulum_id' => 'required|exists:kurikulums,id',
            'tingkat' => 'required|in:X,XI,XII',
            'nama_kelas' => 'required|string|max:255'
        ]);

        $kelas = Kelas::findOrFail($id);
        $kelas->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil diperbarui',
            'data' => $kelas
        ]);
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil dihapus'
        ]);
    }
}
