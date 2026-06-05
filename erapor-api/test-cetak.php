<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $user = App\Models\User::find(15);
    Auth::login($user);
    $controller = new App\Http\Controllers\Api\Guru\WalasCetakController();
    $req = new Illuminate\Http\Request();
    $res = $controller->index($req);
    echo json_encode($res->getData(true));
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . " on line " . $e->getLine() . " in " . $e->getFile();
}
