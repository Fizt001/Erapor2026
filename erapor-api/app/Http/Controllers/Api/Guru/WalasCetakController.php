<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use App\Models\AbsensiSiswa;
use App\Models\CatatanWaliKelas;
use App\Models\EkskulSiswa;
use App\Models\FormatifNilai;
use App\Models\Kelas;
use App\Models\Kokurikuler;
use App\Models\Kurikulum;
use App\Models\Mapel;
use App\Models\Siswa;
use App\Models\SumatifNilai;
use App\Models\TahunAjaran;
use App\Models\Titimangsa;
use App\Models\WaliKelas;
use App\Models\KelompokMapel;
use App\Models\Sekolah;
use App\Models\Referensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalasCetakController extends Controller
{
    private function getWalasContext()
    {
        $user = Auth::user();
        
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAktif) return null;

        $walas = WaliKelas::with(['kelas.kurikulum', 'kelas.kejuruan.program.bidang', 'guru.biodata'])
            ->where('guru_id', $user->id)
            ->whereHas('kelas', function ($query) use ($tahunAktif) {
                $query->where('tahun_ajaran_id', $tahunAktif->id);
            })
            ->first();
        if (!$walas) return null;

        $titimangsas = Titimangsa::where('tahun_ajaran_id', $tahunAktif->id)->orderBy('id')->get();
        if ($titimangsas->isEmpty()) return null;

        return [
            'walas' => $walas,
            'kelas' => $walas->kelas,
            'tahun_ajaran' => $tahunAktif,
            'titimangsas' => $titimangsas,
        ];
    }

    public function index(Request $request)
    {
        $context = $this->getWalasContext();
        if (!$context) {
            return response()->json(['success' => false, 'message' => 'Anda bukan wali kelas aktif.'], 403);
        }

        $siswas = Siswa::with('user')
            ->where('kelas_id', $context['kelas']->id)
            ->whereNull('tanggal_keluar')
            ->get()
            ->sortBy('user.name')
            ->values();
        $tahunAktif = $context['tahun_ajaran'];
        
        // Cari tahun ajaran berikutnya (misal: 2025/2026 -> 2026/2027)
        $parts = explode('/', $tahunAktif->tahun);
        $nextTahun = '';
        if (count($parts) == 2) {
            $nextTahun = (intval($parts[0]) + 1) . '/' . (intval($parts[1]) + 1);
        }
        $nextTahunAjaran = \App\Models\TahunAjaran::where('tahun', $nextTahun)->first();

        $tingkatSekarang = $context['kelas']->tingkat;
        $tingkatBerikutnya = '';
        if ($tingkatSekarang == 'X') $tingkatBerikutnya = 'XI';
        else if ($tingkatSekarang == 'XI') $tingkatBerikutnya = 'XII';

        $kelasTahunDepan = collect();
        if ($nextTahunAjaran) {
            $kelasTahunDepan = Kelas::with('tahunAjaran')
                ->where('tahun_ajaran_id', $nextTahunAjaran->id)
                ->whereIn('tingkat', array_filter([$tingkatSekarang, $tingkatBerikutnya]))
                ->get();
        }

        $all_kelas = $kelasTahunDepan->sortBy(['tingkat', 'nama_kelas'])->values();

        $isSemesterGenap = false;
        foreach ($context['titimangsas'] as $titimangsa) {
            $month = date('n', strtotime($titimangsa->tanggal_cetak ?? date('Y-m-d')));
            if ($month >= 1 && $month <= 6) {
                $isSemesterGenap = true;
                break;
            }
        }

        $hasPsatBackup = false;
        $backupDir = storage_path('app/backups');
        if (\Illuminate\Support\Facades\File::exists($backupDir)) {
            $files = \Illuminate\Support\Facades\File::files($backupDir);
            $currentYear = \Carbon\Carbon::now()->year;
            foreach ($files as $file) {
                if (str_contains(strtolower($file->getFilename()), '_psat_') && date('Y', $file->getMTime()) == $currentYear) {
                    $hasPsatBackup = true;
                    break;
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'kelas' => $context['kelas'],
                'walas' => $context['walas'],
                'tahun_ajaran' => $context['tahun_ajaran'],
                'titimangsas' => $context['titimangsas'],
                'siswas' => $siswas,
                'all_kelas' => $all_kelas,
                'is_semester_genap' => $isSemesterGenap,
                'has_psat_backup' => $hasPsatBackup
            ]
        ]);
    }

    public function raporSiswa(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'titimangsa_id' => 'required|exists:titimangsas,id',
        ]);

        $context = $this->getWalasContext();
        if (!$context) {
            return response()->json(['success' => false, 'message' => 'Anda bukan wali kelas aktif.'], 403);
        }

        $siswa = Siswa::with('user')->find($request->siswa_id);
        if ($siswa->kelas_id != $context['kelas']->id) {
            return response()->json(['success' => false, 'message' => 'Siswa tidak berada di kelas Anda.'], 403);
        }

        $titimangsa = Titimangsa::find($request->titimangsa_id);
        $tahunAktif = $context['tahun_ajaran'];
        $kelas = $context['kelas'];
        $kurikulumId = $kelas->kurikulum_id;
        $tingkat = $kelas->tingkat;

        // 1. Ambil Kelompok Mapel dari referensi
        $kelompokMapping = Referensi::where('jenis', 'kelompok_mapel')->pluck('nama', 'kode')->toArray();

        $dataTabel = [];
        foreach ($kelompokMapping as $key => $nama) {
            $dataTabel[$key] = [
                'nama_kelompok' => $nama,
                'items' => collect()
            ];
        }

        $keyKejuruan = 'B';

        // 2. Ambil Semua Mapel
        $allMapels = Mapel::with([
            'strukturKurikulums',
            'strukturKejuruans.program'
        ])
        ->where(function($query) use ($tingkat, $kurikulumId) {
            $query->whereHas('strukturKurikulums', fn($q) => $q->where('tingkat', $tingkat)->where('kurikulum_id', $kurikulumId))
                ->orWhereHas('strukturKejuruans', fn($q) => $q->where('tingkat', $tingkat)->where('kurikulum_id', $kurikulumId));
        })->get();

        $kejuruanMapelIds = [];
        $namaProgramX = '';

        foreach ($allMapels as $m) {
            $sk = $m->strukturKurikulums->first();
            $kategori = strtoupper($m->kelompok ?? 'A');
            
            if ($sk) {
                if (isset($dataTabel[$kategori])) {
                    $dataTabel[$kategori]['items']->push($m);
                    continue; 
                }
            }
            
            $skj = $m->strukturKejuruans->first();
            if ($skj) {
                if ($tingkat == 'X') {
                    $kejuruanMapelIds[] = $m->id;
                    if (empty($namaProgramX) && $skj->program) {
                        $namaProgramX = $skj->program->nama_program;
                    }
                } else {
                    $dataTabel[$keyKejuruan]['items']->push($m);
                }
            }
        }

        // Virtual Kolom Dasar-dasar Keahlian Kelas X
        if ($tingkat == 'X' && count($kejuruanMapelIds) > 0) {
            $namaProgramX = $namaProgramX ?: 'Program Keahlian';
            $dummyMapel = (object)[
                'id' => 'merged_kejuruan',
                'kode_mapel' => 'DK',
                'nama_mapel' => 'Dasar - Dasar ' . $namaProgramX
            ];
            $dataTabel[$keyKejuruan]['items']->push($dummyMapel);
        }

        // 3. Ambil Nilai Sumatif (Nilai Akhir)
        $rawSumatif = SumatifNilai::where('siswa_id', $siswa->id)
            ->where('titimangsa_id', $titimangsa->id)
            ->get()->keyBy('mapel_id');

        // 4. Ambil Nilai Formatif (Deskripsi TP Tertinggi/Terendah)
        $rawFormatif = FormatifNilai::with('tp')
            ->where('siswa_id', $siswa->id)
            ->where('titimangsa_id', $titimangsa->id)
            ->get()->groupBy('mapel_id');

        $nilaiAkhir = [];
        foreach ($allMapels as $m) {
            $sum = $rawSumatif->get($m->id);
            $formList = $rawFormatif->get($m->id) ?? collect();
            
            $tpTertinggi = $formList->sortByDesc('nilai')->first();
            $tpTerendah = $formList->sortBy('nilai')->first();

            $deskripsi = "";
            if ($tpTertinggi && $tpTertinggi->nilai >= 70) {
                $deskripsi .= "Peserta didik menunjukkan penguasaan yang baik dalam " . strtolower($tpTertinggi->tp->nama_tp ?? '') . ". ";
            }
            if ($tpTerendah && $tpTerendah->nilai < 70) {
                $deskripsi .= "Perlu bimbingan dalam " . strtolower($tpTerendah->tp->nama_tp ?? '') . ".";
            }
            if (empty($deskripsi) && $sum) {
                $deskripsi = "Peserta didik menguasai kompetensi mata pelajaran ini dengan baik.";
            }

            $nilaiAkhir[$m->id] = [
                'nilai' => $sum ? $sum->na_value : null,
                'deskripsi' => $deskripsi
            ];
        }

        // Proses merging kejuruan untuk Kelas X
        if ($tingkat == 'X' && count($kejuruanMapelIds) > 0) {
            $totalKejuruan = 0;
            $countValid = 0;
            $descKejuruan = "";
            foreach ($kejuruanMapelIds as $mId) {
                if (isset($nilaiAkhir[$mId]) && is_numeric($nilaiAkhir[$mId]['nilai'])) {
                    $totalKejuruan += $nilaiAkhir[$mId]['nilai'];
                    $countValid++;
                    if (!empty($nilaiAkhir[$mId]['deskripsi'])) {
                        $descKejuruan .= $nilaiAkhir[$mId]['deskripsi'] . " ";
                    }
                }
            }
            $nilaiAkhir['merged_kejuruan'] = [
                'nilai' => $countValid > 0 ? round($totalKejuruan / $countValid) : null,
                'deskripsi' => trim($descKejuruan) ?: "Menunjukkan pemahaman dasar program keahlian."
            ];
        }

        // Susun Hasil Rapor Mapel & Calculate Total
        $raporMapel = [];
        $totalSemuaMapel = 0;
        $countMapel = 0;

        foreach ($dataTabel as $key => $grup) {
            if ($grup['items']->isEmpty()) continue;
            
            $listMapel = [];
            foreach ($grup['items'] as $item) {
                $n = $nilaiAkhir[$item->id] ?? null;
                $val = $n ? $n['nilai'] : null;
                $listMapel[] = [
                    'id' => $item->id,
                    'nama_mapel' => $item->nama_mapel,
                    'nilai' => $val !== null ? $val : '-',
                    'deskripsi' => $n ? $n['deskripsi'] : '-'
                ];

                if (is_numeric($val)) {
                    $totalSemuaMapel += $val;
                    $countMapel++;
                }
            }
            $raporMapel[] = [
                'kelompok' => $grup['nama_kelompok'],
                'mapels' => $listMapel
            ];
        }

        $rataRata = $countMapel > 0 ? round($totalSemuaMapel / $countMapel, 2) : 0;

        // Hitung Peringkat
        // Ambil semua siswa di kelas ini
        $allSiswaIds = Siswa::where('kelas_id', $kelas->id)->pluck('id')->toArray();
        $allSumatif = SumatifNilai::whereIn('siswa_id', $allSiswaIds)
            ->where('titimangsa_id', $titimangsa->id)
            ->get();
        
        $totalsPerSiswa = [];
        foreach ($allSiswaIds as $sId) {
            $totalsPerSiswa[$sId] = 0;
        }
        foreach ($allSumatif as $sum) {
            $totalsPerSiswa[$sum->siswa_id] += $sum->na_value;
        }
        
        // Handle merged kejuruan for all students (simplified approach: skip or calculate if needed)
        // Since calculating dynamic kejuruan merge for all students here is heavy, 
        // we'll just sort by raw total of SumatifNilai which is a very close proxy for rank.
        arsort($totalsPerSiswa);
        $peringkat = array_search($siswa->id, array_keys($totalsPerSiswa)) + 1;
        $jumlahSiswa = count($allSiswaIds);

        // 5. Ekstrakurikuler
        $ekskulsData = EkskulSiswa::with('ekskul')
            ->where('siswa_id', $siswa->id)
            ->where('titimangsa_id', $titimangsa->id)
            ->get();
        
        $deskripsiTpl = \App\Models\DeskripsiTemplate::where('kurikulum_id', $kurikulumId)->first();
        $templateEkskul = $deskripsiTpl->teks_ekskul ?? 'Sangat baik dalam mengikuti kegiatan [NAMA_EKSKUL], dan memperoleh nilai [NILAI]';

        $ekskulWajib = [];
        $ekskulPilihan = [];

        foreach ($ekskulsData as $e) {
            $namaEks = $e->ekskul->nama_ekskul ?? 'Pramuka';
            // Sesuai template master data (menggunakan tag [NAMA_EKSKUL] dan [NILAI])
            $deskripsi = str_replace(
                ['[NAMA_EKSKUL]', '[NILAI]'], 
                [$namaEks, $e->nilai], 
                $templateEkskul
            );
            
            $item = [
                'nama' => $namaEks,
                'deskripsi' => $deskripsi
            ];

            if (stripos($namaEks, 'pramuka') !== false) {
                $ekskulWajib[] = $item;
            } else {
                $ekskulPilihan[] = $item;
            }
        }
        $ekskuls = [
            'wajib' => $ekskulWajib,
            'pilihan' => $ekskulPilihan
        ];

        // 6. Kokurikuler
        $p5 = Kokurikuler::where('siswa_id', $siswa->id)
            ->where('titimangsa_id', $titimangsa->id)
            ->first();

        // 7. Catatan Wali Kelas
        $catatan = CatatanWaliKelas::where('siswa_id', $siswa->id)
            ->where('titimangsa_id', $titimangsa->id)
            ->first();

        // 8. Absensi Dinamis (Berdasarkan Titimangsa tanggal cetak)
        $bulanCetak = date('n', strtotime($titimangsa->tanggal_cetak));
        $months = [];
        if ($bulanCetak >= 7 && $bulanCetak <= 9) {
            $months = [7, 8, 9]; // ASTS Ganjil
        } elseif ($bulanCetak >= 10 && $bulanCetak <= 12) {
            $months = [10, 11, 12]; // ASAS
        } elseif ($bulanCetak >= 1 && $bulanCetak <= 3) {
            $months = [1, 2, 3]; // ASTS Genap
        } else {
            $months = [4, 5, 6]; // ASAT
        }

        $absensiRecords = AbsensiSiswa::where('siswa_id', $siswa->id)
            ->where('tahun_ajaran', $tahunAktif->tahun)
            ->whereIn('bulan', $months)
            ->get();
        
        $totalS = 0; $totalI = 0; $totalA = 0;
        foreach ($absensiRecords as $ab) {
            for ($i = 1; $i <= 31; $i++) {
                $col = 'tgl_' . $i;
                if ($ab->$col === 'S') $totalS++;
                if ($ab->$col === 'I') $totalI++;
                if ($ab->$col === 'A') $totalA++;
            }
        }
        $absensi = ['s' => $totalS, 'i' => $totalI, 'a' => $totalA];

        // 9. Poin Pelanggaran & Penghargaan
        $poinRecords = \App\Models\PoinSiswa::where('siswa_id', $siswa->id)
            ->where('titimangsa_id', $titimangsa->id)
            ->get();
        $totalPoin = 100; // Base poin default
        foreach ($poinRecords as $pr) {
            $totalPoin -= $pr->skor_pengurang ?? 0;
            $totalPoin += $pr->skor_penambah ?? 0;
        }

        // Setup Sekolah (Ambil dari DB)
        $dbSekolah = Sekolah::first();
        
        $alamatLine1 = $dbSekolah ? trim($dbSekolah->alamat . ' ' . $dbSekolah->kelurahan) : '-';
        $alamatLine2 = ($titimangsa->tempat_cetak ?? 'Bekasi') . ' - ' . ($dbSekolah->provinsi ?? 'Jawa Barat');
        
        $sekolah = [
            'nama' => $dbSekolah ? $dbSekolah->nama_sekolah : 'SMK Tinta Emas Indonesia',
            'nama_yayasan' => $dbSekolah ? $dbSekolah->nama_yayasan : '-',
            'akreditasi' => $dbSekolah ? $dbSekolah->akreditasi : '-',
            'npsn' => $dbSekolah ? $dbSekolah->npsn : '-',
            'alamat' => $dbSekolah ? $dbSekolah->alamat : '-',
            'kelurahan' => $dbSekolah ? $dbSekolah->kelurahan : '-',
            'kecamatan' => $dbSekolah ? $dbSekolah->kecamatan : '-',
            'kota' => $dbSekolah ? $dbSekolah->kota : '-',
            'provinsi' => $dbSekolah ? $dbSekolah->provinsi : '-',
            'kode_pos' => $dbSekolah ? $dbSekolah->kode_pos : '-',
            'telepon' => $dbSekolah ? $dbSekolah->telepon : '-',
            'website' => $dbSekolah ? $dbSekolah->website : '-',
            'email' => $dbSekolah ? $dbSekolah->email : '-',
            'logo' => $dbSekolah && $dbSekolah->logo ? url($dbSekolah->logo) : null,
            'logo_kiri' => $dbSekolah && $dbSekolah->logo_kiri ? url($dbSekolah->logo_kiri) : null,
            'alamat_line1' => $alamatLine1,
            'alamat_line2' => $alamatLine2,
            'kepsek' => $dbSekolah ? $dbSekolah->nama_kepsek : '-',
            'nip_kepsek' => $dbSekolah ? $dbSekolah->nip_kepsek : '-'
        ];

        // Format Kelas: tambahkan tingkat jika belum ada
        $namaKelasFull = $kelas->nama_kelas;
        if (!str_starts_with($namaKelasFull, $tingkat)) {
            $namaKelasFull = $tingkat . ' ' . $namaKelasFull;
        }

        // Extract Keahlian Data
        $bidangKeahlian = '-';
        $programKeahlian = '-';
        $konsentrasiKeahlian = '-';
        
        if ($kelas->kejuruan) {
            $konsentrasiKeahlian = $kelas->kejuruan->nama_konsentrasi ?? '-';
            if ($kelas->kejuruan->program) {
                $programKeahlian = $kelas->kejuruan->program->nama_program ?? '-';
                if ($kelas->kejuruan->program->bidang) {
                    $bidangKeahlian = $kelas->kejuruan->program->bidang->nama_bidang ?? '-';
                }
            }
        }

        // Fetch Kokurikuler
        $kokurikuler = \App\Models\Kokurikuler::where('siswa_id', $siswa->id)
            ->where('titimangsa_id', $titimangsa->id)
            ->first();

        $refPeriode = \App\Models\Referensi::where('jenis', 'nama_periode')->where('nama', $titimangsa->nama_periode)->first();
        $namaPeriodePanjang = $refPeriode && !empty($refPeriode->keterangan) ? $refPeriode->keterangan : $titimangsa->nama_periode;

        $nm = strtolower($titimangsa->nama_periode);
        $isTengahSemester = str_contains($nm, 'sts') || str_contains($nm, 'pts') || str_contains($nm, 'tengah');
        
        $month = date('n', strtotime($titimangsa->tanggal_cetak ?? date('Y-m-d')));
        $isSemesterGenap = ($month >= 1 && $month <= 6);
        $semesterStr = $isSemesterGenap ? '2 (Genap)' : '1 (Ganjil)';

        return response()->json([
            'success' => true,
            'data' => [
                'sekolah' => $sekolah,
                'siswa' => [
                    'nama' => strtoupper($siswa->user->name),
                    'nis' => $siswa->nis,
                    'nisn' => $siswa->nisn,
                    'fase' => $tingkat == 'X' ? 'E' : 'F',
                ],
                'kelas' => $tingkat,
                'kelas_full' => $namaKelasFull,
                'keahlian' => [
                    'bidang' => $bidangKeahlian,
                    'program' => $programKeahlian,
                    'konsentrasi' => $konsentrasiKeahlian,
                    'kode_konsentrasi' => $kelas->kejuruan->kode_konsentrasi ?? ''
                ],
                'semester' => $semesterStr,
                'is_semester_genap' => $isSemesterGenap,
                'is_tengah_semester' => $isTengahSemester,
                'nama_periode' => $titimangsa->nama_periode,
                'nama_periode_panjang' => $namaPeriodePanjang,
                'tahun_ajaran' => str_replace('/', ' / ', $tahunAktif->tahun), // "2025/2026" to "2025 / 2026"
                'rapor_mapel' => $raporMapel,
                'statistik' => [
                    'jumlah' => number_format($totalSemuaMapel, 2, ',', '.'),
                    'rata_rata' => number_format($rataRata, 2, ',', '.'),
                    'peringkat' => "{$peringkat} dari {$jumlahSiswa} peserta didik"
                ],
                'ekskuls' => $ekskuls,
                'kokurikuler' => $kokurikuler ? $kokurikuler->keterangan : '',
                'p5' => $p5 ? $p5->keterangan : '',
                'catatan' => $catatan ? $catatan->catatan : '',
                'absensi' => $absensi,
                'poin' => $totalPoin,
                'tanggal_cetak' => $titimangsa->tanggal_cetak ? date('d F Y', strtotime($titimangsa->tanggal_cetak)) : date('d F Y')
            ]
        ]);
    }

    public function legerKelas(Request $request)
    {
        $request->validate([
            'titimangsa_id' => 'required|exists:titimangsas,id',
        ]);

        $context = $this->getWalasContext();
        if (!$context) {
            return response()->json(['success' => false, 'message' => 'Anda bukan wali kelas aktif.'], 403);
        }

        $titimangsa = Titimangsa::find($request->titimangsa_id);
        $kelas = $context['kelas'];
        $kurikulumId = $kelas->kurikulum_id;
        $tingkat = $kelas->tingkat;

        $siswas = Siswa::with('user')
            ->where('kelas_id', $kelas->id)
            ->whereNull('tanggal_keluar')
            ->get()
            ->sortBy('user.name')
            ->values();

        // 1. Ambil Kelompok Mapel dari referensi
        $kelompokMapping = Referensi::where('jenis', 'kelompok_mapel')->pluck('nama', 'kode')->toArray();

        $dataTabel = [];
        foreach ($kelompokMapping as $key => $nama) {
            $dataTabel[$key] = [
                'nama_kelompok' => $nama,
                'items' => collect()
            ];
        }

        $keyKejuruan = 'B';

        // 2. Ambil Semua Mapel
        $allMapels = Mapel::with([
            'strukturKurikulums',
            'strukturKejuruans.program'
        ])
        ->where(function($query) use ($tingkat, $kurikulumId) {
            $query->whereHas('strukturKurikulums', fn($q) => $q->where('tingkat', $tingkat)->where('kurikulum_id', $kurikulumId))
                ->orWhereHas('strukturKejuruans', fn($q) => $q->where('tingkat', $tingkat)->where('kurikulum_id', $kurikulumId));
        })->get();

        $kejuruanMapelIds = [];
        $namaProgramX = '';
        $totalMapelDiTabel = 0;

        foreach ($allMapels as $m) {
            $sk = $m->strukturKurikulums->first();
            $kategori = strtoupper($m->kelompok ?? 'A');
            
            if ($sk) {
                if (isset($dataTabel[$kategori])) {
                    $dataTabel[$kategori]['items']->push($m);
                    $totalMapelDiTabel++;
                    continue; 
                }
            }
            
            $skj = $m->strukturKejuruans->first();
            if ($skj) {
                if ($tingkat == 'X') {
                    $kejuruanMapelIds[] = $m->id;
                    if (empty($namaProgramX) && $skj->program) {
                        $namaProgramX = $skj->program->nama_program;
                    }
                } else {
                    $dataTabel[$keyKejuruan]['items']->push($m);
                    $totalMapelDiTabel++;
                }
            }
        }

        // Virtual Kolom Dasar-dasar Keahlian Kelas X
        if ($tingkat == 'X' && count($kejuruanMapelIds) > 0) {
            $namaProgramX = $namaProgramX ?: 'Program Keahlian';
            $dummyMapel = (object)[
                'id' => 'merged_kejuruan',
                'kode_mapel' => 'DK',
                'nama_mapel' => 'Dasar - Dasar ' . $namaProgramX
            ];
            $dataTabel[$keyKejuruan]['items']->push($dummyMapel);
            $totalMapelDiTabel++;
        }

        // 3. Ambil Nilai Sumatif untuk SELURUH SISWA
        $rawSumatif = SumatifNilai::whereIn('siswa_id', $siswas->pluck('id'))
            ->where('titimangsa_id', $titimangsa->id)
            ->get();
            
        $nilaiMatriks = [];
        foreach ($rawSumatif as $n) {
            $nilaiMatriks[$n->siswa_id][$n->mapel_id] = $n->na_value;
        }

        // 4. Kalkulasi Rata-rata Kejuruan Kelas X
        if ($tingkat == 'X' && count($kejuruanMapelIds) > 0) {
            $jumlahMapelProduktif = count($kejuruanMapelIds);
            foreach ($siswas as $s) {
                $totalKejuruan = 0;
                $countValid = 0;
                foreach ($kejuruanMapelIds as $mId) {
                    $val = $nilaiMatriks[$s->id][$mId] ?? null;
                    if (is_numeric($val)) {
                        $totalKejuruan += $val;
                        $countValid++;
                    }
                }
                
                $nilaiMatriks[$s->id]['merged_kejuruan'] = $countValid > 0 
                    ? round($totalKejuruan / $countValid, 2) 
                    : null;
            }
        }

        // 5. Kalkulasi Jumlah, Rata, & Ranking
        $rekapSiswa = [];
        foreach ($siswas as $s) {
            $totalNilai = 0;
            foreach ($dataTabel as $grup) {
                foreach ($grup['items'] as $item) {
                    $val = $nilaiMatriks[$s->id][$item->id] ?? 0;
                    if (is_numeric($val)) {
                        $totalNilai += floatval($val);
                    }
                }
            }
            
            $rataRata = $totalMapelDiTabel > 0 ? round($totalNilai / $totalMapelDiTabel, 2) : 0;
            
            $rekapSiswa[$s->id] = [
                'jumlah' => $totalNilai,
                'rata' => $rataRata,
                'rank' => 0
            ];
        }

        $rankedIds = collect($rekapSiswa)->sortByDesc('jumlah')->keys()->toArray();
        $rank = 1;
        foreach ($rankedIds as $id) {
            $rekapSiswa[$id]['rank'] = $rank++;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'kelas' => $kelas,
                'titimangsa' => $titimangsa,
                'siswas' => $siswas,
                'dataTabel' => $dataTabel,
                'nilaiMatriks' => $nilaiMatriks,
                'rekapSiswa' => $rekapSiswa
            ]
        ]);
    }

    public function naikKelas(Request $request)
    {
        $context = $this->getWalasContext();
        if (!$context) {
            return response()->json(['success' => false, 'message' => 'Anda bukan wali kelas aktif.'], 403);
        }

        $request->validate([
            'siswa_id' => 'required|exists:siswa,id'
        ]);

        if ($request->kelas_id_tujuan !== 'LULUS') {
            $request->validate([
                'kelas_id_tujuan' => 'required|exists:kelas,id'
            ]);
        }

        // Verifikasi bahwa siswa ada di kelas walas saat ini
        $siswa = Siswa::where('id', $request->siswa_id)
            ->where('kelas_id', $context['kelas']->id)
            ->first();

        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Siswa tidak ditemukan atau bukan bagian dari kelas Anda.'], 404);
        }

        if ($request->kelas_id_tujuan === 'LULUS') {
            // Tandai siswa lulus
            $siswa->tanggal_keluar = now();
            $siswa->alasan_keluar = 'Lulus';
            $siswa->save();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil meluluskan ' . $siswa->user->name . '.',
            ]);
        }

        // Pindahkan siswa ke kelas tujuan
        $siswa->kelas_id = $request->kelas_id_tujuan;
        $siswa->save();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil memindahkan ' . $siswa->user->name . ' ke kelas baru.',
        ]);
    }
}
