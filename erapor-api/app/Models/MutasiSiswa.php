<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiSiswa extends Model
{
    protected $table = 'mutasi_siswa';
    
    protected $fillable = [
        'siswa_id',
        'jenis_mutasi',
        'tanggal_mutasi',
        'alasan',
        'kelas_asal_id',
        'kelas_tujuan_id',
        'status_approval',
        'diajukan_oleh',
        'diacc_oleh',
        'catatan_admin'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function kelas_asal()
    {
        return $this->belongsTo(Kelas::class, 'kelas_asal_id');
    }

    public function kelas_tujuan()
    {
        return $this->belongsTo(Kelas::class, 'kelas_tujuan_id');
    }

    public function diajukanOleh()
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }

    public function diaccOleh()
    {
        return $this->belongsTo(User::class, 'diacc_oleh');
    }
}
