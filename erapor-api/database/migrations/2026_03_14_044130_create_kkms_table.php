<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kkms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->foreignId('kurikulum_id')->constrained('kurikulums')->cascadeOnDelete();
            $table->enum('tingkat', ['X', 'XI', 'XII']);
            $table->integer('nilai'); 
            $table->timestamps();

            // Proteksi: 1 TA + 1 Kurikulum + 1 Tingkat = 1 Nilai KKM
            $table->unique(['tahun_ajaran_id', 'kurikulum_id', 'tingkat'], 'kkm_unik_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kkms');
    }
};