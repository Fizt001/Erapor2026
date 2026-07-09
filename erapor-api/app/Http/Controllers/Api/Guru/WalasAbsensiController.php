<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PertemuanGuru;
use App\Models\AbsensiPertemuan;
use App\Models\Siswa;
use App\Models\WaliKelas;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;

class WalasAbsensiController extends Controller
{
    private function getWalasContext(Request $request)
    {
        $user = Auth::user();
        $tahunAktif = $request->tahun_ajaran_id 
            ? TahunAjaran::find($request->tahun_ajaran_id) 
            : TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAktif) return null;

        $walas = WaliKelas::where('guru_id', $user->id)
            ->whereHas('kelas', function($query) use ($tahunAktif) {
                $query->where('tahun_ajaran_id', $tahunAktif->id);
            })->first();

        if (!$walas) return null;

        return [
            'kelas_id' => $walas->kelas_id,
            'tahun_ajaran' => $tahunAktif,
        ];
    }

    public function getMonthlyCalendar(Request $request)
    {
        $context = $this->getWalasContext($request);
        if (!$context) {
            return response()->json(['success' => false, 'message' => 'Anda bukan wali kelas aktif.'], 403);
        }

        $bulan = $request->query('bulan', date('n'));
        $tahunTeks = $context['tahun_ajaran']->tahun; // "2026/2027"
        $tahunSplit = explode('/', $tahunTeks);
        
        // If month is July-Dec (7-12), use startYear. If Jan-Jun (1-6), use endYear.
        $tahun = (intval($bulan) >= 7) ? intval($tahunSplit[0]) : intval($tahunSplit[1]);

        // Get all meetings in that month for the class
        $pertemuans = PertemuanGuru::where('kelas_id', $context['kelas_id'])
            ->whereMonth('tanggal', $bulan)
            ->orderBy('tanggal')
            ->orderBy('jam_selesai', 'desc')
            ->get();

        $siswas = Siswa::with('user')
            ->where('kelas_id', $context['kelas_id'])
            ->whereNull('tanggal_keluar')
            ->get()
            ->sortBy(function($s) { return $s->user ? $s->user->name : ''; })
            ->values();

        $absensiRecords = AbsensiPertemuan::whereIn('pertemuan_id', $pertemuans->pluck('id'))->get()->groupBy('pertemuan_id');

        // Build Daily Status Matrix [siswa_id][day] = 'status'
        // Using Last Known State logic
        $matrix = [];
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

        foreach ($siswas as $siswa) {
            $matrix[$siswa->id] = [];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $matrix[$siswa->id][$d] = ''; // default empty
            }
        }

        // Loop over pertemuans (already ordered by date and jam_selesai desc)
        // The first meeting found for a specific date is the last one in the day.
        $processedDates = [];
        foreach ($pertemuans as $pert) {
            $date = date('j', strtotime($pert->tanggal));
            
            if (isset($absensiRecords[$pert->id])) {
                foreach ($absensiRecords[$pert->id] as $ab) {
                    // Only set if not already set by a later meeting that day
                    if (!isset($processedDates[$ab->siswa_id][$date])) {
                        if (in_array($ab->status, ['S', 'I', 'A'])) {
                            $matrix[$ab->siswa_id][$date] = $ab->status;
                            $processedDates[$ab->siswa_id][$date] = true;
                        } elseif ($ab->status === 'PKL') {
                            $matrix[$ab->siswa_id][$date] = 'P';
                            $processedDates[$ab->siswa_id][$date] = true;
                        }
                    }
                }
            }
        }

        // Calculate totals
        $data = $siswas->map(function($siswa) use ($matrix) {
            $sCount = 0; $iCount = 0; $aCount = 0; $pCount = 0;
            foreach ($matrix[$siswa->id] as $status) {
                if ($status == 'S') $sCount++;
                if ($status == 'I') $iCount++;
                if ($status == 'A') $aCount++;
                if ($status == 'P') $pCount++;
            }
            return [
                'id' => $siswa->id,
                'nama' => $siswa->user ? $siswa->user->name : '',
                'nis' => $siswa->nis,
                'absensi' => $matrix[$siswa->id],
                'totals' => ['S' => $sCount, 'I' => $iCount, 'A' => $aCount, 'P' => $pCount]
            ];
        });

        return response()->json([
            'success' => true,
            'tahun_ajaran' => $context['tahun_ajaran']->tahun,
            'days_in_month' => $daysInMonth,
            'data' => $data
        ]);
    }
}
