<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
   protected $fillable = [
        'kurikulum_id', 'kode_mapel', 'nama_mapel', 'kategori', 'kelompok'
    ];
    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class);
    }
    
    public function pengampus()
    {
        return $this->hasMany(Pengampu::class, 'mapel_id');
    }

    public function strukturKurikulums() 
    {
        return $this->hasMany(StrukturKurikulum::class, 'mapel_id');
    }

    public function strukturKejuruans() 
    {
        return $this->hasMany(StrukturKejuruan::class, 'mapel_id');
    }
}