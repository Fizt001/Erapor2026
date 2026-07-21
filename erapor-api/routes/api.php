<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/public/stats', [App\Http\Controllers\Api\PublicController::class, 'stats']);

Route::get('/run-migration-live', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return response()->json(['message' => 'Migrasi berhasil dijalankan!', 'output' => \Illuminate\Support\Facades\Artisan::output()]);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Gagal migrate: ' . $e->getMessage()]);
    }
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    
    // Master Database (Public for all roles)
    Route::get('/referensi', [\App\Http\Controllers\Api\ReferensiController::class, 'index']);
    
    // Sekolah Info (Public for all authenticated roles)
    Route::get('/admin/sekolah', [AdminController::class, 'getSekolah']);
    
    // Rute Backup
    Route::prefix('/admin/backup')->middleware('role:admin')->group(function () {
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
        Route::get('/buku-kasus/export/pdf', [\App\Http\Controllers\Api\Bk\BkBukuKasusController::class, 'exportPdf']);
        Route::get('/buku-kasus/export/excel', [\App\Http\Controllers\Api\Bk\BkBukuKasusController::class, 'exportExcel']);
        
        // Master Referensi Khusus BK
        Route::apiResource('/referensi', \App\Http\Controllers\Api\Bk\BkReferensiController::class);
        
        Route::get('absensi', [App\Http\Controllers\Api\Bk\BkAbsensiController::class, 'index']);
        Route::post('absensi/update', [App\Http\Controllers\Api\Bk\BkAbsensiController::class, 'updateAbsensi']);
        Route::get('laporan', [App\Http\Controllers\Api\Bk\BkLaporanController::class, 'index']);
    });

    // Rute Kurikulum (Role Waka Kurikulum)
    Route::prefix('/kurikulum')->middleware('role:kurikulum,admin')->group(function () {
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
        Route::put('/struktur-umum/{id}', [App\Http\Controllers\Api\Kurikulum\StrukturUmumController::class, 'strukturUpdate']);
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
        
        // Penanganan Kasus (SP2 & SP3)
        Route::get('/penanganan', [App\Http\Controllers\Api\Kurikulum\KurikulumPenangananController::class, 'index']);
        Route::put('/penanganan/{id}/acc', [App\Http\Controllers\Api\Kurikulum\KurikulumPenangananController::class, 'accSp3']);
    });

    // Rute Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    
    // Buku Induk
    Route::prefix('/admin/buku-induk')->group(function () {
        Route::get('/dependencies', [\App\Http\Controllers\Api\AdminBukuIndukController::class, 'getDependencies']);
        Route::get('/biodata', [\App\Http\Controllers\Api\AdminBukuIndukController::class, 'getBiodataByKurikulum']);
        Route::get('/{kelas_id}/mapel-struktur', [\App\Http\Controllers\Api\AdminBukuIndukController::class, 'getMapelStruktur']);
    });
    Route::get('/admin/buku-induk/siswa/{siswa_id}/nilai', [App\Http\Controllers\Api\AdminBukuIndukController::class, 'getNilaiSiswa']);
    
    Route::post('/admin/sekolah', [AdminController::class, 'updateSekolah']);
    
    Route::get('/admin/users/template', [App\Http\Controllers\Api\AdminUserController::class, 'downloadTemplate']);
    Route::post('/admin/users/import', [App\Http\Controllers\Api\AdminUserController::class, 'importUsers']);
    Route::post('/admin/users/{id}/reset-password', [App\Http\Controllers\Api\AdminUserController::class, 'resetPassword']);
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
    Route::get('/admin/kelas/dependencies', [\App\Http\Controllers\Api\AdminKelasController::class, 'dependencies']);
    Route::post('/admin/kelas/generate', [\App\Http\Controllers\Api\AdminKelasController::class, 'generate']);
    Route::apiResource('/admin/kelas', \App\Http\Controllers\Api\AdminKelasController::class);

    // Kelola Siswa (Assign Rombel)
    Route::get('/admin/kelas/{id}/siswa', [App\Http\Controllers\Api\AdminKelolaSiswaController::class, 'index']);
    Route::post('/admin/kelas/{id}/siswa', [App\Http\Controllers\Api\AdminKelolaSiswaController::class, 'storeBulk']);
        Route::put('/admin/siswa/{id}', [App\Http\Controllers\Api\AdminKelolaSiswaController::class, 'update']);
        Route::delete('/admin/siswa/{id}', [App\Http\Controllers\Api\AdminKelolaSiswaController::class, 'destroy']);

        // Kenaikan Kelas & Mutasi
        Route::get('/admin/kenaikan-kelas/setup', [App\Http\Controllers\Api\AdminKenaikanKelasController::class, 'getSetupData']);
        Route::get('/admin/kenaikan-kelas/{kelas_id}/siswa', [App\Http\Controllers\Api\AdminKenaikanKelasController::class, 'getSiswa']);
        Route::post('/admin/kenaikan-kelas/proses', [App\Http\Controllers\Api\AdminKenaikanKelasController::class, 'proses']);

        Route::get('/admin/mutasi', [App\Http\Controllers\Api\AdminMutasiController::class, 'index']);
        Route::post('/admin/mutasi/proses', [App\Http\Controllers\Api\AdminMutasiController::class, 'prosesLangsung']);
        Route::post('/admin/mutasi/{id}/approve', [App\Http\Controllers\Api\AdminMutasiController::class, 'approve']);
        Route::post('/admin/mutasi/{id}/reject', [App\Http\Controllers\Api\AdminMutasiController::class, 'reject']);
        Route::delete('/admin/mutasi/{id}', [App\Http\Controllers\Api\AdminMutasiController::class, 'cancel']);
    });

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

        // ABSENSI PERTEMUAN (GURU MAPEL)
        Route::get('absensi/referensi', [\App\Http\Controllers\Api\Guru\AbsensiController::class, 'getReferensi']);
        Route::get('absensi/pertemuan', [\App\Http\Controllers\Api\Guru\AbsensiController::class, 'getPertemuan']);
        Route::get('absensi/last-jam', [\App\Http\Controllers\Api\Guru\AbsensiController::class, 'getLastJam']);
        Route::post('absensi/pertemuan', [\App\Http\Controllers\Api\Guru\AbsensiController::class, 'createPertemuan']);
        Route::put('absensi/pertemuan/{id}', [\App\Http\Controllers\Api\Guru\AbsensiController::class, 'updatePertemuan']);
        Route::delete('absensi/pertemuan/{id}', [\App\Http\Controllers\Api\Guru\AbsensiController::class, 'deletePertemuan']);
        Route::get('absensi/pertemuan/{id}/siswa', [\App\Http\Controllers\Api\Guru\AbsensiController::class, 'getAbsensiSiswa']);
        Route::post('absensi/pertemuan/{id}/simpan', [\App\Http\Controllers\Api\Guru\AbsensiController::class, 'simpanAbsensi']);

        // JURNAL MENGAJAR (REKAP PERTEMUAN)
        Route::get('jurnal-mengajar', [\App\Http\Controllers\Api\Guru\JurnalMengajarController::class, 'getJurnal']);
        
        // ==========================================
        // WALI KELAS
        // ==========================================
        Route::prefix('walas')->group(function () {
            Route::get('dashboard-stats', [\App\Http\Controllers\Api\Guru\WalasDashboardStatsController::class, 'getStats']);
            Route::get('rekap', [\App\Http\Controllers\Api\Guru\WalasRekapController::class, 'index']);
            Route::post('rekap/catatan', [\App\Http\Controllers\Api\Guru\WalasRekapController::class, 'saveCatatan']);
            Route::post('rekap/kurikulum', [\App\Http\Controllers\Api\Guru\WalasRekapController::class, 'saveKurikulum']);
            Route::get('absensi/kalender', [\App\Http\Controllers\Api\Guru\WalasAbsensiController::class, 'getMonthlyCalendar']);
            Route::get('mutasi', [\App\Http\Controllers\Api\Guru\WalasMutasiController::class, 'index']);
            Route::get('mutasi/kelas', [\App\Http\Controllers\Api\Guru\WalasMutasiController::class, 'getKelasList']);
            Route::post('mutasi', [\App\Http\Controllers\Api\Guru\WalasMutasiController::class, 'store']);
            Route::get('biodata', [\App\Http\Controllers\Api\Guru\WalasController::class, 'getBiodataSiswa']);
            Route::put('biodata/{id}', [\App\Http\Controllers\Api\Guru\WalasController::class, 'updateBiodataSiswa']);
            Route::post('biodata/lock-all', [\App\Http\Controllers\Api\Guru\WalasController::class, 'lockAllBiodata']);
            Route::get('monitoring', [\App\Http\Controllers\Api\Guru\WalasController::class, 'monitoringNilai']);
            
            // EKSTRAKURIKULER
            Route::get('ekskul', [\App\Http\Controllers\Api\Guru\WalasEkskulController::class, 'index']);
            Route::post('ekskul/store', [\App\Http\Controllers\Api\Guru\WalasEkskulController::class, 'store']);
            Route::post('ekskul/kurikulum', [\App\Http\Controllers\Api\Guru\WalasEkskulController::class, 'updateKurikulum']);
            
            // KOKURIKULER WALAS
            Route::get('kokurikuler', [App\Http\Controllers\Api\Guru\WalasKokurikulerController::class, 'index']);
            Route::post('kokurikuler/store', [App\Http\Controllers\Api\Guru\WalasKokurikulerController::class, 'store']);
            Route::post('kokurikuler/kurikulum', [App\Http\Controllers\Api\Guru\WalasKokurikulerController::class, 'updateKurikulum']);

            // BIMBINGAN WALAS (TINDAK LANJUT)
            Route::get('bimbingan', [App\Http\Controllers\Api\Guru\WalasPenangananController::class, 'index']);
            Route::put('bimbingan/{id}', [App\Http\Controllers\Api\Guru\WalasPenangananController::class, 'update']);

            // REKAP ABSENSI, CATATAN WALI KELAS, DAN POIN
            Route::get('rekap', [App\Http\Controllers\Api\Guru\WalasRekapController::class, 'index']);
            Route::post('rekap/catatan', [App\Http\Controllers\Api\Guru\WalasRekapController::class, 'storeCatatan']);
            Route::post('rekap/poin', [App\Http\Controllers\Api\Guru\WalasRekapController::class, 'storePoinTambahan']);

            // CETAK LEGER DAN RAPOR
            Route::get('cetak', [\App\Http\Controllers\Api\Guru\WalasCetakController::class, 'index']);
            Route::post('cetak/simpan-catatan', [\App\Http\Controllers\Api\Guru\WalasCetakController::class, 'simpanCatatan']);
            Route::get('cetak/rapor', [\App\Http\Controllers\Api\Guru\WalasCetakController::class, 'raporSiswa']);
            Route::get('cetak/leger', [\App\Http\Controllers\Api\Guru\WalasCetakController::class, 'legerKelas']);
            Route::post('naik-kelas', [\App\Http\Controllers\Api\Guru\WalasCetakController::class, 'naikKelas']);

            Route::get('prestasi', [\App\Http\Controllers\Api\Guru\WalasPrestasiController::class, 'index']);
            Route::post('prestasi', [\App\Http\Controllers\Api\Guru\WalasPrestasiController::class, 'store']);
            Route::put('prestasi/{id}', [\App\Http\Controllers\Api\Guru\WalasPrestasiController::class, 'update']);
            Route::delete('prestasi/{id}', [\App\Http\Controllers\Api\Guru\WalasPrestasiController::class, 'destroy']);

            // CATATAN KENAIKAN KELAS
            Route::get('kenaikan-kelas', [\App\Http\Controllers\Api\Guru\WalasKenaikanController::class, 'index']);
        });
    });

    // ROUTES SISWA
    Route::middleware('auth:sanctum')->prefix('siswa')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\Api\Siswa\SiswaDashboardController::class, 'index']);
        
        Route::get('biodata', [\App\Http\Controllers\Api\Siswa\SiswaBiodataController::class, 'index']);
        Route::put('biodata', [\App\Http\Controllers\Api\Siswa\SiswaBiodataController::class, 'update']);
        
        Route::get('rapor', [\App\Http\Controllers\Api\Siswa\SiswaRaporController::class, 'index']);
        Route::get('rapor/cetak', [\App\Http\Controllers\Api\Siswa\SiswaRaporController::class, 'cetak']);
        
        Route::get('analisis', [\App\Http\Controllers\Api\Siswa\SiswaAnalisisController::class, 'index']);
        Route::post('analisis/target', [\App\Http\Controllers\Api\Siswa\SiswaAnalisisController::class, 'setTarget']);

        Route::get('kedisiplinan', [\App\Http\Controllers\Api\Siswa\SiswaKedisiplinanController::class, 'index']);
        Route::get('absensi', [\App\Http\Controllers\Api\Siswa\SiswaAbsensiController::class, 'index']);
        
        Route::get('portofolio', [\App\Http\Controllers\Api\Siswa\SiswaPortofolioController::class, 'index']);
        Route::get('jadwal', [\App\Http\Controllers\Api\Siswa\SiswaJadwalController::class, 'index']);
    });
    // ==========================================
    // MODULE SUPERADMIN
    // ==========================================
    Route::middleware(['auth:sanctum', 'role:superadmin'])->prefix('superadmin')->group(function () {
        Route::get('/gurus', [\App\Http\Controllers\Api\SuperadminController::class, 'getGurus']);
        Route::get('/walas-classes', [\App\Http\Controllers\Api\SuperadminController::class, 'getWalasClasses']);
        Route::get('/siswas', [\App\Http\Controllers\Api\SuperadminController::class, 'getSiswas']);
    });
});
