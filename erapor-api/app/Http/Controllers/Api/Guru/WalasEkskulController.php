<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WaliKelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Titimangsa;
use App\Models\Ekskul;
use App\Models\EkskulSiswa;

class WalasEkskulController extends Controller
{
    private function getWalasContext()
    {
        $user = Auth::user();
        
        $walas = WaliKelas::with(['kelas.kurikulum'])->where('guru_id', $user->id)->first();
        if (!$walas) {
            return null;
        }

        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAktif) return null;

        $titimangsas = Titimangsa::where('tahun_ajaran_id', $tahunAktif->id)
            ->orderBy('id')->get();
            
        if ($titimangsas->isEmpty()) return null;

        return [
            'kelas' => $walas->kelas,
            'tahun_ajaran' => $tahunAktif,
            'titimangsas' => $titimangsas,
        ];
    }

    public function index(Request $request)
    {
        $context = $this->getWalasContext();
        if (!$context) {
            return response()->json(['success' => false, 'message' => 'Anda bukan wali kelas aktif saat ini atau pengaturan periode belum lengkap.'], 403);
        }

        $kelas = $context['kelas'];
        $titimangsas = $context['titimangsas'];
        $titimangsaIds = $titimangsas->pluck('id')->toArray();

        // Get Master Ekskul
        $masterEkskul = Ekskul::orderBy('nama_ekskul')->get();
        
        // Find Pramuka (if any)
        $pramuka = $masterEkskul->first(function($item) {
            return stripos($item->nama_ekskul, 'pramuka') !== false;
        });

        // Auto-create Pramuka if not exists to avoid silent save failure
        if (!$pramuka) {
            $pramuka = Ekskul::create([
                'nama_ekskul' => 'Pramuka (Wajib)',
                'nama_pembina' => '-'
            ]);
            $masterEkskul = Ekskul::orderBy('nama_ekskul')->get();
        }
        $pramukaId = $pramuka->id;

        // Get Siswa
        $siswas = Siswa::with('user')->where('kelas_id', $kelas->id)->get()->sortBy(function($siswa) {
            return $siswa->user ? $siswa->user->name : '';
        })->values();

        $siswaIds = $siswas->pluck('id')->toArray();

        // Get all stored ekskul data for these students and active periods
        $ekskulSiswas = EkskulSiswa::whereIn('siswa_id', $siswaIds)
            ->whereIn('titimangsa_id', $titimangsaIds)
            ->get();

        // Group data for easy frontend parsing
        $savedData = [];
        foreach ($ekskulSiswas as $es) {
            if (!isset($savedData[$es->siswa_id])) {
                $savedData[$es->siswa_id] = [];
            }
            if (!isset($savedData[$es->siswa_id][$es->titimangsa_id])) {
                $savedData[$es->siswa_id][$es->titimangsa_id] = [
                    'pramuka' => null,
                    'pilihan1' => null,
                    'pilihan2' => null
                ];
            }

            if ($es->ekskul_id === $pramukaId) {
                $savedData[$es->siswa_id][$es->titimangsa_id]['pramuka'] = [
                    'id' => $es->ekskul_id,
                    'nilai' => $es->nilai
                ];
            } else {
                // Populate choices
                if (!$savedData[$es->siswa_id][$es->titimangsa_id]['pilihan1']) {
                    $savedData[$es->siswa_id][$es->titimangsa_id]['pilihan1'] = [
                        'id' => $es->ekskul_id,
                        'nilai' => $es->nilai
                    ];
                } else if (!$savedData[$es->siswa_id][$es->titimangsa_id]['pilihan2']) {
                    $savedData[$es->siswa_id][$es->titimangsa_id]['pilihan2'] = [
                        'id' => $es->ekskul_id,
                        'nilai' => $es->nilai
                    ];
                }
            }
        }

        $masterKurikulum = \App\Models\Kurikulum::all();

        return response()->json([
            'success' => true,
            'data' => [
                'kelas' => [
                    'id' => $kelas->id,
                    'tingkat' => $kelas->tingkat,
                    'nama_kelas' => $kelas->nama_kelas,
                    'kurikulum_id' => $kelas->kurikulum_id,
                    'kurikulum' => $kelas->kurikulum ? $kelas->kurikulum->nama_kurikulum : '-'
                ],
                'tahun_ajaran' => $context['tahun_ajaran'],
                'titimangsas' => $titimangsas,
                'master_ekskul' => $masterEkskul,
                'master_kurikulum' => $masterKurikulum,
                'pramuka_id' => $pramukaId,
                'siswas' => $siswas->map(function($s) {
                    return [
                        'id' => $s->id,
                        'nama' => $s->user ? $s->user->name : '-'
                    ];
                }),
                'saved_data' => $savedData
            ]
        ]);
    }

    public function store(Request $request)
    {
        $context = $this->getWalasContext();
        if (!$context) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        // payload structure: { ekskul: { [siswa_id]: { [titimangsa_id]: { pramuka_nilai, pilihan1_id, pilihan1_nilai, pilihan2_id, pilihan2_nilai } } } }
        $request->validate([
            'ekskul' => 'required|array'
        ]);

        $pramukaId = $request->input('pramuka_id'); // passed from frontend to ensure consistency

        $activeTitimangsaIds = Titimangsa::where('tahun_ajaran_id', $context['tahun_ajaran']->id)
            ->where('is_aktif', true)
            ->pluck('id')
            ->toArray();

        DB::beginTransaction();
        try {
            foreach ($request->ekskul as $siswaId => $titimangsaData) {
                foreach ($titimangsaData as $titimangsaId => $data) {
                    
                    // SECURITY GUARD: Only process if the titimangsa is actively open by kurikulum
                    if (!in_array($titimangsaId, $activeTitimangsaIds)) {
                        continue;
                    }

                    // To prevent duplication when user changes "pilihan1_id" to another ekskul,
                    // we safely delete existing records for this student and this titimangsa first.
                    EkskulSiswa::where('siswa_id', $siswaId)
                        ->where('titimangsa_id', $titimangsaId)
                        ->delete();

                    // Insert Pramuka (if provided)
                    if ($pramukaId && isset($data['pramuka_nilai']) && trim($data['pramuka_nilai']) !== '') {
                        EkskulSiswa::create([
                            'siswa_id' => $siswaId,
                            'titimangsa_id' => $titimangsaId,
                            'ekskul_id' => $pramukaId,
                            'nilai' => $data['pramuka_nilai']
                        ]);
                    }

                    // Insert Pilihan 1
                    if (!empty($data['pilihan1_id'])) {
                        EkskulSiswa::create([
                            'siswa_id' => $siswaId,
                            'titimangsa_id' => $titimangsaId,
                            'ekskul_id' => $data['pilihan1_id'],
                            'nilai' => isset($data['pilihan1_nilai']) && trim($data['pilihan1_nilai']) !== '' ? $data['pilihan1_nilai'] : null
                        ]);
                    }

                    // Insert Pilihan 2
                    if (!empty($data['pilihan2_id'])) {
                        EkskulSiswa::create([
                            'siswa_id' => $siswaId,
                            'titimangsa_id' => $titimangsaId,
                            'ekskul_id' => $data['pilihan2_id'],
                            'nilai' => isset($data['pilihan2_nilai']) && trim($data['pilihan2_nilai']) !== '' ? $data['pilihan2_nilai'] : null
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data Ekstrakurikuler berhasil disimpan!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
}
