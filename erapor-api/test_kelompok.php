<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$kelompok = \App\Models\Mapel::select('kelompok')->distinct()->pluck('kelompok');
print_r($kelompok->toArray());
