<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TahunAjaran;
use App\Models\Kurikulum;
use App\Models\Titimangsa;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\FormatifTp;
use App\Models\FormatifNilai;
use App\Models\Kkm;

class FormatifNilaiController extends Controller
{
    /**
     * Get Dependensi Filter & Data Matriks Nilai
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // 1. Ambil opsi referensi (Sama seperti Master)
        $tahunAjarans = TahunAjaran::orderBy('id', 'desc')->get();
        $kurikulums = Kurikulum::all();

        $selectedTahunId = $request->tahun_ajaran_id ?? (TahunAjaran::where('is_aktif', true)->first()->id ?? null);
        $selectedKurikulumId = $request->kurikulum_id ?? ($kurikulums->first()->id ?? null);

        $periodes = Titimangsa::where('tahun_ajaran_id', $selectedTahunId)
                              ->where('kurikulum_id', $selectedKurikulumId)
                              ->get();
                              
        $selectedTitimangsaId = $request->titimangsa_id ?? ($periodes->where('is_aktif', true)->first()->id ?? null);
        $titimangsaData = $periodes->where('id', $selectedTitimangsaId)->first();
        $isTitimangsaAktif = $titimangsaData ? $titimangsaData->is_aktif : false;

        // 2. Filter Rombel & Mapel berdasarkan tugas mengajar guru (Paralel Data)
        $selectedKelasId = $request->kelas_id;
        $selectedMapelId = $request->mapel_id;

        // Ambil semua kelas di mana guru ini mengajar mapel apa pun
        $kelases = Kelas::whereHas('pengampus', function($q) use ($user) {
            $q->where('guru_id', $user->id);
        })->get();

        $mapels = [];
        if ($selectedKelasId) {
            // Ambil mapel yang HANYA diajarkan oleh guru ini di kelas yang dipilih
            $mapels = Mapel::where(function($q) use ($user, $selectedKelasId) {
                $q->whereHas('strukturKurikulums.pengampus', function($sq) use ($user, $selectedKelasId) {
                    $sq->where('guru_id', $user->id)
                       ->where('kelas_id', $selectedKelasId);
                })
                ->orWhereHas('strukturKejuruans.pengampus', function($sq) use ($user, $selectedKelasId) {
                    $sq->where('guru_id', $user->id)
                       ->where('kelas_id', $selectedKelasId);
                });
            })->get();
        }

        // 3. Ambil data Matriks (Siswa & TP) jika filter lengkap
        $siswas = [];
        $tps = [];
        $existingNilai = [];
        $kkmNilai = null;

        if ($selectedKelasId && $selectedTitimangsaId && $selectedMapelId) {
            // Ambil Siswa
            $siswas = Siswa::where('kelas_id', $selectedKelasId)->with('user')->get()->map(function($s) {
                return [
                    'id' => $s->id,
                    'nama' => $s->user->name ?? 'Tanpa Nama',
                    'nis' => $s->nis
                ];
            });

            // Ambil TP yang dibuat oleh Guru ini di Master Data
            $tps = FormatifTp::whereHas('master', function($q) use ($selectedTitimangsaId, $selectedMapelId, $selectedKelasId, $user) {
                $q->where('titimangsa_id', $selectedTitimangsaId)
                  ->where('mapel_id', $selectedMapelId)
                  ->where('kelas_id', $selectedKelasId)
                  ->where('user_id', $user->id);
            })->get();

            // Ambil Nilai Formatif
            $siswaIds = $siswas->pluck('id')->toArray();
            $tpIds = $tps->pluck('id')->toArray();
            
            $existingNilai = FormatifNilai::whereIn('siswa_id', $siswaIds)
                                          ->whereIn('formatif_tp_id', $tpIds)
                                          ->get();

            // Cek KKM
            $kelasObj = Kelas::find($selectedKelasId);
            if ($kelasObj) {
                $kkmObj = Kkm::where('tahun_ajaran_id', $selectedTahunId)
                             ->where('kurikulum_id', $selectedKurikulumId)
                             ->where('tingkat', $kelasObj->tingkat)
                             ->first();
                if ($kkmObj) {
                    $kkmNilai = $kkmObj->nilai;
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'tahun_ajarans' => $tahunAjarans,
                'kurikulums' => $kurikulums,
                'periodes' => $periodes,
                'mapels' => $mapels,
                'kelases' => $kelases,
                'siswas' => $siswas,
                'tps' => $tps,
                'nilai' => $existingNilai,
                'selections' => [
                    'tahun_ajaran_id' => $selectedTahunId,
                    'kurikulum_id' => $selectedKurikulumId,
                    'titimangsa_id' => $selectedTitimangsaId,
                    'mapel_id' => $selectedMapelId,
                    'kelas_id' => $selectedKelasId,
                    'is_titimangsa_aktif' => $isTitimangsaAktif,
                    'kkm' => $kkmNilai
                ]
            ]
        ]);
    }

    /**
     * Manual Bulk Save Nilai
     */
    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran_id' => 'required',
            'titimangsa_id' => 'required',
            'mapel_id' => 'required',
            'kelas_id' => 'required',
            'data' => 'required|array'
        ]);

        foreach ($request->data as $row) {
            if ($row['nilai'] === null || $row['nilai'] === '') {
                // Delete record if empty
                FormatifNilai::where('siswa_id', $row['siswa_id'])
                             ->where('formatif_tp_id', $row['tp_id'])
                             ->delete();
            } else {
                FormatifNilai::updateOrCreate(
                    [
                        'siswa_id' => $row['siswa_id'],
                        'formatif_tp_id' => $row['tp_id']
                    ],
                    [
                        'tahun_ajaran_id' => $request->tahun_ajaran_id,
                        'titimangsa_id' => $request->titimangsa_id,
                        'mapel_id' => $request->mapel_id,
                        'kelas_id' => $request->kelas_id,
                        'nilai' => $row['nilai']
                    ]
                );
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Data formatif berhasil disimpan secara manual'
        ]);
    }
}
