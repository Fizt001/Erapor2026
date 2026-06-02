<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EkskulSiswa extends Model
{
    use HasFactory;
    
    // Ganti $fillable dengan $guarded agar tidak ada data yang mental
    protected $guarded = [];

    public function siswa() { return $this->belongsTo(Siswa::class); }
    public function ekskul() { return $this->belongsTo(Ekskul::class); }
    public function titimangsa() { return $this->belongsTo(Titimangsa::class); }
}