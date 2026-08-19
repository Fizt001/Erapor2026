<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupervisiGuru extends Model
{
    use HasFactory;

    protected $fillable = [
        'kepsek_id',
        'guru_id',
        'tanggal',
        'waktu',
        'keterangan',
        'evaluasi',
        'tindak_lanjut',
        'status'
    ];

    public function kepsek()
    {
        return $this->belongsTo(User::class, 'kepsek_id');
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }
}
