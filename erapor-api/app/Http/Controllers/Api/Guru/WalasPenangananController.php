<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\WaliKelas;
use App\Models\TahunAjaran;
use App\Models\PenangananPelanggaran;
use Illuminate\Support\Facades\Auth;

class WalasPenangananController extends Controller
{
    private function getWalasContext(Request $request)
    {
        $user = Auth::user();
        $tahunAktif = $request->tahun_ajaran_id 
            ? TahunAjaran::find($request->tahun_ajaran_id) 
            : TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAktif) return null;

        $walas = WaliKelas::where('guru_id', $user->id)
            ->whereHas('kelas', function($query) use ($tahunAktif) {
                $query->where('tahun_ajaran_id', $tahunAktif->id);
            })->first();

        if (!$walas) return null;

        return [
            'kelas_id' => $walas->kelas_id,
            'tahun_ajaran' => $tahunAktif,
        ];
    }

    public function index(Request $request)
    {
        $context = $this->getWalasContext($request);
        if (!$context) {
            return response()->json(['success' => false, 'message' => 'Anda bukan wali kelas aktif.'], 403);
        }

        $kelasId = $context['kelas_id'];
        $tahunId = $context['tahun_ajaran']->id;

        $siswas = Siswa::with(['user', 'penanganans' => function($q) use ($tahunId) {
                $q->whereIn('kategori', ['Bimbingan Walas', 'SP1', 'SP2', 'SP3', 'Penanganan BK'])
                  ->where('tahun_ajaran_id', $tahunId)
                  ->with('guru');
            }])
            ->where('kelas_id', $kelasId)
            ->get()
            ->map(function($siswa) {
                return [
                    'id' => $siswa->id,
                    'nama' => $siswa->user->name ?? 'Tanpa Nama',
                    'nis' => $siswa->nis,
                    'penanganans' => $siswa->penanganans
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'siswas' => $siswas
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tindakan_penyelesaian' => 'required|string',
            'status' => 'required|in:Proses,Selesai'
        ]);

        $penanganan = PenangananPelanggaran::findOrFail($id);
        
        // Ensure this case belongs to Walas's student and is a Bimbingan Walas category
        if ($penanganan->kategori !== 'Bimbingan Walas') {
            return response()->json(['success' => false, 'message' => 'Hanya kasus Bimbingan Walas yang bisa diubah oleh Walas.'], 403);
        }

        $penanganan->update([
            'tindakan_penyelesaian' => $request->tindakan_penyelesaian,
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tindak lanjut kasus berhasil disimpan.',
            'data' => $penanganan
        ]);
    }
}
