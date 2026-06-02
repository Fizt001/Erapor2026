<?php

namespace App\Http\Controllers\Api\Kurikulum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Titimangsa;
use App\Models\TahunAjaran;
use App\Models\Kurikulum;
use App\Models\Referensi;

class TitimangsaController extends Controller
{
    public function index()
    {
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAktif) {
            return response()->json(['success' => false, 'message' => 'Tahun Ajaran aktif tidak ditemukan.'], 400);
        }

        $titimangsas = Titimangsa::with(['kurikulum', 'tahunAjaran'])
            ->where('tahun_ajaran_id', $tahunAktif->id)
            ->orderBy('kurikulum_id')
            ->orderBy('id')
            ->get();
            
        $kurikulums = Kurikulum::all();
        $referensiPeriode = Referensi::where('jenis', 'nama_periode')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'titimangsas' => $titimangsas,
                'kurikulums' => $kurikulums,
                'referensi_periode' => $referensiPeriode,
                'tahun_aktif' => $tahunAktif
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kurikulum_id' => 'required|exists:kurikulums,id',
            'nama_periode' => 'required|string',
            'target_tingkat' => 'required|string',
            'tanggal_cetak' => 'required|date',
            'tempat_cetak' => 'required|string'
        ]);

        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAktif) {
            return response()->json(['success' => false, 'message' => 'Tahun Ajaran aktif tidak ditemukan.'], 400);
        }

        $titimangsa = Titimangsa::create([
            'tahun_ajaran_id' => $tahunAktif->id,
            'kurikulum_id' => $request->kurikulum_id,
            'nama_periode' => $request->nama_periode,
            'target_tingkat' => $request->target_tingkat,
            'tanggal_cetak' => $request->tanggal_cetak,
            'tempat_cetak' => $request->tempat_cetak,
            'is_aktif' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Periode Titimangsa berhasil ditambahkan.',
            'data' => $titimangsa
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kurikulum_id' => 'required|exists:kurikulums,id',
            'nama_periode' => 'required|string',
            'target_tingkat' => 'required|string',
            'tanggal_cetak' => 'required|date',
            'tempat_cetak' => 'required|string'
        ]);

        $titimangsa = Titimangsa::findOrFail($id);
        $titimangsa->update([
            'kurikulum_id' => $request->kurikulum_id,
            'nama_periode' => $request->nama_periode,
            'target_tingkat' => $request->target_tingkat,
            'tanggal_cetak' => $request->tanggal_cetak,
            'tempat_cetak' => $request->tempat_cetak,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Periode Titimangsa berhasil diperbarui.',
            'data' => $titimangsa
        ]);
    }

    public function toggle($id)
    {
        $titimangsa = Titimangsa::findOrFail($id);
        
        // Aktifkan atau nonaktifkan.
        $titimangsa->update(['is_aktif' => !$titimangsa->is_aktif]);

        return response()->json([
            'success' => true,
            'message' => 'Status aktif Titimangsa berhasil diubah.',
            'data' => $titimangsa
        ]);
    }

    public function destroy($id)
    {
        $titimangsa = Titimangsa::findOrFail($id);
        $titimangsa->delete();

        return response()->json([
            'success' => true,
            'message' => 'Periode Titimangsa berhasil dihapus.'
        ]);
    }
}
