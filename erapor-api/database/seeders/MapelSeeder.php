<?php

namespace Database\Seeders;

use App\Models\Mapel;
use App\Models\Kurikulum;
use Illuminate\Database\Seeder;

class MapelSeeder extends Seeder
{
    public function run(): void
    {
        // Cari Kurikulum berdasarkan singkatan 'KN' sesuai database kamu
        $kurikulum = Kurikulum::where('singkatan', 'KN')->first() ?? Kurikulum::first();

        $dataMapel = [
            ['kode' => 'A1', 'nama' => 'Pendidikan Agama dan Budi Pekerti'],
            ['kode' => 'A2', 'nama' => 'Pendidikan Pancasila'],
            ['kode' => 'A3', 'nama' => 'Bahasa Indonesia'],
            ['kode' => 'A4', 'nama' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan'],
            ['kode' => 'A5', 'nama' => 'Sejarah'],
            ['kode' => 'A6', 'nama' => 'Seni Tari'],
            ['kode' => 'B1', 'nama' => 'Matematika'],
            ['kode' => 'B2', 'nama' => 'Bahasa Inggris'],
            ['kode' => 'B3', 'nama' => 'Informatika'],
            ['kode' => 'B4', 'nama' => 'Projek Ilmu Pengetahuan Alam dan Sosial'],
            ['kode' => 'B7', 'nama' => 'Projek Kreatif dan Kewirausahaan XI'],
            ['kode' => 'B8', 'nama' => 'Projek Kreatif dan Kewirausahaan XII'],
            ['kode' => 'B9', 'nama' => 'Praktik Kerja Lapangan'],
            ['kode' => 'C1', 'nama' => 'Koding Kecerdasan dan Artifisial'],
            ['kode' => 'C2', 'nama' => 'Bahasa Jepang'],
            ['kode' => 'D1', 'nama' => 'Bahasa Sunda'],
            ['kode' => 'D2', 'nama' => 'Public Speaking'],
        ];

        foreach ($dataMapel as $m) {
            $kelompok = substr($m['kode'], 0, 1);
            Mapel::updateOrCreate(
                ['kode_mapel' => $m['kode'], 'nama_mapel' => $m['nama']],
                [
                    'kurikulum_id' => $kurikulum->id,
                    'kategori' => 'umum',
                    'kelompok' => in_array($kelompok, ['A', 'B', 'C', 'D']) ? $kelompok : null
                ]
            );
        }
    }
}