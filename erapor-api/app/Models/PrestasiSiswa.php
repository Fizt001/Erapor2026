<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestasiSiswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'siswa_id', 'nama_prestasi', 'jenis_prestasi', 'tingkat', 
        'penyelenggara', 'tahun', 'titimangsa_id', 'keterangan'
    ];

    public function siswa() {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function titimangsa() {
        return $this->belongsTo(Titimangsa::class, 'titimangsa_id');
    }
}
