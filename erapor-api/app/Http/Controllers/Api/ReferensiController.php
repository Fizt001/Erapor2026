<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReferensiController extends Controller
{
    /**
     * Get referensi list for all users (Read-only)
     */
    public function index(Request $request)
    {
        $query = \App\Models\Referensi::query();
        
        if ($request->has('jenis')) {
            $query->where('jenis', strtoupper($request->jenis));
        }
        
        return response()->json([
            'success' => true,
            'data' => $query->orderBy('kode')->get()
        ]);
    }
}
