<?php

namespace App\Http\Controllers\Api\Kurikulum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kkm;
use App\Models\Kurikulum;
use App\Models\TahunAjaran;

class KkmController extends Controller
{
    public function index(Request $request)
    {
        $tahun_ajaran_id = $request->query('tahun_ajaran_id');
        if (!$tahun_ajaran_id) {
            $aktif = TahunAjaran::where('status', 'Aktif')->first();
            $tahun_ajaran_id = $aktif ? $aktif->id : null;
        }

        $kurikulums = Kurikulum::orderBy('nama_kurikulum')->get();
        $kkms = Kkm::where('tahun_ajaran_id', $tahun_ajaran_id)->get();
        
        $tingkats = ['X', 'XI', 'XII'];
        $data = [];
        
        foreach ($kurikulums as $kur) {
            $kurikulumData = [
                'kurikulum_id' => $kur->id,
                'nama_kurikulum' => $kur->nama_kurikulum,
                'tingkat' => []
            ];
            
            foreach ($tingkats as $tingkat) {
                $kkm = $kkms->where('kurikulum_id', $kur->id)->where('tingkat', $tingkat)->first();
                $kurikulumData['tingkat'][] = [
                    'tingkat' => $tingkat,
                    'nilai' => $kkm ? $kkm->nilai : null,
                    'id' => $kkm ? $kkm->id : null
                ];
            }
            $data[] = $kurikulumData;
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'tahun_ajaran_id' => $tahun_ajaran_id
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'kurikulum_id' => 'required|exists:kurikulums,id',
            'tingkat' => 'required|in:X,XI,XII',
            'nilai' => 'required|numeric|min:0|max:100'
        ]);

        $kkm = Kkm::updateOrCreate(
            [
                'tahun_ajaran_id' => $request->tahun_ajaran_id,
                'kurikulum_id' => $request->kurikulum_id,
                'tingkat' => $request->tingkat
            ],
            [
                'nilai' => $request->nilai
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Nilai KKM berhasil disimpan.',
            'data' => $kkm
        ]);
    }
}
