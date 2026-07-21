<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_sekolah', 'npsn', 'nss', 'website', 'alamat', 
        'kelurahan', 'kecamatan', 'kota', 'provinsi', 'kode_pos', 
        'telepon', 'email', 'nama_kepsek', 'nip_kepsek', 'logo',
        'logo_kiri', 'nama_yayasan', 'akreditasi',
        'foto_1', 'foto_2', 'foto_3',
        'visi', 'misi'
    ];
}