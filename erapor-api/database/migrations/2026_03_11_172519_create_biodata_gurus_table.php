<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biodata_gurus', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ke ID di tabel users
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Kolom yang akan diisi oleh masing-masing user nanti
            $table->string('nama_lengkap')->nullable();
            $table->string('nip', 20)->unique()->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('no_hp')->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biodata_gurus');
    }
};