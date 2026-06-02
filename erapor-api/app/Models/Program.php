<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    use HasFactory;

    protected $fillable = ['bidang_id', 'nama_program'];

    public function bidang()
    {
        return $this->belongsTo(Bidang::class);
    }

    public function kejuruans()
    {
        return $this->hasMany(Kejuruan::class);
    }

    public function strukturKejuruans(): HasMany
    {
        return $this->hasMany(StrukturKejuruan::class, 'program_id');
    }
}