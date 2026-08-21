<?php

namespace App\Http\Controllers\Api\Kurikulum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\JamPelajaran;
use App\Models\JadwalPelajaran;

class KurikulumJamPelajaranController extends Controller
{
    /**
     * Get all jam pelajaran grouped by kategori_hari
     */
    public function index()
    {
        $jams = JamPelajaran::orderBy('jam_ke')->get();

        $seninKamis = $jams->where('kategori_hari', 'Senin-Kamis')->values();
        $jumat = $jams->where('kategori_hari', 'Jumat')->values();

        return response()->json([
            'success' => true,
            'data' => [
                'senin_kamis' => $seninKamis,
                'jumat' => $jumat
            ]
        ]);
    }

    /**
     * Update bulk jam pelajaran
     */
    public function updateBulk(Request $request)
    {
        $request->validate([
            'senin_kamis' => 'required|array',
            'jumat' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            // 1. Update JamPelajaran (Master)
            foreach ($request->senin_kamis as $item) {
                JamPelajaran::updateOrCreate(
                    ['kategori_hari' => 'Senin-Kamis', 'jam_ke' => $item['jam_ke']],
                    ['waktu_mulai' => $item['waktu_mulai'], 'waktu_selesai' => $item['waktu_selesai']]
                );
            }

            foreach ($request->jumat as $item) {
                JamPelajaran::updateOrCreate(
                    ['kategori_hari' => 'Jumat', 'jam_ke' => $item['jam_ke']],
                    ['waktu_mulai' => $item['waktu_mulai'], 'waktu_selesai' => $item['waktu_selesai']]
                );
            }

            // 2. Sync to JadwalPelajaran
            // Reload the updated jams to use for syncing
            $updatedJams = JamPelajaran::all();
            
            $seninKamisMap = [];
            foreach ($updatedJams->where('kategori_hari', 'Senin-Kamis') as $j) {
                $seninKamisMap[$j->jam_ke] = ['mulai' => $j->waktu_mulai, 'selesai' => $j->waktu_selesai];
            }

            $jumatMap = [];
            foreach ($updatedJams->where('kategori_hari', 'Jumat') as $j) {
                $jumatMap[$j->jam_ke] = ['mulai' => $j->waktu_mulai, 'selesai' => $j->waktu_selesai];
            }

            $jadwals = JadwalPelajaran::all();
            foreach ($jadwals as $jadwal) {
                $updateData = [];
                if (in_array($jadwal->hari, ['Senin', 'Selasa', 'Rabu', 'Kamis'])) {
                    if (isset($seninKamisMap[$jadwal->jam_ke])) {
                        $updateData['waktu_mulai'] = $seninKamisMap[$jadwal->jam_ke]['mulai'];
                        $updateData['waktu_selesai'] = $seninKamisMap[$jadwal->jam_ke]['selesai'];
                    }
                } else if ($jadwal->hari === 'Jumat') {
                    if (isset($jumatMap[$jadwal->jam_ke])) {
                        $updateData['waktu_mulai'] = $jumatMap[$jadwal->jam_ke]['mulai'];
                        $updateData['waktu_selesai'] = $jumatMap[$jadwal->jam_ke]['selesai'];
                    }
                }

                if (!empty($updateData)) {
                    JadwalPelajaran::where('id', $jadwal->id)->update($updateData);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan jam pelajaran berhasil disimpan dan disinkronisasi ke seluruh jadwal.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
