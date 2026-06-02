<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggaran extends Model
{
    protected $fillable = ['nama_pelanggaran', 'bobot', 'jenis'];

    public function poinLogs() { return $this->hasMany(PoinSiswa::class); }
}