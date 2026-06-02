<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StrukturKejuruan extends Model
{
    use HasFactory;
    protected $fillable = ['kurikulum_id', 'mapel_id', 'program_id', 'konsentrasi_id', 'tingkat', 'jp'];

    public function mapel() { return $this->belongsTo(Mapel::class); }
    public function program() { return $this->belongsTo(Program::class); }
    public function konsentrasi() { return $this->belongsTo(Kejuruan::class, 'konsentrasi_id'); }

    public function pengampus()
    {
        // RELASI DIPERBAIKI: Mengarah ke struktur_kejuruan_id
        return $this->hasMany(Pengampu::class, 'struktur_kejuruan_id');
    }
}