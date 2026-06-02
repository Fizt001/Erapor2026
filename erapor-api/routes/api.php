<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Rute Backup
    Route::prefix('/admin/backup')->group(function () {
        Route::get('/list', [App\Http\Controllers\Api\AdminBackupController::class, 'listBackups']);
        Route::post('/generate', [App\Http\Controllers\Api\AdminBackupController::class, 'generateBackup']);
        Route::post('/upload', [App\Http\Controllers\Api\AdminBackupController::class, 'uploadBackup']);
        Route::post('/restore/{filename}', [App\Http\Controllers\Api\AdminBackupController::class, 'restoreBackup']);
        Route::get('/download/{filename}', [App\Http\Controllers\Api\AdminBackupController::class, 'downloadBackup']);
        Route::delete('/{filename}', [App\Http\Controllers\Api\AdminBackupController::class, 'destroyBackup']);
    });

    // ==========================================
    // MODULE BK
    // ==========================================
    Route::prefix('bk')->group(function () {
        Route::get('dashboard', [App\Http\Controllers\Api\Bk\DashboardController::class, 'index']);
        Route::apiResource('pelanggaran', App\Http\Controllers\Api\Bk\BkPelanggaranController::class);
        Route::apiResource('poin', App\Http\Controllers\Api\Bk\BkPoinController::class)->only(['index', 'store', 'destroy']);
        Route::apiResource('penanganan', App\Http\Controllers\Api\Bk\BkPenangananController::class);
        Route::get('buku-kasus', [App\Http\Controllers\Api\Bk\BkBukuKasusController::class, 'index']);
        
        Route::get('absensi', [App\Http\Controllers\Api\Bk\BkAbsensiController::class, 'index']);
        Route::post('absensi/update', [App\Http\Controllers\Api\Bk\BkAbsensiController::class, 'updateAbsensi']);
        Route::get('laporan', [App\Http\Controllers\Api\Bk\BkLaporanController::class, 'index']);
    });

    // Rute Kurikulum (Role Waka Kurikulum)
    Route::prefix('/kurikulum')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Api\Kurikulum\DashboardController::class, 'index']);
        
        Route::apiResource('mapel', App\Http\Controllers\Api\Kurikulum\MapelController::class);
        Route::apiResource('ekskul', App\Http\Controllers\Api\Kurikulum\EkskulController::class);

        // Titimangsa
        Route::get('/titimangsa', [\App\Http\Controllers\Api\Kurikulum\TitimangsaController::class, 'index']);
        Route::post('/titimangsa', [\App\Http\Controllers\Api\Kurikulum\TitimangsaController::class, 'store']);
        Route::put('/titimangsa/{id}', [\App\Http\Controllers\Api\Kurikulum\TitimangsaController::class, 'update']);
        Route::delete('/titimangsa/{id}', [\App\Http\Controllers\Api\Kurikulum\TitimangsaController::class, 'destroy']);
        Route::post('/titimangsa/{id}/toggle', [\App\Http\Controllers\Api\Kurikulum\TitimangsaController::class, 'toggle']);

        // Struktur Umum
        Route::get('/struktur-umum', [App\Http\Controllers\Api\Kurikulum\StrukturUmumController::class, 'index']);
        Route::post('/struktur-umum', [App\Http\Controllers\Api\Kurikulum\StrukturUmumController::class, 'strukturStore']);
        Route::delete('/struktur-umum/{id}', [App\Http\Controllers\Api\Kurikulum\StrukturUmumController::class, 'strukturDestroy']);

        // Struktur Kejuruan
        Route::get('/struktur-kejuruan', [App\Http\Controllers\Api\Kurikulum\StrukturKejuruanController::class, 'index']);
        Route::post('/struktur-kejuruan', [App\Http\Controllers\Api\Kurikulum\StrukturKejuruanController::class, 'store']);
        Route::delete('/struktur-kejuruan/{id}', [App\Http\Controllers\Api\Kurikulum\StrukturKejuruanController::class, 'destroy']);

        // Plot Guru Mapel (Pengampu)
        Route::apiResource('pengampu', App\Http\Controllers\Api\Kurikulum\PengampuController::class)->only(['index', 'store', 'destroy']);
        
        // Wali Kelas
        Route::apiResource('wali-kelas', App\Http\Controllers\Api\Kurikulum\WaliKelasController::class)->only(['index', 'store', 'destroy']);
        
        // Standar Nilai (KKM)
        Route::apiResource('kkm', App\Http\Controllers\Api\Kurikulum\KkmController::class)->only(['index', 'store']);
        
        // Master Deskripsi
        Route::get('/deskripsi-template', [App\Http\Controllers\Api\Kurikulum\DeskripsiTemplateController::class, 'index']);
        Route::post('/deskripsi-template', [App\Http\Controllers\Api\Kurikulum\DeskripsiTemplateController::class, 'store']);
    });

    // Rute Admin
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    
    Route::get('/admin/sekolah', [AdminController::class, 'getSekolah']);
    Route::put('/admin/sekolah', [AdminController::class, 'updateSekolah']);
    
    Route::get('/admin/users/template', [App\Http\Controllers\Api\AdminUserController::class, 'downloadTemplate']);
    Route::post('/admin/users/import', [App\Http\Controllers\Api\AdminUserController::class, 'importUsers']);
    Route::apiResource('/admin/users', App\Http\Controllers\Api\AdminUserController::class);

    // Master Kejuruan (Bidang -> Program -> Konsentrasi)
    Route::prefix('/admin/kejuruan')->group(function () {
        Route::get('/data', [App\Http\Controllers\Api\AdminKejuruanController::class, 'index']);
        
        Route::post('/bidang', [App\Http\Controllers\Api\AdminKejuruanController::class, 'storeBidang']);
        Route::put('/bidang/{id}', [App\Http\Controllers\Api\AdminKejuruanController::class, 'updateBidang']);
        Route::delete('/bidang/{id}', [App\Http\Controllers\Api\AdminKejuruanController::class, 'destroyBidang']);
        
        Route::post('/program', [App\Http\Controllers\Api\AdminKejuruanController::class, 'storeProgram']);
        Route::put('/program/{id}', [App\Http\Controllers\Api\AdminKejuruanController::class, 'updateProgram']);
        Route::delete('/program/{id}', [App\Http\Controllers\Api\AdminKejuruanController::class, 'destroyProgram']);
        
        Route::post('/konsentrasi', [App\Http\Controllers\Api\AdminKejuruanController::class, 'storeKejuruan']);
        Route::put('/konsentrasi/{id}', [App\Http\Controllers\Api\AdminKejuruanController::class, 'updateKejuruan']);
        Route::delete('/konsentrasi/{id}', [App\Http\Controllers\Api\AdminKejuruanController::class, 'destroyKejuruan']);
    });

    // Kelola Master Data Referensi
    Route::apiResource('/admin/referensi', \App\Http\Controllers\Api\AdminReferensiController::class);

    // Kelola Kurikulum
    Route::apiResource('/admin/kurikulum', \App\Http\Controllers\Api\AdminKurikulumController::class);
    Route::apiResource('/admin/tahun-ajaran', App\Http\Controllers\Api\AdminTahunAjaranController::class);

    // Master Kelas
    Route::get('/admin/kelas/dependencies', [App\Http\Controllers\Api\AdminKelasController::class, 'dependencies']);
    Route::apiResource('/admin/kelas', App\Http\Controllers\Api\AdminKelasController::class);

    // Kelola Siswa (Assign Rombel)
    Route::get('/admin/kelas/{id}/siswa', [App\Http\Controllers\Api\AdminKelolaSiswaController::class, 'index']);
    Route::post('/admin/kelas/{id}/siswa', [App\Http\Controllers\Api\AdminKelolaSiswaController::class, 'storeBulk']);
    Route::put('/admin/siswa/{id}', [App\Http\Controllers\Api\AdminKelolaSiswaController::class, 'update']);
    Route::delete('/admin/siswa/{id}', [App\Http\Controllers\Api\AdminKelolaSiswaController::class, 'destroy']);

    // ==========================================
    // MODULE GURU / WALAS
    // ==========================================
    Route::prefix('guru')->group(function () {
        Route::get('dashboard', [App\Http\Controllers\Api\Guru\DashboardController::class, 'index']);
        
        // Formatif Master (Auto-Save)
        Route::get('formatif/master', [App\Http\Controllers\Api\Guru\FormatifMasterController::class, 'index']);
        Route::post('formatif/master/elemen', [App\Http\Controllers\Api\Guru\FormatifMasterController::class, 'autoSaveElemen']);
        Route::post('formatif/master/tp', [App\Http\Controllers\Api\Guru\FormatifMasterController::class, 'autoSaveTp']);
        Route::delete('formatif/master/elemen/{id}', [App\Http\Controllers\Api\Guru\FormatifMasterController::class, 'deleteElemen']);
        Route::delete('formatif/master/tp/{id}', [App\Http\Controllers\Api\Guru\FormatifMasterController::class, 'deleteTp']);
        
        // FORMATIF NILAI
        Route::get('formatif/nilai', [\App\Http\Controllers\Api\Guru\FormatifNilaiController::class, 'index']);
        Route::post('formatif/nilai/store', [\App\Http\Controllers\Api\Guru\FormatifNilaiController::class, 'store']);
        
        // SUMATIF NILAI
        Route::get('sumatif/nilai', [\App\Http\Controllers\Api\Guru\SumatifNilaiController::class, 'index']);
        Route::post('sumatif/nilai/store', [\App\Http\Controllers\Api\Guru\SumatifNilaiController::class, 'store']);
        
        // REKAP SUMATIF
        Route::get('sumatif/rekap', [\App\Http\Controllers\Api\Guru\SumatifRekapController::class, 'index']);
    });
});
