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
        Schema::create('formatif_masters', function (Blueprint $table) {
            $table->id();
            // Guru yang mengampu
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // Hubungkan ke Mata Pelajaran
            $table->foreignId('mapel_id')->constrained('mapels')->onDelete('cascade');
            
            // Filter Akademik
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans');
            $table->foreignId('kurikulum_id')->constrained('kurikulums');
            $table->foreignId('titimangsa_id')->constrained('titimangsas');
            $table->foreignId('kelas_id')->constrained('kelas'); 
            
            $table->string('nama_elemen'); // Nama Elemen/Aspek (Max 3 nanti di logic)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formatif_masters');
    }
};