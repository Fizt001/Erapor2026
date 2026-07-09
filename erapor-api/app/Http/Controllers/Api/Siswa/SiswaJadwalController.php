<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TahunAjaran;
use App\Models\JadwalPelajaran;
use App\Models\KalenderAkademik;

class SiswaJadwalController extends Controller
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

        // 1. Ambil Jadwal Pelajaran (berdasarkan kelas anak saat ini)
        $kelasId = $siswa->kelas_id;
        $jadwalList = [];
        
        if ($kelasId) {
            $jadwals = JadwalPelajaran::with(['mapel', 'guru'])
                ->where('kelas_id', $kelasId)
                ->orderBy('jam_ke', 'asc')
                ->get();
                
            // Kelompokkan per hari
            $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            
            foreach ($hariList as $hari) {
                $jadwalHariIni = $jadwals->where('hari', $hari)->map(function ($j) {
                    return [
                        'id' => $j->id,
                        'jam_ke' => $j->jam_ke,
                        'waktu_mulai' => \Carbon\Carbon::parse($j->waktu_mulai)->format('H:i'),
                        'waktu_selesai' => \Carbon\Carbon::parse($j->waktu_selesai)->format('H:i'),
                        'mapel' => $j->mapel ? $j->mapel->nama_mapel : '-',
                        'guru' => $j->guru ? $j->guru->name : '-'
                    ];
                })->values();
                
                if ($jadwalHariIni->count() > 0) {
                    $jadwalList[$hari] = $jadwalHariIni;
                }
            }
        }

        // 2. Ambil Kalender Akademik Tahun Ajaran Aktif
        $kalenderList = KalenderAkademik::where('tahun_ajaran_id', $tahunAjaranAktif->id)
            ->orderBy('tanggal_mulai', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tanggal_mulai' => $item->tanggal_mulai,
                    'tanggal_selesai' => $item->tanggal_selesai,
                    'nama_kegiatan' => $item->nama_kegiatan,
                    'jenis_kegiatan' => $item->jenis_kegiatan
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'jadwal' => $jadwalList,
                'kalender' => $kalenderList
            ]
        ]);
    }
}
