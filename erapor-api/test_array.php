<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$t = App\Models\Titimangsa::first();
$t->tingkat = 'X';
echo json_encode($t->toArray());
