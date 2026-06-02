<?php

namespace App\Http\Controllers\Api\Bk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\PoinSiswa;
use App\Models\Pelanggaran;
use App\Models\TahunAjaran;
use App\Models\Titimangsa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BkPoinController extends Controller
{
    public function index(Request $request)
    {
        $kelases = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $selectedKelasId = $request->kelas_id;

        $siswas = [];
        if ($selectedKelasId) {
            $siswas = Siswa::with(['user', 'poinLogs.pelanggaran', 'poinLogs.tahunAjaran'])
                ->where('kelas_id', $selectedKelasId)
                ->get()
                ->map(function($siswa) {
                    return [
                        'id' => $siswa->id,
                        'nama' => $siswa->user->name ?? 'Tanpa Nama',
                        'nis' => $siswa->nis,
                        'sisa_poin' => $siswa->sisa_poin,
                        'poin_logs' => $siswa->poinLogs
                    ];
                });
        }

        $pelanggarans = Pelanggaran::orderBy('jenis')->orderBy('bobot', 'desc')->get();
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        
        $periodeAktif = [];
        if ($tahunAktif) {
            $titimangsa = Titimangsa::where('tahun_ajaran_id', $tahunAktif->id)->where('is_aktif', true)->get();
            $periodeAktif = $titimangsa->pluck('nama_periode')->toArray();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'kelases' => $kelases,
                'selectedKelasId' => $selectedKelasId,
                'siswas' => $siswas,
                'pelanggarans' => $pelanggarans,
                'tahun_aktif' => $tahunAktif,
                'periode_aktif' => $periodeAktif
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'jenis_input' => 'required|in:Pelanggaran,Penghargaan',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string'
        ]);

        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAktif) {
            return response()->json(['success' => false, 'message' => 'Tahun Ajaran aktif tidak ditemukan.'], 400);
        }

        $titimangsa = Titimangsa::where('tahun_ajaran_id', $tahunAktif->id)
                                ->where('is_aktif', true)
                                ->get();
        
        if ($titimangsa->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Tidak ada Titimangsa (Periode Penilaian) yang aktif. Input data dikunci.'], 400);
        }

        $activePeriods = $titimangsa->pluck('nama_periode')->toArray();
        $requestPeriode = $request->periode;
        
        if ($requestPeriode && !in_array($requestPeriode, $activePeriods)) {
            $activeList = implode(', ', $activePeriods);
            return response()->json(['success' => false, 'message' => "Periode terkunci. Hanya periode aktif ($activeList) yang dapat diinput."], 400);
        }

        // Cari titimangsa yang spesifik sesuai request
        $selectedTitimangsa = $titimangsa->where('nama_periode', $requestPeriode)->first() ?? $titimangsa->first();

        $data = [
            'siswa_id' => $request->siswa_id,
            'tahun_ajaran_id' => $tahunAktif->id,
            'titimangsa_id' => $selectedTitimangsa->id,
            'guru_id' => Auth::id() ?? 1, // Fallback if auth is not caught
            'tanggal' => $request->tanggal,
            'catatan' => $request->keterangan,
            'pelanggaran_id' => null,
            'skor_pengurang' => 0,
            'skor_penambah' => 0,
            'is_tambahan_walas' => false
        ];

        if ($request->jenis_input === 'Pelanggaran') {
            $request->validate(['pelanggaran_id' => 'required|exists:pelanggarans,id']);
            $pelanggaran = Pelanggaran::find($request->pelanggaran_id);
            $data['pelanggaran_id'] = $pelanggaran->id;
            $data['skor_pengurang'] = $pelanggaran->bobot;
        } else {
            $request->validate(['skor_penambah' => 'required|integer|min:1']);
            $data['skor_penambah'] = $request->skor_penambah;
        }

        $poin = PoinSiswa::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Data poin berhasil dicatat.',
            'data' => $poin
        ], 201);
    }

    public function destroy($id)
    {
        $poin = PoinSiswa::findOrFail($id);
        $poin->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data poin berhasil dihapus.'
        ]);
    }
}
