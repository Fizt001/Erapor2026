<?php

namespace App\Http\Controllers\Api\Kurikulum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kurikulum;
use App\Models\StrukturKurikulum;
use App\Models\Mapel;
use App\Models\Referensi;

class StrukturUmumController extends Controller
{
    public function index(Request $request)
    {
        $kurikulums = Kurikulum::all();
        $selectedKurikulumId = $request->query('kurikulum_id') ?? ($kurikulums->first()?->id ?? null);

        $strukturs = [];
        $mapels = [];
        $referensi_kelompok = [];

        if ($selectedKurikulumId) {
            $strukturs = StrukturKurikulum::with('mapel')
                ->where('kurikulum_id', $selectedKurikulumId)
                ->get();

            $mapels = Mapel::where('kurikulum_id', $selectedKurikulumId)
                ->where('kategori', 'umum') 
                ->orderBy('nama_mapel')
                ->get();
                
            $referensi_kelompok = Referensi::where('jenis', 'kelompok_mapel')->orderBy('kode')->get();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'kurikulums' => $kurikulums,
                'selectedKurikulumId' => $selectedKurikulumId,
                'strukturs' => $strukturs,
                'mapels' => $mapels,
                'referensi_kelompok' => $referensi_kelompok
            ]
        ]);
    }

    public function strukturStore(Request $request)
    {
        $request->validate([
            'kurikulum_id'      => 'required|exists:kurikulums,id',
            'mapel_id'          => 'required|exists:mapels,id',
            'tingkat'           => 'required|in:X,XI,XII',
            'jp'                => 'required|integer|min:1',
        ]);

        $isExists = StrukturKurikulum::where('kurikulum_id', $request->kurikulum_id)
            ->where('mapel_id', $request->mapel_id)
            ->where('tingkat', $request->tingkat)
            ->exists();

        if ($isExists) {
            return response()->json([
                'success' => false,
                'message' => 'Mapel ini sudah terdaftar di tingkat tersebut.'
            ], 422);
        }

        $struktur = StrukturKurikulum::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Mapel berhasil ditambahkan ke struktur kurikulum!',
            'data' => $struktur
        ], 201);
    }

    public function strukturDestroy($id)
    {
        StrukturKurikulum::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mapel berhasil dikeluarkan dari struktur!'
        ]);
    }

    public function strukturUpdate(Request $request, $id)
    {
        $request->validate([
            'jp' => 'required|integer|min:1',
        ]);

        $struktur = StrukturKurikulum::findOrFail($id);
        $struktur->update(['jp' => $request->jp]);

        return response()->json([
            'success' => true,
            'message' => 'JP berhasil diperbarui!',
            'data' => $struktur
        ]);
    }
}
