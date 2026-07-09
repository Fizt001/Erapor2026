<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TahunAjaran;
use App\Models\Titimangsa;
use App\Models\SumatifNilai;
use App\Models\TargetBelajar;
use Illuminate\Support\Facades\Validator;

class SiswaAnalisisController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $siswa = $user->siswa;
        
        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Data siswa tidak ditemukan.'], 404);
        }

        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAjaranAktif) {
            return response()->json(['success' => false, 'message' => 'Belum ada tahun ajaran aktif.'], 404);
        }

        $allTitimangsa = Titimangsa::where('tahun_ajaran_id', $tahunAjaranAktif->id)
            ->orderBy('id', 'asc')
            ->get();
            
        $titimangsaIds = $allTitimangsa->pluck('id')->toArray();

        // Ambil nilai sumatif untuk semua periode di tahun ajaran aktif
        $allNilaiSumatif = SumatifNilai::with('mapel')
            ->where('siswa_id', $siswa->id)
            ->whereIn('titimangsa_id', $titimangsaIds)
            ->whereNotNull('na_value')
            ->get();
            
        // Ambil target belajar siswa
        $allTargets = TargetBelajar::where('siswa_id', $siswa->id)
            ->whereIn('titimangsa_id', $titimangsaIds)
            ->get();

        $tabsData = [];

        // 1. Tab "1 Tahun" (Akumulasi / Rata-rata)
        $groupedByMapel = $allNilaiSumatif->groupBy('mapel_id');
        $akumulasiNilai = collect();
        foreach ($groupedByMapel as $mapelId => $nilaiPerMapel) {
            $avgNaValue = $nilaiPerMapel->avg('na_value');
            $avgUh1 = $nilaiPerMapel->avg('uh1');
            $avgUh2 = $nilaiPerMapel->avg('uh2');
            $avgUh3 = $nilaiPerMapel->avg('uh3');
            $avgUh4 = $nilaiPerMapel->avg('uh4');
            $avgPraktek = $nilaiPerMapel->avg('praktek');
            $avgTeori = $nilaiPerMapel->avg('teori');
            $avgLiterasi = $nilaiPerMapel->avg('literasi');
            
            $firstItem = $nilaiPerMapel->first();
            
            // Average target for 1 year
            $targetPerMapel = $allTargets->where('mapel_id', $mapelId);
            $avgTarget = $targetPerMapel->count() > 0 ? $targetPerMapel->avg('target_nilai') : null;
            
            $akumulasiNilai->push((object)[
                'mapel' => $firstItem->mapel,
                'mapel_id' => $mapelId,
                'titimangsa_id' => 'akumulasi',
                'uh1' => $avgUh1 ? round($avgUh1, 2) : null,
                'uh2' => $avgUh2 ? round($avgUh2, 2) : null,
                'uh3' => $avgUh3 ? round($avgUh3, 2) : null,
                'uh4' => $avgUh4 ? round($avgUh4, 2) : null,
                'praktek' => $avgPraktek ? round($avgPraktek, 2) : null,
                'teori' => $avgTeori ? round($avgTeori, 2) : null,
                'literasi' => $avgLiterasi ? round($avgLiterasi, 2) : null,
                'na_value' => round($avgNaValue, 2),
                'target_nilai' => $avgTarget ? round($avgTarget, 2) : 0
            ]);
        }
        
        $tabsData[] = $this->formatTabItem('akumulasi', '1 Tahun Ajaran', 'Tahun Ajaran ' . $tahunAjaranAktif->tahun_ajaran, $akumulasiNilai);

        // 2. Tab untuk masing-masing Periode
        foreach ($allTitimangsa as $tm) {
            $nilaiTm = $allNilaiSumatif->where('titimangsa_id', $tm->id)->map(function($item) use ($allTargets) {
                $target = $allTargets->where('titimangsa_id', $item->titimangsa_id)
                                     ->where('mapel_id', $item->mapel_id)
                                     ->first();
                $item->target_nilai = $target ? $target->target_nilai : 0;
                return $item;
            });
            $tabsData[] = $this->formatTabItem($tm->id, $tm->nama_periode, $tm->nama_periode, $nilaiTm);
        }

        return response()->json([
            'success' => true,
            'data' => $tabsData
        ]);
    }

    private function formatTabItem($tabId, $tabName, $periodeLabel, $nilaiSumatif)
    {
        if ($nilaiSumatif->count() === 0) {
            return [
                'tab_id' => $tabId,
                'tab_name' => $tabName,
                'periode' => $periodeLabel,
                'has_data' => false,
                'radar' => ['labels' => [], 'data' => [], 'target' => []],
                'kekuatan' => [],
                'kelemahan' => [],
                'detail' => []
            ];
        }

        $sortedByNA = $nilaiSumatif->sortByDesc('na_value')->values();

        $kekuatan = $sortedByNA->take(3)->map(function ($item) {
            return [
                'nama_mapel' => $item->mapel->nama_mapel,
                'nilai' => round($item->na_value, 2)
            ];
        });

        $kelemahanCount = min(3, max(0, $sortedByNA->count() - 3));
        $kelemahan = $kelemahanCount > 0 ? $sortedByNA->take(-$kelemahanCount)->reverse()->map(function ($item) {
            return [
                'nama_mapel' => $item->mapel->nama_mapel,
                'nilai' => round($item->na_value, 2)
            ];
        })->values() : [];

        $radarLabels = [];
        $radarData = [];
        $radarTarget = [];
        foreach ($nilaiSumatif as $item) {
            $radarLabels[] = $item->mapel->kode_mapel ?: substr($item->mapel->nama_mapel, 0, 15);
            $radarData[] = round($item->na_value, 2);
            $radarTarget[] = $item->target_nilai ?? 0;
        }

        $detail = $sortedByNA->map(function ($item) {
            return [
                'mapel_id' => $item->mapel_id,
                'nama_mapel' => $item->mapel->nama_mapel,
                'kode_mapel' => $item->mapel->kode_mapel,
                'uh1' => $item->uh1 ?? 0,
                'uh2' => $item->uh2 ?? 0,
                'uh3' => $item->uh3 ?? 0,
                'uh4' => $item->uh4 ?? 0,
                'praktek' => $item->praktek ?? 0,
                'teori' => $item->teori ?? 0,
                'literasi' => $item->literasi ?? 0,
                'na_value' => round($item->na_value, 2),
                'target_nilai' => $item->target_nilai ?? 0
            ];
        });

        return [
            'tab_id' => $tabId,
            'tab_name' => $tabName,
            'periode' => $periodeLabel,
            'has_data' => true,
            'radar' => [
                'labels' => $radarLabels,
                'data' => $radarData,
                'target' => $radarTarget
            ],
            'kekuatan' => $kekuatan,
            'kelemahan' => $kelemahan,
            'detail' => $detail
        ];
    }
    
    public function setTarget(Request $request)
    {
        $user = $request->user();
        $siswa = $user->siswa;
        
        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Data siswa tidak ditemukan.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'titimangsa_id' => 'required|integer',
            'targets' => 'required|array',
            'targets.*.mapel_id' => 'required|integer',
            'targets.*.target_nilai' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $titimangsaId = $request->titimangsa_id;
        
        // Cek apakah titimangsa aktif/valid
        $titimangsa = Titimangsa::find($titimangsaId);
        if (!$titimangsa) {
            return response()->json(['success' => false, 'message' => 'Periode tidak valid.'], 404);
        }

        foreach ($request->targets as $t) {
            TargetBelajar::updateOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'titimangsa_id' => $titimangsaId,
                    'mapel_id' => $t['mapel_id']
                ],
                [
                    'target_nilai' => $t['target_nilai']
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Target belajar berhasil disimpan.'
        ]);
    }
}
