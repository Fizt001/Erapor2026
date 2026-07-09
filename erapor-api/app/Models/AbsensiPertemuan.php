<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiPertemuan extends Model
{
    protected $table = 'absensi_pertemuan';
    
    protected $fillable = [
        'pertemuan_id',
        'siswa_id',
        'status',
    ];

    public function pertemuan(): BelongsTo
    {
        return $this->belongsTo(PertemuanGuru::class, 'pertemuan_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }
}
