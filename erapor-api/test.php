<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$k = App\Models\Kelas::withCount('siswas')->where('nama_kelas', 'TAV-1')->where('tingkat', 'XII')->first();
echo json_encode($k);
