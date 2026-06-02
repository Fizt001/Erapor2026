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
        Schema::create('kejuruans', function (Blueprint $table) {
            $table->id();
            // Ini relasi yang mengikat ke tabel programs
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->string('kode_konsentrasi'); // Cth: TKJ
            $table->string('nama_konsentrasi'); // Cth: Teknik Komputer dan Jaringan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kejuruans');
    }
};
