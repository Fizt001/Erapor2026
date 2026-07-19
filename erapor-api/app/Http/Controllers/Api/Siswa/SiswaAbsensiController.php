<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TahunAjaran;
use App\Models\PertemuanGuru;
use App\Models\AbsensiPertemuan;

class SiswaAbsensiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $siswa = $user->siswa;

        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Data siswa tidak ditemukan.'], 404);
        }

        $bulan = $request->query('bulan');
        $tahunQuery = $request->query('tahun');

        if (!$bulan) {
            $bulan = (int) date('n');
        }

        $taAktif = TahunAjaran::where('is_aktif', 1)->first();
        if (!$taAktif) {
            return response()->json(['success' => false, 'message' => 'Tahun Ajaran aktif tidak ditemukan.'], 404);
        }

        $tahun = $tahunQuery ? (int)$tahunQuery : (int) explode('/', $taAktif->tahun)[0];

        if ($bulan >= 1 && $bulan <= 6) {
            $tahunAktual = $tahun + 1;
        } else {
            $tahunAktual = $tahun;
        }

        $pertemuans = PertemuanGuru::where('kelas_id', $siswa->kelas_id)
            ->whereYear('tanggal', $tahunAktual)
            ->whereMonth('tanggal', $bulan)
            ->orderBy('tanggal')
            ->orderBy('jam_selesai', 'desc')
            ->get();

        $absensiRecords = AbsensiPertemuan::where('siswa_id', $siswa->id)
            ->whereIn('pertemuan_id', $pertemuans->pluck('id'))
            ->get()
            ->groupBy('pertemuan_id');

        $matrix = [];
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahunAktual);

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $matrix[$d] = 'H';
        }

        $processedDates = [];
        $meetingDates = [];

        foreach ($pertemuans as $pert) {
            $date = date('j', strtotime($pert->tanggal));
            $meetingDates[$date] = true;
            
            if (isset($absensiRecords[$pert->id])) {
                foreach ($absensiRecords[$pert->id] as $ab) {
                    if (!isset($processedDates[$date])) {
                        if (in_array($ab->status, ['S', 'I', 'A'])) {
                            $matrix[$date] = $ab->status;
                            $processedDates[$date] = true;
                        } elseif ($ab->status === 'PKL') {
                            $matrix[$date] = 'P';
                            $processedDates[$date] = true;
                        }
                    }
                }
            }
        }

        for ($d = 1; $d <= $daysInMonth; $d++) {
            if (!isset($meetingDates[$d])) {
                $matrix[$d] = '';
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'bulan' => (int)$bulan,
                'tahun' => $tahunAktual,
                'tahun_ajaran' => $taAktif->tahun,
                'absensi' => $matrix
            ]
        ]);
    }
}
