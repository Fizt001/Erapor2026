<?php

namespace App\Http\Controllers\Api\Kurikulum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\PertemuanGuru;
use Carbon\Carbon;

class KurikulumJurnalController extends Controller
{
    public function getKelasList()
    {
        $taAktif = \App\Models\TahunAjaran::where('is_aktif', true)->first();
        $query = Kelas::orderBy('tingkat')->orderBy('nama_kelas');
        
        if ($taAktif) {
            $query->where('tahun_ajaran_id', $taAktif->id);
        }

        $kelas = $query->get(['id', 'tingkat', 'nama_kelas']);
        $formatted = $kelas->map(function($k) {
            return [
                'id' => $k->id,
                'nama' => $k->tingkat . ' ' . $k->nama_kelas
            ];
        });
                     
        return response()->json([
            'success' => true,
            'data' => $formatted
        ]);
    }

    public function index(Request $request)
    {
        $kelasId = $request->query('kelas_id');
        $bulan = $request->query('bulan'); // format: YYYY-MM

        if (!$kelasId || !$bulan) {
            return response()->json([
                'success' => true,
                'siswas' => [],
                'jurnals_per_tanggal' => []
            ]);
        }
        
        try {
            $date = Carbon::createFromFormat('Y-m', $bulan);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Format bulan tidak valid'
            ]);
        }

        // Ambil data siswa untuk matriks
        $siswas = Siswa::join('users', 'siswa.user_id', '=', 'users.id')
            ->where('siswa.kelas_id', $kelasId)
            ->orderBy('users.name')
            ->select('siswa.id', 'users.name as nama_lengkap', 'siswa.nisn')
            ->get();

        // Ambil data pertemuan beserta absensi
        $pertemuans = PertemuanGuru::with(['guru', 'mapel', 'absensi'])
            ->where('kelas_id', $kelasId)
            ->whereYear('tanggal', $date->year)
            ->whereMonth('tanggal', $date->month)
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->get();

        $jurnals_per_tanggal = [];
        
        foreach ($pertemuans as $p) {
            $tanggal = $p->tanggal;
            if (!isset($jurnals_per_tanggal[$tanggal])) {
                $jurnals_per_tanggal[$tanggal] = [];
            }
            
            // Map absensi untuk O(1) lookup di frontend (siswa_id => status)
            $absensiMap = [];
            foreach ($p->absensi as $abs) {
                $absensiMap[$abs->siswa_id] = $abs->status;
            }
            
            $jurnals_per_tanggal[$tanggal][] = [
                'id' => $p->id,
                'waktu' => substr($p->jam_mulai, 0, 5) . ' - ' . substr($p->jam_selesai, 0, 5),
                'guru' => $p->guru ? $p->guru->name : 'Unknown',
                'mapel' => $p->mapel ? $p->mapel->nama_mapel : 'Unknown',
                'materi' => $p->materi,
                'absensi' => $absensiMap
            ];
        }

        return response()->json([
            'success' => true,
            'siswas' => $siswas,
            'jurnals_per_tanggal' => $jurnals_per_tanggal
        ]);
    }
}
