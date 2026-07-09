<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$t = App\Models\Titimangsa::orderBy('tanggal_cetak')->get();
foreach($t as $x) {
    echo $x->nama_periode . ' - ' . $x->tanggal_cetak . "\n";
}
