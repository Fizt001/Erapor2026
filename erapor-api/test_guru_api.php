<?php
try {
    $request = new Illuminate\Http\Request();
    $user = App\Models\User::where('email', 'drajat@erapor.com')->first();
    auth()->login($user);
    $controller = new App\Http\Controllers\Api\Guru\FormatifMasterController();
    $response = $controller->index($request);
    echo json_encode($response->getData());
} catch (\Exception $e) {
    echo $e->getMessage();
}
