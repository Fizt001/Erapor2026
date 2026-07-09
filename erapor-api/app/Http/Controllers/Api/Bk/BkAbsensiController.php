<?php

namespace App\Http\Controllers\Api\Bk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\AbsensiSiswa;
use App\Models\TahunAjaran;
use App\Models\Titimangsa;

class BkAbsensiController extends Controller
{
    public function index(Request $request)
    {
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        $tahun = $tahunAktif ? $tahunAktif->tahun : date('Y') . '/' . (date('Y') + 1);

        $kelases = [];
        if ($tahunAktif) {
            $kelases = Kelas::where('tahun_ajaran_id', $tahunAktif->id)
                            ->withCount('siswas')
                            ->orderBy('tingkat')->orderBy('nama_kelas')->get();
        }
        $selectedKelasId = $request->kelas_id;
        $bulan = $request->bulan ?? date('n');

        $siswas = [];
        if ($selectedKelasId) {
            $siswas = Siswa::with(['user', 'absensi' => function($q) use ($bulan, $tahun) {
                    $q->where('bulan', $bulan)->where('tahun_ajaran', $tahun);
                }])
                ->where('kelas_id', $selectedKelasId)
                ->get()
                ->map(function($siswa) {
                    return [
                        'id' => $siswa->id,
                        'nama' => $siswa->user->name ?? 'Tanpa Nama',
                        'nis' => $siswa->nis,
                        'absensi' => $siswa->absensi->first()
                    ];
                });
        }
        
        $periodeAktif = [];
        $titimangsas = [];
        if ($tahunAktif) {
            $titimangsaData = Titimangsa::where('tahun_ajaran_id', $tahunAktif->id)->get();
            $periodeAktif = $titimangsaData->where('is_aktif', true)->pluck('nama_periode')->toArray();
            $titimangsas = $titimangsaData->map(function($t) {
                return [
                    'id' => $t->id,
                    'kurikulum_id' => $t->kurikulum_id,
                    'nama_periode' => $t->nama_periode,
                    'is_aktif' => $t->is_aktif,
                    'tanggal_cetak' => $t->tanggal_cetak
                ];
            });
        }

        $masterKurikulum = \App\Models\Kurikulum::orderBy('id', 'asc')->get(['id', 'nama_kurikulum']);

        return response()->json([
            'success' => true,
            'data' => [
                'kelases' => $kelases,
                'siswas' => $siswas,
                'bulan' => $bulan,
                'tahun_ajaran' => $tahun,
                'periode_aktif' => $periodeAktif,
                'titimangsas' => $titimangsas,
                'master_kurikulum' => $masterKurikulum
            ]
        ]);
    }

    public function updateAbsensi(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'bulan' => 'required|integer|min:1|max:12',
            'tanggal' => 'required|integer|min:1|max:31',
            'status' => 'required|in:H,S,I,A,L,P'
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

        // Cari titimangsa yang spesifik sesuai request, atau ambil yang pertama jika tidak dikirim (fallback)
        $selectedTitimangsa = $titimangsa->where('nama_periode', $requestPeriode)->first() ?? $titimangsa->first();
        
        // Load kurikulum for the selected titimangsa
        $selectedTitimangsa->load('kurikulum');
        $kurikulumName = $selectedTitimangsa->kurikulum ? $selectedTitimangsa->kurikulum->nama_kurikulum : 'Kurikulum Merdeka';
        $periode = $selectedTitimangsa->nama_periode;

        $absensi = AbsensiSiswa::firstOrCreate(
            [
                'siswa_id' => $request->siswa_id,
                'bulan' => $request->bulan,
                'tahun_ajaran' => $tahunAktif->tahun
            ],
            [
                'kurikulum' => $kurikulumName,
                'periode' => $periode
            ]
        );

        $col = 'tgl_' . $request->tanggal;
        $absensi->$col = $request->status;
        $absensi->save();

        return response()->json([
            'success' => true,
            'message' => 'Data absensi berhasil diupdate.'
        ]);
    }
}
