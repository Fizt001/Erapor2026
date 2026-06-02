<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kkm extends Model
{
    protected $table = 'kkms'; // Tegaskan nama tabel plural

    protected $fillable = [
        'tahun_ajaran_id', 
        'kurikulum_id', 
        'tingkat', 
        'nilai'
    ];

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class);
    }
}