<?php
$k = App\Models\Kelas::where('nama_kelas', 'TAV-1')->where('tahun_ajaran_id', 2)->where('tingkat', 'X')->first();
if ($k) {
    App\Models\Kelas::create([
        'nama_kelas' => 'TAV-1',
        'tingkat' => 'XI',
        'tahun_ajaran_id' => 3,
        'kejuruan_id' => $k->kejuruan_id,
        'kurikulum_id' => $k->kurikulum_id
    ]);
    echo "Kelas XI TAV-1 (2027/2028) berhasil dibuat.\n";
} else {
    echo "Kelas lama tidak ditemukan.\n";
}
