<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TahunAjaran;
use App\Models\EkskulSiswa;
use App\Models\PrestasiSiswa;
use App\Models\Titimangsa;

class SiswaPortofolioController extends Controller
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

        $allTitimangsa = Titimangsa::where('tahun_ajaran_id', $tahunAjaranAktif->id)
            ->pluck('id')
            ->toArray();

        // 1. Ambil Riwayat Ekskul Siswa di Tahun Ajaran Aktif
        $ekskulList = EkskulSiswa::with(['ekskul', 'titimangsa'])
            ->where('siswa_id', $siswa->id)
            ->whereIn('titimangsa_id', $allTitimangsa)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_ekskul' => $item->ekskul ? $item->ekskul->nama_ekskul : '-',
                    'nilai' => $item->nilai,
                    'periode' => $item->titimangsa ? $item->titimangsa->nama_periode : '-'
                ];
            });

        // 2. Ambil Riwayat Prestasi (All Time atau Tahun Ini)
        // Kita ambil all time agar terlihat portofolio penuhnya
        $prestasiList = PrestasiSiswa::with('titimangsa')
            ->where('siswa_id', $siswa->id)
            ->orderBy('tahun', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama_prestasi' => $item->nama_prestasi,
                    'jenis_prestasi' => $item->jenis_prestasi,
                    'tingkat' => $item->tingkat,
                    'penyelenggara' => $item->penyelenggara,
                    'tahun' => $item->tahun,
                    'periode' => $item->titimangsa ? $item->titimangsa->nama_periode : '-',
                    'keterangan' => $item->keterangan
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'ekskul' => $ekskulList,
                'prestasi' => $prestasiList
            ]
        ]);
    }
}
