<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penanganan_pelanggarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('guru_id')->constrained('users')->cascadeOnDelete(); 
            // Wajib ada agar riwayat SP bisa dikunci per tahun ajaran
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            
            $table->enum('kategori', ['Bimbingan Walas', 'SP1', 'SP2', 'SP3']);
            $table->text('deskripsi_masalah');
            $table->text('tindakan_penyelesaian'); // Kolom solusi/tindakan
            $table->enum('status', ['Proses', 'Selesai'])->default('Proses');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penanganan_pelanggarans');
    }
};