<?php

namespace App\Http\Controllers\Api\Kurikulum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\JadwalPelajaran;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KurikulumJadwalMengajarController extends Controller
{
    /**
     * Mengambil struktur kelas per tingkat dan opsi dropdown mapel+guru
     */
    public function getOptions(Request $request)
    {
        $taAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$taAktif) {
            return response()->json(['success' => false, 'message' => 'Tahun Ajaran Aktif tidak ditemukan'], 404);
        }

        // 1. Ambil Kelas berdasarkan Tingkat
        $kelasQuery = Kelas::where('tahun_ajaran_id', $taAktif->id)
                           ->orderBy('tingkat')
                           ->orderBy('nama_kelas')
                           ->get();

        $kelasPerTingkat = [
            '10' => [],
            '11' => [],
            '12' => []
        ];

        foreach ($kelasQuery as $k) {
            // Tingkat di DB biasanya "X", "XI", "XII" atau "10", "11", "12". Kita petakan ke format yg konsisten.
            $tingkatKey = $k->tingkat;
            if ($tingkatKey == 'X') $tingkatKey = '10';
            if ($tingkatKey == 'XI') $tingkatKey = '11';
            if ($tingkatKey == 'XII') $tingkatKey = '12';

            if (isset($kelasPerTingkat[$tingkatKey])) {
                $kelasPerTingkat[$tingkatKey][] = [
                    'id' => $k->id,
                    'nama_kelas' => $k->nama_kelas
                ];
            }
        }

        // 2. Ambil Opsi Dropdown Mapel + Guru yang di-plot
        $mapels = Mapel::orderBy('kode_mapel')->get();
        $mapelOptions = [];

        foreach ($mapels as $mapel) {
            // Cari guru yang di-plot ke mapel ini
            $gurus = DB::table('pengampus')
                ->leftJoin('struktur_kurikulums', 'pengampus.struktur_kurikulum_id', '=', 'struktur_kurikulums.id')
                ->leftJoin('struktur_kejuruans', 'pengampus.struktur_kejuruan_id', '=', 'struktur_kejuruans.id')
                ->join('users', 'pengampus.guru_id', '=', 'users.id')
                ->where(function($q) use ($mapel) {
                    $q->where('struktur_kurikulums.mapel_id', $mapel->id)
                      ->orWhere('struktur_kejuruans.mapel_id', $mapel->id);
                })
                ->select('users.id', 'users.name')
                ->distinct()
                ->get();
                
            if ($gurus->isEmpty()) {
                // Jika tidak ada guru yang diplot
                $mapelOptions[] = [
                    'value' => $mapel->id . '_null', // format: mapel_id_guru_id
                    'label' => $mapel->kode_mapel . '. ' . $mapel->nama_mapel,
                    'mapel_id' => $mapel->id,
                    'guru_id' => null
                ];
            } else {
                // Jika ada guru, duplikasi per guru
                foreach ($gurus as $guru) {
                    $mapelOptions[] = [
                        'value' => $mapel->id . '_' . $guru->id,
                        'label' => $mapel->kode_mapel . '. ' . $mapel->nama_mapel . ' - ' . $guru->name,
                        'mapel_id' => $mapel->id,
                        'guru_id' => $guru->id
                    ];
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'kelasPerTingkat' => $kelasPerTingkat,
                'mapelOptions' => $mapelOptions
            ]
        ]);
    }

    /**
     * Mengambil jadwal tersimpan untuk satu hari
     */
    public function getByHari(Request $request)
    {
        $hari = $request->query('hari', 'Senin');
        $taAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$taAktif) {
            return response()->json(['success' => false, 'message' => 'Tahun Ajaran Aktif tidak ditemukan'], 404);
        }

        $kelasIds = Kelas::where('tahun_ajaran_id', $taAktif->id)->pluck('id')->toArray();

        $jadwals = JadwalPelajaran::whereIn('kelas_id', $kelasIds)
                                  ->where('hari', $hari)
                                  ->get();

        // Format untuk frontend agar mudah di-bind ke grid: key = jamKe_kelasId
        $formattedJadwal = [];
        foreach ($jadwals as $j) {
            $key = $j->jam_ke . '_' . $j->kelas_id;
            // Value di dropdown = mapel_id_guru_id (jika guru ada)
            $val = $j->mapel_id . '_' . ($j->guru_id ?? 'null');
            $formattedJadwal[$key] = $val;
        }

        return response()->json([
            'success' => true,
            'data' => $formattedJadwal
        ]);
    }

    /**
     * Menyimpan jadwal secara bulk untuk satu hari
     */
    public function saveJadwal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hari' => 'required|string',
            'jadwals' => 'array' // format: [['jam_ke' => 1, 'kelas_id' => 2, 'mapel_id' => 3, 'guru_id' => 4], ...]
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $hari = $request->hari;
        $jadwalsData = $request->jadwals ?? [];

        $taAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$taAktif) {
            return response()->json(['success' => false, 'message' => 'Tahun Ajaran Aktif tidak ditemukan'], 404);
        }

        $kelasIds = Kelas::where('tahun_ajaran_id', $taAktif->id)->pluck('id')->toArray();

        DB::beginTransaction();
        try {
            // Hapus jadwal lama di hari ini untuk semua kelas aktif
            JadwalPelajaran::whereIn('kelas_id', $kelasIds)
                           ->where('hari', $hari)
                           ->delete();

            // Insert jadwal baru
            $insertData = [];
            $now = now();
            foreach ($jadwalsData as $j) {
                // Jangan insert jika mapel_id kosong (artinya dihapus/clear)
                if (!empty($j['mapel_id'])) {
                    $insertData[] = [
                        'kelas_id' => $j['kelas_id'],
                        'hari' => $hari,
                        'jam_ke' => $j['jam_ke'],
                        'mapel_id' => $j['mapel_id'],
                        'guru_id' => !empty($j['guru_id']) ? $j['guru_id'] : null,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                }
            }

            if (!empty($insertData)) {
                JadwalPelajaran::insert($insertData);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Jadwal hari ' . $hari . ' berhasil disimpan.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan jadwal: ' . $e->getMessage()
            ], 500);
        }
    }
}
