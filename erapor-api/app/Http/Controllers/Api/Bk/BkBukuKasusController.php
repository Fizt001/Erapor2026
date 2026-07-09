<?php

namespace App\Http\Controllers\Api\Bk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Titimangsa;

class BkBukuKasusController extends Controller
{
    public function index(Request $request)
    {
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        $titimangsaAktif = Titimangsa::where('is_aktif', true)->first();
        
        $kelases = [];
        $titimangsas = collect();
        if ($tahunAktif) {
            $titimangsas = Titimangsa::with('tahunAjaran')
                ->where('tahun_ajaran_id', $tahunAktif->id)
                ->where(function($q) {
                    $q->where('nama_periode', 'like', '%ASAS%')
                      ->orWhere('nama_periode', 'like', '%ASAT%')
                      ->orWhere('nama_periode', 'like', '%PSAS%')
                      ->orWhere('nama_periode', 'like', '%PSAT%')
                      ->orWhere('nama_periode', 'like', '%PAS%')
                      ->orWhere('nama_periode', 'like', '%PAT%')
                      ->orWhere('nama_periode', 'like', '%Akhir%');
                })
                ->orderBy('id', 'asc')
                ->get();
        }
            
        if ($tahunAktif) {
            $kelases = Kelas::where('tahun_ajaran_id', $tahunAktif->id)
                            ->withCount('siswas')
                            ->with('waliKelas.guru')
                            ->orderBy('tingkat')->orderBy('nama_kelas')->get();
        }
        $selectedKelasId = $request->kelas_id;

        $siswas = [];
        if ($selectedKelasId) {
            $siswas = Siswa::with([
                'user', 
                'poinLogs.pelanggaran', 
                'poinLogs.tahunAjaran',
                'poinLogs.titimangsa',
                'penanganans.guru',
                'penanganans.tahunAjaran'
            ])
            ->where('kelas_id', $selectedKelasId)
            ->get()
            ->map(function($siswa) {
                return [
                    'id' => $siswa->id,
                    'nama' => $siswa->user->name ?? 'Tanpa Nama',
                    'nis' => $siswa->nis,
                    'sisa_poin' => $siswa->sisa_poin,
                    'poin_logs' => $siswa->poinLogs,
                    'penanganans' => $siswa->penanganans
                ];
            });
        }

        return response()->json([
            'success' => true,
            'data' => [
                'kelases' => $kelases,
                'siswas' => $siswas,
                'tahun_aktif' => $tahunAktif,
                'titimangsa_aktif' => $titimangsaAktif,
                'titimangsas' => $titimangsas
            ]
        ]);
    }
}
