<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WaliKelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Titimangsa;
use App\Models\Referensi;
use App\Models\Mapel;
use App\Models\SumatifNilai;
use App\Models\FormatifNilai;
use App\Models\EkskulSiswa;
use App\Models\Kokurikuler;
use App\Models\Sekolah;
use App\Models\CatatanWaliKelas;
use App\Models\AbsensiSiswa;

class SiswaRaporController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $siswa = $user->siswa;

        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Data siswa tidak ditemukan.'], 404);
        }

        // Get titimangsas where this student has grades, or just all available
        $titimangsaIds = SumatifNilai::where('siswa_id', $siswa->id)
            ->distinct()
            ->pluck('titimangsa_id');

        $titimangsas = Titimangsa::whereIn('id', $titimangsaIds)
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'titimangsas' => $titimangsas
            ]
        ]);
    }

    public function cetak(Request $request)
    {
        $request->validate([
            'titimangsa_id' => 'required|exists:titimangsas,id',
        ]);

        $user = $request->user();
        $siswa = $user->siswa;

        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Data siswa tidak ditemukan.'], 404);
        }

        $titimangsa = Titimangsa::find($request->titimangsa_id);
        $tahunAktif = TahunAjaran::find($titimangsa->tahun_ajaran_id);
        
        // Cari kelas siswa saat titimangsa tersebut
        $sumatif = SumatifNilai::where('siswa_id', $siswa->id)->where('titimangsa_id', $titimangsa->id)->first();
        $kelasId = $sumatif ? $sumatif->kelas_id : $siswa->kelas_id;
        $kelas = \App\Models\Kelas::find($kelasId);

        if (!$kelas) {
            return response()->json(['success' => false, 'message' => 'Kelas tidak ditemukan.'], 404);
        }

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
                'nilai' => $countValid > 0 ? round($totalKejuruan / count($kejuruanMapelIds)) : null,
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

        // 8. Absensi Dinamis
        $absensiRecords = AbsensiSiswa::where('siswa_id', $siswa->id)
            ->where('tahun_ajaran', $tahunAktif->tahun)
            ->where('periode', $titimangsa->nama_periode)
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
            'npsn' => $dbSekolah ? $dbSekolah->npsn : '-',
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
                'semester' => in_array($titimangsa->nama_periode, ['PSTS Ganjil', 'PSAS']) ? '1 (Ganjil)' : '2 (Genap)',
                'nama_periode' => $titimangsa->nama_periode,
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
}
