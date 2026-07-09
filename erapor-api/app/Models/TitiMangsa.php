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

    protected $appends = ['nama_periode_panjang'];

    public function getNamaPeriodePanjangAttribute()
    {
        $ref = \App\Models\Referensi::where('jenis', 'titimangsa')
                                    ->where('nama', $this->nama_periode)
                                    ->first();
        return $ref ? ($ref->keterangan ?: $this->nama_periode) : $this->nama_periode;
    }

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