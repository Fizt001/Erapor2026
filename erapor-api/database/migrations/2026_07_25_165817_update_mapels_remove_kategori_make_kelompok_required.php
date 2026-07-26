<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * - Removes the old 'kategori' column (replaced by 'kelompok')
     * - Makes 'kelompok' required (not nullable)
     */
    public function up(): void
    {
        Schema::table('mapels', function (Blueprint $table) {
            // Drop the old kategori column if it exists
            if (Schema::hasColumn('mapels', 'kategori')) {
                $table->dropColumn('kategori');
            }
            // Make kelompok required (change from nullable to not null)
            if (Schema::hasColumn('mapels', 'kelompok')) {
                $table->string('kelompok', 50)->nullable(false)->default('A')->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mapels', function (Blueprint $table) {
            // Re-add kategori
            $table->string('kategori', 50)->nullable()->after('kurikulum_id');
            // Make kelompok nullable again
            if (Schema::hasColumn('mapels', 'kelompok')) {
                $table->string('kelompok', 50)->nullable()->change();
            }
        });
    }
};
