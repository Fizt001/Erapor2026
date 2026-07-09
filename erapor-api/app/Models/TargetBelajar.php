<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetBelajar extends Model
{
    use HasFactory;

    protected $fillable = [
        'siswa_id', 'titimangsa_id', 'mapel_id', 'target_nilai'
    ];

    public function siswa() {
        return $this->belongsTo(Siswa::class);
    }

    public function titimangsa() {
        return $this->belongsTo(Titimangsa::class);
    }

    public function mapel() {
        return $this->belongsTo(Mapel::class);
    }
}
