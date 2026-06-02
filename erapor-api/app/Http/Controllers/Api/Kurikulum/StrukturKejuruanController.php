<?php

namespace App\Http\Controllers\Api\Kurikulum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kurikulum;
use App\Models\StrukturKejuruan;
use App\Models\Program;
use App\Models\Kejuruan;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\User;

class StrukturKejuruanController extends Controller
{
    public function index(Request $request)
    {
        $kurikulums = Kurikulum::all();
        $selectedKurikulumId = $request->query('kurikulum_id') ?? ($kurikulums->first()->id ?? null);
        $tingkat = $request->query('tingkat', 'X');

        $mapels = [];
        $kelases = [];
        $gurus = [];
        $dataUnit = [];

        if ($selectedKurikulumId) {
            $mapels = Mapel::where('kurikulum_id', $selectedKurikulumId)
                ->where('kategori', 'kejuruan')
                ->orderBy('nama_mapel')
                ->get();
            
            $kelases = Kelas::where('tingkat', $tingkat)->orderBy('nama_kelas')->get();
            
            $gurus = User::where('role', 'guru')
                ->where('is_pengampu_kejuruan', true)
                ->orderBy('name')
                ->get();

            if ($tingkat == 'X') {
                $dataUnit = Program::with(['strukturKejuruans' => function($q) use ($selectedKurikulumId, $tingkat) {
                    $q->where('kurikulum_id', $selectedKurikulumId)->where('tingkat', $tingkat)
                    ->with(['mapel', 'pengampus.guru', 'pengampus.kelas']); 
                }])->get();
            } else {
                $dataUnit = Kejuruan::with(['strukturKejuruans' => function($q) use ($selectedKurikulumId, $tingkat) {
                    $q->where('kurikulum_id', $selectedKurikulumId)->where('tingkat', $tingkat)
                    ->with(['mapel', 'pengampus.guru', 'pengampus.kelas']);
                }])->get();
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'kurikulums' => $kurikulums,
                'selectedKurikulumId' => $selectedKurikulumId,
                'tingkat' => $tingkat,
                'mapels' => $mapels,
                'kelases' => $kelases,
                'gurus' => $gurus,
                'dataUnit' => $dataUnit
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kurikulum_id'   => 'required|exists:kurikulums,id',
            'mapel_id'       => 'required|exists:mapels,id',
            'tingkat'        => 'required|in:X,XI,XII',
            'jp'             => 'required|integer|min:1',
            'program_id'     => 'required_if:tingkat,X', 
            'konsentrasi_id' => 'required_if:tingkat,XI,XII',
        ]);

        $isDuplicate = StrukturKejuruan::where([
            ['kurikulum_id', $request->kurikulum_id],
            ['mapel_id', $request->mapel_id],
            ['tingkat', $request->tingkat],
            ['program_id', $request->program_id],
            ['konsentrasi_id', $request->konsentrasi_id],
        ])->exists();

        if ($isDuplicate) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal! Mata pelajaran ini sudah terdaftar dalam struktur.'
            ], 422);
        }

        $struktur = StrukturKejuruan::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Mapel produktif berhasil ditambahkan!',
            'data' => $struktur
        ], 201);
    }

    public function destroy($id)
    {
        $struktur = StrukturKejuruan::findOrFail($id);
        $struktur->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Mapel produktif berhasil dihapus dari struktur!'
        ]);
    }
}
