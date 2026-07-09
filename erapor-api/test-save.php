<?php
$siswa = App\Models\Siswa::first();
echo "Before: " . $siswa->kelas_id . "\n";
$siswa->kelas_id = 2; // assuming class 2 exists
$siswa->save();
echo "After save: " . $siswa->kelas_id . "\n";

$siswa2 = App\Models\Siswa::first();
echo "After reload: " . $siswa2->kelas_id . "\n";
