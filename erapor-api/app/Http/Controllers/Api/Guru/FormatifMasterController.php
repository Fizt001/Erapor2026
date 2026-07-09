<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TahunAjaran;
use App\Models\Kurikulum;
use App\Models\Titimangsa;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\FormatifMaster;
use App\Models\FormatifTp;

class FormatifMasterController extends Controller
{
    /**
     * Get Data Dependensi Filter & Formatif Master Data
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // 1. Ambil opsi referensi
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
        $kelases = Kelas::where('tahun_ajaran_id', $selectedTahunId)
            ->whereHas('pengampus', function($q) use ($user) {
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

        // 3. Ambil data Formatif Master (Elemen & TP) jika filter lengkap
        $elemens = [];
        if ($selectedKelasId && $selectedTitimangsaId && $selectedMapelId) {
            $elemens = FormatifMaster::where('titimangsa_id', $selectedTitimangsaId)
                ->where('mapel_id', $selectedMapelId)
                ->where('kelas_id', $selectedKelasId)
                ->where('user_id', $user->id)
                ->with('tps')
                ->get();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'tahun_ajarans' => $tahunAjarans,
                'kurikulums' => $kurikulums,
                'periodes' => $periodes,
                'mapels' => $mapels,
                'kelases' => $kelases,
                'elemens' => $elemens,
                'selections' => [
                    'tahun_ajaran_id' => $selectedTahunId,
                    'kurikulum_id' => $selectedKurikulumId,
                    'titimangsa_id' => $selectedTitimangsaId,
                    'mapel_id' => $selectedMapelId,
                    'kelas_id' => $selectedKelasId,
                    'is_titimangsa_aktif' => $isTitimangsaAktif
                ]
            ]
        ]);
    }

    /**
     * Auto-Save Elemen
     */
    public function autoSaveElemen(Request $request)
    {
        $request->validate([
            'tahun_ajaran_id' => 'required',
            'kurikulum_id' => 'required',
            'titimangsa_id' => 'required',
            'mapel_id' => 'required',
            'kelas_id' => 'required',
            'nama_elemen' => 'required|string|max:255',
        ]);

        $elemen = FormatifMaster::updateOrCreate(
            ['id' => $request->id],
            [
                'tahun_ajaran_id' => $request->tahun_ajaran_id,
                'kurikulum_id'    => $request->kurikulum_id,
                'titimangsa_id'   => $request->titimangsa_id,
                'mapel_id'        => $request->mapel_id,
                'kelas_id'        => $request->kelas_id,
                'user_id'         => auth()->id(),
                'nama_elemen'     => $request->nama_elemen
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $elemen
        ]);
    }

    /**
     * Auto-Save Tujuan Pembelajaran (TP)
     */
    public function autoSaveTp(Request $request)
    {
        $request->validate([
            'formatif_master_id' => 'required',
            'nama_tp' => 'required|string|max:1000',
        ]);

        $tp = FormatifTp::updateOrCreate(
            ['id' => $request->id],
            [
                'formatif_master_id' => $request->formatif_master_id,
                'nama_tp' => $request->nama_tp
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $tp
        ]);
    }

    /**
     * Hapus Elemen
     */
    public function deleteElemen($id)
    {
        $elemen = FormatifMaster::where('id', $id)->where('user_id', auth()->id())->first();
        if ($elemen) {
            $elemen->delete(); // Akan trigger cascade pada formatif_tps karena constraint database
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Elemen tidak ditemukan atau Anda tidak punya akses'], 404);
    }

    /**
     * Hapus Tujuan Pembelajaran (TP)
     */
    public function deleteTp($id)
    {
        $tp = FormatifTp::whereHas('master', function($q) {
            $q->where('user_id', auth()->id());
        })->where('id', $id)->first();
        
        if ($tp) {
            $tp->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'TP tidak ditemukan atau Anda tidak punya akses'], 404);
    }
}
