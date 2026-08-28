<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$res = \DB::select("SELECT id, kelas_id, tanggal, jam_mulai FROM pertemuan_guru WHERE tanggal = '2026-08-20'");
foreach($res as $r) {
    echo "ID: {$r->id} Tanggal: {$r->tanggal} Jam: {$r->jam_mulai}\n";
    $abs = \DB::select("SELECT status, count(*) as c FROM absensi_pertemuan WHERE pertemuan_id = {$r->id} GROUP BY status");
    foreach($abs as $a) {
        echo "  - {$a->status}: {$a->c}\n";
    }
}
