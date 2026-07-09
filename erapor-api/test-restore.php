<?php
$s = App\Models\Siswa::find(2);
$s->kelas_id = 1;
$s->tanggal_keluar = null;
$s->alasan_keluar = null;
$s->save();
echo "Restored!";
