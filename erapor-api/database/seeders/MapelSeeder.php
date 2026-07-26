<?php

namespace Database\Seeders;

use App\Models\Mapel;
use App\Models\Kurikulum;
use Illuminate\Database\Seeder;

class MapelSeeder extends Seeder
{
    public function run(): void
    {
        $kurikulum = Kurikulum::where('singkatan', 'KN')->first() ?? Kurikulum::first();

        if (!$kurikulum) return;

        $dataMapel = [
            // === A: MATA PELAJARAN UMUM ===
            ['kode' => 'A1', 'nama' => 'Pendidikan Agama dan Budi Pekerti',          'kelompok' => 'A'],
            ['kode' => 'A2', 'nama' => 'Pendidikan Pancasila',                        'kelompok' => 'A'],
            ['kode' => 'A3', 'nama' => 'Bahasa Indonesia',                            'kelompok' => 'A'],
            ['kode' => 'A4', 'nama' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan', 'kelompok' => 'A'],
            ['kode' => 'A5', 'nama' => 'Sejarah',                                     'kelompok' => 'A'],
            ['kode' => 'A6', 'nama' => 'Seni Budaya',                                 'kelompok' => 'A'],

            // === B: MATA PELAJARAN KEJURUAN ===
            ['kode' => 'B1', 'nama' => 'Matematika',                                             'kelompok' => 'B'],
            ['kode' => 'B2', 'nama' => 'Bahasa Inggris',                                         'kelompok' => 'B'],
            ['kode' => 'B3', 'nama' => 'Informatika',                                            'kelompok' => 'B'],
            ['kode' => 'B4', 'nama' => 'Projek Ilmu Pengetahuan Alam dan Sosial',                'kelompok' => 'B'],
            ['kode' => 'B7', 'nama' => 'Projek Kreatif dan Kewirausahaan XI',                    'kelompok' => 'B'],
            ['kode' => 'B8', 'nama' => 'Projek Kreatif dan Kewirausahaan XII',                   'kelompok' => 'B'],
            ['kode' => 'B9', 'nama' => 'Praktik Kerja Lapangan',                                 'kelompok' => 'B'],

            // === KEJURUAN-PRODUKTIF (kode khusus per jurusan, contoh TITL) ===
            // Kode kelompok menggunakan kode bidang/program keahlian
            // Input manual sesuai jurusan masing-masing sekolah
            ['kode' => '251.X.B5a',  'nama' => 'Dasar Listrik Elektronika',              'kelompok' => '251.X'],
            ['kode' => '251.XI.B6a', 'nama' => 'Teknik Dasar Pemograman Mikrocontroler', 'kelompok' => '251.XI'],
            ['kode' => '251.XI.B6b', 'nama' => 'Dasar Rangkaian Elektronika',            'kelompok' => '251.XI'],
            ['kode' => '251.XII.B6a','nama' => 'Pemograman dan Aplikasi Microcontroler', 'kelompok' => '251.XII'],

            // === C: MATA PELAJARAN PILIHAN ===
            ['kode' => 'C1', 'nama' => 'Koding dan Kecerdasan Artifisial', 'kelompok' => 'C'],
            ['kode' => 'C2', 'nama' => 'Bahasa Jepang',                    'kelompok' => 'C'],

            // === D: MUATAN LOKAL ===
            ['kode' => 'D1', 'nama' => 'Bahasa Sunda',   'kelompok' => 'D'],
            ['kode' => 'D2', 'nama' => 'Public Speaking', 'kelompok' => 'D'],
        ];

        foreach ($dataMapel as $m) {
            Mapel::updateOrCreate(
                ['kode_mapel' => $m['kode']],
                [
                    'nama_mapel'   => $m['nama'],
                    'kurikulum_id' => $kurikulum->id,
                    'kelompok'     => $m['kelompok'],
                ]
            );
        }
    }
}