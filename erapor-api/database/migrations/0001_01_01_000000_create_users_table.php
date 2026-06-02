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
        // 1. Tabel Utama Akun
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // <--- TAMBAHAN: Sumber nama tunggal untuk Siswa & Guru
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            /** * Role user: admin, kepsek, kurikulum, bk, guru, siswa 
             * Default kita set ke guru agar aman.
             */
            $table->string('role')->default('guru');
            // Menandai apakah guru bisa mengajar mapel umum
            $table->boolean('is_pengampu_umum')->default(true); 
            // Menandai apakah guru bisa mengajar mapel produktif/kejuruan
            $table->boolean('is_pengampu_kejuruan')->default(false); 
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Tabel Reset Token (Bawaan Laravel)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 3. Tabel Session (Penting karena di .env kamu SESSION_DRIVER=database)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};