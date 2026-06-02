<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
    Schema::create('struktur_kejuruans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('kurikulum_id')->constrained('kurikulums')->cascadeOnDelete();
        $table->foreignId('mapel_id')->constrained('mapels')->cascadeOnDelete();
        $table->foreignId('program_id')->nullable()->constrained('programs')->cascadeOnDelete();
        $table->foreignId('konsentrasi_id')->nullable()->constrained('kejuruans')->cascadeOnDelete(); 
        $table->enum('tingkat', ['X', 'XI', 'XII']);
        $table->integer('jp');
        $table->timestamps();
        
        // Agar tidak ada mapel ganda di satu jurusan & tingkat
        $table->unique(['kurikulum_id', 'mapel_id', 'tingkat', 'program_id', 'konsentrasi_id'], 'kejuruan_unique');
    });
}

    public function down(): void {
        Schema::dropIfExists('struktur_kejuruans');
    }
};