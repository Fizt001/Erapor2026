<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPelajaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'kelas_id', 'hari', 'jam_ke', 'waktu_mulai', 'waktu_selesai', 
        'mapel_id', 'guru_id'
    ];

    public function kelas() {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function mapel() {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    public function guru() {
        return $this->belongsTo(User::class, 'guru_id');
    }
}
