<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengampu extends Model
{
    protected $fillable = [
        'struktur_kurikulum_id', 
        'struktur_kejuruan_id',
        'kelas_id', 
        'guru_id', 
        'jp'
    ];

    public function strukturKurikulum()
{
    return $this->belongsTo(StrukturKurikulum::class, 'struktur_kurikulum_id');
}

public function strukturKejuruan()
{
    return $this->belongsTo(StrukturKejuruan::class, 'struktur_kejuruan_id');
}

    public function kelas() { return $this->belongsTo(Kelas::class); }
    public function guru() { return $this->belongsTo(User::class, 'guru_id'); }
}