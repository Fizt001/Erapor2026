<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $kurikulums = \App\Models\Kurikulum::all();
    echo "Kurikulums: " . count($kurikulums) . "\n";
    $selectedKurikulumId = $kurikulums->first()?->id ?? null;
    echo "Selected ID: " . ($selectedKurikulumId ?? 'null') . "\n";

    if ($selectedKurikulumId) {
        $strukturs = \App\Models\StrukturKurikulum::with('mapel')->where('kurikulum_id', $selectedKurikulumId)->get();
        echo "Strukturs: " . count($strukturs) . "\n";
        
        $mapels = \App\Models\Mapel::where('kurikulum_id', $selectedKurikulumId)
            ->where('kategori', 'umum') 
            ->orderBy('nama_mapel')
            ->get();
        echo "Mapels: " . count($mapels) . "\n";
            
        $referensi_kelompok = \App\Models\Referensi::where('jenis', 'kelompok_mapel')->orderBy('kode')->get();
        echo "Refs: " . count($referensi_kelompok) . "\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
