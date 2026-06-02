<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Ekskul extends Model
{
    // Ganti pembina_id jadi nama_pembina
    protected $fillable = ['nama_ekskul', 'nama_pembina']; 

    public function ekskulSiswas()
    {
        return $this->hasMany(EkskulSiswa::class);
    }
}