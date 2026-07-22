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

            // Tambahan Agama
            ['jenis' => 'Kategori Agama', 'kode' => 'islam', 'nama' => 'Islam'],
            ['jenis' => 'Kategori Agama', 'kode' => 'protestan', 'nama' => 'Kristen Protestan'],
            ['jenis' => 'Kategori Agama', 'kode' => 'katolik', 'nama' => 'Katolik'],
            ['jenis' => 'Kategori Agama', 'kode' => 'hindu', 'nama' => 'Hindu'],
            ['jenis' => 'Kategori Agama', 'kode' => 'buddha', 'nama' => 'Buddha'],
            ['jenis' => 'Kategori Agama', 'kode' => 'konghucu', 'nama' => 'Konghucu'],

            // Jenis Kelamin
            ['jenis' => 'Jenis Kelamin', 'kode' => 'L', 'nama' => 'Laki-laki'],
            ['jenis' => 'Jenis Kelamin', 'kode' => 'P', 'nama' => 'Perempuan'],

            // Kategori Pekerjaan
            ['jenis' => 'Kategori Pekerjaan', 'kode' => 'pns', 'nama' => 'PNS/TNI/Polri'],
            ['jenis' => 'Kategori Pekerjaan', 'kode' => 'swasta', 'nama' => 'Karyawan Swasta'],
            ['jenis' => 'Kategori Pekerjaan', 'kode' => 'wiraswasta', 'nama' => 'Wiraswasta / Wirausaha'],
            ['jenis' => 'Kategori Pekerjaan', 'kode' => 'petani', 'nama' => 'Petani / Peternak'],
            ['jenis' => 'Kategori Pekerjaan', 'kode' => 'nelayan', 'nama' => 'Nelayan'],
            ['jenis' => 'Kategori Pekerjaan', 'kode' => 'buruh', 'nama' => 'Buruh'],
            ['jenis' => 'Kategori Pekerjaan', 'kode' => 'pensiunan', 'nama' => 'Pensiunan'],
            ['jenis' => 'Kategori Pekerjaan', 'kode' => 'irt', 'nama' => 'Ibu Rumah Tangga'],
            ['jenis' => 'Kategori Pekerjaan', 'kode' => 'lainnya', 'nama' => 'Lainnya / Tidak Bekerja'],
        ];

        foreach ($data as $item) {
            \App\Models\Referensi::updateOrCreate(
                ['jenis' => $item['jenis'], 'kode' => $item['kode']],
                ['nama' => $item['nama']]
            );
        }
    }
}
