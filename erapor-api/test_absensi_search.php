<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$siswas = App\Models\Siswa::with('user')->get();
foreach($siswas as $s) {
    $abs = App\Models\AbsensiSiswa::where('siswa_id', $s->id)->orderBy('bulan')->get();
    if($abs->isEmpty()) continue;

    $found = false;
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
        
        if($s_count == 2 && $i_count == 2 && $a_count == 0) {
            $found = true;
            echo "MATCH: Siswa ID {$s->id} ({$s->user->name}) has 2,2,0 in Bulan {$a->bulan}\n";
        }
        
        if($s_count == 1 && $i_count == 1 && $a_count == 0) {
            echo "MATCH 110: Siswa ID {$s->id} ({$s->user->name}) has 1,1,0 in Bulan {$a->bulan}\n";
        }
    }
}
