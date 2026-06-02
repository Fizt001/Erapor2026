<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kurikulum extends Model
{
    protected $fillable = ['nama_kurikulum', 'singkatan'];

    // Kurikulum punya banyak Titimangsa (PSTS, PSAS, dll)
    public function titimangsas(): HasMany
    {
        return $this->hasMany(Titimangsa::class);
    }

    // Kurikulum punya banyak Kelas
    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class);
    }
}