<?php
$sakit = \App\Models\PenangananPelanggaran::where('deskripsi_masalah', 'LIKE', '%3x Sakit%')->where('tindakan_penyelesaian', '!=', '')->first(); 
$izin = \App\Models\PenangananPelanggaran::where('deskripsi_masalah', 'LIKE', '%3x Izin%')->where('siswa_id', $sakit->siswa_id)->first(); 
if($sakit && $izin) { 
    $izin->tindakan_penyelesaian = $sakit->tindakan_penyelesaian; 
    $izin->status = 'Selesai'; 
    $izin->save(); 
    $sakit->delete(); 
    echo 'Fixed Desti!'; 
}
