<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$strukturs = \App\Models\StrukturKejuruan::with(['mapel', 'program', 'konsentrasi'])->get();
foreach ($strukturs as $s) {
    echo "Mapel: " . $s->mapel->nama_mapel . "\n";
    echo "  program_id: " . $s->program_id . "\n";
    echo "  konsentrasi_id: " . $s->konsentrasi_id . "\n";
}

echo "\n--- KELAS ---\n";
$kelases = \App\Models\Kelas::with('kejuruan.program')->get();
foreach ($kelases as $k) {
    echo "Kelas: " . $k->nama_kelas . "\n";
    echo "  kejuruan_id: " . $k->kejuruan_id . "\n";
    if ($k->kejuruan) {
        echo "  kejuruan.program_id: " . $k->kejuruan->program_id . "\n";
    } else {
        echo "  NO KEJURUAN\n";
    }
}
