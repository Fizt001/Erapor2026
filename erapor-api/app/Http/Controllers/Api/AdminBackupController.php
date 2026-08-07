<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class AdminBackupController extends Controller
{
    public function listBackups()
    {
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $files = File::files($backupDir);
        $backups = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'json') {
                $backups[] = [
                    'filename' => $file->getFilename(),
                    'size' => $this->formatBytes($file->getSize()),
                    'timestamp' => date('Y-m-d H:i:s', $file->getMTime()),
                    'role' => $this->extractRoleFromFilename($file->getFilename()),
                    'mode' => $this->extractModeFromFilename($file->getFilename())
                ];
            }
        }

        // Sort by timestamp desc
        usort($backups, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        return response()->json([
            'success' => true,
            'data' => $backups
        ]);
    }

    public function generateBackup(Request $request)
    {
        $request->validate([
            'role' => 'required|in:admin,kurikulum,guru,walikelas,bk,siswa,kepsek',
            'mode' => 'required|in:psas,psat'
        ]);

        $role = $request->role;
        $mode = $request->mode;

        $mainDataTables = [];
        $growDataQueries = [];

        try {
            if ($role === 'admin') {
                $mainDataTables = [];
                // FULL DATABASE BACKUP: Retrieve all tables dynamically
                $tables = array_map('reset', \Illuminate\Support\Facades\DB::select('SHOW TABLES'));
                
                // Exclude system tables that shouldn't be moved to another PC
                $excludedTables = ['migrations', 'failed_jobs', 'jobs', 'job_batches', 'personal_access_tokens', 'sessions', 'cache', 'cache_locks', 'password_reset_tokens'];
                
                foreach ($tables as $table) {
                    if (!in_array($table, $excludedTables)) {
                        // value is null to indicate no specific model (use DB facade)
                        $mainDataTables[$table] = null;
                    }
                }
                
                // For admin, we don't need growDataQueries because we backup EVERYTHING in mainDataTables
                $growDataQueries = [];


            } elseif ($role === 'kurikulum') {
                $mainDataTables = [
                    'mapels' => \App\Models\Mapel::class,
                    'ekskuls' => \App\Models\Ekskul::class,
                    'struktur_kurikulums' => \App\Models\StrukturKurikulum::class,
                    'struktur_kejuruans' => \App\Models\StrukturKejuruan::class,
                    'kkms' => \App\Models\Kkm::class,
                    'pengampus' => \App\Models\Pengampu::class,
                    'wali_kelas' => \App\Models\WaliKelas::class,
                    'deskripsi_templates' => \App\Models\DeskripsiTemplate::class
                ];

                if (class_exists(\App\Models\Titimangsa::class)) {
                    $query = \App\Models\Titimangsa::query();
                    if ($mode === 'psas') {
                        $query->whereMonth('created_at', '>=', 7)->whereMonth('created_at', '<=', 12);
                    } else if ($mode === 'psat') {
                        $query->whereMonth('created_at', '>=', 1)->whereMonth('created_at', '<=', 6);
                    }
                    $growDataQueries['titimangsas'] = $query;
                }
            } elseif ($role === 'bk') {
                $mainDataTables = [
                    'pelanggarans' => \App\Models\Pelanggaran::class
                ];

                if (class_exists(\App\Models\AbsensiSiswa::class)) {
                    $queryAbsensi = \App\Models\AbsensiSiswa::query();
                    $queryPoin = \App\Models\PoinSiswa::query();
                    $queryPenanganan = \App\Models\PenangananPelanggaran::query();

                    if ($mode === 'psas') {
                        $queryAbsensi->whereMonth('created_at', '>=', 7)->whereMonth('created_at', '<=', 12);
                        $queryPoin->whereMonth('tanggal', '>=', 7)->whereMonth('tanggal', '<=', 12);
                        $queryPenanganan->whereMonth('created_at', '>=', 7)->whereMonth('created_at', '<=', 12);
                    } else if ($mode === 'psat') {
                        $queryAbsensi->whereMonth('created_at', '>=', 1)->whereMonth('created_at', '<=', 6);
                        $queryPoin->whereMonth('tanggal', '>=', 1)->whereMonth('tanggal', '<=', 6);
                        $queryPenanganan->whereMonth('created_at', '>=', 1)->whereMonth('created_at', '<=', 6);
                    }

                    $growDataQueries['absensi_siswas'] = $queryAbsensi;
                    $growDataQueries['poin_siswas'] = $queryPoin;
                    $growDataQueries['penanganan_pelanggarans'] = $queryPenanganan;
                }
            } elseif ($role === 'guru') {
                $mainDataTables = [
                    'jadwal_pelajarans' => \App\Models\JadwalPelajaran::class
                ];

                if (class_exists(\App\Models\FormatifMaster::class)) {
                    $queryMaster = \App\Models\FormatifMaster::query();
                    $queryTp = \App\Models\FormatifTp::query();
                    $queryF_Nilai = \App\Models\FormatifNilai::query();
                    $queryS_Nilai = \App\Models\SumatifNilai::query();

                    if ($mode === 'psas') {
                        $queryMaster->whereMonth('created_at', '>=', 7)->whereMonth('created_at', '<=', 12);
                        $queryTp->whereMonth('created_at', '>=', 7)->whereMonth('created_at', '<=', 12);
                        $queryF_Nilai->whereMonth('created_at', '>=', 7)->whereMonth('created_at', '<=', 12);
                        $queryS_Nilai->whereMonth('created_at', '>=', 7)->whereMonth('created_at', '<=', 12);
                    } else if ($mode === 'psat') {
                        $queryMaster->whereMonth('created_at', '>=', 1)->whereMonth('created_at', '<=', 6);
                        $queryTp->whereMonth('created_at', '>=', 1)->whereMonth('created_at', '<=', 6);
                        $queryF_Nilai->whereMonth('created_at', '>=', 1)->whereMonth('created_at', '<=', 6);
                        $queryS_Nilai->whereMonth('created_at', '>=', 1)->whereMonth('created_at', '<=', 6);
                    }

                    $growDataQueries['formatif_masters'] = $queryMaster;
                    $growDataQueries['formatif_tps'] = $queryTp;
                    $growDataQueries['formatif_nilais'] = $queryF_Nilai;
                    $growDataQueries['sumatif_nilais'] = $queryS_Nilai;
                }
            } elseif ($role === 'walikelas') {
                $mainDataTables = [];

                if (class_exists(\App\Models\CatatanWaliKelas::class)) {
                    $queryCatatan = \App\Models\CatatanWaliKelas::query();
                    $queryPrestasi = \App\Models\PrestasiSiswa::query();
                    $queryEkskul = \App\Models\EkskulSiswa::query();

                    if ($mode === 'psas') {
                        $queryCatatan->whereMonth('created_at', '>=', 7)->whereMonth('created_at', '<=', 12);
                        $queryPrestasi->whereMonth('created_at', '>=', 7)->whereMonth('created_at', '<=', 12);
                        $queryEkskul->whereMonth('created_at', '>=', 7)->whereMonth('created_at', '<=', 12);
                    } else if ($mode === 'psat') {
                        $queryCatatan->whereMonth('created_at', '>=', 1)->whereMonth('created_at', '<=', 6);
                        $queryPrestasi->whereMonth('created_at', '>=', 1)->whereMonth('created_at', '<=', 6);
                        $queryEkskul->whereMonth('created_at', '>=', 1)->whereMonth('created_at', '<=', 6);
                    }

                    $growDataQueries['catatan_wali_kelas'] = $queryCatatan;
                    $growDataQueries['prestasi_siswas'] = $queryPrestasi;
                    $growDataQueries['ekskul_siswas'] = $queryEkskul;
                }
            } elseif ($role === 'siswa') {
                $mainDataTables = [];

                if (class_exists(\App\Models\TargetBelajar::class)) {
                    $queryTarget = \App\Models\TargetBelajar::query();

                    if ($mode === 'psas') {
                        $queryTarget->whereMonth('created_at', '>=', 7)->whereMonth('created_at', '<=', 12);
                    } else if ($mode === 'psat') {
                        $queryTarget->whereMonth('created_at', '>=', 1)->whereMonth('created_at', '<=', 6);
                    }

                    $growDataQueries['target_belajars'] = $queryTarget;
                }
            } elseif ($role === 'kepsek') {
                $mainDataTables = [
                    'kalender_akademiks' => \App\Models\KalenderAkademik::class,
                    'kokurikulers' => \App\Models\Kokurikuler::class
                ];
            }

            $filename = $this->streamBackupFile($role, $mode, $mainDataTables, $growDataQueries);

            return response()->json([
                'success' => true,
                'message' => "Backup {$role} berhasil dibuat dengan mode {$mode}",
                'filename' => $filename
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat backup: ' . $e->getMessage()
            ], 500);
        }
    }

    private function streamBackupFile($role, $mode, $mainDataTables, $growDataQueries) {
        $timestamp = \Carbon\Carbon::now()->format('Y_m_d_His');
        $filename = "Backup_{$role}_{$mode}_{$timestamp}.json";
        
        $backupDir = storage_path('app/backups');
        if (!\Illuminate\Support\Facades\File::exists($backupDir)) {
            \Illuminate\Support\Facades\File::makeDirectory($backupDir, 0755, true);
        }
        $filePath = $backupDir . '/' . $filename;
        
        $handle = fopen($filePath, 'w');
        fwrite($handle, '{"role":"'.$role.'","mode":"'.$mode.'","generated_at":"'.\Carbon\Carbon::now()->toIso8601String().'","main_data":{');
        
        $firstMain = true;
        foreach($mainDataTables as $table => $modelClass) {
            if (!$firstMain) fwrite($handle, ',');
            fwrite($handle, '"'.$table.'":[');
            
            $firstRecord = true;
            if ($modelClass && class_exists($modelClass)) {
                $modelClass::query()->orderBy('id')->chunk(500, function($records) use ($handle, &$firstRecord) {
                    foreach($records as $record) {
                        if (!$firstRecord) fwrite($handle, ',');
                        fwrite($handle, json_encode($record->getAttributes()));
                        $firstRecord = false;
                    }
                });
            } else {
                // Fetch directly from DB table using DB facade
                $query = \Illuminate\Support\Facades\DB::table($table);
                $hasId = \Illuminate\Support\Facades\Schema::hasColumn($table, 'id');
                if ($hasId) {
                    $query->orderBy('id')->chunk(500, function($records) use ($handle, &$firstRecord) {
                        foreach($records as $record) {
                            if (!$firstRecord) fwrite($handle, ',');
                            fwrite($handle, json_encode((array)$record));
                            $firstRecord = false;
                        }
                    });
                } else {
                    $records = $query->get();
                    foreach($records as $record) {
                        if (!$firstRecord) fwrite($handle, ',');
                        fwrite($handle, json_encode((array)$record));
                        $firstRecord = false;
                    }
                }
            }
            fwrite($handle, ']');
            $firstMain = false;
        }
        
        fwrite($handle, '},"grow_data":{');
        
        $firstGrow = true;
        foreach($growDataQueries as $table => $query) {
            if (!$firstGrow) fwrite($handle, ',');
            fwrite($handle, '"'.$table.'":[');
            $firstRecord = true;
            // Get base model to ensure id column
            $query->orderBy($query->getModel()->getKeyName());
            $query->chunk(500, function($records) use ($handle, &$firstRecord) {
                foreach($records as $record) {
                    if (!$firstRecord) fwrite($handle, ',');
                    fwrite($handle, json_encode($record->getAttributes()));
                    $firstRecord = false;
                }
            });
            fwrite($handle, ']');
            $firstGrow = false;
        }
        
        fwrite($handle, '}}');
        fclose($handle);
        
        return $filename;
    }


    public function downloadBackup($filename, Request $request)
    {
        $filePath = storage_path('app/backups/' . $filename);
        
        if (File::exists($filePath)) {
            $format = $request->query('format', 'json');

            if ($format === 'excel') {
                return $this->convertToExcel($filePath, $filename);
            }

            return response()->download($filePath, $filename, [
                'Content-Type' => 'application/json',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'File backup tidak ditemukan'
        ], 404);
    }

    private function convertToExcel($jsonFilePath, $filename)
    {
        // Karena paket multi-sheet complex, kita konversi JSON ini ke beberapa file CSV lalu di-zip, 
        // ATAU kita buat satu file CSV besar. Namun, Excel bisa membuka HTML format table.
        // Untuk menjaga kesederhanaan dan kompabilitas, kita akan men-generate multi-sheet .xlsx
        // menggunakan Spatie\SimpleExcel jika bisa, tapi karena SimpleExcel tak dukung multi-sheet mudah,
        // kita akan buat Zip berisi file-file CSV untuk tiap tabel (yang bisa dibuka di Excel).
        
        $data = json_decode(file_get_contents($jsonFilePath), true);
        $zipFile = storage_path('app/backups/' . str_replace('.json', '.zip', $filename));
        
        $zip = new \ZipArchive();
        if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            
            // Tulis Main Data
            if(isset($data['main_data'])) {
                foreach($data['main_data'] as $table => $rows) {
                    if(!empty($rows)) {
                        $csvContent = $this->arrayToCsv($rows);
                        $zip->addFromString("main_data_{$table}.csv", $csvContent);
                    }
                }
            }
            
            // Tulis Grow Data
            if(isset($data['grow_data'])) {
                foreach($data['grow_data'] as $table => $rows) {
                    if(!empty($rows)) {
                        $csvContent = $this->arrayToCsv($rows);
                        $zip->addFromString("grow_data_{$table}.csv", $csvContent);
                    }
                }
            }
            
            // Tambahkan file info agar zip tidak pernah kosong
            $role = isset($data['role']) ? $data['role'] : '-';
            $mode = isset($data['mode']) ? $data['mode'] : '-';
            $date = isset($data['generated_at']) ? $data['generated_at'] : '-';
            $zip->addFromString("backup_info.txt", "Format: Erapor2026 Backup\nRole: {$role}\nMode: {$mode}\nDate: {$date}");

            $zip->close();
            
            return response()->download($zipFile)->deleteFileAfterSend(true);
        }

        return response()->json(['success' => false, 'message' => 'Gagal membuat file zip/excel'], 500);
    }

    private function arrayToCsv($array)
    {
        if (count($array) == 0) return '';
        ob_start();
        $df = fopen("php://output", 'w');
        fputcsv($df, array_keys(reset($array)));
        foreach ($array as $row) {
            fputcsv($df, $row);
        }
        fclose($df);
        return ob_get_clean();
    }

    public function uploadBackup(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimetypes:application/json'
        ]);

        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $file->move($backupDir, $filename);

        return response()->json([
            'success' => true,
            'message' => 'File backup berhasil diunggah'
        ]);
    }

    public function restoreBackup($filename)
    {
        $filePath = storage_path('app/backups/' . $filename);
        
        if (!File::exists($filePath)) {
            return response()->json(['success' => false, 'message' => 'File tidak ditemukan'], 404);
        }

        $data = json_decode(file_get_contents($filePath), true);

        if (!$data || !isset($data['main_data'])) {
            return response()->json(['success' => false, 'message' => 'Format file backup tidak valid (JSON rusak)'], 400);
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Restore Main Data
            if(isset($data['main_data'])) {
                foreach($data['main_data'] as $table => $rows) {
                    // Always try to use DB::table to ensure pivot tables are handled
                    if (\Illuminate\Support\Facades\Schema::hasTable($table)) {
                        \Illuminate\Support\Facades\DB::table($table)->delete();
                        
                        $adminPassword = \Illuminate\Support\Facades\Hash::make('admin123');
                        $defaultPassword = \Illuminate\Support\Facades\Hash::make('12345678');
                        
                        $chunks = array_chunk($rows, 200);
                        foreach($chunks as $chunk) {
                            $insertData = [];
                            foreach ($chunk as $row) {
                                if ($table === 'users' && !isset($row['password'])) {
                                    if (isset($row['email']) && $row['email'] === 'admin@erapor.com') {
                                        $row['password'] = $adminPassword;
                                    } else {
                                        $row['password'] = $defaultPassword;
                                    }
                                }
                                $insertData[] = $row;
                            }
                            if (!empty($insertData)) {
                                \Illuminate\Support\Facades\DB::table($table)->insert($insertData);
                            }
                        }
                    }
                }
            }

            // Restore Grow Data
            if(isset($data['grow_data'])) {
                foreach($data['grow_data'] as $table => $rows) {
                    if (\Illuminate\Support\Facades\Schema::hasTable($table)) {
                        \Illuminate\Support\Facades\DB::table($table)->delete();
                        
                        $chunks = array_chunk($rows, 200);
                        foreach($chunks as $chunk) {
                            if (!empty($chunk)) {
                                \Illuminate\Support\Facades\DB::table($table)->insert($chunk);
                            }
                        }
                    }
                }
            }

            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil direstore'
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return response()->json([
                'success' => false,
                'message' => 'Restore gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyBackup($filename)
    {
        $filePath = storage_path('app/backups/' . $filename);
        
        if (File::exists($filePath)) {
            File::delete($filePath);
            return response()->json([
                'success' => true,
                'message' => 'File backup berhasil dihapus'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'File backup tidak ditemukan'
        ], 404);
    }

    private function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
        $bytes = max($bytes, 0); 
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
        $pow = min($pow, count($units) - 1); 
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow]; 
    }

    private function extractRoleFromFilename($filename) {
        $parts = explode('_', $filename);
        return isset($parts[1]) ? ucfirst($parts[1]) : '-';
    }

    private function extractModeFromFilename($filename) {
        $parts = explode('_', $filename);
        return isset($parts[2]) ? ucfirst($parts[2]) : '-';
    }

    public function openStorageFolder()
    {
        $path = storage_path('app/public');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
        return response()->json([
            'success' => true,
            'path' => $path
        ]);
    }
}
