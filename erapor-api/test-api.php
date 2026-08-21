<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\Illuminate\Support\Facades\Auth::loginUsingId(4); // Login as guru/kepsek? Actually we can just instantiate the controller.

$controller = new \App\Http\Controllers\Api\Kepsek\KepsekDashboardController();
$response = $controller->index();
$data = json_decode($response->getContent(), true);

echo "Success: " . ($data['success'] ? 'true' : 'false') . "\n";
echo "Total Guru: " . $data['data']['totalGuru'] . "\n";
echo "Total Kelas: " . $data['data']['totalKelas'] . "\n";
echo "Total Siswa: " . $data['data']['totalSiswa'] . "\n";
echo "Tingkat: " . implode(', ', array_keys($data['data']['kelasPerTingkat'])) . "\n";
