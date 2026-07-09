<?php
$cat = ['Bimbingan Walas', 'Teguran BK', 'SP1', 'SP2', 'SP3']; 
foreach($cat as $c) { 
    \App\Models\Referensi::firstOrCreate(
        ['jenis' => 'kategori_penanganan', 'kode' => str_replace(' ', '_', strtolower($c))], 
        ['nama' => $c]
    ); 
} 
echo 'Seeded!';
