<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

try {
    $user = App\Models\User::find(17);
    Auth::login($user);
    
    echo "Logged in as: " . $user->name . "\n";
    
    // Test getWalasContext from WalasController
    $tahunAktif = App\Models\TahunAjaran::where('is_aktif', true)->first();
    echo "Tahun Aktif: " . ($tahunAktif ? $tahunAktif->tahun . " (ID: {$tahunAktif->id})" : 'NONE') . "\n";
    
    $walas = App\Models\WaliKelas::with(['kelas.kurikulum'])->where('guru_id', $user->id)
        ->whereHas('kelas', function($query) use ($tahunAktif) {
            $query->where('tahun_ajaran_id', $tahunAktif->id);
        })->first();
    echo "WaliKelas found: " . ($walas ? "YES" : "NO") . "\n";
    
    $titimangsas = App\Models\Titimangsa::where('tahun_ajaran_id', $tahunAktif->id)->orderBy('id')->get();
    echo "Titimangsas count: " . $titimangsas->count() . "\n";
    
    foreach ($titimangsas as $t) {
        echo " - ID: {$t->id}, Nama: {$t->nama_periode}, is_aktif: {$t->is_aktif}\n";
    }
    
    if ($walas && $titimangsas->count() > 0) {
        $kelas = $walas->kelas;
        $siswaIds = App\Models\Siswa::where('kelas_id', $kelas->id)->pluck('id')->toArray();
        $titimangsaIds = $titimangsas->pluck('id')->toArray();
        
        echo "\nSiswa IDs count: " . count($siswaIds) . "\n";
        
        // Test PoinSiswa query from WalasRekapController
        echo "\n--- PoinSiswa query ---\n";
        $allBkPoin = App\Models\PoinSiswa::whereIn('siswa_id', $siswaIds)
            ->whereIn('titimangsa_id', $titimangsaIds)
            ->where(function($q) {
                $q->where('is_tambahan_walas', false)->orWhereNull('is_tambahan_walas');
            })
            ->get();
        echo "BK Poin records: " . $allBkPoin->count() . "\n";
        
        // Check PoinSiswa table schema
        echo "\n--- PoinSiswa table columns ---\n";
        $columns = DB::select("DESCRIBE poin_siswas");
        foreach ($columns as $col) {
            echo " - " . $col->Field . " (" . $col->Type . ")\n";
        }
    }
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
