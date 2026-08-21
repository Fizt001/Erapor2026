<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/api/kurikulum/jadwal-mengajar', 'POST', [
    'hari' => 'Senin',
    'jadwals' => [
        [
            'jam_ke' => 1,
            'kelas_id' => \App\Models\Kelas::first()->id ?? 1,
            'mapel_id' => \App\Models\Mapel::first()->id ?? 1,
            'guru_id' => null,
        ]
    ]
]);

$controller = app()->make(\App\Http\Controllers\Api\Kurikulum\KurikulumJadwalMengajarController::class);
$response = $controller->saveJadwal($request);
echo $response->getContent();
