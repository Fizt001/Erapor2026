<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Titimangsa extends Model
{
    protected $fillable = [
        'tahun_ajaran_id', 
        'kurikulum_id', 
        'nama_periode', 
        'target_tingkat',
        'tanggal_cetak', 
        'tempat_cetak', 
        'is_aktif'
    ];

    // Titimangsa balik ke Tahun Ajaran
    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    // Titimangsa balik ke Kurikulum
    public function kurikulum(): BelongsTo
    {
        return $this->belongsTo(Kurikulum::class);
    }
}