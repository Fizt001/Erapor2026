<?php

namespace App\Http\Controllers\Api\Kurikulum;

use App\Http\Controllers\Controller;
use App\Models\DeskripsiTemplate;
use Illuminate\Http\Request;

class DeskripsiTemplateController extends Controller
{
    public function index()
    {
        $templates = DeskripsiTemplate::with('kurikulum')->get();
        return response()->json([
            'status' => 'success',
            'data' => $templates
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kurikulum_id' => 'required|exists:kurikulums,id',
            'teks_tertinggi' => 'required|string',
            'teks_terendah' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $template = DeskripsiTemplate::updateOrCreate(
            ['kurikulum_id' => $validated['kurikulum_id']],
            $validated
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Template deskripsi berhasil disimpan',
            'data' => $template
        ]);
    }
}
