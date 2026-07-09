<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne; // <--- Tambahkan ini

class Kelas extends Model
{
    protected $fillable = [
        'tahun_ajaran_id',
        'kejuruan_id', 
        'kurikulum_id',
        'tingkat', 
        'nama_kelas',
        // 'wali_kelas_id' // Sebaiknya ini ditinggalkan karena kita pakai tabel WaliKelas
    ];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function kejuruan(): BelongsTo
    {
        return $this->belongsTo(Kejuruan::class);
    }

    public function kurikulum(): BelongsTo
    {
        return $this->belongsTo(Kurikulum::class);
    }
   
    public function waliKelas(): HasOne
    {
        // Mencari data di tabel wali_kelas yang punya kelas_id sesuai id kelas ini
        return $this->hasOne(WaliKelas::class, 'kelas_id');
    }

    public function siswas(): HasMany
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }

    public function pengampus() 
    {
        return $this->hasMany(Pengampu::class, 'kelas_id');
    }
}