<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Login as BK
$bk = App\Models\User::where('role', 'bk')->first();
if($bk) {
    Auth::login($bk);
}

$s = App\Models\Siswa::with('user', 'kelas')->find(10);
if(!$s) {
    echo "Siswa Ivan tidak ditemukan!\n";
    exit;
}

echo "Siswa: {$s->user->name} (ID: {$s->id}) di Kelas: {$s->kelas->nama_kelas} (ID: {$s->kelas_id})\n";

for ($bulan = 7; $bulan <= 9; $bulan++) {
    $req = Illuminate\Http\Request::create('/api/bk/absensi', 'GET', ['kelas_id' => $s->kelas_id, 'bulan' => $bulan]);
    $res = app()->handle($req);
    $data = json_decode($res->getContent(), true);
    
    if (isset($data['success']) && $data['success']) {
        foreach ($data['data']['siswas'] as $siswaData) {
            if ($siswaData['id'] == $s->id) {
                echo "\n--- Data BK Absensi API untuk Bulan {$bulan} ---\n";
                $absensi = $siswaData['absensi'];
                if ($absensi) {
                    $s_cnt = 0; $i_cnt = 0; $a_cnt = 0;
                    for ($d = 1; $d <= 31; $d++) {
                        $col = 'tgl_' . $d;
                        if (isset($absensi[$col])) {
                            if ($absensi[$col] == 'S') $s_cnt++;
                            if ($absensi[$col] == 'I') $i_cnt++;
                            if ($absensi[$col] == 'A') $a_cnt++;
                        }
                    }
                    echo "Periode di DB: " . ($absensi['periode'] ?? 'NULL') . "\n";
                    echo "Total S: {$s_cnt}, I: {$i_cnt}, A: {$a_cnt}\n";
                } else {
                    echo "TIDAK ADA DATA ABSENSI\n";
                }
            }
        }
    } else {
        echo "API call failed for bulan $bulan\n";
    }
}
