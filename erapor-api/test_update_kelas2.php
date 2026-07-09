<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$kelas = App\Models\Kelas::find(4);
if ($kelas) {
    $kelas->tingkat = 'XII';
    $kelas->save();
    echo "BERHASIL UPDATE KELAS ID 4 KE TINGKAT XII";
} else {
    echo "Kelas tidak ditemukan";
}
