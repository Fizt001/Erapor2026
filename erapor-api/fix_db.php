<?php
// Fix old descriptions to prevent duplicate cases
foreach(\App\Models\PenangananPelanggaran::all() as $p) { 
    if(strpos($p->deskripsi_masalah, 'Sakit/Izin') !== false) { 
        $p->deskripsi_masalah = str_replace('3x Sakit/Izin', '3x Sakit', $p->deskripsi_masalah); 
        $p->save(); 
        echo "Updated old record ID: {$p->id}\n";
    } 
}

// Delete the accidental duplicate cases created recently (ID 12 and 13)
\App\Models\PenangananPelanggaran::whereIn('id', [12, 13])->delete();
echo "Deleted duplicate records 12 and 13.\n";
