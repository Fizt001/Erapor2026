<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormatifMaster extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi secara massal.
     * Disesuaikan dengan migration yang menggunakan user_id sebagai pengampu.
     */
    protected $fillable = [
        'tahun_ajaran_id',
        'kurikulum_id',
        'titimangsa_id',
        'user_id',
        'mapel_id',
        'kelas_id',
        'nama_elemen',
    ];

    /**
     * Relasi ke Tujuan Pembelajaran (TP)
     * Satu Master Elemen memiliki banyak detail TP.
     */
    public function tps(): HasMany
    {
        return $this->hasMany(FormatifTp::class, 'formatif_master_id');
    }

    /**
     * Relasi ke Guru (User)
     */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Mata Pelajaran
     */
    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    /**
     * Relasi ke Kelas
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Relasi ke Tahun Ajaran
     */
    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    /**
     * Relasi ke Titimangsa (Periode Penilaian)
     */
    public function titimangsa(): BelongsTo
    {
        return $this->belongsTo(Titimangsa::class, 'titimangsa_id');
    }
}