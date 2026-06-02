<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReferensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['jenis' => 'kategori_mapel', 'kode' => 'umum', 'nama' => 'Mapel Umum'],
            ['jenis' => 'kategori_mapel', 'kode' => 'kejuruan', 'nama' => 'Mapel Kejuruan'],
            
            ['jenis' => 'kelompok_mapel', 'kode' => 'A', 'nama' => 'Mata Pelajaran Umum'],
            ['jenis' => 'kelompok_mapel', 'kode' => 'B', 'nama' => 'Mata Pelajaran Kejuruan'],
            ['jenis' => 'kelompok_mapel', 'kode' => 'C', 'nama' => 'Mata Pelajaran Pilihan'],
            ['jenis' => 'kelompok_mapel', 'kode' => 'D', 'nama' => 'Muatan Lokal'],

            ['jenis' => 'nama_periode', 'kode' => 'TM-1', 'nama' => 'PSTS Ganjil'],
            ['jenis' => 'nama_periode', 'kode' => 'TM-2', 'nama' => 'PSAS'],
            ['jenis' => 'nama_periode', 'kode' => 'TM-3', 'nama' => 'PSTS Genap'],
            ['jenis' => 'nama_periode', 'kode' => 'TM-4', 'nama' => 'PSAT'],
        ];

        foreach ($data as $item) {
            \App\Models\Referensi::updateOrCreate(
                ['jenis' => $item['jenis'], 'kode' => $item['kode']],
                ['nama' => $item['nama']]
            );
        }
    }
}
