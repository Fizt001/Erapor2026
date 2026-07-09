<?php
foreach(\App\Models\PenangananPelanggaran::all() as $p) { 
    echo $p->id . ' | ' . $p->kategori . ' | ' . $p->deskripsi_masalah . ' | ' . $p->status . PHP_EOL; 
}
