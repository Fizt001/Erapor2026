<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('struktur_kurikulums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kurikulum_id')->constrained('kurikulums')->cascadeOnDelete();
            
            // Pintu masuk ke Kelompok Mapel (WAJIB ADA)
            $table->foreignId('kelompok_mapel_id')->constrained('kelompok_mapels')->cascadeOnDelete();
            
            $table->foreignId('mapel_id')->constrained('mapels')->cascadeOnDelete();
            $table->enum('tingkat', ['X', 'XI', 'XII']);
            $table->integer('jp'); 
            $table->timestamps();
            
            // Unique constraint agar satu mapel tidak double di tingkat yang sama dalam satu kurikulum
            $table->unique(['kurikulum_id', 'mapel_id', 'tingkat'], 'struktur_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('struktur_kurikulums');
    }
};