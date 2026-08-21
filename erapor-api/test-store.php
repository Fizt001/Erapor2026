<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = new \App\Http\Controllers\Api\Kepsek\KepsekSupervisiController();
$request = new \Illuminate\Http\Request();
$request->merge([
    'guru_id' => 4,
    'tanggal' => '2026-08-19',
    'waktu' => '08:00',
    'keterangan' => 'Test supervisi'
]);

try {
    \Illuminate\Support\Facades\Auth::loginUsingId(1);
    $response = $controller->store($request);
    echo $response->getContent();
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
