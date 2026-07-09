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
        Schema::table('catatan_wali_kelas', function (Blueprint $table) {
            $table->enum('rekomendasi_kenaikan', ['naik', 'tinggal', 'lulus', 'belum_ditentukan'])->default('belum_ditentukan')->after('catatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catatan_wali_kelas', function (Blueprint $table) {
            $table->dropColumn('rekomendasi_kenaikan');
        });
    }
};
