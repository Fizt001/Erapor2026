<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasusGuru extends Model
{
    use HasFactory;

    protected $fillable = [
        'guru_id',
        'pelapor_id',
        'tanggal',
        'kasus',
        'tindak_lanjut',
        'status'
    ];

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function pelapor()
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }
}
