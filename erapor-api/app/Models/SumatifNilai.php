<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SumatifNilai extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun_ajaran_id', 'kurikulum_id', 'titimangsa_id', 'mapel_id', 'kelas_id', 'siswa_id',
        'uh1', 'uh2', 'uh3', 'uh4', 'praktek', 'teori', 'literasi',
        'na_value', 'b_uh', 'b_ujian', 'b_praktek', 'b_teori', 'b_psts_lalu'
    ];

    public function siswa() {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }


}