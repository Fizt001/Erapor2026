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
        Schema::create('referensis', function (Blueprint $table) {
            $table->id();
            $table->string('jenis')->index(); // e.g. 'kategori_mapel', 'kelompok_mapel'
            $table->string('kode'); // e.g. 'umum', 'A'
            $table->string('nama'); // e.g. 'Muatan Umum', 'Kelompok A (Nasional)'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referensis');
    }
};
