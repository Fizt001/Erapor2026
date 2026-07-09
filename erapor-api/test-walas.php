<?php
$context = App\Models\WaliKelas::whereHas('kelas', function($q) { $q->where('tingkat', 'X'); })->first();
$siswa = App\Models\Siswa::where('kelas_id', $context->kelas_id)->first();
echo json_encode([
    'walas_id' => $context->id, 
    'kelas_id' => $context->kelas_id, 
    'siswa_id' => $siswa ? $siswa->id : null,
    'siswa_nama' => $siswa ? $siswa->user->name : null
]);
