<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$guruId = \App\Models\User::where('role', 'guru')->first()->id;
$taAktif = \App\Models\TahunAjaran::where('is_aktif', true)->first();

$jadwals = \App\Models\JadwalPelajaran::with(['kelas', 'mapel'])
    ->where('guru_id', $guruId)
    ->whereHas('kelas', function($q) use ($taAktif) {
        if ($taAktif) {
            $q->where('tahun_ajaran_id', $taAktif->id);
        }
    })
    ->orderBy('jam_ke')
    ->get();

echo "MINGGUAN COUNT: " . $jadwals->count() . "\n";
foreach ($jadwals as $j) {
    echo "ID: {$j->id}, Hari: '{$j->hari}', Guru: {$j->guru_id}\n";
}
