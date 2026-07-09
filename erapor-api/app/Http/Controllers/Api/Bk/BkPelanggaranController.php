<?php

namespace App\Http\Controllers\Api\Bk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pelanggaran;
use App\Models\TahunAjaran;

class BkPelanggaranController extends Controller
{
    public function index(Request $request)
    {
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        $query = Pelanggaran::query();

        if ($request->filled('search')) {
            $query->where('nama_pelanggaran', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('jenis') && $request->jenis !== 'Semua') {
            $query->where('jenis', $request->jenis);
        }

        $pelanggarans = $query->orderBy('jenis')->orderBy('bobot', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $pelanggarans,
            'tahun_aktif' => $tahunAktif
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggaran' => 'required|string|max:255',
            'bobot' => 'required|integer|min:1',
            'jenis' => 'required|in:Ringan,Sedang,Berat'
        ]);

        $pelanggaran = Pelanggaran::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Pelanggaran berhasil ditambahkan',
            'data' => $pelanggaran
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pelanggaran' => 'required|string|max:255',
            'bobot' => 'required|integer|min:1',
            'jenis' => 'required|in:Ringan,Sedang,Berat'
        ]);

        $pelanggaran = Pelanggaran::findOrFail($id);
        $pelanggaran->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Pelanggaran berhasil diperbarui',
            'data' => $pelanggaran
        ]);
    }

    public function destroy($id)
    {
        $pelanggaran = Pelanggaran::findOrFail($id);
        
        if ($pelanggaran->poinLogs()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus pelanggaran yang sudah tercatat pada data siswa.'
            ], 400);
        }
        
        $pelanggaran->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pelanggaran berhasil dihapus'
        ]);
    }
}
