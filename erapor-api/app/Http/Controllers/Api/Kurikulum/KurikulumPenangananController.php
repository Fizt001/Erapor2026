<?php

namespace App\Http\Controllers\Api\Kurikulum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PenangananPelanggaran;
use App\Models\TahunAjaran;

class KurikulumPenangananController extends Controller
{
    public function index(Request $request)
    {
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAktif) {
            return response()->json(['success' => false, 'message' => 'Tahun ajaran aktif tidak ditemukan.'], 404);
        }

        $kasus = PenangananPelanggaran::with(['siswa.user', 'siswa.kelas', 'guru'])
            ->where('tahun_ajaran_id', $tahunAktif->id)
            ->whereIn('kategori', ['SP2', 'SP3'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($k) {
                return [
                    'id' => $k->id,
                    'kategori' => $k->kategori,
                    'deskripsi_masalah' => $k->deskripsi_masalah,
                    'tindakan_penyelesaian' => $k->tindakan_penyelesaian,
                    'status' => $k->status,
                    'created_at' => $k->created_at,
                    'siswa' => [
                        'id' => $k->siswa->id ?? null,
                        'nama' => $k->siswa->user->name ?? 'Unknown',
                        'nis' => $k->siswa->nis ?? '-',
                        'kelas' => $k->siswa->kelas->nama_kelas ?? '-'
                    ],
                    'pelapor' => $k->guru->name ?? 'Sistem'
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $kasus
        ]);
    }

    public function accSp3(Request $request, $id)
    {
        $request->validate([
            'catatan_kurikulum' => 'required|string',
            'keputusan' => 'required|string|in:Keluarkan,Pertahankan'
        ]);

        $kasus = PenangananPelanggaran::findOrFail($id);

        if ($kasus->kategori !== 'SP3') {
            return response()->json(['success' => false, 'message' => 'Hanya kasus SP3 yang bisa di-ACC.'], 400);
        }

        // Karena Kepsek belum ada, Kurikulum yang mengeksekusi akhir (ACC)
        $tindakanLama = $kasus->tindakan_penyelesaian;
        $tindakanBaru = "Catatan BK: " . ($tindakanLama ?: "Belum ada") . "\n\n" .
                        "ACC Kurikulum (" . date('d M Y H:i') . "): \n" .
                        "Keputusan: " . strtoupper($request->keputusan) . "\n" .
                        "Catatan: " . $request->catatan_kurikulum;

        $kasus->update([
            'tindakan_penyelesaian' => $tindakanBaru,
            'status' => 'Selesai'
        ]);

        if (strtolower($request->keputusan) === 'keluarkan' && $kasus->siswa) {
            $kasus->siswa->update([
                'status_siswa' => 'dikeluarkan',
                'tanggal_keluar' => date('Y-m-d'),
                'alasan_keluar' => 'Dikeluarkan karena kasus SP3. Catatan Kurikulum: ' . $request->catatan_kurikulum
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kasus SP3 berhasil di-ACC dan dieksekusi.'
        ]);
    }
}
