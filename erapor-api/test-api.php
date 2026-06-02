<?php
// Test API endpoint directly
$request = Request::create('/api/kurikulum/struktur-umum', 'GET');
// Mock auth user
$user = App\Models\User::first();
$request->setUserResolver(function() use ($user) { return $user; });
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request);
echo $response->getStatusCode() . "\n";
echo $response->getContent();
