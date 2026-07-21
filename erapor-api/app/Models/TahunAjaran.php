<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAjaran extends Model
{
    protected $fillable = ['tahun', 'is_aktif'];

    protected $casts = [
        'is_aktif' => 'boolean'
    ];

    // Menghubungkan Tahun ke banyak Titimangsa
    public function titimangsas(): HasMany
    {
        return $this->hasMany(Titimangsa::class);
    }
}