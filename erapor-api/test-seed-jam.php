<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\JamPelajaran;

JamPelajaran::truncate();

$seninKamis = [
    1 => ['07:00:00', '07:45:00'],
    2 => ['07:45:00', '08:20:00'],
    3 => ['08:20:00', '08:55:00'],
    4 => ['08:55:00', '09:30:00'],
    5 => ['09:50:00', '10:35:00'],
    6 => ['10:35:00', '11:10:00'],
    7 => ['11:10:00', '11:45:00'],
    8 => ['11:45:00', '12:20:00'],
    9 => ['13:15:00', '13:50:00'],
    10 => ['13:50:00', '14:25:00'],
    11 => ['14:25:00', '15:00:00'],
    12 => ['15:00:00', '15:35:00'],
];

foreach ($seninKamis as $jp => $times) {
    JamPelajaran::create([
        'kategori_hari' => 'Senin-Kamis',
        'jam_ke' => $jp,
        'waktu_mulai' => $times[0],
        'waktu_selesai' => $times[1],
    ]);
}

$jumat = [
    1 => ['07:00:00', '07:40:00'],
    2 => ['07:40:00', '08:15:00'],
    3 => ['08:15:00', '08:50:00'],
    4 => ['08:50:00', '09:25:00'],
    5 => ['09:45:00', '10:20:00'],
    6 => ['10:20:00', '10:55:00'],
    7 => ['10:55:00', '11:30:00'],
    8 => ['13:00:00', '13:35:00'],
    9 => ['13:35:00', '14:10:00'],
    10 => ['14:10:00', '14:45:00'],
    11 => ['14:45:00', '15:20:00'],
    12 => ['15:20:00', '15:55:00'],
];

foreach ($jumat as $jp => $times) {
    JamPelajaran::create([
        'kategori_hari' => 'Jumat',
        'jam_ke' => $jp,
        'waktu_mulai' => $times[0],
        'waktu_selesai' => $times[1],
    ]);
}
echo "Seeded Jam Pelajaran successfully!\n";
