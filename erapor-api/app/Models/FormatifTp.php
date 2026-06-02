<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormatifTp extends Model
{
    use HasFactory;

    // WAJIB: Daftarkan kolom agar bisa disimpan secara otomatis
    protected $fillable = [
        'formatif_master_id',
        'nama_tp',
    ];

    /**
     * Relasi balik ke Master Elemen
     */
    public function master(): BelongsTo
    {
        return $this->belongsTo(FormatifMaster::class, 'formatif_master_id');
    }

    /**
     * Relasi ke Nilai-Nilai Formatif
     */
    public function nilais(): HasMany
    {
        return $this->hasMany(FormatifNilai::class, 'formatif_tp_id');
    }


}