<?php

namespace App\Http\Controllers\Api\Kurikulum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengampu;
use App\Models\Kelas;
use App\Models\StrukturKurikulum;
use App\Models\StrukturKejuruan;
use App\Models\User;
use App\Models\TahunAjaran;

class PengampuController extends Controller
{
    public function index(Request $request)
    {
        $kurikulum_id = $request->query('kurikulum_id');
        $tingkat = $request->query('tingkat', 'X');
        $kategori = $request->query('kategori', 'umum'); // umum or kejuruan

        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();

        if (!$kurikulum_id || !$tingkat) {
            return response()->json([
                'success' => true,
                'data' => [
                    'strukturs' => [],
                    'kelases' => []
                ],
                // For empty initial state, fetch all gurus or general gurus
                'gurus' => User::where('role', 'guru')->orderBy('name')->get(['id', 'name'])
            ]);
        }

        // 1. Fetch Kelases for this Kurikulum and Tingkat
        $kelases = Kelas::with('kejuruan.program')
            ->where('kurikulum_id', $kurikulum_id)
            ->where('tingkat', $tingkat)
            ->orderBy('nama_kelas')
            ->get();

        // 2. Fetch Strukturs (Umum or Kejuruan)
        if ($kategori === 'umum') {
            $strukturs = StrukturKurikulum::with(['mapel', 'pengampus.guru'])
                ->where('kurikulum_id', $kurikulum_id)
                ->where('tingkat', $tingkat)
                ->get();
        } else {
            $strukturs = StrukturKejuruan::with(['mapel', 'program', 'konsentrasi', 'pengampus.guru'])
                ->where('kurikulum_id', $kurikulum_id)
                ->where('tingkat', $tingkat)
                ->get();
        }

        // 3. Fetch Gurus based on kategori
        $guruQuery = User::where('role', 'guru')->orderBy('name');
        if ($kategori === 'umum') {
            $guruQuery->where('is_pengampu_umum', true);
        } else {
            $guruQuery->where('is_pengampu_kejuruan', true);
        }
        $gurus = $guruQuery->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => [
                'strukturs' => $strukturs,
                'kelases' => $kelases
            ],
            'gurus' => $gurus
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'struktur_id' => 'required',
            'jenis' => 'required|in:umum,kejuruan',
            'guru_id' => 'required|exists:users,id',
            'jp' => 'required|numeric'
        ]);

        $matchFields = [
            'kelas_id' => $request->kelas_id,
        ];
        
        if ($request->jenis === 'umum') {
            $matchFields['struktur_kurikulum_id'] = $request->struktur_id;
        } else {
            $matchFields['struktur_kejuruan_id'] = $request->struktur_id;
        }

        // Karena guru bisa ngajar dua kali di kelas yg sama untuk struktur yg sama (meski jarang),
        // biasanya kita match pakai guru_id juga, atau cukup replace guru sebelumnya?
        // Tapi old app pakai form tambah dan punya "sisa JP". Artinya satu struktur di satu kelas bisa diampu
        // oleh MULTIPLE guru (misal team teaching, atau JP dibagi dua guru).
        // Oleh karena itu, kita tidak pakai updateOrCreate hanya berdasarkan kelas & struktur,
        // tapi kita biarkan create jika kombinasi guru-nya berbeda. Atau create new record saja.
        
        // Wait, old app "updateOrCreate" or just "create"?
        // Let's just create new, so team teaching is possible. Tapi kalau updateOrCreate, ya ketimpa.
        // Old app: `Pengampu::create($request->all())`
        
        $pengampu = Pengampu::create([
            'kelas_id' => $request->kelas_id,
            'struktur_kurikulum_id' => $request->jenis === 'umum' ? $request->struktur_id : null,
            'struktur_kejuruan_id' => $request->jenis === 'kejuruan' ? $request->struktur_id : null,
            'guru_id' => $request->guru_id,
            'jp' => $request->jp
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tugas mengajar berhasil ditambahkan.',
            'data' => $pengampu
        ]);
    }

    public function destroy($id)
    {
        $pengampu = Pengampu::findOrFail($id);
        $pengampu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tugas mengajar berhasil dihapus.'
        ]);
    }
}
