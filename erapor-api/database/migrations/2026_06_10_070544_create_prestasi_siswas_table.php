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
        Schema::create('prestasi_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->string('nama_prestasi');
            $table->string('jenis_prestasi')->nullable(); // Akademik, Non-Akademik
            $table->string('tingkat')->nullable(); // Sekolah, Kabupaten, Provinsi, Nasional, Internasional
            $table->string('penyelenggara')->nullable();
            $table->string('tahun');
            $table->foreignId('titimangsa_id')->nullable()->constrained('titimangsas')->onDelete('set null');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestasi_siswas');
    }
};
