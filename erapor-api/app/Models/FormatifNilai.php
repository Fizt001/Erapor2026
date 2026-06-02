<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormatifNilai extends Model
{
    use HasFactory;

    protected $fillable = [
        'siswa_id',
        'formatif_tp_id', 
        'titimangsa_id',
        'mapel_id',
        'kelas_id',
        'tahun_ajaran_id',
        'nilai',
    ];

    public function tp()
    {
        return $this->belongsTo(FormatifTp::class, 'formatif_tp_id', 'id');
    }
    
}