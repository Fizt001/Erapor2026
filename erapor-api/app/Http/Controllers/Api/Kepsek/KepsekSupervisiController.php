<?php

namespace App\Http\Controllers\Api\Kepsek;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupervisiGuru;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class KepsekSupervisiController extends Controller
{
    public function index()
    {
        $supervisi = SupervisiGuru::with('guru')->orderBy('tanggal', 'desc')->get();
        return response()->json([
            'success' => true,
            'data' => $supervisi
        ]);
    }

    public function getGuruList()
    {
        $gurus = User::where('role', 'guru')->orderBy('name')->get();
        return response()->json([
            'success' => true,
            'data' => $gurus
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'guru_id' => 'required|exists:users,id',
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'keterangan' => 'nullable|string',
        ]);

        $supervisi = SupervisiGuru::create([
            'kepsek_id' => Auth::id(),
            'guru_id' => $request->guru_id,
            'tanggal' => $request->tanggal,
            'waktu' => $request->waktu,
            'keterangan' => $request->keterangan,
            'status' => 'Terjadwal'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal supervisi berhasil dibuat',
            'data' => $supervisi
        ]);
    }

    public function update(Request $request, $id)
    {
        $supervisi = SupervisiGuru::findOrFail($id);

        $request->validate([
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'keterangan' => 'nullable|string',
            'evaluasi' => 'nullable|string',
            'tindak_lanjut' => 'nullable|string',
            'status' => 'required|string'
        ]);

        $supervisi->update([
            'tanggal' => $request->tanggal,
            'waktu' => $request->waktu,
            'keterangan' => $request->keterangan,
            'evaluasi' => $request->evaluasi,
            'tindak_lanjut' => $request->tindak_lanjut,
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data supervisi berhasil diupdate',
            'data' => $supervisi
        ]);
    }

    public function destroy($id)
    {
        $supervisi = SupervisiGuru::findOrFail($id);
        $supervisi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal supervisi berhasil dihapus'
        ]);
    }
}
