<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('poin_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            
            // Nullable karena inputan walas (poin plus) tidak butuh jenis pelanggaran
            $table->foreignId('pelanggaran_id')->nullable()->constrained('pelanggarans')->nullOnDelete();
            
            // Kolom Skor
            $table->integer('skor_pengurang')->default(0); 
            $table->integer('skor_penambah')->default(0); // 🔑 BARU: Untuk poin penghargaan/plus
            $table->boolean('is_tambahan_walas')->default(false); // 🔑 BARU: Identifikasi row walas
            
            $table->foreignId('guru_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->foreignId('titimangsa_id')->constrained('titimangsas')->cascadeOnDelete();
            
            $table->text('catatan')->nullable();
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poin_siswas');
    }
};