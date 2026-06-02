<?php

namespace Database\Seeders;

use App\Models\Titimangsa;
use App\Models\TahunAjaran;
use App\Models\Kurikulum;
use Illuminate\Database\Seeder;

class TitimangsaSeeder extends Seeder
{
    public function run(): void
    {
        // Cari Tahun Ajaran
        $ta = TahunAjaran::where('tahun', '2025/2026')->first();
        
        // CARI KN (Bukan KM) agar sinkron dengan KurikulumSeeder
        $kn = Kurikulum::where('singkatan', 'KN')->first();
        
        // Tambahkan pengecekan sederhana biar nggak error saat migrate:fresh --seed
        if ($ta && $kn) {
            Titimangsa::create([
                'tahun_ajaran_id' => $ta->id,
                'kurikulum_id' => $kn->id,
                'nama_periode' => 'PSTS Genap',
                'target_tingkat' => 'X,XI',
                'tanggal_cetak' => '2026-03-15',
                'is_aktif' => true,
            ]);
        
            Titimangsa::create([
                'tahun_ajaran_id' => $ta->id,
                'kurikulum_id' => $kn->id,
                'nama_periode' => 'Rapor PKL',
                'target_tingkat' => 'XII',
                'tanggal_cetak' => '2026-03-15',
                'is_aktif' => true, 
            ]);
        }
    }
}