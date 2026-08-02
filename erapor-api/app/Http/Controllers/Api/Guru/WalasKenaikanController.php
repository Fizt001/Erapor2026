<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TahunAjaran;
use App\Models\WaliKelas;
use App\Models\Siswa;
use App\Models\Kkm;
use App\Models\SumatifNilai;
use App\Models\Titimangsa;
use App\Models\AbsensiSiswa;
use App\Models\PenangananPelanggaran;
use App\Models\Sekolah;

class WalasKenaikanController extends Controller
{
    private function getWalasContext(Request $request = null)
    {
        $user = Auth::user();
        
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAktif) return null;

        $query = WaliKelas::with(['kelas.kurikulum', 'kelas.kejuruan.program.bidang', 'guru.biodata'])
            ->where('guru_id', $user->id)
            ->whereHas('kelas', function($query) use ($tahunAktif) {
                $query->where('tahun_ajaran_id', $tahunAktif->id);
            });
            
        if ($request && $request->has('kelas_id') && $request->kelas_id != '') {
            $query->where('kelas_id', $request->kelas_id);
        }
        
        $walas = $query->first();
        if (!$walas) return null;

        $titimangsas = Titimangsa::where('tahun_ajaran_id', $tahunAktif->id)->orderBy('id')->get();
        if ($titimangsas->isEmpty()) return null;

