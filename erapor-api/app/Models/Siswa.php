<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa'; // Wajib karena nama tabel tunggal

    protected $fillable = [
        'user_id', 'kelas_id', 'nis', 'nisn', 'jenis_kelamin', 
        'tempat_lahir', 'tanggal_lahir', 'anak_ke', 'status_keluarga', 
        'warga_negara', 'agama', 'alamat', 'no_hp',
        'nama_ayah', 'ttl_ayah', 'pekerjaan_ayah', 'no_hp_ayah',
        'nama_ibu', 'ttl_ibu', 'pekerjaan_ibu', 'no_hp_ibu',
        'nama_wali', 'pekerjaan_wali', 'no_hp_wali', 'alamat_wali',
        'asal_sekolah', 'alamat_sekolah_asal', 'no_sttb_smp', 'tgl_sttb_smp',
        'tanggal_masuk', 'kelas_masuk', 'tanggal_keluar', 'alasan_keluar',
        'no_sttb_smk', 'tgl_sttb_smk',
        'tempat_pkl', 'alamat_pkl', 'tgl_mulai_pkl', 'tgl_selesai_pkl',
    ];

    // ==========================================
    // RELASI
    // ==========================================

    public function user() 
    { 
        return $this->belongsTo(User::class); 
    }

    public function kelas() 
    { 
        return $this->belongsTo(Kelas::class); 
    }

    public function poinLogs() 
    { 
        return $this->hasMany(PoinSiswa::class, 'siswa_id'); 
    }

    public function penanganans() 
    { 
        return $this->hasMany(PenangananPelanggaran::class, 'siswa_id'); 
    }

    public function absensi()
    {
        return $this->hasMany(AbsensiSiswa::class, 'siswa_id');
    }
   
    public function getNameAttribute()
    {
        return $this->user->name ?? 'Tanpa Nama';
    }
    
    public function getSisaPoinAttribute()
    {
        // 1. Cari Tahun Ajaran yang sedang aktif
        $taAktif = \App\Models\TahunAjaran::where('is_aktif', true)->first();
        
        // 2. Jika tidak ada yang aktif, asumsikan poin penuh (100)
        if (!$taAktif) {
            return 100;
        }

        // 3. Hitung total skor pengurang (Pelanggaran Master + Pindahan/Manual)
        // Hanya menghitung poin_siswas yang terikat pada tahun ajaran aktif
        $totalPotongan = $this->poinLogs()
            ->where('tahun_ajaran_id', $taAktif->id)
            ->sum('skor_pengurang');

        return 100 - $totalPotongan;
    }

    public function ekskulSiswas()
    {
        return $this->hasMany(EkskulSiswa::class, 'siswa_id');
    }

    public function kokurikulers()
    {
        return $this->hasMany(Kokurikuler::class, 'siswa_id');
    }
}