<?php

namespace App\Http\Controllers\Api\Bk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Siswa;

class BkBukuKasusController extends Controller
{
    public function index(Request $request)
    {
        $kelases = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $selectedKelasId = $request->kelas_id;

        $siswas = [];
        if ($selectedKelasId) {
            $siswas = Siswa::with([
                'user', 
                'poinLogs.pelanggaran', 
                'penanganans.guru'
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
                'siswas' => $siswas
            ]
        ]);
    }
}
