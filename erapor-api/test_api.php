<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$c = app()->make('App\Http\Controllers\Api\AdminKenaikanKelasController');
try {
    $res = $c->getSiswa(10);
    echo json_encode($res->original);
} catch (\Exception $e) {
    echo $e->getMessage() . "\n" . $e->getTraceAsString();
}
