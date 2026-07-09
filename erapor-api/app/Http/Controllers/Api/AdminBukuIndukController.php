<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Kurikulum;

class AdminBukuIndukController extends Controller
{
    /**
     * Get dependencies for Buku Induk Filter (Tahun Ajaran Aktif, Kurikulum, Kelas)
     */
    public function getDependencies()
    {
        $tahunAjarans = TahunAjaran::orderBy('tahun', 'desc')->get();
        $tahunAjaran = TahunAjaran::where('is_aktif', true)->first();
        $kurikulums = Kurikulum::select('id', 'nama_kurikulum', 'singkatan')->get();
        // Fetch classes (we can return all classes or just let the frontend filter, but it's better to return all classes so frontend can filter by tahun_ajaran)
        $kelas = Kelas::with([
            'kurikulum:id,singkatan', 
            'kejuruan.program.bidang'
        ])
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        return response()->json([
            'success' => true,
            'tahun_ajarans' => $tahunAjarans,
            'active_tahun_ajaran' => $tahunAjaran,
            'kurikulums' => $kurikulums,
            'kelas' => $kelas
        ]);
    }

    /**
     * Get biodata for all students in a specific class
     */
    public function getBiodataKelas($kelas_id)
    {
        // Ensure class exists
        $kelas = Kelas::find($kelas_id);
        if (!$kelas) {
            return response()->json(['success' => false, 'message' => 'Kelas tidak ditemukan'], 404);
        }

        // Get all students in the class (current or past based on sumatif_nilai)
        $siswas = Siswa::with('user')
            ->where(function ($query) use ($kelas_id) {
                $query->where('kelas_id', $kelas_id)
                      ->orWhereHas('sumatifNilai', function ($q) use ($kelas_id) {
                          $q->where('kelas_id', $kelas_id);
                      });
            })
            ->get()
            ->sortBy(function($siswa) {
                return $siswa->user ? $siswa->user->name : '';
            })->values();

        $fields = [
            'nis', 'nisn', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 
            'anak_ke', 'status_keluarga', 'warga_negara', 'agama', 'alamat', 'no_hp',
            'nama_ayah', 'ttl_ayah', 'pekerjaan_ayah', 'no_hp_ayah', 
            'nama_ibu', 'ttl_ibu', 'pekerjaan_ibu', 'no_hp_ibu', 
            'nama_wali', 'pekerjaan_wali', 'no_hp_wali', 'alamat_wali',
            'asal_sekolah', 'alamat_sekolah_asal', 'no_sttb_smp', 'tgl_sttb_smp',
            'tanggal_masuk', 'kelas_masuk', 'tanggal_keluar', 'alasan_keluar',
            'no_sttb_smk', 'tgl_sttb_smk', 'tempat_pkl', 'alamat_pkl',
            'tgl_mulai_pkl', 'tgl_selesai_pkl'
        ];
        $totalFields = count($fields);

        // Map data to students
        $data = $siswas->map(function ($siswa) use ($fields, $totalFields) {
            $filled = 0;
            foreach ($fields as $f) {
                if (!empty($siswa->{$f})) $filled++;
            }
            $persentase = round(($filled / $totalFields) * 100);

            return [
                'id' => $siswa->id,
                'user_id' => $siswa->user_id,
                'nama_lengkap' => $siswa->user ? $siswa->user->name : '-',
                'nis' => $siswa->nis,
                'nisn' => $siswa->nisn,
                'jenis_kelamin' => $siswa->jenis_kelamin === 'L' ? 'LAKI-LAKI' : ($siswa->jenis_kelamin === 'P' ? 'PEREMPUAN' : '-'),
                'tempat_lahir' => $siswa->tempat_lahir,
                'tanggal_lahir' => $siswa->tanggal_lahir ? date('d F Y', strtotime($siswa->tanggal_lahir)) : null,
                'anak_ke' => $siswa->anak_ke,
                'status_keluarga' => $siswa->status_keluarga,
                'warga_negara' => $siswa->warga_negara,
                'agama' => $siswa->agama,
                'alamat' => $siswa->alamat,
                'no_hp' => $siswa->no_hp,
                
                'nama_ayah' => $siswa->nama_ayah,
                'ttl_ayah' => $siswa->ttl_ayah,
                'pekerjaan_ayah' => $siswa->pekerjaan_ayah,
                'no_hp_ayah' => $siswa->no_hp_ayah,
                
                'nama_ibu' => $siswa->nama_ibu,
                'ttl_ibu' => $siswa->ttl_ibu,
                'pekerjaan_ibu' => $siswa->pekerjaan_ibu,
                'no_hp_ibu' => $siswa->no_hp_ibu,
                
                'nama_wali' => $siswa->nama_wali,
                'pekerjaan_wali' => $siswa->pekerjaan_wali,
                'no_hp_wali' => $siswa->no_hp_wali,
                'alamat_wali' => $siswa->alamat_wali,
                
                'asal_sekolah' => $siswa->asal_sekolah,
                'alamat_sekolah_asal' => $siswa->alamat_sekolah_asal,
                'no_sttb_smp' => $siswa->no_sttb_smp,
                'tgl_sttb_smp' => $siswa->tgl_sttb_smp ? date('d F Y', strtotime($siswa->tgl_sttb_smp)) : null,
                'tanggal_masuk' => $siswa->tanggal_masuk ? date('d F Y', strtotime($siswa->tanggal_masuk)) : null,
                'kelas_masuk' => $siswa->kelas_masuk,
                'tanggal_keluar' => $siswa->tanggal_keluar ? date('d F Y', strtotime($siswa->tanggal_keluar)) : null,
                'alasan_keluar' => $siswa->alasan_keluar,
                'no_sttb_smk' => $siswa->no_sttb_smk,
                'tgl_sttb_smk' => $siswa->tgl_sttb_smk ? date('d F Y', strtotime($siswa->tgl_sttb_smk)) : null,
                'tempat_pkl' => $siswa->tempat_pkl,
                'alamat_pkl' => $siswa->alamat_pkl,
                'tgl_mulai_pkl' => $siswa->tgl_mulai_pkl ? date('d F Y', strtotime($siswa->tgl_mulai_pkl)) : null,
                'tgl_selesai_pkl' => $siswa->tgl_selesai_pkl ? date('d F Y', strtotime($siswa->tgl_selesai_pkl)) : null,
                
                'persentase_lengkap' => $persentase,
                'is_biodata_locked' => $siswa->is_biodata_locked,
            ];
        });

        return response()->json([
            'success' => true,
            'kelas' => $kelas,
            'data' => $data,
        ]);
    }

