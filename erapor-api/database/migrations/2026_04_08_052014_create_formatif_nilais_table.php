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
    Schema::create('formatif_nilais', function (Blueprint $table) {
        $table->id();
        
        // Relasi Utama
        $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
        $table->foreignId('formatif_tp_id')->constrained('formatif_tps')->cascadeOnDelete();
        
        // Nilai Desimal (Max 100.00)
        $table->decimal('nilai', 5, 2)->nullable(); 

        // Pembungkus Data (Scope)
        $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
        $table->foreignId('titimangsa_id')->constrained('titimangsas')->cascadeOnDelete();
        $table->foreignId('mapel_id')->constrained('mapels')->cascadeOnDelete();
        $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formatif_nilais');
    }
};
