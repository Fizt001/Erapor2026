<?php

namespace Database\Seeders;

use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    public function run(): void
    {
        // Hanya kolom 'tahun' dan 'is_aktif' sesuai migration baru
        TahunAjaran::create([
            'tahun' => '2023/2024',
            'is_aktif' => true,
        ]);
    }
}