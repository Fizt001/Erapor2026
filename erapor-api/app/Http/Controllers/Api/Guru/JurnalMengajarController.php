<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PertemuanGuru;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;

class JurnalMengajarController extends Controller
{
    public function getJurnal(Request $request)
    {
        $user = Auth::user();
        
        // Dapatkan tahun ajaran aktif
        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAjaranAktif) {
            return response()->json(['success' => false, 'message' => 'Tidak ada Tahun Ajaran aktif']);
        }

        // Ambil semua pertemuan untuk guru ini di tahun ajaran aktif, include kelas, mapel, dan titimangsa
        $pertemuans = PertemuanGuru::with(['kelas', 'mapel', 'titimangsa'])
            ->where('guru_id', $user->id)
            ->whereHas('titimangsa', function($q) use ($tahunAjaranAktif) {
                $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
            })
            ->orderBy('tanggal', 'asc')
            ->get();

        // Struktur data output
        $jurnalGanjil = [
            '07' => [], '08' => [], '09' => [], '10' => [], '11' => [], '12' => []
        ];
        
        $jurnalGenap = [
            '01' => [], '02' => [], '03' => [], '04' => [], '05' => [], '06' => []
        ];

        // Untuk menampung rincian pertemuan per kelas/mapel
        $totalGanjil = [];
        $totalGenap = [];

        foreach ($pertemuans as $p) {
            $bulan = date('m', strtotime($p->tanggal));
            $semester = (in_array($bulan, ['07', '08', '09', '10', '11', '12'])) ? 'Ganjil' : 'Genap';
            
            $key = $p->kelas->tingkat . ' ' . $p->kelas->nama_kelas . ' - ' . $p->mapel->nama_mapel;

            $dataItem = [
                'id' => $p->id,
                'tanggal' => $p->tanggal,
                'jam' => $p->jam_mulai . ' - ' . $p->jam_selesai,
                'materi' => $p->materi
            ];

            if ($semester == 'Ganjil') {
                if (!isset($jurnalGanjil[$bulan][$key])) {
                    $jurnalGanjil[$bulan][$key] = [];
                }
                $jurnalGanjil[$bulan][$key][] = $dataItem;

                if (!isset($totalGanjil[$key])) {
                    $totalGanjil[$key] = [];
                }
                $totalGanjil[$key][] = $dataItem;
            } else {
                if (!isset($jurnalGenap[$bulan][$key])) {
                    $jurnalGenap[$bulan][$key] = [];
                }
                $jurnalGenap[$bulan][$key][] = $dataItem;

                if (!isset($totalGenap[$key])) {
                    $totalGenap[$key] = [];
                }
                $totalGenap[$key][] = $dataItem;
            }
        }

        return response()->json([
            'success' => true,
            'tahun_ajaran' => $tahunAjaranAktif->tahun_ajaran,
            'data' => [
                'ganjil' => [
                    'bulanan' => $jurnalGanjil,
                    'total' => $totalGanjil
                ],
                'genap' => [
                    'bulanan' => $jurnalGenap,
                    'total' => $totalGenap
                ]
            ]
        ]);
    }
}
