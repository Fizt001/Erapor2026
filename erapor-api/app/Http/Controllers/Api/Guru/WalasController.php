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
use App\Models\AbsensiSiswa;
use App\Models\CatatanWaliKelas;

class WalasController extends Controller
{
    /**
     * Get class context (kelas_id, tahun_ajaran_id, titimangsa_id) for the current Walas
     */
    private function getWalasContext()
    {
        $user = Auth::user();
        
        $walas = WaliKelas::where('guru_id', $user->id)->first();
        if (!$walas) {
            return null;
        }

        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAktif) return null;

        $titimangsaAktif = Titimangsa::where('tahun_ajaran_id', $tahunAktif->id)
            ->where('is_aktif', true)
            ->first();
            
        if (!$titimangsaAktif) return null;

        return [
            'kelas_id' => $walas->kelas_id,
            'tahun_ajaran' => $tahunAktif,
            'titimangsa' => $titimangsaAktif,
        ];
    }

    /**
     * Get biodata and completion percentage for all students in the class
     */
    public function getBiodataSiswa(Request $request)
    {
        $context = $this->getWalasContext();
        if (!$context) {
            return response()->json(['success' => false, 'message' => 'Anda bukan wali kelas aktif saat ini.'], 403);
        }

        $kelasId = $context['kelas_id'];

        // Get all students in the class
        $siswas = Siswa::with('user')->where('kelas_id', $kelasId)->get()->sortBy(function($siswa) {
            return $siswa->user ? $siswa->user->name : '';
        })->values();

        $fields = [
            'nis', 'nisn', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 
            'anak_ke', 'status_keluarga', 'warga_negara', 'agama', 'alamat', 'no_hp',
            'nama_ayah', 'ttl_ayah', 'pekerjaan_ayah', 'no_hp_ayah', 
            'nama_ibu', 'ttl_ibu', 'pekerjaan_ibu', 'no_hp_ibu', 
            'asal_sekolah', 'alamat_sekolah_asal', 'no_sttb_smp', 'tgl_sttb_smp',
            'tanggal_masuk', 'kelas_masuk'
        ];
        $totalFields = count($fields);

        // Map data to students
        $data = $siswas->map(function ($siswa) use ($fields, $totalFields) {
            $filled = 0;
            foreach ($fields as $f) {
                if (!empty($siswa->{$f})) $filled++;
            }
            $persentase = round(($filled / $totalFields) * 100);

            return [
                'id' => $siswa->id,
                'user_id' => $siswa->user_id,
                'nama_lengkap' => $siswa->user ? $siswa->user->name : '-',
                'nis' => $siswa->nis,
                'nisn' => $siswa->nisn,
                'jenis_kelamin' => $siswa->jenis_kelamin,
                'tempat_lahir' => $siswa->tempat_lahir,
                'tanggal_lahir' => $siswa->tanggal_lahir,
                'anak_ke' => $siswa->anak_ke,
                'status_keluarga' => $siswa->status_keluarga,
                'warga_negara' => $siswa->warga_negara,
                'agama' => $siswa->agama,
                'alamat' => $siswa->alamat,
                'no_hp' => $siswa->no_hp,
                
                'nama_ayah' => $siswa->nama_ayah,
                'ttl_ayah' => $siswa->ttl_ayah,
                'pekerjaan_ayah' => $siswa->pekerjaan_ayah,
                'no_hp_ayah' => $siswa->no_hp_ayah,
                
                'nama_ibu' => $siswa->nama_ibu,
                'ttl_ibu' => $siswa->ttl_ibu,
                'pekerjaan_ibu' => $siswa->pekerjaan_ibu,
                'no_hp_ibu' => $siswa->no_hp_ibu,
                
                'nama_wali' => $siswa->nama_wali,
                'pekerjaan_wali' => $siswa->pekerjaan_wali,
                'no_hp_wali' => $siswa->no_hp_wali,
                'alamat_wali' => $siswa->alamat_wali,
                
                'asal_sekolah' => $siswa->asal_sekolah,
                'alamat_sekolah_asal' => $siswa->alamat_sekolah_asal,
                'no_sttb_smp' => $siswa->no_sttb_smp,
                'tgl_sttb_smp' => $siswa->tgl_sttb_smp,
                'tanggal_masuk' => $siswa->tanggal_masuk,
                'kelas_masuk' => $siswa->kelas_masuk,
                'tanggal_keluar' => $siswa->tanggal_keluar,
                'alasan_keluar' => $siswa->alasan_keluar,
                'no_sttb_smk' => $siswa->no_sttb_smk,
                'tgl_sttb_smk' => $siswa->tgl_sttb_smk,
                'tempat_pkl' => $siswa->tempat_pkl,
                'alamat_pkl' => $siswa->alamat_pkl,
                'tgl_mulai_pkl' => $siswa->tgl_mulai_pkl,
                'tgl_selesai_pkl' => $siswa->tgl_selesai_pkl,
                
                'persentase_lengkap' => $persentase,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Update Biodata Siswa
     */
    public function updateBiodataSiswa(Request $request, $id)
    {
        $context = $this->getWalasContext();
        if (!$context) {
            return response()->json(['success' => false, 'message' => 'Anda bukan wali kelas aktif.'], 403);
        }

        $siswa = Siswa::find($id);
        if (!$siswa || $siswa->kelas_id != $context['kelas_id']) {
            return response()->json(['success' => false, 'message' => 'Siswa tidak ditemukan atau bukan anggota kelas Anda.'], 404);
        }

        $request->validate([
            'nis' => 'required|unique:siswa,nis,'.$siswa->id,
        ]);

        DB::beginTransaction();
        try {
            $siswa->update($request->except(['nama_lengkap']));

            if ($request->has('nama_lengkap') && !empty($request->nama_lengkap)) {
                $siswa->user->update(['name' => $request->nama_lengkap]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Biodata '.$siswa->user->name.' berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan biodata: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get monitoring of grades completion by subject teachers for this class
     */
    public function monitoringNilai(Request $request)
    {
        $context = $this->getWalasContext();
        if (!$context) {
            return response()->json(['success' => false, 'message' => 'Anda bukan wali kelas aktif.'], 403);
        }

        $kelasId = $context['kelas_id'];
        $tahunId = $context['tahun_ajaran']->id;
        $titimangsaId = $context['titimangsa']->id;

        // Get total students in class
        $totalSiswa = Siswa::where('kelas_id', $kelasId)->count();

        // Get subjects taught in this class
        $pengampus = \App\Models\Pengampu::with(['strukturKurikulum.mapel', 'strukturKejuruan.mapel', 'guru'])
            ->where('kelas_id', $kelasId)
            ->get();

        $data = $pengampus->map(function ($pengampu) use ($kelasId, $tahunId, $titimangsaId, $totalSiswa) {
            $mapelId = null;
            $namaMapel = '-';
            
            if ($pengampu->struktur_kurikulum_id && $pengampu->strukturKurikulum) {
                $mapelId = $pengampu->strukturKurikulum->mapel_id;
                $namaMapel = $pengampu->strukturKurikulum->mapel->nama_mapel ?? '-';
            } elseif ($pengampu->struktur_kejuruan_id && $pengampu->strukturKejuruan) {
                $mapelId = $pengampu->strukturKejuruan->mapel_id;
                $namaMapel = $pengampu->strukturKejuruan->mapel->nama_mapel ?? '-';
            }

            if (!$mapelId) return null;

            // Count unique students who have formatif scores for this subject
            $formatifCount = \App\Models\FormatifNilai::where('mapel_id', $mapelId)
                ->where('tahun_ajaran_id', $tahunId)
                ->where('titimangsa_id', $titimangsaId)
                ->where('kelas_id', $kelasId)
                ->distinct('siswa_id')
                ->count('siswa_id');

            // Count unique students who have sumatif scores for this subject
            $sumatifCount = \App\Models\SumatifNilai::where('mapel_id', $mapelId)
                ->where('tahun_ajaran_id', $tahunId)
                ->where('titimangsa_id', $titimangsaId)
                ->where('kelas_id', $kelasId)
                ->distinct('siswa_id')
                ->count('siswa_id');

            return [
                'mapel_id' => $mapelId,
                'nama_mapel' => $namaMapel,
                'guru_pengampu' => $pengampu->guru->name ?? '-',
                'total_siswa' => $totalSiswa,
                'formatif_terisi' => $formatifCount,
                'sumatif_terisi' => $sumatifCount,
                'status_formatif' => $formatifCount >= $totalSiswa && $totalSiswa > 0,
                'status_sumatif' => $sumatifCount >= $totalSiswa && $totalSiswa > 0,
            ];
        })->filter()->values();

        return response()->json([
            'success' => true,
            'data' => $data,
            'total_siswa' => $totalSiswa
        ]);
    }
}
