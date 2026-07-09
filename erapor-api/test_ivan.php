<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ivan = \App\Models\Siswa::with('user')->get()->filter(function($s) {
    return stripos($s->user->name ?? '', 'ivan') !== false;
})->first();

if (!$ivan) {
    echo "Ivan not found\n";
    exit;
}

echo "Ivan's Siswa ID: " . $ivan->id . "\n";
echo "Ivan's Kelas ID: " . $ivan->kelas_id . "\n";

$ekskul = \App\Models\EkskulSiswa::with('ekskul')->where('siswa_id', $ivan->id)->get();
echo "Ekskul Data:\n";
echo $ekskul->toJson(JSON_PRETTY_PRINT);
