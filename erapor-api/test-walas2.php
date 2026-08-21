<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\Illuminate\Support\Facades\Auth::loginUsingId(4); // guru user

$controller = new \App\Http\Controllers\Api\Guru\WalasDashboardStatsController();
$response = $controller->getStats(new \Illuminate\Http\Request());
$data = json_decode($response->getContent(), true);

echo "success: " . ($data['success'] ? 'true' : 'false') . "\n";
if (!$data['success']) {
    echo "message: " . ($data['message'] ?? 'none') . "\n";
} else {
    $keys = array_keys($data['data'] ?? []);
    echo "keys: " . implode(', ', $keys) . "\n";
    
    if (isset($data['data']['grafik_siswa'])) {
        $siswa = $data['data']['grafik_siswa'];
        echo "grafik_siswa count: " . count($siswa) . "\n";
        foreach (array_slice($siswa, 0, 3) as $s) {
            echo "  id=" . $s['id'] . " nama=" . $s['nama'] . "\n";
        }
    }
}
