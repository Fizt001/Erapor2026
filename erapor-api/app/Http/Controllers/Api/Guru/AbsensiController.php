<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PertemuanGuru;
use App\Models\AbsensiPertemuan;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\TahunAjaran;
use App\Models\Pengampu;
use App\Models\Kurikulum;
use App\Models\Titimangsa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    public function getReferensi(Request $request)
    {
        $user = Auth::user();

        // 1. Ambil opsi referensi
        $tahunAjarans = TahunAjaran::orderBy('id', 'desc')->get();
        $kurikulums = Kurikulum::all();

        $selectedTahunId = $request->tahun_ajaran_id ?? optional(TahunAjaran::where('is_aktif', true)->first())->id;
        $selectedKurikulumId = $request->kurikulum_id ?? optional($kurikulums->first())->id;

        $periodes = Titimangsa::where('tahun_ajaran_id', $selectedTahunId)
                              ->where('kurikulum_id', $selectedKurikulumId)
                              ->get();
                              
        $selectedTitimangsaId = $request->titimangsa_id ?? ($periodes->where('is_aktif', true)->first()->id ?? null);
        $titimangsaData = $periodes->where('id', $selectedTitimangsaId)->first();
        $isTitimangsaAktif = $titimangsaData ? $titimangsaData->is_aktif : false;

        $selectedKelasId = $request->kelas_id;

        // Kelas
        $kelases = Kelas::where('tahun_ajaran_id', $selectedTahunId)
            ->whereHas('pengampus', function($q) use ($user) {
                $q->where('guru_id', $user->id);
            })->get();

        $mapels = [];
        if ($selectedKelasId) {
            $mapels = Mapel::where(function($q) use ($user, $selectedKelasId) {
                $q->whereHas('strukturKurikulums.pengampus', function($sq) use ($user, $selectedKelasId) {
                    $sq->where('guru_id', $user->id)
                       ->where('kelas_id', $selectedKelasId);
                })
                ->orWhereHas('strukturKejuruans.pengampus', function($sq) use ($user, $selectedKelasId) {
                    $sq->where('guru_id', $user->id)
                       ->where('kelas_id', $selectedKelasId);
                });
            })->get();
        }

        return response()->json([
            'success' => true,
            'tahunAjarans' => $tahunAjarans,
            'kurikulums' => $kurikulums,
            'periodes' => $periodes,
            'kelases' => $kelases,
            'mapels' => $mapels,
            'is_titimangsa_aktif' => $isTitimangsaAktif,
        ]);
    }

    /**
     * Get list of meetings for a specific class and subject
     */
    public function getPertemuan(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapels,id',
            'titimangsa_id' => 'required|exists:titimangsas,id',
        ]);

        $pertemuan = PertemuanGuru::withCount('absensi')
            ->where('kelas_id', $request->kelas_id)
            ->where('mapel_id', $request->mapel_id)
            ->where('titimangsa_id', $request->titimangsa_id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_mulai', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $pertemuan]);
    }

    /**
     * Get the last jam_selesai for a specific class on a specific date
     */
    public function getLastJam(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal' => 'required|date'
        ]);

        $lastJam = PertemuanGuru::where('kelas_id', $request->kelas_id)
            ->whereDate('tanggal', $request->tanggal)
            ->max('jam_selesai');

        return response()->json([
            'success' => true,
            'last_jam' => $lastJam ? (int) $lastJam : 0
        ]);
    }

    /**
     * Create a new meeting
     */
    public function createPertemuan(Request $request)
    {
        $request->validate([
            'titimangsa_id' => 'required|exists:titimangsas,id',
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapels,id',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|integer|min:1|max:12',
            'jam_selesai' => 'required|integer|min:1|max:12|gte:jam_mulai',
            'materi' => 'nullable|string|max:255'
        ]);

        // Validate conflict (teacher cannot teach two classes at the same time)
        // Ignoring this for simplicity unless requested, as they might have double bookings in real life sometimes

        $pertemuan = PertemuanGuru::create([
            'guru_id' => Auth::id(),
            'titimangsa_id' => $request->titimangsa_id,
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mapel_id,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'materi' => $request->materi,
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Pertemuan berhasil ditambahkan', 
            'data' => $pertemuan
        ]);
    }
    
    /**
     * Update a meeting
     */
    public function updatePertemuan(Request $request, $id)
    {
        $pertemuan = PertemuanGuru::findOrFail($id);
        
        $request->validate([
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|integer|min:1|max:12',
            'jam_selesai' => 'required|integer|min:1|max:12|gte:jam_mulai',
            'materi' => 'nullable|string|max:255'
        ]);

        $pertemuan->update([
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'materi' => $request->materi,
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Pertemuan berhasil diperbarui', 
            'data' => $pertemuan
        ]);
    }
    
    /**
     * Delete a meeting
     */
    public function deletePertemuan($id)
    {
        $pertemuan = PertemuanGuru::findOrFail($id);
        $pertemuan->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Pertemuan berhasil dihapus'
        ]);
    }

    /**
     * Get students and their attendance for a specific meeting
     */
    public function getAbsensiSiswa($pertemuan_id)
    {
        $pertemuan = PertemuanGuru::findOrFail($pertemuan_id);

        $siswas = Siswa::with('user:id,name')
            ->where('kelas_id', $pertemuan->kelas_id)
            ->whereNull('tanggal_keluar')
            ->get()
            ->sortBy(function($s) { return $s->user ? $s->user->name : ''; })
            ->values();

        $absensi = AbsensiPertemuan::where('pertemuan_id', $pertemuan_id)->get()->keyBy('siswa_id');
        
        // Fetch the most recent attendance status for each student earlier in the same day
        $previousStatuses = DB::table('absensi_pertemuan')
            ->join('pertemuan_guru', 'absensi_pertemuan.pertemuan_id', '=', 'pertemuan_guru.id')
            ->where('pertemuan_guru.kelas_id', $pertemuan->kelas_id)
            ->whereDate('pertemuan_guru.tanggal', $pertemuan->tanggal)
            ->where('pertemuan_guru.jam_mulai', '<', $pertemuan->jam_mulai)
            ->orderBy('pertemuan_guru.jam_mulai', 'desc')
            ->get(['absensi_pertemuan.siswa_id', 'absensi_pertemuan.status'])
            ->groupBy('siswa_id')
            ->map(function ($items) {
                return $items->first()->status;
            });

        $data = $siswas->map(function($siswa) use ($absensi, $previousStatuses) {
            $status = null;
            if (isset($absensi[$siswa->id])) {
                $status = $absensi[$siswa->id]->status;
            } else if (isset($previousStatuses[$siswa->id])) {
                $status = $previousStatuses[$siswa->id];
            }
            
            return [
                'siswa_id' => $siswa->id,
                'nama' => $siswa->user ? $siswa->user->name : 'Unknown',
                'nis' => $siswa->nis,
                'status' => $status,
            ];
        });

        return response()->json([
            'success' => true, 
            'pertemuan' => $pertemuan,
            'data' => $data
        ]);
    }

    /**
     * Save attendance and trigger escalation
     */
    public function simpanAbsensi(Request $request, $pertemuan_id)
    {
        $request->validate([
            'absensi' => 'required|array',
            'absensi.*.siswa_id' => 'required|exists:siswa,id',
            'absensi.*.status' => 'nullable|in:H,S,I,A,PKL'
        ]);

        $pertemuan = PertemuanGuru::findOrFail($pertemuan_id);

        // Pre-load di luar loop untuk menghindari N+1
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        $mapel = $tahunAktif ? Mapel::find($pertemuan->mapel_id) : null;
        $nama_mapel = $mapel ? $mapel->nama_mapel : 'Unknown Mapel';

        // Ambil semua siswa_id dari request
        $allSiswaIds = collect($request->absensi)->pluck('siswa_id')->toArray();

        // Pre-load count S/I/A per siswa untuk mapel ini dalam 1 query per status
        $kelasIdForYear = $tahunAktif
            ? DB::table('kelas')->where('tahun_ajaran_id', $tahunAktif->id)->pluck('id')->toArray()
            : [];

        $pertemuanIdsForMapel = PertemuanGuru::where('mapel_id', $pertemuan->mapel_id)
            ->whereIn('kelas_id', $kelasIdForYear)
            ->pluck('id');

        $countsRaw = AbsensiPertemuan::whereIn('siswa_id', $allSiswaIds)
            ->whereIn('pertemuan_id', $pertemuanIdsForMapel)
            ->whereIn('status', ['S', 'I', 'A'])
            ->selectRaw('siswa_id, status, COUNT(*) as total')
            ->groupBy('siswa_id', 'status')
            ->get();

        // Build lookup: $counts[$siswa_id][$status] = count
        $counts = [];
        foreach ($countsRaw as $row) {
            $counts[$row->siswa_id][$row->status] = $row->total;
        }

        DB::beginTransaction();
        try {
            foreach ($request->absensi as $absen) {
                if (empty($absen['status'])) {
                    AbsensiPertemuan::where('pertemuan_id', $pertemuan_id)
                        ->where('siswa_id', $absen['siswa_id'])
                        ->delete();
                    continue;
                }

                AbsensiPertemuan::updateOrCreate(
                    [
                        'pertemuan_id' => $pertemuan_id,
                        'siswa_id'     => $absen['siswa_id'],
                    ],
                    ['status' => $absen['status']]
                );

                // ESCALATION LOGIC — pakai data yang sudah di-bulk load
                $status = $absen['status'];
                if ($tahunAktif && in_array($status, ['S', 'I', 'A'])) {
                    // Tambah 1 untuk status yang baru saja disimpan
                    $countS = ($counts[$absen['siswa_id']]['S'] ?? 0) + ($status === 'S' ? 1 : 0);
                    $countI = ($counts[$absen['siswa_id']]['I'] ?? 0) + ($status === 'I' ? 1 : 0);
                    $countA = ($counts[$absen['siswa_id']]['A'] ?? 0) + ($status === 'A' ? 1 : 0);

                    $kategori = null;
                    $deskripsi = null;

                    if ($status == 'S' && $countS >= 3) {
                        $kategori = 'Bimbingan Walas';
                        $deskripsi = "Siswa mencapai {$countS}x Sakit pada mapel $nama_mapel.";
                    } elseif ($status == 'I' && $countI >= 3) {
                        $kategori = 'Bimbingan Walas';
                        $deskripsi = "Siswa mencapai {$countI}x Izin pada mapel $nama_mapel.";
                    } elseif ($status == 'A') {
                        if ($countA == 3) {
                            $kategori = 'Bimbingan Walas';
                            $deskripsi = "Siswa mencapai 3x Alpa pada mapel $nama_mapel.";
                        } elseif ($countA == 5) {
                            $kategori = 'SP1';
                            $deskripsi = "Siswa mencapai 5x Alpa pada mapel $nama_mapel. Proses SP1.";
                        } elseif ($countA == 10) {
                            $kategori = 'SP2';
                            $deskripsi = "Siswa mencapai 10x Alpa pada mapel $nama_mapel. Proses SP2.";
                        } elseif ($countA == 15) {
                            $kategori = 'SP3';
                            $deskripsi = "Siswa mencapai 15x Alpa pada mapel $nama_mapel. Proses SP3/Pengeluaran.";
                        }
                    }

                    if ($kategori) {
                        \App\Models\PenangananPelanggaran::firstOrCreate(
                            [
                                'siswa_id'          => $absen['siswa_id'],
                                'tahun_ajaran_id'   => $tahunAktif->id,
                                'kategori'          => $kategori,
                                'deskripsi_masalah' => $deskripsi,
                            ],
                            [
                                'guru_id'              => Auth::id(),
                                'tindakan_penyelesaian' => '',
                                'status'               => 'Proses'
                            ]
                        );
                    }
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Absensi berhasil disimpan.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan absensi: ' . $e->getMessage()], 500);
        }
    }
}
