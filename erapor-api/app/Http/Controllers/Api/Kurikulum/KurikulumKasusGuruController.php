<?php

namespace App\Http\Controllers\Api\Kurikulum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KasusGuru;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class KurikulumKasusGuruController extends Controller
{
    public function index()
    {
        $gurus = User::where('role', 'guru')
            ->withCount(['tugasMengajar as total_kasus' => function ($query) {
                // Just a placeholder trick to get guru count? No, we need actual kasus count
            }])
            // Actually let's fetch gurus and their kasus
            ->get();
            
        $kasus = KasusGuru::with(['guru', 'pelapor'])->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $kasus
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
            'kasus' => 'required|string',
        ]);

        $kasus = KasusGuru::create([
            'guru_id' => $request->guru_id,
            'pelapor_id' => Auth::id(),
            'tanggal' => $request->tanggal,
            'kasus' => $request->kasus,
            'tindak_lanjut' => $request->tindak_lanjut,
            'status' => $request->status ?? 'Terbuka'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kasus guru berhasil ditambahkan',
            'data' => $kasus
        ]);
    }

    public function update(Request $request, $id)
    {
        $kasus = KasusGuru::findOrFail($id);

        $request->validate([
            'tanggal' => 'required|date',
            'kasus' => 'required|string',
            'status' => 'required|string'
        ]);

        $kasus->update([
            'tanggal' => $request->tanggal,
            'kasus' => $request->kasus,
            'tindak_lanjut' => $request->tindak_lanjut,
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kasus guru berhasil diupdate',
            'data' => $kasus
        ]);
    }

    public function destroy($id)
    {
        $kasus = KasusGuru::findOrFail($id);
        $kasus->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kasus guru berhasil dihapus'
        ]);
    }
}
