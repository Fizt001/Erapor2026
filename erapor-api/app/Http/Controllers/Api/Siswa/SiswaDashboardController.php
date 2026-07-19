<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Titimangsa;
use App\Models\TahunAjaran;
use App\Models\AbsensiSiswa;
use App\Models\PoinSiswa;
use App\Models\SumatifNilai;

class SiswaDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $siswa = $user->siswa;
        
        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Data siswa tidak ditemukan.'], 404);
        }

        $kelas = $siswa->kelas;
        $walasName = '-';
        $namaKelasFull = '-';

        if ($kelas) {
            $namaKelasFull = $kelas->nama_kelas;
            if (!str_starts_with($namaKelasFull, $kelas->tingkat)) {
                $namaKelasFull = $kelas->tingkat . ' ' . $namaKelasFull;
            }

            $walasRecord = \App\Models\WaliKelas::with('guru.biodata')->where('kelas_id', $kelas->id)->first();
            if ($walasRecord && $walasRecord->guru) {
                $walasName = $walasRecord->guru->biodata->nama_lengkap ?? $walasRecord->guru->name ?? '-';
            }
        }

        $currentMonth = (int) date('n');

        // Cari tahun ajaran berdasarkan is_aktif
        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();

        // Dapatkan Titimangsa/Periode yang aktif berdasarkan bulan riil
        $titimangsaAktif = null;
        if ($tahunAjaranAktif) {
            $allTitimangsa = \App\Models\Titimangsa::where('tahun_ajaran_id', $tahunAjaranAktif->id)
                                ->orderBy('tanggal_cetak', 'asc')
                                ->get();
                                
            if ($allTitimangsa->count() > 0) {
                if ($currentMonth >= 7 && $currentMonth <= 9) {
                    $titimangsaAktif = $allTitimangsa->get(0);
                } elseif ($currentMonth >= 10 && $currentMonth <= 12) {
                    $titimangsaAktif = $allTitimangsa->get(1) ?? $allTitimangsa->last();
                } elseif ($currentMonth >= 1 && $currentMonth <= 3) {
                    $titimangsaAktif = $allTitimangsa->get(2) ?? $allTitimangsa->last();
                } else {
                    $titimangsaAktif = $allTitimangsa->get(3) ?? $allTitimangsa->last();
                }
            }
        }

        $semesterGanjil = ['PSTS Ganjil', 'PSAS'];
        $semesterGenap = ['PSTS Genap', 'PSAT'];
        
        $activeSemester = '';
        $activePeriods = [];
        $months = [];
        if ($currentMonth >= 7 && $currentMonth <= 12) {
            $activeSemester = 'Ganjil';
            $activePeriods = $semesterGanjil;
            $months = [7, 8, 9, 10, 11, 12];
        } else {
            $activeSemester = 'Genap';
            $activePeriods = $semesterGenap;
            $months = [1, 2, 3, 4, 5, 6];
        }

        // 1. Absensi (Ganjil & Genap)
        $absensiGanjil = ['s' => 0, 'i' => 0, 'a' => 0];
        $absensiGenap = ['s' => 0, 'i' => 0, 'a' => 0];
        
        if ($tahunAjaranAktif) {
            $absensiData = AbsensiSiswa::where('siswa_id', $siswa->id)
                ->where('tahun_ajaran', $tahunAjaranAktif->tahun)
                ->get();
            
            foreach ($absensiData as $ab) {
                $isGanjil = $ab->bulan >= 7 && $ab->bulan <= 12;
                
                for ($i = 1; $i <= 31; $i++) {
                    $col = 'tgl_' . $i;
                    if ($ab->$col === 'S') {
                        if ($isGanjil) $absensiGanjil['s']++; else $absensiGenap['s']++;
                    }
                    if ($ab->$col === 'I') {
                        if ($isGanjil) $absensiGanjil['i']++; else $absensiGenap['i']++;
                    }
                    if ($ab->$col === 'A') {
                        if ($isGanjil) $absensiGanjil['a']++; else $absensiGenap['a']++;
                    }
                }
            }
        }

        // 2. Poin Pelanggaran
        $poinRecords = PoinSiswa::where('siswa_id', $siswa->id)->get();
        $totalPoin = 100; // Base poin default
        foreach ($poinRecords as $pr) {
            $totalPoin -= $pr->skor_pengurang ?? 0;
            $totalPoin += $pr->skor_penambah ?? 0;
        }

        // 3. Kemajuan Akademis (Grow) - Rata-rata Nilai Sumatif per Titimangsa
        $grafikAkademis = [];
        
        if ($tahunAjaranAktif && $kelas) {
            $allTitimangsa = Titimangsa::where('tahun_ajaran_id', $tahunAjaranAktif->id)
                ->where('kurikulum_id', $kelas->kurikulum_id)
                ->orderBy('tanggal_cetak', 'asc')
                ->get();
                
            $nilaiSumatif = SumatifNilai::where('siswa_id', $siswa->id)->get();
            
            foreach ($allTitimangsa as $tm) {
                $nilaiTm = $nilaiSumatif->where('titimangsa_id', $tm->id);
                $rataRata = $nilaiTm->count() > 0 ? round($nilaiTm->avg('na_value'), 2) : null;
                $grafikAkademis[] = [
                    'label' => $tm->nama_periode,
                    'rata_rata' => $rataRata
                ];
            }
        }

        // 4. Peringkat Kelas di Periode Aktif
        $peringkat = null;
        $jumlahSiswa = 0;
        if ($titimangsaAktif && $kelas) {
            $allSiswaIds = \App\Models\Siswa::where('kelas_id', $kelas->id)->pluck('id')->toArray();
            $jumlahSiswa = count($allSiswaIds);
            
            $allSumatif = \App\Models\SumatifNilai::whereIn('siswa_id', $allSiswaIds)
                ->where('titimangsa_id', $titimangsaAktif->id)
                ->get();
            
            if ($allSumatif->count() > 0) {
                $totalsPerSiswa = [];
                foreach ($allSiswaIds as $sId) {
                    $totalsPerSiswa[$sId] = 0;
                }
                foreach ($allSumatif as $sum) {
                    $totalsPerSiswa[$sum->siswa_id] += $sum->na_value;
                }
                
                arsort($totalsPerSiswa);
                $peringkat = array_search($siswa->id, array_keys($totalsPerSiswa)) + 1;
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'siswa' => [
                    'nama' => $user->name,
                    'nis' => $siswa->nis,
                    'nisn' => $siswa->nisn,
                    'foto' => $siswa->foto
                ],
                'kelas' => [
                    'nama_kelas' => $namaKelasFull,
                    'tingkat' => $kelas ? $kelas->tingkat : '-',
                    'walas' => $walasName
                ],
                'periode' => [
                    'nama' => $titimangsaAktif ? $titimangsaAktif->nama_periode : '-',
                    'id' => $titimangsaAktif ? $titimangsaAktif->id : null
                ],
                'tahun_ajaran' => [
                    'nama' => $tahunAjaranAktif ? $tahunAjaranAktif->tahun : '-'
                ],
                'semester' => $activeSemester,
                'rekap' => [
                    'absensi_ganjil' => $absensiGanjil,
                    'absensi_genap' => $absensiGenap,
                    'total_poin' => $totalPoin,
                    'grafik_akademis' => $grafikAkademis,
                    'peringkat' => $peringkat,
                    'jumlah_siswa' => $jumlahSiswa
                ]
            ]
        ]);
    }
}
