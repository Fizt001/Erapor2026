<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::where('role', 'siswa')->first();
$siswa = $user->siswa;
$sumatifs = App\Models\SumatifNilai::with('kelas')
    ->where('siswa_id', $siswa->id)
    ->select('titimangsa_id', 'kelas_id')
    ->get();

$titimangsaTingkat = [];
foreach($sumatifs as $s) {
    if($s->kelas) {
        $titimangsaTingkat[$s->titimangsa_id] = $s->kelas->tingkat;
    }
}

$activeTitimangsa = App\Models\Titimangsa::where('is_aktif', 1)->first();
$currentTingkat = $siswa->kelas->tingkat ?? 'X';

if($activeTitimangsa) {
    $titimangsaTingkat[$activeTitimangsa->id] = $currentTingkat;
}

$titimangsas = App\Models\Titimangsa::whereIn('id', array_keys($titimangsaTingkat))
    ->orderBy('id', 'desc')
    ->get()
    ->map(function($t) use ($titimangsaTingkat) {
        $t->tingkat = $titimangsaTingkat[$t->id] ?? 'X';
        return $t;
    });

echo json_encode([
    'titimangsas' => $titimangsas,
    'keys' => array_keys($titimangsaTingkat),
    'current' => $currentTingkat
], JSON_PRETTY_PRINT);
