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
            $jenisVal = $request->jenis;
            $jenisWithSpace = str_replace('_', ' ', $jenisVal);
            $jenisWithUnderscore = str_replace(' ', '_', $jenisVal);
            
            $query->where(function ($q) use ($jenisVal, $jenisWithSpace, $jenisWithUnderscore) {
                $q->where('jenis', $jenisVal)
                  ->orWhere('jenis', $jenisWithSpace)
                  ->orWhere('jenis', $jenisWithUnderscore)
                  ->orWhere('jenis', strtoupper($jenisVal))
                  ->orWhere('jenis', strtoupper($jenisWithSpace))
                  ->orWhere('jenis', strtoupper($jenisWithUnderscore))
                  ->orWhere('jenis', strtolower($jenisVal))
                  ->orWhere('jenis', strtolower($jenisWithSpace))
                  ->orWhere('jenis', strtolower($jenisWithUnderscore));
            });
        }
        
        return response()->json([
            'success' => true,
            'data' => $query->orderBy('kode')->get()
        ]);
    }
}
