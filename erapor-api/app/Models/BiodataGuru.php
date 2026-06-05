<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiodataGuru extends Model
{
    protected $table = 'biodata_gurus';
    protected $fillable = ['user_id', 'nama_lengkap', 'nip', 'jenis_kelamin', 'no_hp', 'alamat'];
}
