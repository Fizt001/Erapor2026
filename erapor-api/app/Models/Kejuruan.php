<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kejuruan extends Model
{
    use HasFactory;

    protected $fillable = ['program_id', 'kode_konsentrasi', 'nama_konsentrasi'];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function strukturKejuruans(): HasMany
    {
        // Di sini kita pakai konsentrasi_id sesuai migrasi kamu tadi
        return $this->hasMany(StrukturKejuruan::class, 'konsentrasi_id');
    }
}