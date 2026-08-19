<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupervisiGuru;
use Illuminate\Support\Facades\Auth;

class GuruSupervisiController extends Controller
{
    public function index()
    {
        $supervisi = SupervisiGuru::with('kepsek')
            ->where('guru_id', Auth::id())
            ->orderBy('tanggal', 'desc')
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $supervisi
        ]);
    }
}
