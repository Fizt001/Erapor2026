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
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            
            // 1. Relasi Kejuruan (Pastikan file migrasi kejuruan ada di urutan sebelum ini)
            $table->foreignId('kejuruan_id')->constrained('kejuruans')->cascadeOnDelete();
            
            // 2. Relasi Kurikulum (Pastikan file migrasi kurikulums ada di urutan sebelum ini)
            $table->foreignId('kurikulum_id')->constrained('kurikulums')->cascadeOnDelete();
            
            // 3. Relasi Wali Kelas (Nullable jika belum ada gurunya)
            $table->foreignId('wali_kelas_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('tingkat'); // X, XI, XII
            $table->string('nama_kelas'); // Contoh: X TKJ 1
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};