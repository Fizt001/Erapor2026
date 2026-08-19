<?php

namespace App\Http\Controllers\Api\Kepsek;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KasusGuru;
use App\Models\User;

class KepsekKasusGuruController extends Controller
{
    public function index()
    {
        $kasus = KasusGuru::with(['guru', 'pelapor'])->orderBy('created_at', 'desc')->get();
        
        // Group by guru to count cases
        $guruCasesCount = KasusGuru::selectRaw('guru_id, count(*) as total')
            ->groupBy('guru_id')
            ->pluck('total', 'guru_id')
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => $kasus,
            'guru_cases_count' => $guruCasesCount
        ]);
    }

    public function panggil(Request $request, $guru_id)
    {
        $guru = User::findOrFail($guru_id);
        
        $totalKasus = KasusGuru::where('guru_id', $guru_id)->count();
        if ($totalKasus < 3) {
            return response()->json(['success' => false, 'message' => 'Belum memenuhi syarat panggilan (minimal 3 kasus).'], 400);
        }

        // Logic to send notification to Guru
        // Since we are adding it to the dashboard notification, we can just create a special KasusGuru entry or a new table for Notifications.
        // Actually, the Guru Dashboard can just count KasusGuru. If count >= 3, it shows a "PANGGILAN KEPSEK" warning.
        // But the Kepsek button "Panggil" should explicitly mark it.
        // Let's add a `is_dipanggil` column or just use the existence of a specific record?
        // Wait, the user said "notifikasi aja". Maybe we don't even need to save the state of "Panggil", or maybe we do.
        // Let's just create a new KasusGuru with status "Panggilan Kepsek" or update the latest one.
        
        $latestKasus = KasusGuru::where('guru_id', $guru_id)->orderBy('created_at', 'desc')->first();
        if ($latestKasus) {
            $latestKasus->tindak_lanjut = $latestKasus->tindak_lanjut . "\n[SISTEM] Kepsek telah melakukan Panggilan Resmi.";
            $latestKasus->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Panggilan berhasil dikirim ke dashboard Guru.'
        ]);
    }
}
