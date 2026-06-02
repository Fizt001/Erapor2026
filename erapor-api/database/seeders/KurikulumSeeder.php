<?php

namespace Database\Seeders;

use App\Models\Kurikulum;
use Illuminate\Database\Seeder;

class KurikulumSeeder extends Seeder
{
    public function run(): void
    {
        Kurikulum::create(['nama_kurikulum' => 'Kurikulum Nasional', 'singkatan' => 'KN']);
    }
}