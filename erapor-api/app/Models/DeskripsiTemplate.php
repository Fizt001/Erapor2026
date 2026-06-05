<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeskripsiTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'kurikulum_id',
        'teks_tertinggi',
        'teks_terendah',
        'teks_ekskul',
        'is_active',
    ];

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class);
    }
}
