<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = app()->make(\App\Http\Controllers\Api\PublicController::class);
$response = $controller->stats();
echo json_encode($response->getData(true), JSON_PRETTY_PRINT);
