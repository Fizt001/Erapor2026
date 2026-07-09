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
        // Change enum to string
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE penanganan_pelanggarans MODIFY kategori VARCHAR(255) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to enum
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE penanganan_pelanggarans MODIFY kategori ENUM('Bimbingan Walas', 'SP1', 'SP2', 'SP3') NOT NULL");
    }
};
