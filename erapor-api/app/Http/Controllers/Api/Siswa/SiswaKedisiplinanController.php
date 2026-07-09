<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PoinSiswa;
use App\Models\TahunAjaran;

class SiswaKedisiplinanController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $siswa = $user->siswa;
        
        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Data siswa tidak ditemukan.'], 404);
        }

        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAjaranAktif) {
            return response()->json(['success' => false, 'message' => 'Belum ada tahun ajaran aktif.'], 404);
        }

        $history = PoinSiswa::with(['pelanggaran', 'guru', 'titimangsa'])
            ->where('siswa_id', $siswa->id)
            ->where('tahun_ajaran_id', $tahunAjaranAktif->id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $totalPotongan = $history->sum('skor_pengurang');
        $totalPenambah = $history->sum('skor_penambah');
        
        $sisaPoin = 100 - $totalPotongan + $totalPenambah;
        
        $data = $history->map(function ($item) {
            return [
                'id' => $item->id,
                'tanggal' => $item->tanggal,
                'jenis' => $item->skor_pengurang > 0 ? 'Pelanggaran' : 'Penghargaan',
                'deskripsi' => $item->pelanggaran ? $item->pelanggaran->nama_pelanggaran : 'Catatan Khusus',
                'catatan' => $item->catatan,
                'skor_pengurang' => $item->skor_pengurang,
                'skor_penambah' => $item->skor_penambah,
                'guru' => $item->guru ? $item->guru->name : 'Sistem/Admin',
                'periode' => $item->titimangsa ? $item->titimangsa->nama_periode : '-'
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'tahun_ajaran' => $tahunAjaranAktif->tahun_ajaran,
                'sisa_poin' => $sisaPoin,
                'history' => $data
            ]
        ]);
    }
}
