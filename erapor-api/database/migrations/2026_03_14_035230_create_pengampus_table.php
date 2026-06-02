<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('pengampus', function (Blueprint $table) {
        $table->id();
       
        $table->foreignId('struktur_kurikulum_id')->nullable()->constrained('struktur_kurikulums')->cascadeOnDelete();
        $table->foreignId('struktur_kejuruan_id')->nullable()->constrained('struktur_kejuruans')->cascadeOnDelete();
        
        $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
        $table->foreignId('guru_id')->constrained('users')->cascadeOnDelete();
        
        $table->integer('jp');
        $table->timestamps();

       $table->unique(['struktur_kurikulum_id', 'struktur_kejuruan_id', 'kelas_id', 'guru_id'], 'pengampu_fix_unique');
    });
}
    
    public function down(): void
    {
        Schema::dropIfExists('pengampus');
    }
};
