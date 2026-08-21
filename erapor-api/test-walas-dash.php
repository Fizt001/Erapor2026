<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\Illuminate\Support\Facades\Auth::loginUsingId(4); // guru user

$controller = new \App\Http\Controllers\Api\Guru\WalasDashboardStatsController();
$response = $controller->getStats(new \Illuminate\Http\Request()); echo json_encode(array_keys($data), JSON_PRETTY_PRINT);
$data = json_decode($response->getContent(), true);

if (isset($data['data']['grafik_siswa'])) {
    $siswa = $data['data']['grafik_siswa'];
    echo "Total: " . count($siswa) . "\n";
    foreach (array_slice($siswa, 0, 3) as $s) {
        echo "id=" . $s['id'] . " nama=" . $s['nama'] . "\n";
    }
} else {
    echo "No grafik_siswa key\n";
    echo json_encode(array_keys($data['data'] ?? []), JSON_PRETTY_PRINT);
}
