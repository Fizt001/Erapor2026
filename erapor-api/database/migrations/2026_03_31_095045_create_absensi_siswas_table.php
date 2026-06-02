<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('absensi_siswas', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel Siswa (Nama tabel: 'siswa' sesuai database kamu)
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            
            // Metadata Absensi untuk mempermudah filter & rekap
            $table->string('tahun_ajaran'); // Cth: "2023/2024"
            $table->string('kurikulum');    // Cth: "Merdeka" atau "K13"
            $table->string('periode');      // Cth: PSTS Ganjil, PSAS, dst.
            $table->integer('bulan');       // 1 sampai 12

            // Membuat 31 Kolom Tanggal (tgl_1 sampai tgl_31)
            // Menggunakan Opsi C: Default 'H' (Hadir)
            for ($i = 1; $i <= 31; $i++) {
                $table->string("tgl_$i", 1)->default('H')->comment('H=Hadir, S=Sakit, I=Izin, A=Alpha, L=Libur, P=PKL');
            }

            // Kolom Counter untuk Cache (Agar Query Rekap Semester sangat cepat)
            $table->integer('total_h')->default(0); 
            $table->integer('total_s')->default(0); 
            $table->integer('total_i')->default(0); 
            $table->integer('total_a')->default(0); 
            $table->integer('total_l')->default(0); 
            $table->integer('total_p')->default(0); // Rekap PKL khusus permintaan kamu
            
            // Persentase Kehadiran Bulanan (H / (H+S+I+A) * 100)
            $table->decimal('persen_hadir', 5, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_siswas');
    }
};