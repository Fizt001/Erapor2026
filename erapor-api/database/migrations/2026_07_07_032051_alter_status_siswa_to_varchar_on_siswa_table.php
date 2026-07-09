<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ubah enum menjadi varchar agar dinamis, menggunakan raw DB statement
        DB::statement("ALTER TABLE siswa MODIFY status_siswa VARCHAR(255) DEFAULT 'aktif'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke enum
        DB::statement("ALTER TABLE siswa MODIFY status_siswa ENUM('aktif', 'lulus', 'pindah', 'keluar', 'dikeluarkan') DEFAULT 'aktif'");
    }
};
