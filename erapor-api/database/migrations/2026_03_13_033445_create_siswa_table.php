<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            
            $table->string('nis')->unique();
            $table->string('nisn')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('anak_ke')->nullable();
            $table->string('status_keluarga')->nullable();
            $table->string('warga_negara')->nullable();
            $table->string('agama')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_hp')->nullable();

            // Orang Tua
            $table->string('nama_ayah')->nullable();
            $table->string('ttl_ayah')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->string('no_hp_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('ttl_ibu')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->string('no_hp_ibu')->nullable();

            // Wali
            $table->string('nama_wali')->nullable();
            $table->string('pekerjaan_wali')->nullable();
            $table->string('no_hp_wali')->nullable();
            $table->string('alamat_wali')->nullable();

            // Sekolah Asal
            $table->string('asal_sekolah')->nullable();
            $table->text('alamat_sekolah_asal')->nullable();
            $table->string('no_sttb_smp')->nullable();
            $table->string('tgl_sttb_smp')->nullable();

            // SMK
            $table->date('tanggal_masuk')->nullable();
            $table->string('kelas_masuk')->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->text('alasan_keluar')->nullable();
            $table->string('no_sttb_smk')->nullable();
            $table->string('tgl_sttb_smk')->nullable();

            // PKL
            $table->string('tempat_pkl')->nullable();
            $table->text('alamat_pkl')->nullable();
            $table->date('tgl_mulai_pkl')->nullable();
            $table->date('tgl_selesai_pkl')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};