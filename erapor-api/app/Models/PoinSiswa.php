<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PoinSiswa extends Model
{
    use HasFactory;

    protected $table = 'poin_siswas';

    protected $fillable = [
        'siswa_id', 
        'pelanggaran_id', 
        'skor_pengurang',
        'skor_penambah',
        'is_tambahan_walas',
        'guru_id', 
        'tahun_ajaran_id',
        'titimangsa_id',
        'catatan', 
        'tanggal'
    ];

    public function siswa() 
    { 
        return $this->belongsTo(Siswa::class, 'siswa_id'); 
    }
    
    public function pelanggaran() 
    { 
        return $this->belongsTo(Pelanggaran::class, 'pelanggaran_id'); 
    }

   
    public function guru() 
    { 
        return $this->belongsTo(User::class, 'guru_id'); 
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function scopeTahunIni($query, $tahunId)
    {
        return $query->where('tahun_ajaran_id', $tahunId);
    }
}