        return [
            'walas' => $walas,
            'kelas' => $walas->kelas,
            'tahun_ajaran' => $tahunAktif,
            'titimangsas' => $titimangsas,
        ];
    }

    public function index(Request $request)
    {
        $context = $this->getWalasContext($request);
        if (!$context) {
            return response()->json(['success' => false, 'message' => 'Anda bukan wali kelas aktif.'], 403);
        }

        $kelas = $context['kelas'];
        $tahunAktif = $context['tahun_ajaran'];
        $titimangsas = $context['titimangsas'];

        $sekolah = Sekolah::first();

        // Get Siswa
        $siswas = Siswa::with('user')
            ->where('kelas_id', $kelas->id)
            ->whereNull('tanggal_keluar')
            ->get()
            ->sortBy('user.name')
            ->values();

        // Stats
        $jumlahSiswa = $siswas->count();
        $jumlahLaki = $siswas->filter(function($s) { return $s->jenis_kelamin === 'L'; })->count();
        $jumlahPerempuan = $siswas->filter(function($s) { return $s->jenis_kelamin === 'P'; })->count();

        // Separate titimangsa by semester
        $titimangsaGanjil = $titimangsas->filter(function($t) { 
            $month = date('n', strtotime($t->tanggal_cetak ?? date('Y-m-d')));
            return $month >= 7 && $month <= 12;
        })->pluck('id')->toArray();
        $titimangsaGenap = $titimangsas->filter(function($t) { 
            $month = date('n', strtotime($t->tanggal_cetak ?? date('Y-m-d')));
            return $month >= 1 && $month <= 6;
        })->pluck('id')->toArray();

        // Helper string for semester
        $getSemesterStr = function($id) use ($titimangsaGanjil, $titimangsaGenap) {
            if (in_array($id, $titimangsaGanjil)) return 'Ganjil';
            if (in_array($id, $titimangsaGenap)) return 'Genap';
            return 'Lainnya';
        };

        // KKM lookup
        $kkms = Kkm::where('tahun_ajaran_id', $tahunAktif->id)
            ->where('kurikulum_id', $kelas->kurikulum_id)
            ->where('tingkat', $kelas->tingkat)
            ->first();
        $kkmNilai = $kkms ? floatval($kkms->nilai) : 70; // Default 70 if not set

        // Absensi dinamis dari absen pertemuan (menggantikan AbsensiSiswa)
        $tahunStart = intval(explode('/', $tahunAktif->tahun)[0]);
        $pertemuans = \App\Models\PertemuanGuru::where('kelas_id', $kelas->id)
            ->whereYear('tanggal', '>=', $tahunStart)
            ->orderBy('tanggal')
            ->orderBy('jam_selesai', 'desc')
            ->get();
            
        $allAbsensi = \App\Models\AbsensiPertemuan::whereIn('pertemuan_id', $pertemuans->pluck('id'))
            ->get()
            ->groupBy('siswa_id');

        // Sumatif all
        $sumatifRecords = SumatifNilai::with(['mapel', 'titimangsa'])
            ->whereIn('siswa_id', $siswas->pluck('id'))
            ->whereIn('titimangsa_id', $titimangsas->pluck('id'))
            ->get();

        // Penanganan (BK)
        $penangananRecords = PenangananPelanggaran::whereIn('siswa_id', $siswas->pluck('id'))
            ->where('tahun_ajaran_id', $tahunAktif->id)
            ->get()
            ->groupBy('siswa_id');

        $dataSiswa = [];
        $no = 1;
        foreach ($siswas as $siswa) {
            // Absensi calculation (Dinamis dari PertemuanGuru)
            $abGanjilH = 0; $abGanjilS = 0; $abGanjilI = 0; $abGanjilA = 0;
            $abGenapH = 0; $abGenapS = 0; $abGenapI = 0; $abGenapA = 0;

            $dailyStatus = [];
            $siswaAbsensi = isset($allAbsensi[$siswa->id]) ? $allAbsensi[$siswa->id]->keyBy('pertemuan_id') : collect();

            foreach ($pertemuans as $pert) {
                if (!isset($dailyStatus[$pert->tanggal])) {
                    if (isset($siswaAbsensi[$pert->id])) {
                        $status = $siswaAbsensi[$pert->id]->status;
                        $dailyStatus[$pert->tanggal] = in_array($status, ['S', 'I', 'A']) ? $status : 'H';
                    } else {
                        $dailyStatus[$pert->tanggal] = 'H';
                    }
                }
            }

            foreach ($dailyStatus as $tanggal => $status) {
                $month = intval(date('n', strtotime($tanggal)));
                $isGanjil = ($month >= 7 && $month <= 12);
                $isGenap = ($month >= 1 && $month <= 6);
                
                if ($status === 'H') {
                    if ($isGanjil) $abGanjilH++;
                    if ($isGenap) $abGenapH++;
                } elseif ($status === 'S') {
                    if ($isGanjil) $abGanjilS++;
                    if ($isGenap) $abGenapS++;
                } elseif ($status === 'I') {
                    if ($isGanjil) $abGanjilI++;
                    if ($isGenap) $abGenapI++;
                } elseif ($status === 'A') {
                    if ($isGanjil) $abGanjilA++;
                    if ($isGenap) $abGenapA++;
                }
            }

            $totalGanjil = $abGanjilH + $abGanjilS + $abGanjilI + $abGanjilA;
            $persenGanjil = $totalGanjil > 0 ? round(($abGanjilH / $totalGanjil) * 100) : 0;
            
            $totalGenap = $abGenapH + $abGenapS + $abGenapI + $abGenapA;
            $persenGenap = $totalGenap > 0 ? round(($abGenapH / $totalGenap) * 100) : 0;

            // Mapel di bawah KKM
            $mapelBawahKkm = [];
            $sumSiswa = $sumatifRecords->where('siswa_id', $siswa->id);
            foreach ($sumSiswa as $sum) {
                if ($sum->na_value !== null && floatval($sum->na_value) < $kkmNilai) {
                    $semesterStr = $getSemesterStr($sum->titimangsa_id);
                    $mapelBawahKkm[] = [
                        'mapel' => $sum->mapel->nama_mapel ?? 'Mapel',
                        'semester' => $semesterStr,
                        'nilai' => floatval($sum->na_value)
                    ];
                }
            }

            // BK notes
            $bkNotes = $penangananRecords->get($siswa->id) ?? collect();
            $sikapKasusArr = [];
            $tindakLanjutArr = [];
            foreach ($bkNotes as $note) {
                if ($note->deskripsi_masalah) $sikapKasusArr[] = "- " . $note->deskripsi_masalah;
                if ($note->tindakan_penyelesaian) $tindakLanjutArr[] = "- " . $note->tindakan_penyelesaian;
            }

            $dataSiswa[] = [
                'no' => $no++,
                'nama' => $siswa->user->name ?? '',
                'absensi' => [
                    'ganjil' => $totalGanjil > 0 ? "{$persenGanjil}% (S:$abGanjilS I:$abGanjilI A:$abGanjilA)" : "S:$abGanjilS I:$abGanjilI A:$abGanjilA",
                    'genap' => $totalGenap > 0 ? "{$persenGenap}% (S:$abGenapS I:$abGenapI A:$abGenapA)" : "S:$abGenapS I:$abGenapI A:$abGenapA"
                ],
                'mapel_bawah_kkm' => $mapelBawahKkm,
                'sikap_kasus' => implode("\n", $sikapKasusArr),
                'tindak_lanjut' => implode("\n", $tindakLanjutArr)
            ];
        }

        $namaKelasFull = $kelas->nama_kelas;
        if (!str_starts_with($namaKelasFull, $kelas->tingkat)) {
            $namaKelasFull = $kelas->tingkat . ' ' . $namaKelasFull;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'sekolah' => $sekolah ? $sekolah->nama_sekolah : 'SMK Tinta Emas Indonesia',
                'tahun_ajaran' => $tahunAktif->tahun,
                'kelas' => $namaKelasFull,
                'wali_kelas' => $context['walas']->guru->biodata->nama_lengkap ?? $context['walas']->guru->name ?? '',
                'jumlah_siswa' => $jumlahSiswa,
                'jumlah_laki' => $jumlahLaki,
                'jumlah_perempuan' => $jumlahPerempuan,
                'kkm_kelas' => $kkmNilai,
                'tabel' => $dataSiswa
            ]
        ]);
    }
}
