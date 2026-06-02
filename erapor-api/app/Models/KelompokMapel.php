<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KelompokMapel extends Model
{
    use HasFactory;

    protected $fillable = [
        'kurikulum_id',
        'nama_kelompok'
    ];

    /**
     * Relasi ke Kurikulum (Induk)
     */
    public function kurikulum(): BelongsTo
    {
        return $this->belongsTo(Kurikulum::class);
    }

    /**
     * Relasi ke isi Mapel di dalam kelompok ini
     */
    public function strukturs(): HasMany
    {
        return $this->hasMany(StrukturKurikulum::class, 'kelompok_mapel_id');
    }
}