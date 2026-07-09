<?php
$kelasLama = App\Models\Kelas::where('nama_kelas', 'TAV-1')->where('tingkat', 'XI')->where('tahun_ajaran_id', 1)->first();
$kelasBaru = App\Models\Kelas::where('nama_kelas', 'TAV-1')->where('tingkat', 'XI')->where('tahun_ajaran_id', 2)->first();

echo json_encode([
    'kelas_lama' => $kelasLama,
    'kelas_baru' => $kelasBaru
]);
