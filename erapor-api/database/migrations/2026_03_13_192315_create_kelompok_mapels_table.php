<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelompok_mapels', function (Blueprint $table) {
            $table->id();
            // Nempel ke kurikulum (misal: KN punya Kelompok A, B, C sendiri)
            $table->foreignId('kurikulum_id')->constrained('kurikulums')->cascadeOnDelete();
            $table->string('nama_kelompok'); // Contoh: Kelompok A (Umum)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelompok_mapels');
    }
};