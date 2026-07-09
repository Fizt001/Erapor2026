<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$ta = App\Models\TahunAjaran::where('is_aktif', 1)->first();
echo "Active TA ID: " . ($ta ? $ta->id : 'none') . "\n";

$titimangsas = App\Models\Titimangsa::where('is_aktif', 1)->get();
echo "Active Titimangsas:\n";
foreach($titimangsas as $t) {
    echo "- ID: {$t->id}, TA: {$t->tahun_ajaran_id}, Nama: {$t->nama_periode}\n";
}
