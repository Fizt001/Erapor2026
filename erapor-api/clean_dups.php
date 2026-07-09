<?php
namespace App\Models;
use Illuminate\Support\Facades\DB;

$duplicates = DB::table('penanganan_pelanggarans')
    ->selectRaw('MIN(id) as keep_id, siswa_id, kategori, deskripsi_masalah')
    ->groupBy('siswa_id', 'kategori', 'deskripsi_masalah')
    ->havingRaw('COUNT(id) > 1')
    ->get();

foreach ($duplicates as $dup) {
    DB::table('penanganan_pelanggarans')
        ->where('siswa_id', $dup->siswa_id)
        ->where('kategori', $dup->kategori)
        ->where('deskripsi_masalah', $dup->deskripsi_masalah)
        ->where('id', '!=', $dup->keep_id)
        ->delete();
    echo "Deleted duplicates for siswa " . $dup->siswa_id . PHP_EOL;
}
echo "Cleanup complete." . PHP_EOL;
