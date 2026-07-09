<?php

namespace App\Http\Controllers\Api\Bk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\PenangananPelanggaran;
use App\Models\TahunAjaran;
use App\Models\Referensi;
use Illuminate\Support\Facades\Auth;

class BkPenangananController extends Controller
{
    public function index(Request $request)
    {
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        $kelases = [];
        $kategoriList = [];
        if ($tahunAktif) {
            $kelases = Kelas::where('tahun_ajaran_id', $tahunAktif->id)
                            ->withCount('siswas')
                            ->orderBy('tingkat')->orderBy('nama_kelas')->get();
            $kategoriList = Referensi::where('jenis', 'kategori_penanganan')->get();
        }
        $selectedKelasId = $request->kelas_id;

        $siswas = [];
        if ($selectedKelasId) {
            $siswas = Siswa::with(['user', 'penanganans.guru', 'penanganans.tahunAjaran'])
                ->where('kelas_id', $selectedKelasId)
                ->get()
                ->map(function($siswa) {
                    return [
                        'id' => $siswa->id,
                        'nama' => $siswa->user->name ?? 'Tanpa Nama',
                        'nis' => $siswa->nis,
                        'sisa_poin' => $siswa->sisa_poin,
                        'penanganans' => $siswa->penanganans
                    ];
                });
        }

        return response()->json([
            'success' => true,
            'data' => [
                'kelases' => $kelases,
                'selectedKelasId' => $selectedKelasId,
                'siswas' => $siswas,
                'tahun_aktif' => $tahunAktif,
                'kategoriList' => $kategoriList
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'kategori' => 'required|string',
            'deskripsi_masalah' => 'required|string',
            'tindakan_penyelesaian' => 'required|string',
            'status' => 'required|in:Proses,Selesai'
        ]);

        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAktif) {
            return response()->json(['success' => false, 'message' => 'Tahun Ajaran aktif tidak ditemukan.'], 400);
        }

        $penanganan = PenangananPelanggaran::create([
            'siswa_id' => $request->siswa_id,
            'guru_id' => Auth::id() ?? 1,
            'tahun_ajaran_id' => $tahunAktif->id,
            'kategori' => $request->kategori,
            'deskripsi_masalah' => $request->deskripsi_masalah,
            'tindakan_penyelesaian' => $request->tindakan_penyelesaian,
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tindak lanjut kasus berhasil disimpan.',
            'data' => $penanganan
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori' => 'required|string',
            'deskripsi_masalah' => 'required|string',
            'tindakan_penyelesaian' => 'required|string',
            'status' => 'required|in:Proses,Selesai'
        ]);

        $penanganan = PenangananPelanggaran::findOrFail($id);
        $penanganan->update([
            'kategori' => $request->kategori,
            'deskripsi_masalah' => $request->deskripsi_masalah,
            'tindakan_penyelesaian' => $request->tindakan_penyelesaian,
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status penanganan berhasil diubah.',
            'data' => $penanganan
        ]);
    }

    public function destroy($id)
    {
        $penanganan = PenangananPelanggaran::findOrFail($id);
        $penanganan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data penanganan berhasil dihapus.'
        ]);
    }
}
