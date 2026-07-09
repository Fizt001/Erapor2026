<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$u = \App\Models\User::where('email', 'admin@erapor.com')->first();
if (!$u) {
    echo "USER NOT FOUND\n";
    exit;
}
echo "USER FOUND\n";
echo "Role: " . $u->role . "\n";
echo "Password hash: " . $u->password . "\n";
$check = \Illuminate\Support\Facades\Hash::check('admin123', $u->password);
echo "Password match 'admin123': " . ($check ? "YES" : "NO") . "\n";
