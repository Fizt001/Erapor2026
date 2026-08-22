<?php
// Script to force clear all Laravel caches and PHP OPCache via Web Request

// 1. Clear OPCache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPCache has been reset.<br>";
} else {
    echo "OPCache is not enabled or opcache_reset function does not exist.<br>";
}

// 2. Clear Laravel Caches
try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    Illuminate\Support\Facades\Artisan::call('optimize:clear');
    
    echo "Laravel Optimize:Clear executed successfully.<br>";
    echo "<pre>" . Illuminate\Support\Facades\Artisan::output() . "</pre>";
    
} catch (\Exception $e) {
    echo "Error clearing Laravel cache: " . $e->getMessage() . "<br>";
}

echo "<br><b>All caches cleared successfully! Please refresh your main website.</b>";
