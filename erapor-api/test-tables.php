<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo json_encode(array_map(function($t) { return array_values((array)$t)[0]; }, \DB::select('SHOW TABLES')));
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . " on line " . $e->getLine() . " in " . $e->getFile();
}
