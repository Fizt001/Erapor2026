<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StrukturKurikulum extends Model
{
    use HasFactory;

    protected $fillable = [
        'kurikulum_id',
        'kelompok_mapel_id',
        'mapel_id',
        'tingkat',
        'jp'
    ];

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(KelompokMapel::class, 'kelompok_mapel_id');
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class);
    }

    public function kurikulum(): BelongsTo
    {
        return $this->belongsTo(Kurikulum::class);
    }

    public function pengampus()
    {
        return $this->hasMany(Pengampu::class, 'struktur_kurikulum_id');
    }

    public function kelompokMapel()
{
    // Sesuaikan nama foreign key-nya (di migrasi Anda tertulis 'kelompok_mapel_id')
    return $this->belongsTo(KelompokMapel::class, 'kelompok_mapel_id');
}
}