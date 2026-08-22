<?php

namespace App\Http\Controllers\Api\Kurikulum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PertemuanGuru;
use App\Models\TahunAjaran;
use Carbon\Carbon;

class KurikulumJurnalController extends Controller
{
    public function getGuruList()
    {
        $gurus = User::where('role', 'guru')
                     ->orderBy('name')
                     ->get(['id', 'name']);
                     
        return response()->json([
            'success' => true,
            'data' => $gurus
        ]);
    }

    public function index(Request $request)
    {
        $guruId = $request->query('guru_id');
        $bulan = $request->query('bulan'); // format: YYYY-MM

        if (!$guruId) {
            return response()->json([
                'success' => true,
                'data' => [] // Wajib pilih guru dulu
            ]);
        }

        $query = PertemuanGuru::with(['kelas', 'mapel'])
            ->withCount([
                'absensi as hadir_count' => function ($q) {
                    $q->where('status', 'H');
                },
                'absensi as sakit_count' => function ($q) {
                    $q->where('status', 'S');
                },
                'absensi as izin_count' => function ($q) {
                    $q->where('status', 'I');
                },
                'absensi as alpa_count' => function ($q) {
                    $q->where('status', 'A');
                }
            ])
            ->where('guru_id', $guruId);

        if ($bulan) {
            try {
                $date = Carbon::createFromFormat('Y-m', $bulan);
                $query->whereYear('tanggal', $date->year)
                      ->whereMonth('tanggal', $date->month);
            } catch (\Exception $e) {
                // Invalid format, do not filter by date
            }
        }

        $jurnals = $query->orderBy('tanggal', 'desc')
                         ->orderBy('jam_mulai', 'desc')
                         ->get();

        // Format response
        $formattedData = $jurnals->map(function ($jurnal) {
            // Find total siswa
            $totalAbsensi = $jurnal->hadir_count + $jurnal->sakit_count + $jurnal->izin_count + $jurnal->alpa_count;
            
            return [
                'id' => $jurnal->id,
                'tanggal' => $jurnal->tanggal,
                'waktu' => substr($jurnal->jam_mulai, 0, 5) . ' - ' . substr($jurnal->jam_selesai, 0, 5),
                'kelas' => $jurnal->kelas ? $jurnal->kelas->tingkat . ' ' . $jurnal->kelas->nama_kelas : '-',
                'mapel' => $jurnal->mapel ? $jurnal->mapel->nama_mapel : '-',
                'materi' => $jurnal->materi,
                'kehadiran' => [
                    'h' => $jurnal->hadir_count,
                    's' => $jurnal->sakit_count,
                    'i' => $jurnal->izin_count,
                    'a' => $jurnal->alpa_count,
                    'total_absen' => $totalAbsensi // for default if H is missing some
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedData
        ]);
    }
}
