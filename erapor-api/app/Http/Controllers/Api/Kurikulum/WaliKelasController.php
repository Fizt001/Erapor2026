<?php

namespace App\Http\Controllers\Api\Kurikulum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\WaliKelas;
use App\Models\User;

class WaliKelasController extends Controller
{
    public function index(Request $request)
    {
        $tahun_ajaran_id = $request->query('tahun_ajaran_id');
        if (!$tahun_ajaran_id) {
            $aktif = \App\Models\TahunAjaran::where('is_aktif', true)->first();
            $tahun_ajaran_id = $aktif ? $aktif->id : null;
        }

        $query = Kelas::with(['kurikulum', 'kejuruan', 'waliKelas.guru', 'tahunAjaran'])
            ->orderBy('tingkat')
            ->orderBy('nama_kelas');
            
        if ($tahun_ajaran_id) {
            $query->where('tahun_ajaran_id', $tahun_ajaran_id);
        }

        $kelas = $query->get();
            
        $gurus = User::where('role', 'guru')->orderBy('name')->get(['id', 'name']);
        $tahun_ajaran = \App\Models\TahunAjaran::orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $kelas,
            'gurus' => $gurus,
            'tahun_ajaran' => $tahun_ajaran,
            'active_tahun_ajaran_id' => $tahun_ajaran_id
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
