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
        Schema::create('sumatif_nilais', function (Blueprint $table) {
            $table->id();
            
            // Relasi Dasar (Foreign Key)
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->foreignId('kurikulum_id')->constrained('kurikulums')->cascadeOnDelete();
            $table->foreignId('titimangsa_id')->constrained('titimangsas')->cascadeOnDelete();
            $table->foreignId('mapel_id')->constrained('mapels')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            
            // Komponen Nilai (Format Desimal Presisi)
            $table->decimal('uh1', 5, 2)->nullable();
            $table->decimal('uh2', 5, 2)->nullable();
            $table->decimal('uh3', 5, 2)->nullable();
            $table->decimal('uh4', 5, 2)->nullable();
            $table->decimal('praktek', 5, 2)->nullable();
            $table->decimal('teori', 5, 2)->nullable();
            
            // Literasi Booster (Penambah Nilai Akhir)
            $table->decimal('literasi', 5, 2)->default(0);

            // 🎯 FONDASI BARU: Kolom Penyimpan Nilai Akhir Matang (Hasil Kalkulasi Otomatis)
            $table->decimal('na_value', 5, 2)->nullable();

            // Setting Konfigurasi Bobot Dinamis per Baris Model
            $table->integer('b_uh')->default(60);    
            $table->integer('b_ujian')->default(40); 
            $table->integer('b_praktek')->default(100); 
            $table->integer('b_teori')->default(0);
            
            // Bobot Tambahan Rekam Jejak PSTS Lalu untuk PSAS/PSAT
            $table->integer('b_psts_lalu')->default(20);

            $table->timestamps();

            // Index Unik: Gembok Keamanan agar data Nilai Siswa tidak Duplikat di Mapel & Periode yang sama
            $table->unique(['titimangsa_id', 'mapel_id', 'kelas_id', 'siswa_id'], 'sumatif_complete_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sumatif_nilais');
    }
};