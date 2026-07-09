<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Simulate request for siswa 8, titimangsa 5
$siswa = \App\Models\Siswa::find(8);
$titimangsa = \App\Models\Titimangsa::find(5);

$ekskulsData = \App\Models\EkskulSiswa::with('ekskul')
    ->where('siswa_id', $siswa->id)
    ->where('titimangsa_id', $titimangsa->id)
    ->get();

echo "Ekskul data from DB:\n";
echo $ekskulsData->toJson(JSON_PRETTY_PRINT) . "\n";

$kurikulumId = 1; // Assuming
$deskripsiTpl = \App\Models\DeskripsiTemplate::where('kurikulum_id', $kurikulumId)->first();
$templateEkskul = $deskripsiTpl->teks_ekskul ?? 'Sangat baik dalam mengikuti kegiatan [NAMA_EKSKUL], dan memperoleh nilai [NILAI]';

$ekskulWajib = [];
$ekskulPilihan = [];

foreach ($ekskulsData as $e) {
    $namaEks = $e->ekskul->nama_ekskul ?? 'Pramuka';
    
    // Check if $e->nilai is valid
    if (is_null($e->nilai) || trim($e->nilai) === '') {
        echo "Nilai is null/empty for $namaEks, skipping?\n";
        // Wait, the controller DOES NOT SKIP!
    }

    $deskripsi = str_replace(
        ['[NAMA_EKSKUL]', '[NILAI]'], 
        [$namaEks, $e->nilai], 
        $templateEkskul
    );
    
    $item = [
        'nama' => $namaEks,
        'deskripsi' => $deskripsi
    ];

    if (stripos($namaEks, 'pramuka') !== false) {
        $ekskulWajib[] = $item;
    } else {
        $ekskulPilihan[] = $item;
    }
}

$ekskuls = [
    'wajib' => $ekskulWajib,
    'pilihan' => $ekskulPilihan
];

echo "Final Ekskuls array:\n";
echo json_encode($ekskuls, JSON_PRETTY_PRINT) . "\n";
