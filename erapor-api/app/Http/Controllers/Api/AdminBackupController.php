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

        // Cek maintenance role
        $maintenanceRoles = ['guru', 'walikelas', 'siswa', 'kepsek'];
        if (in_array($role, $maintenanceRoles)) {
            return response()->json([
                'success' => false,
                'message' => 'Fitur backup untuk role ini sedang dalam tahap maintenance.'
            ], 400);
        }
        
        $backupData = [
            'role' => $role,
            'mode' => $mode,
            'generated_at' => Carbon::now()->toIso8601String(),
            'main_data' => [],
            'grow_data' => []
        ];

        try {
            if ($role === 'admin') {
                $backupData['main_data'] = [
                    'users' => class_exists(\App\Models\User::class) ? \App\Models\User::all() : [],
                    'sekolah' => class_exists(\App\Models\Sekolah::class) ? \App\Models\Sekolah::all() : [],
                    'kelas' => class_exists(\App\Models\Kelas::class) ? \App\Models\Kelas::all() : [],
                    'bidang' => class_exists(\App\Models\Bidang::class) ? \App\Models\Bidang::all() : [],
                    'program' => class_exists(\App\Models\Program::class) ? \App\Models\Program::all() : [],
                    'kejuruan' => class_exists(\App\Models\Kejuruan::class) ? \App\Models\Kejuruan::all() : [],
                    'kurikulum' => class_exists(\App\Models\Kurikulum::class) ? \App\Models\Kurikulum::all() : [],
                    'tahun_ajaran' => class_exists(\App\Models\TahunAjaran::class) ? \App\Models\TahunAjaran::all() : [],
                    'referensi' => class_exists(\App\Models\Referensi::class) ? \App\Models\Referensi::all() : [],
                    'mapel' => class_exists(\App\Models\Mapel::class) ? \App\Models\Mapel::all() : [],
                    'ekskul' => class_exists(\App\Models\Ekskul::class) ? \App\Models\Ekskul::all() : []
                ];

                if (class_exists(\App\Models\Siswa::class)) {
                    $query = \App\Models\Siswa::query();
                    if ($mode === 'psas') {
                        $query->whereMonth('created_at', '>=', 7)->whereMonth('created_at', '<=', 12);
                    } else if ($mode === 'psat') {
                        $query->whereMonth('created_at', '>=', 1)->whereMonth('created_at', '<=', 6);
                    }
                    $backupData['grow_data']['siswa'] = $query->get();
                }

            } elseif ($role === 'kurikulum') {
                $backupData['main_data'] = [
                    'struktur_kurikulum' => class_exists(\App\Models\StrukturKurikulum::class) ? \App\Models\StrukturKurikulum::all() : [],
                    'struktur_kejuruan' => class_exists(\App\Models\StrukturKejuruan::class) ? \App\Models\StrukturKejuruan::all() : [],
                    'kkm' => class_exists(\App\Models\Kkm::class) ? \App\Models\Kkm::all() : [],
                    'pengampu' => class_exists(\App\Models\Pengampu::class) ? \App\Models\Pengampu::all() : [],
                    'wali_kelas' => class_exists(\App\Models\WaliKelas::class) ? \App\Models\WaliKelas::all() : []
                ];

                if (class_exists(\App\Models\Titimangsa::class)) {
                    $query = \App\Models\Titimangsa::query();
                    if ($mode === 'psas') {
                        $query->whereMonth('created_at', '>=', 7)->whereMonth('created_at', '<=', 12);
                    } else if ($mode === 'psat') {
                        $query->whereMonth('created_at', '>=', 1)->whereMonth('created_at', '<=', 6);
                    }
                    $backupData['grow_data']['titimangsa'] = $query->get();
                }
            } elseif ($role === 'bk') {
                $backupData['main_data'] = [
                    'pelanggarans' => class_exists(\App\Models\Pelanggaran::class) ? \App\Models\Pelanggaran::all() : []
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

                    $backupData['grow_data']['absensi_siswas'] = $queryAbsensi->get();
                    $backupData['grow_data']['poin_siswas'] = $queryPoin->get();
                    $backupData['grow_data']['penanganan_pelanggarans'] = $queryPenanganan->get();
                }
            }

            // Generate Filename
            $timestamp = Carbon::now()->format('Y_m_d_His');
            $filename = "Backup_{$role}_{$mode}_{$timestamp}.json";
            
            $backupDir = storage_path('app/backups');
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }

            $filePath = $backupDir . '/' . $filename;
            File::put($filePath, json_encode($backupData, JSON_PRETTY_PRINT));

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
                    $modelClass = '\\App\\Models\\' . \Illuminate\Support\Str::studly(\Illuminate\Support\Str::singular($table));
                    if (class_exists($modelClass)) {
                        $modelClass::truncate();
                        foreach ($rows as $row) {
                            $modelClass::insert($row);
                        }
                    }
                }
            }

            // Restore Grow Data
            if(isset($data['grow_data'])) {
                foreach($data['grow_data'] as $table => $rows) {
                    $modelClass = '\\App\\Models\\' . \Illuminate\Support\Str::studly(\Illuminate\Support\Str::singular($table));
                    if (class_exists($modelClass)) {
                        // Karena grow data di backup itu partial (berdasarkan bulan),
                        // kita mungkin tidak bisa truncate semua.
                        // Tapi asumsi "restore" menimpa data, kita harus berhati-hati.
                        // Agar aman, kita abaikan duplikasi atau replace?
                        // Mengingat instruksi "kosongkan atau backup dulu", 
                        // kita akan truncate seluruh tabel grow yang direstore.
                        $modelClass::truncate();
                        foreach ($rows as $row) {
                            $modelClass::insert($row);
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
}