    /**
     * Get Mapel Struktur for Buku Induk based on Kelas (Kurikulum & Kejuruan)
     */
    public function getMapelStruktur($kelas_id)
    {
        $kelas = \App\Models\Kelas::with(['kurikulum', 'kejuruan.program'])->find($kelas_id);
        if (!$kelas) {
            return response()->json(['success' => false, 'message' => 'Kelas tidak ditemukan'], 404);
        }

        $kurikulum_id = $kelas->kurikulum_id;
        $konsentrasi_id = $kelas->kejuruan_id; // using kejuruan_id as konsentrasi
        $program_id = $kelas->kejuruan ? $kelas->kejuruan->program_id : null;
        
        // Fetch all mapels for this kurikulum from StrukturKurikulum
        $strukturUmum = \App\Models\StrukturKurikulum::with(['mapel'])
            ->where('kurikulum_id', $kurikulum_id)
            ->get()
            ->sortBy(function($st) {
                return $st->mapel ? $st->mapel->kode_mapel : '';
            }, SORT_NATURAL);
            
        $strukturKejuruan = \App\Models\StrukturKejuruan::with(['mapel'])
            ->where('kurikulum_id', $kurikulum_id)
            ->where(function($q) use ($konsentrasi_id, $program_id) {
                if ($konsentrasi_id) $q->where('konsentrasi_id', $konsentrasi_id);
                if ($program_id) $q->orWhere('program_id', $program_id);
            })
            ->get()
            ->sortBy(function($st) {
                return $st->mapel ? $st->mapel->kode_mapel : '';
            }, SORT_NATURAL);

        // Grouping logic
        $groups = [
            'MATA PELAJARAN UMUM' => [],
            'MATA PELAJARAN KEJURUAN' => [],
            'MATA PELAJARAN PILIHAN' => [],
            'MUATAN LOKAL' => []
        ];

        // Get Mapping from Referensi, ordered by kode
        $referensiKelompok = \App\Models\Referensi::where('jenis', 'kelompok_mapel')->orderBy('kode')->pluck('nama', 'kode')->toArray();

        // Process umum
        $groups = [];
        
        // Force initialize groups in exact A, B, C, D order
        foreach ($referensiKelompok as $kode => $nama) {
            $groups[strtoupper($nama)] = [];
        }
        
        foreach ($strukturUmum as $st) {
            if (!$st->mapel) continue;
            $kelompokKode = $st->mapel->kelompok;
            
            if (!$kelompokKode) {
                $groupKey = 'LAINNYA';
            } elseif (isset($referensiKelompok[$kelompokKode])) {
                $groupKey = strtoupper($referensiKelompok[$kelompokKode]);
            } else {
                $groupKey = strtoupper($kelompokKode);
            }
            
            if (!isset($groups[$groupKey])) $groups[$groupKey] = [];
            
            $mapelName = $st->mapel->nama_mapel;
            if (!in_array($mapelName, $groups[$groupKey])) {
                $groups[$groupKey][] = $mapelName;
            }
        }

        // Process kejuruan
        $kejuruanGroupKey = isset($referensiKelompok['B']) ? strtoupper($referensiKelompok['B']) : 'MATA PELAJARAN KEJURUAN';
        if (!isset($groups[$kejuruanGroupKey])) $groups[$kejuruanGroupKey] = [];
        
        // Virtual Kolom Dasar-dasar Keahlian Kelas X (seperti di rapor)
        if ($kelas->kejuruan && $kelas->kejuruan->program) {
            $namaProgramX = $kelas->kejuruan->program->nama_program ?: 'Program Keahlian';
            $dummyMapelName = 'Dasar - Dasar ' . $namaProgramX;
            if (!in_array($dummyMapelName, $groups[$kejuruanGroupKey])) {
                $groups[$kejuruanGroupKey][] = $dummyMapelName;
            }
        }

        foreach ($strukturKejuruan as $st) {
            if (!$st->mapel) continue;
            
            // Kecuali Tingkat X karena sudah digabung jadi Dasar-Dasar
            if ($st->tingkat == 'X') continue;
            
            $mapelName = $st->mapel->nama_mapel;
            if (!in_array($mapelName, $groups[$kejuruanGroupKey])) {
                $groups[$kejuruanGroupKey][] = $mapelName;
            }
        }

        // Format for response
        $formattedGroups = [];
        
        foreach ($groups as $name => $mapels) {
            if (count($mapels) > 0) {
                $formattedGroups[] = [
                    'group_name' => $name,
                    'mapels' => $mapels
                ];
            }
        }

        return response()->json([
            'success' => true,
            'kelas' => $kelas,
            'data' => $formattedGroups
        ]);
    }

