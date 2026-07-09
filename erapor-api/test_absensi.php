<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$siswaIds = App\Models\Siswa::whereHas('user', function($q) {
    $q->where('name', 'like', '%Ivan%');
})->orWhere('nis', 'like', '%Ivan%')->pluck('id');

if($siswaIds->isEmpty()) {
    echo "Tidak ditemukan siswa bernama Ivan.\n";
    // Coba cari semua siswa aja
    $siswaIds = App\Models\Siswa::limit(5)->pluck('id');
    if($siswaIds->isEmpty()) {
         echo "DB Siswa kosong!\n";
         exit;
    }
}

foreach($siswaIds as $id) {
    $s = App\Models\Siswa::with('user')->find($id);
    echo "============================================\n";
    echo "Data Absensi untuk Siswa ID: {$s->id} - Nama: " . ($s->user->name ?? 'Tanpa Nama') . "\n";
    
    $abs = App\Models\AbsensiSiswa::where('siswa_id', $s->id)->orderBy('bulan')->get();
    if($abs->isEmpty()){
         echo "Tidak ada data absensi untuk siswa ini.\n";
    }
    
    foreach($abs as $a) {
        $s_count = 0;
        $i_count = 0;
        $a_count = 0;
        
        for($j=1; $j<=31; $j++) {
            $col = 'tgl_'.$j;
            if($a->$col == 'S') $s_count++;
            if($a->$col == 'I') $i_count++;
            if($a->$col == 'A') $a_count++;
        }
        
        echo "Bulan: {$a->bulan} | Tahun Ajaran: {$a->tahun_ajaran} | Periode DB: {$a->periode} | S: {$s_count}, I: {$i_count}, A: {$a_count}\n";
    }
}
