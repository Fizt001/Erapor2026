<?php
use Illuminate\Support\Facades\Route;
use App\Models\PertemuanGuru;
use Illuminate\Http\Request;

Route::get('/debug-test-save', function (Request $request) {
    try {
        $controller = app()->make(\App\Http\Controllers\Api\Guru\GuruJadwalMengajarController::class);
        
        // Mock a request with the exact payload
        $mockRequest = Request::create('/api/guru/jadwal-simpan', 'POST', [
            'kelas_id' => 1, // Just a test
            'mapel_id' => 1,
            'tanggal' => date('Y-m-d'),
            'jam_ke_string' => '1',
            'waktu_mulai' => '07:00:00',
            'waktu_selesai' => '08:00:00',
            'materi' => 'Test',
            'absensi' => []
        ]);
        
        // Fake the auth user
        $user = \App\Models\User::where('email', 'drajat@erapor.com')->first();
        if (!$user) return response()->json(['error' => 'User Drajat not found']);
        $mockRequest->setUserResolver(function() use ($user) { return $user; });
        
        $response = $controller->simpanJurnalAbsensi($mockRequest);
        return $response;
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }
});
