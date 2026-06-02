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
        // Bersihkan data lama terlebih dahulu karena akan menyebabkan error fk jika langsung didrop
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
        \Illuminate\Support\Facades\DB::table('struktur_kurikulums')->truncate();
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        Schema::table('struktur_kurikulums', function (Blueprint $table) {
            $table->dropForeign(['kelompok_mapel_id']);
            $table->dropColumn('kelompok_mapel_id');
        });

        Schema::dropIfExists('kelompok_mapels');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('kelompok_mapels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kurikulum_id')->constrained('kurikulums')->cascadeOnDelete();
            $table->string('nama_kelompok');
            $table->timestamps();
        });

        Schema::table('struktur_kurikulums', function (Blueprint $table) {
            $table->foreignId('kelompok_mapel_id')->constrained('kelompok_mapels')->cascadeOnDelete();
        });
    }
};
