<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenangananPelanggaran extends Model
{
    protected $table = 'penanganan_pelanggarans';

    protected $fillable = [
        'siswa_id', 
        'guru_id', 
        'tahun_ajaran_id', 
        'kategori', 
        'deskripsi_masalah', 
        'tindakan_penyelesaian', 
        'status'
    ];

    public function siswa() { return $this->belongsTo(Siswa::class, 'siswa_id'); }
    public function guru() { return $this->belongsTo(User::class, 'guru_id'); }
    public function tahunAjaran() { return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id'); }
}