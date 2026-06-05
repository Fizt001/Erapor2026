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
        Schema::table('deskripsi_templates', function (Blueprint $table) {
            $table->text('teks_ekskul')->nullable()->after('teks_terendah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deskripsi_templates', function (Blueprint $table) {
            //
        });
    }
};
