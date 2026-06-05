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
        if ($kelas && $kelas->walas) {
            $walasName = $kelas->walas->user->name ?? '-';
        }

        $titimangsaAktif = Titimangsa::where('is_aktif', true)->first();
        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();

        // 1. Absensi 1 Tahun (Tahun Ajaran Aktif)
        $absensi = ['s' => 0, 'i' => 0, 'a' => 0];
        if ($tahunAjaranAktif) {
            $titimangsaIds = Titimangsa::where('tahun_ajaran_id', $tahunAjaranAktif->id)->pluck('id');
            $absensiData = AbsensiSiswa::where('siswa_id', $siswa->id)
                ->whereIn('titimangsa_id', $titimangsaIds)
                ->get();
            
            foreach ($absensiData as $ab) {
                $absensi['s'] += $ab->s;
                $absensi['i'] += $ab->i;
                $absensi['a'] += $ab->a;
            }
        }

        // 2. Poin Pelanggaran
        $totalPoin = PoinSiswa::where('siswa_id', $siswa->id)->sum('poin');

        // 3. Kemajuan Akademis (Grow) - Rata-rata Nilai Sumatif per Titimangsa
        $allTitimangsa = Titimangsa::orderBy('id', 'asc')->get();
        $grafikAkademis = [];
        
        $nilaiSumatif = SumatifNilai::where('siswa_id', $siswa->id)->get();
        
        foreach ($allTitimangsa as $tm) {
            $nilaiTm = $nilaiSumatif->where('titimangsa_id', $tm->id);
            if ($nilaiTm->count() > 0) {
                $rataRata = $nilaiTm->avg('na_value');
                $grafikAkademis[] = [
                    'label' => $tm->nama_periode,
                    'rata_rata' => round($rataRata, 2)
                ];
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
                    'nama_kelas' => $kelas ? $kelas->nama_kelas : '-',
                    'tingkat' => $kelas ? $kelas->tingkat : '-',
                    'walas' => $walasName
                ],
                'periode' => [
                    'nama' => $titimangsaAktif ? $titimangsaAktif->nama_periode : '-',
                    'id' => $titimangsaAktif ? $titimangsaAktif->id : null
                ],
                'tahun_ajaran' => [
                    'nama' => $tahunAjaranAktif ? $tahunAjaranAktif->tahun_ajaran : '-'
                ],
                'rekap' => [
                    'absensi' => $absensi,
                    'total_poin' => $totalPoin,
                    'grafik_akademis' => $grafikAkademis
                ]
            ]
        ]);
    }
}
