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
        Schema::create('catatan_wali_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->foreignId('titimangsa_id')->constrained('titimangsas')->cascadeOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            // Mencegah duplikasi catatan untuk siswa yang sama pada periode yang sama
            $table->unique(['siswa_id', 'titimangsa_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catatan_wali_kelas');
    }
};
