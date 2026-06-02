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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            // Ini relasi yang mengikat ke tabel bidangs
            $table->foreignId('bidang_id')->constrained('bidangs')->cascadeOnDelete();
            $table->string('nama_program'); // Cth: Teknik Jaringan Komputer
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
