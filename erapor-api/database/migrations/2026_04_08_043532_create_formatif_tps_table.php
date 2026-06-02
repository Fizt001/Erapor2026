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
    Schema::create('formatif_tps', function (Blueprint $table) {
        $table->id();
        // Menghubungkan TP ini ke Elemen mana
        $table->foreignId('formatif_master_id')->constrained('formatif_masters')->onDelete('cascade');
        $table->text('nama_tp'); // Isi kalimat TP-nya
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formatif_tps');
    }
};
