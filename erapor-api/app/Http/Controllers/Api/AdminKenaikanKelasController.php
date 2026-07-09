<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Titimangsa;
use App\Models\CatatanWaliKelas;
use Illuminate\Support\Facades\DB;

class AdminKenaikanKelasController extends Controller
{
    // Mengambil data awal untuk dropdown (Tahun Ajaran, Kelas)
    public function getSetupData()
    {
        $tahunAjaran = TahunAjaran::orderBy('tahun', 'desc')->get();
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        
        $kelasByTahun = [];
        foreach ($kelas as $k) {
            if (!isset($kelasByTahun[$k->tahun_ajaran_id])) {
                $kelasByTahun[$k->tahun_ajaran_id] = [];
            }
            $kelasByTahun[$k->tahun_ajaran_id][] = $k;
        }

        return response()->json([
            'success' => true,
            'tahun_ajaran' => $tahunAjaran,
            'kelas_by_tahun' => $kelasByTahun
        ]);
    }

    // Mengambil siswa di kelas tertentu beserta rekomendasi dari Wali Kelas
    public function getSiswa($kelas_id)
    {
        $kelas = Kelas::with('tahunAjaran')->find($kelas_id);
        if (!$kelas) {
            return response()->json(['success' => false, 'message' => 'Kelas tidak ditemukan'], 404);
        }

        $siswas = Siswa::with('user:id,name')->where('kelas_id', $kelas_id)->get();

        // Cari titimangsa terakhir (biasanya akhir tahun) di tahun ajaran kelas ini
        $titimangsaTerakhir = Titimangsa::where('tahun_ajaran_id', $kelas->tahun_ajaran_id)
            ->orderBy('id', 'desc')
            ->first();

        $catatanWalas = collect();
        if ($titimangsaTerakhir) {
            $catatanWalas = CatatanWaliKelas::where('titimangsa_id', $titimangsaTerakhir->id)
                ->whereIn('siswa_id', $siswas->pluck('id'))
                ->get()
                ->keyBy('siswa_id');
        }

        $siswaData = $siswas->map(function ($s) use ($catatanWalas) {
            $rekomendasi = 'belum_ditentukan';
            if (isset($catatanWalas[$s->id])) {
                $rekomendasi = $catatanWalas[$s->id]->rekomendasi_kenaikan ?? 'belum_ditentukan';
            }

            return [
                'id' => $s->id,
                'user_id' => $s->user_id,
                'name' => $s->user ? $s->user->name : 'Unknown',
                'nis' => $s->nis,
                'status_siswa' => $s->status_siswa,
                'rekomendasi_walas' => $rekomendasi
            ];
        });

        // Urutkan berdasarkan nama
        $siswaData = $siswaData->sortBy('name')->values()->all();

        return response()->json([
            'success' => true,
            'kelas' => [
                'id' => $kelas->id,
                'tingkat' => $kelas->tingkat,
                'nama_kelas' => $kelas->nama_kelas,
                'tahun' => $kelas->tahunAjaran ? $kelas->tahunAjaran->tahun : '-'
            ],
            'siswa' => $siswaData
        ]);
    }

    // Memproses aksi massal / individu
    public function proses(Request $request)
    {
        $request->validate([
            'siswa' => 'required|array',
            'siswa.*.id' => 'required|exists:siswa,id',
            'siswa.*.aksi' => 'required|in:naik,tinggal,lulus,pindah_sekolah,keluar,tetap_aktif',
            'siswa.*.kelas_tujuan_id' => 'nullable|exists:kelas,id'
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->siswa as $s_data) {
                $siswa = Siswa::find($s_data['id']);
                if (!$siswa) continue;

                $aksi = $s_data['aksi'];
                $updateData = [];

                if ($aksi === 'naik' || $aksi === 'tinggal') {
                    if (isset($s_data['kelas_tujuan_id']) && $s_data['kelas_tujuan_id']) {
                        $updateData['kelas_id'] = $s_data['kelas_tujuan_id'];
                        $updateData['status_siswa'] = 'aktif';
                    }
                } elseif ($aksi === 'lulus') {
                    $updateData['status_siswa'] = 'lulus';
                    $updateData['tanggal_keluar'] = date('Y-m-d');
                    $updateData['alasan_keluar'] = 'Lulus';
                } elseif ($aksi === 'pindah_sekolah') {
                    $updateData['status_siswa'] = 'pindah_sekolah';
                    $updateData['tanggal_keluar'] = date('Y-m-d');
                    $updateData['alasan_keluar'] = 'Pindah Sekolah';
                } elseif ($aksi === 'keluar') {
                    $updateData['status_siswa'] = 'keluar';
                    $updateData['tanggal_keluar'] = date('Y-m-d');
                    $updateData['alasan_keluar'] = 'Keluar / Mengundurkan Diri';
                } elseif ($aksi === 'tetap_aktif') {
                     $updateData['status_siswa'] = 'aktif';
                }

                if (!empty($updateData)) {
                    $siswa->update($updateData);
                }
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Proses mutasi / kenaikan kelas berhasil disimpan.'
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
