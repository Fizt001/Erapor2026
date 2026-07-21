<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Kejuruan;
use App\Models\Kurikulum;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class AdminKelasController extends Controller
{
    public function dependencies()
    {
        $kejuruans = Kejuruan::select('id', 'nama_konsentrasi', 'kode_konsentrasi')->get();
        $kurikulums = Kurikulum::select('id', 'nama_kurikulum', 'singkatan')->get();
        $tahunAjarans = TahunAjaran::select('id', 'tahun', 'is_aktif')->orderBy('id', 'desc')->get();

        return response()->json([
            'success' => true,
            'kejuruans' => $kejuruans,
            'kurikulums' => $kurikulums,
            'tahunAjarans' => $tahunAjarans
        ]);
    }

    public function index(Request $request)
    {
        $query = Kelas::with(['kejuruan', 'kurikulum', 'tahunAjaran', 'waliKelas.guru']);

        // Filter by tahun_ajaran_id or fallback to active tahun_ajaran
        if ($request->filled('tahun_ajaran_id')) {
            $query->where('tahun_ajaran_id', $request->tahun_ajaran_id);
        } else {
            $activeTahun = TahunAjaran::where('is_aktif', true)->first();
            if ($activeTahun) {
                $query->where('tahun_ajaran_id', $activeTahun->id);
            }
        }

        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_kelas', 'like', "%{$search}%");
        }

        $kelas = $query->orderBy('tingkat')->orderBy('nama_kelas')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $kelas
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'kejuruan_id' => 'required|exists:kejuruans,id',
            'kurikulum_id' => 'required|exists:kurikulums,id',
            'tingkat' => 'required|in:X,XI,XII',
            'nama_kelas' => 'required|string|max:255'
        ]);

        $kelas = Kelas::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil ditambahkan',
            'data' => $kelas
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'kejuruan_id' => 'required|exists:kejuruans,id',
            'kurikulum_id' => 'required|exists:kurikulums,id',
            'tingkat' => 'required|in:X,XI,XII',
            'nama_kelas' => 'required|string|max:255'
        ]);

        $kelas = Kelas::findOrFail($id);
        $kelas->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil diperbarui',
            'data' => $kelas
        ]);
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        
        try {
            $kelas->delete();
            return response()->json([
                'success' => true,
                'message' => 'Kelas berhasil dihapus'
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus: Kelas ini masih memiliki data terkait (Siswa, Jadwal, dll).'
            ], 500);
        }
    }

    public function generate(Request $request)
    {
        $request->validate([
            'source_tahun_id' => 'required|exists:tahun_ajarans,id',
            'target_tahun_id' => 'required|exists:tahun_ajarans,id|different:source_tahun_id'
        ]);

        $sourceClasses = Kelas::where('tahun_ajaran_id', $request->source_tahun_id)->get();
        if ($sourceClasses->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Tidak ada kelas di tahun ajaran sumber.'], 404);
        }

        $count = 0;
        foreach ($sourceClasses as $kelas) {
            // Check if already exists
            $exists = Kelas::where('tahun_ajaran_id', $request->target_tahun_id)
                ->where('tingkat', $kelas->tingkat)
                ->where('nama_kelas', $kelas->nama_kelas)
                ->exists();

            if (!$exists) {
                Kelas::create([
                    'tahun_ajaran_id' => $request->target_tahun_id,
                    'kejuruan_id' => $kelas->kejuruan_id,
                    'kurikulum_id' => $kelas->kurikulum_id,
                    'tingkat' => $kelas->tingkat,
                    'nama_kelas' => $kelas->nama_kelas
                ]);
                $count++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil menyalin $count kelas ke tahun ajaran target."
        ]);
    }
}
