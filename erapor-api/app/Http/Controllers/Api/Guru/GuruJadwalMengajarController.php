<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalPelajaran;
use App\Models\TahunAjaran;
use App\Models\PertemuanGuru;
use App\Models\AbsensiPertemuan;
use App\Models\Siswa;
use App\Models\Titimangsa;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GuruJadwalMengajarController extends Controller
{
    private $hariMap = [
        'Senin' => 1,
        'Selasa' => 2,
        'Rabu' => 3,
        'Kamis' => 4,
        'Jumat' => 5,
        'Sabtu' => 6,
        'Minggu' => 7,
    ];

    public function getJadwalMingguan(Request $request)
    {
        $guruId = $request->user()->id;
        $taAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$taAktif) return response()->json(['data' => []]);

        $jadwals = JadwalPelajaran::with(['kelas', 'mapel'])
            ->where('guru_id', $guruId)
            ->whereHas('kelas', function($q) use ($taAktif) {
                $q->where('tahun_ajaran_id', $taAktif->id);
            })
            ->orderBy('jam_ke')
            ->get();

        $rekap = [
            'Senin' => [], 'Selasa' => [], 'Rabu' => [], 'Kamis' => [], 'Jumat' => []
        ];

        foreach ($jadwals as $j) {
            if (isset($rekap[$j->hari])) {
                $rekap[$j->hari][] = [
                    'kelas' => $j->kelas->nama_kelas,
                    'mapel' => $j->mapel->nama_mapel,
                    'jam_ke' => $j->jam_ke,
                    'waktu' => substr($j->waktu_mulai, 0, 5) . ' - ' . substr($j->waktu_selesai, 0, 5)
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $rekap
        ]);
    }

    public function getJadwalByHari(Request $request)
    {
        try {
            $guruId = $request->user()->id;
            $hari = $request->query('hari', 'Senin');
            $taAktif = TahunAjaran::where('is_aktif', true)->first();
            
            if (!$taAktif) return response()->json(['success' => false, 'message' => 'Tahun Ajaran Aktif tidak ditemukan'], 404);

            $jadwals = JadwalPelajaran::with(['kelas', 'mapel'])
                ->where('guru_id', $guruId)
                ->where('hari', 'LIKE', '%' . trim($hari) . '%')
                ->whereHas('kelas', function($q) use ($taAktif) {
                    $q->where('tahun_ajaran_id', $taAktif->id);
                })
                ->orderBy('jam_ke')
                ->get();

            $now = Carbon::now('Asia/Jakarta');
            $targetDayIndex = $this->hariMap[$hari] ?? 1;

            $requestedDate = $request->query('tanggal');
            if ($requestedDate) {
                $targetDate = Carbon::parse($requestedDate, 'Asia/Jakarta');
            } else {
                $targetDate = $now->copy()->startOfWeek()->addDays($targetDayIndex - 1);
            }

            $titimangsa = Titimangsa::where('is_aktif', true)->first();

            // Gabungkan jadwal berurutan dengan kelas dan mapel yang sama
            $groupedJadwals = [];
            $currentGroup = null;

            foreach ($jadwals as $j) {
                if ($currentGroup && 
                    $currentGroup['kelas_id'] == $j->kelas_id && 
                    $currentGroup['mapel_id'] == $j->mapel_id &&
                    $currentGroup['last_jam_ke'] == ($j->jam_ke - 1)) {
                    
                    $currentGroup['last_jam_ke'] = $j->jam_ke;
                    $currentGroup['waktu_selesai'] = $j->waktu_selesai;
                    $currentGroup['jam_ke_array'][] = $j->jam_ke;
                } else {
                    if ($currentGroup) {
                        $groupedJadwals[] = $currentGroup;
                    }
                    $currentGroup = [
                        'kelas_id' => $j->kelas_id,
                        'kelas_nama' => $j->kelas ? $j->kelas->nama_kelas : 'Unknown',
                        'mapel_id' => $j->mapel_id,
                        'mapel_nama' => $j->mapel ? $j->mapel->nama_mapel : 'Unknown',
                        'mapel_kode' => $j->mapel ? $j->mapel->kode_mapel : 'Unknown',
                        'first_jam_ke' => $j->jam_ke,
                        'last_jam_ke' => $j->jam_ke,
                        'jam_ke_array' => [$j->jam_ke],
                        'waktu_mulai' => $j->waktu_mulai,
                        'waktu_selesai' => $j->waktu_selesai,
                    ];
                }
            }
            if ($currentGroup) {
                $groupedJadwals[] = $currentGroup;
            }

            $result = [];
            foreach ($groupedJadwals as $g) {
                // Tentukan status kunci waktu berdasarkan targetDate
                $statusWaktu = 'belum_waktunya';
                $today = $now->copy()->startOfDay();
                $targetDay = $targetDate->copy()->startOfDay();

                if ($targetDay->lt($today)) {
                    $statusWaktu = 'sudah_lewat';
                } elseif ($targetDay->gt($today)) {
                    $statusWaktu = 'belum_waktunya';
                } else {
                    // Hari yang sama (hari ini), cek jam
                    $currentTime = $now->format('H:i:s');
                    if ($currentTime < ($g['waktu_mulai'] ?? '00:00:00')) {
                        $statusWaktu = 'belum_waktunya';
                    } elseif ($currentTime > ($g['waktu_selesai'] ?? '23:59:59')) {
                        $statusWaktu = 'sudah_lewat';
                    } else {
                        $statusWaktu = 'sekarang';
                    }
                }

                // Jika status != belum_waktunya, ambil data jurnal/absensi dari database
                $jurnal = null;
                $siswaAbsensi = [];
                $pertemuanKe = 1;

                if ($statusWaktu !== 'belum_waktunya' && $titimangsa) {
                    // Cari pertemuan guru pada tanggal tersebut
                    $pertemuan = PertemuanGuru::where('guru_id', $guruId)
                        ->where('titimangsa_id', $titimangsa->id)
                        ->where('kelas_id', $g['kelas_id'])
                        ->where('mapel_id', $g['mapel_id'])
                        ->where('tanggal', $targetDate->format('Y-m-d'))
                        ->first();

                    // Hitung pertemuan ke berapa
                    $pertemuanKe = PertemuanGuru::where('guru_id', $guruId)
                        ->where('titimangsa_id', $titimangsa->id)
                        ->where('kelas_id', $g['kelas_id'])
                        ->where('mapel_id', $g['mapel_id'])
                        ->where('tanggal', '<', $targetDate->format('Y-m-d'))
                        ->count() + 1;

                    if ($pertemuan) {
                        $jurnal = $pertemuan->materi;
                        
                        // Ambil absensi
                        $absensis = AbsensiPertemuan::where('pertemuan_id', $pertemuan->id)->get()->keyBy('siswa_id');
                        
                        // Ambil list siswa kelas
                        $siswas = Siswa::join('users', 'siswa.user_id', '=', 'users.id')
                                       ->where('siswa.kelas_id', $g['kelas_id'])
                                       ->orderBy('users.name')
                                       ->select('siswa.*', 'users.name as nama_lengkap')
                                       ->get();
                                       
                        foreach ($siswas as $siswa) {
                            $siswaAbsensi[] = [
                                'siswa_id' => $siswa->id,
                                'nama_lengkap' => $siswa->nama_lengkap,
                                'nisn' => $siswa->nisn,
                                'status' => isset($absensis[$siswa->id]) ? $absensis[$siswa->id]->status : 'H'
                            ];
                        }
                    } else {
                        // Siapkan default list siswa (Hadir semua)
                        $siswas = Siswa::join('users', 'siswa.user_id', '=', 'users.id')
                                       ->where('siswa.kelas_id', $g['kelas_id'])
                                       ->orderBy('users.name')
                                       ->select('siswa.*', 'users.name as nama_lengkap')
                                       ->get();
                        foreach ($siswas as $siswa) {
                            $siswaAbsensi[] = [
                                'siswa_id' => $siswa->id,
                                'nama_lengkap' => $siswa->nama_lengkap,
                                'nisn' => $siswa->nisn,
                                'status' => 'H'
                            ];
                        }
                    }
                }

                $jamString = count($g['jam_ke_array']) > 1 
                    ? "{$g['first_jam_ke']} - {$g['last_jam_ke']}" 
                    : "{$g['first_jam_ke']}";

                $result[] = [
                    'kelas_id' => $g['kelas_id'],
                    'kelas_nama' => $g['kelas_nama'],
                    'mapel_id' => $g['mapel_id'],
                    'mapel_nama' => $g['mapel_nama'],
                    'mapel_kode' => $g['mapel_kode'],
                    'jam_ke_string' => $jamString,
                    'waktu_mulai' => $g['waktu_mulai'],
                    'waktu_selesai' => $g['waktu_selesai'],
                    'waktu' => substr($g['waktu_mulai'] ?? '', 0, 5) . ' - ' . substr($g['waktu_selesai'] ?? '', 0, 5),
                    'status_waktu' => $statusWaktu,
                    'jurnal' => $jurnal,
                    'pertemuan_ke' => $pertemuanKe,
                    'absensi' => $siswaAbsensi,
                    'tanggal' => $targetDate->format('Y-m-d')
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $result,
                'tanggal_mulai' => $taAktif->tanggal_mulai,
                'target_tanggal' => $targetDate->format('Y-m-d')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage() . ' at line ' . $e->getLine(),
                'target_tanggal' => date('Y-m-d'),
                'data' => []
            ], 200); // return 200 so Nuxt catches it and displays it via toast!
        }
    }

    public function simpanJurnalAbsensi(Request $request)
    {
        $guruId = $request->user()->id;
        $titimangsa = Titimangsa::where('is_aktif', true)->first();
        if (!$titimangsa) {
            return response()->json(['success' => false, 'message' => 'Titimangsa aktif tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapels,id',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'nullable|string',
            'waktu_selesai' => 'nullable|string',
            'materi' => 'nullable|string',
            'absensi' => 'array'
        ]);

        if ($validator->fails()) {
            file_put_contents(storage_path("logs/debug_save.txt"), "Validation Error: " . json_encode($validator->errors()));
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $validator->errors()], 200);
        }

        DB::beginTransaction();
        try {
            // Cari atau buat jurnal (pertemuan)
            $pertemuan = PertemuanGuru::updateOrCreate(
                [
                    'guru_id' => $guruId,
                    'titimangsa_id' => $titimangsa->id,
                    'kelas_id' => $request->kelas_id,
                    'mapel_id' => $request->mapel_id,
                    'tanggal' => $request->tanggal,
                ],
                [
                    'jam_mulai' => (int)($request->waktu_mulai ?? 0),
                    'jam_selesai' => (int)($request->waktu_selesai ?? 0),
                    'materi' => $request->materi ?? '-',
                ]
            );

            // Simpan absensi
            if ($request->has('absensi') && is_array($request->absensi)) {
                // Delete old absensi for this pertemuan to replace with new ones
                AbsensiPertemuan::where('pertemuan_id', $pertemuan->id)->delete();

                $insertAbsensi = [];
                $now = Carbon::now();
                foreach ($request->absensi as $ab) {
                    $insertAbsensi[] = [
                        'pertemuan_id' => $pertemuan->id,
                        'siswa_id' => $ab['siswa_id'],
                        'status' => $ab['status'],
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                }
                if (!empty($insertAbsensi)) {
                    AbsensiPertemuan::insert($insertAbsensi);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Jurnal & Absensi berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            file_put_contents(storage_path("logs/debug_save.txt"), "Save Error: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
            // Return 200 so Nuxt $fetch doesn't throw and crash
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 200);
        }
    }
}
// trigger deploy
