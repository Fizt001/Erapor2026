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
        Schema::create('supervisi_gurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kepsek_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('guru_id')->constrained('users')->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('waktu')->nullable();
            $table->text('keterangan')->nullable();
            $table->text('evaluasi')->nullable();
            $table->text('tindak_lanjut')->nullable();
            $table->string('status')->default('Terjadwal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supervisi_gurus');
    }
};
