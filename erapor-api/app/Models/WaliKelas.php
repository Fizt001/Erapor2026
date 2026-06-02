<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaliKelas extends Model
{
    // Pastikan nama tabel sesuai database kamu (biasanya wali_kelas)
    protected $table = 'wali_kelas';

    protected $fillable = ['kelas_id', 'guru_id'];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function guru(): BelongsTo
    {
        // Relasi ke User dengan role Guru
        return $this->belongsTo(User::class, 'guru_id');
    }
}