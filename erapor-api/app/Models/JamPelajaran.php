<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JamPelajaran extends Model
{
    protected $fillable = [
        'kategori_hari',
        'jam_ke',
        'waktu_mulai',
        'waktu_selesai'
    ];
}
