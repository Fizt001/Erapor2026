<?php
use App\Models\WaliKelas;
use App\Models\Titimangsa;

// 1. Assign Guru 15 as Walas for Kelas 4 (XI TAV-1 2026/2027)
$exists = WaliKelas::where('kelas_id', 4)->where('guru_id', 15)->first();
if (!$exists) {
    WaliKelas::create([
        'kelas_id' => 4,
        'guru_id' => 15
    ]);
}

// 2. Duplicate Titimangsa for tahun_ajaran_id 2
$tits = Titimangsa::where('tahun_ajaran_id', 1)->get();
foreach ($tits as $t) {
    $existsTit = Titimangsa::where('tahun_ajaran_id', 2)->where('nama_periode', $t->nama_periode)->first();
    if (!$existsTit) {
        Titimangsa::create([
            'tahun_ajaran_id' => 2,
            'kurikulum_id' => $t->kurikulum_id,
            'nama_periode' => $t->nama_periode,
            'target_tingkat' => $t->target_tingkat,
            'tanggal_cetak' => '2026-12-20',
            'tempat_cetak' => 'Bekasi',
            'is_aktif' => $t->is_aktif
        ]);
    }
}
echo "Fixed!";
