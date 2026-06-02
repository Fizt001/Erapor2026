<?php

namespace App\Http\Controllers\Api\Kurikulum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\WaliKelas;
use App\Models\User;

class WaliKelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::with(['kurikulum', 'kejuruan', 'waliKelas.guru'])
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();
            
        $gurus = User::where('role', 'guru')->orderBy('name')->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $kelas,
            'gurus' => $gurus
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'guru_id' => 'required|exists:users,id'
        ]);

        $waliKelas = WaliKelas::updateOrCreate(
            ['kelas_id' => $request->kelas_id],
            ['guru_id' => $request->guru_id]
        );

        return response()->json([
            'success' => true,
            'message' => 'Wali Kelas berhasil ditugaskan.',
            'data' => $waliKelas
        ]);
    }

    public function destroy($id)
    {
        // $id refers to kelas_id
        $waliKelas = WaliKelas::where('kelas_id', $id)->firstOrFail();
        $waliKelas->delete();

        return response()->json([
            'success' => true,
            'message' => 'Wali Kelas berhasil dilepas.'
        ]);
    }
}
