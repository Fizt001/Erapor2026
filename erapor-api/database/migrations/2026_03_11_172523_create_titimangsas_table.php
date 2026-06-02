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
       Schema::create('titimangsas', function (Blueprint $table) {
    $table->id();
    $table->foreignId('tahun_ajaran_id')->constrained()->cascadeOnDelete();
    $table->foreignId('kurikulum_id')->constrained()->cascadeOnDelete();
    $table->string('nama_periode'); 
   $table->string('target_tingkat')->default('X,XI,XII');
    $table->date('tanggal_cetak');  
    $table->string('tempat_cetak')->default('Bekasi');
    $table->boolean('is_aktif')->default(false);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('titimangsas');
    }
};
