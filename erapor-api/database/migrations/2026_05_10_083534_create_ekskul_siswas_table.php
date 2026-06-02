<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ekskul_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('ekskul_id')->constrained('ekskuls')->cascadeOnDelete();
            $table->foreignId('titimangsa_id')->constrained('titimangsas')->cascadeOnDelete();
            // Nilai diganti menjadi Integer sesuai instruksi
            $table->integer('nilai')->nullable(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ekskul_siswas');
    }
};