    public function getBiodataByKurikulum(Request $request)
    {
        $tahun_ajaran_id = $request->query('tahun_ajaran_id');
        $kurikulum_id = $request->query('kurikulum_id');

        if (!$tahun_ajaran_id || !$kurikulum_id) {
            return response()->json(['success' => false, 'message' => 'Tahun Ajaran dan Kurikulum harus diisi'], 400);
        }

        $siswas = \App\Models\Siswa::whereHas('kelas', function($q) use ($tahun_ajaran_id, $kurikulum_id) {
            $q->where('tahun_ajaran_id', $tahun_ajaran_id)
              ->where('kurikulum_id', $kurikulum_id)
              ->where('tingkat', 'XII');
        })
        ->with('user')
        ->get()
        ->sortBy(function($s) {
            return $s->name;
        })
        ->values();

        $fields = [
            'nis', 'nisn', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 
            'anak_ke', 'status_keluarga', 'warga_negara', 'agama', 'alamat', 'no_hp',
            'nama_ayah', 'ttl_ayah', 'pekerjaan_ayah', 'no_hp_ayah', 
            'nama_ibu', 'ttl_ibu', 'pekerjaan_ibu', 'no_hp_ibu', 
            'nama_wali', 'pekerjaan_wali', 'no_hp_wali', 'alamat_wali',
            'asal_sekolah', 'alamat_sekolah_asal', 'no_sttb_smp', 'tgl_sttb_smp',
            'tanggal_masuk', 'kelas_masuk', 'tanggal_keluar', 'alasan_keluar',
            'no_sttb_smk', 'tgl_sttb_smk', 'tempat_pkl', 'alamat_pkl', 'tgl_mulai_pkl', 'tgl_selesai_pkl'
        ];

        $siswaData = $siswas->map(function ($s) use ($fields) {
            $data = [
                'id' => $s->id,
                'kelas_id' => $s->kelas_id,
                'nama_lengkap' => $s->name,
            ];
            foreach ($fields as $field) {
                $data[$field] = $s->$field;
            }
            return $data;
        });

        return response()->json(['success' => true, 'data' => $siswaData]);
    }

