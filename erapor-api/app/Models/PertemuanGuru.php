<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PertemuanGuru extends Model
{
    protected $table = 'pertemuan_guru';
    
    protected $fillable = [
        'guru_id',
        'titimangsa_id',
        'kelas_id',
        'mapel_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'materi',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function titimangsa(): BelongsTo
    {
        return $this->belongsTo(Titimangsa::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(AbsensiPertemuan::class, 'pertemuan_id');
    }
}