    public function getNilaiSiswa($siswa_id)
    {
        $siswa = \App\Models\Siswa::with('kelas.kejuruan.program')->find($siswa_id);
        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Siswa tidak ditemukan'], 404);
        }

        $kurikulum_id = $siswa->kelas->kurikulum_id;
        $konsentrasi_id = $siswa->kelas->kejuruan_id;
        $program_id = $siswa->kelas->kejuruan ? $siswa->kelas->kejuruan->program_id : null;

        // Find Mapel IDs that are StrukturKejuruan Tingkat X
        $kejuruanXIds = \App\Models\StrukturKejuruan::where('kurikulum_id', $kurikulum_id)
            ->where('tingkat', 'X')
            ->where(function($q) use ($konsentrasi_id, $program_id) {
                if ($konsentrasi_id) $q->where('konsentrasi_id', $konsentrasi_id);
                if ($program_id) $q->orWhere('program_id', $program_id);
            })
            ->pluck('mapel_id')->toArray();

        $namaProgramX = $siswa->kelas->kejuruan && $siswa->kelas->kejuruan->program ? $siswa->kelas->kejuruan->program->nama_program : 'Program Keahlian';
        $dummyMapelName = 'Dasar - Dasar ' . $namaProgramX;

        $nilais = \App\Models\SumatifNilai::with(['titimangsa', 'kelas', 'mapel'])
            ->where('siswa_id', $siswa_id)
            ->whereHas('titimangsa', function ($q) {
                $q->whereIn('nama_periode', ['PSAS', 'PSAT']);
            })
            ->get();

        $nilaiMatriks = [];
        $kejuruanXAccumulator = [
            1 => ['total' => 0, 'count' => 0],
            2 => ['total' => 0, 'count' => 0]
        ];

        foreach ($nilais as $n) {
            if (!$n->kelas || !$n->titimangsa || !$n->mapel) continue;

            $tingkat = $n->kelas->tingkat; // X, XI, XII
            $periode = $n->titimangsa->nama_periode; // PSAS, PSAT
            
            $semesterIndex = 0;
            if ($tingkat == 'X' && $periode == 'PSAS') $semesterIndex = 1;
            if ($tingkat == 'X' && $periode == 'PSAT') $semesterIndex = 2;
            if ($tingkat == 'XI' && $periode == 'PSAS') $semesterIndex = 3;
            if ($tingkat == 'XI' && $periode == 'PSAT') $semesterIndex = 4;
            if ($tingkat == 'XII' && $periode == 'PSAS') $semesterIndex = 5;
            if ($tingkat == 'XII' && $periode == 'PSAT') $semesterIndex = 6;
            
            if ($semesterIndex > 0) {
                if ($tingkat == 'X' && in_array($n->mapel_id, $kejuruanXIds)) {
                    if (is_numeric($n->na_value)) {
                        $kejuruanXAccumulator[$semesterIndex]['total'] += floatval($n->na_value);
                        $kejuruanXAccumulator[$semesterIndex]['count']++;
                    }
                } else {
                    $mapelName = $n->mapel->nama_mapel;
                    if (!isset($nilaiMatriks[$mapelName])) {
                        $nilaiMatriks[$mapelName] = [1 => null, 2 => null, 3 => null, 4 => null, 5 => null, 6 => null];
                    }
                    $nilaiMatriks[$mapelName][$semesterIndex] = floatval($n->na_value);
                }
            }
        }

        // Process accumulator
        foreach ($kejuruanXAccumulator as $sem => $data) {
            if ($data['count'] > 0) {
                if (!isset($nilaiMatriks[$dummyMapelName])) {
                    $nilaiMatriks[$dummyMapelName] = [1 => null, 2 => null, 3 => null, 4 => null, 5 => null, 6 => null];
                }
                $nilaiMatriks[$dummyMapelName][$sem] = $data['total'] / $data['count'];
            }
        }

        // Format to 2 decimal places
        foreach ($nilaiMatriks as $mapel => $semesters) {
            foreach ($semesters as $sem => $val) {
                if ($val !== null) {
                    $nilaiMatriks[$mapel][$sem] = number_format($val, 2, ',', '');
                }
            }
        }

        return response()->json([
            'success' => true,
            'nilai' => $nilaiMatriks
        ]);
    }
}
