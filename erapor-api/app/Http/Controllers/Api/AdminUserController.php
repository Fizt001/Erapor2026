<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $query->orderBy('name', 'asc');
        
        // 15 users per page by default
        $users = $query->paginate(15);
        
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,kepsek,kurikulum,bk,guru,siswa',
            'is_pengampu_umum' => 'nullable|boolean',
            'is_pengampu_kejuruan' => 'nullable|boolean',
        ]);

        $data = $request->only(['name', 'email', 'role']);
        $data['password'] = Hash::make($request->password);
        
        if ($request->role === 'guru') {
            $data['is_pengampu_umum'] = filter_var($request->is_pengampu_umum, FILTER_VALIDATE_BOOLEAN) ?? true;
            $data['is_pengampu_kejuruan'] = filter_var($request->is_pengampu_kejuruan, FILTER_VALIDATE_BOOLEAN) ?? false;
        } else {
            $data['is_pengampu_umum'] = false;
            $data['is_pengampu_kejuruan'] = false;
        }

        $user = User::create($data);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil ditambahkan',
            'data' => $user
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:admin,kepsek,kurikulum,bk,guru,siswa',
            'password' => 'nullable|string|min:6',
            'is_pengampu_umum' => 'nullable|boolean',
            'is_pengampu_kejuruan' => 'nullable|boolean',
        ]);

        $data = $request->only(['name', 'email', 'role']);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->role === 'guru') {
            $data['is_pengampu_umum'] = filter_var($request->is_pengampu_umum, FILTER_VALIDATE_BOOLEAN) ?? false;
            $data['is_pengampu_kejuruan'] = filter_var($request->is_pengampu_kejuruan, FILTER_VALIDATE_BOOLEAN) ?? false;
        } else {
            $data['is_pengampu_umum'] = false;
            $data['is_pengampu_kejuruan'] = false;
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diperbarui',
            'data' => $user
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Protect self-deletion (if auth token is correctly parsed)
        if (auth()->id() === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri'
            ], 403);
        }
        
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus'
        ]);
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_user.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Kolom header (gunakan titik koma agar otomatis terpisah di Excel versi Indonesia)
            fputcsv($file, ['Name', 'Email', 'Role', 'Password', 'Pengampu Umum', 'Pengampu Kejuruan'], ';');
            
            // Contoh isi
            fputcsv($file, ['Guru Contoh', 'guru@sekolah.id', 'guru', 'password123', '1', '0'], ';');
            fputcsv($file, ['Siswa Contoh', 'siswa@sekolah.id', 'siswa', 'password123', '0', '0'], ';');
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importUsers(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('file');
        
        // Auto detect delimiter (seringkali Excel Indonesia memakai titik koma)
        $content = file_get_contents($file->getRealPath());
        $firstLine = strtok($content, "\n");
        $delimiter = strpos($firstLine, ';') !== false ? ';' : ',';

        $handle = fopen($file->getRealPath(), "r");
        
        $header = fgetcsv($handle, 1000, $delimiter);
        // Kita tidak hanya cek count($header) < 4, karena bisa jadi formatnya beda. 
        // Setidaknya header harus ada.
        if (!$header || count($header) < 2) {
            return response()->json(['success' => false, 'message' => 'Format file CSV tidak valid. Pastikan memakai delimiter koma (,) atau titik koma (;).'], 400);
        }

        $imported = 0;
        $failed = 0;

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                // Pastikan panjang kolom cukup (minimum nama, email, role, password)
                if (count($row) < 4) continue;
                
                $email = trim($row[1]);
                
                if (User::where('email', $email)->exists()) {
                    $failed++;
                    continue;
                }

                $role = strtolower(trim($row[2]));
                if (!in_array($role, ['admin', 'kepsek', 'kurikulum', 'bk', 'guru', 'siswa'])) {
                    $role = 'siswa';
                }

                $password = trim($row[3]);
                if (empty($password)) $password = 'password123';

                $user = new User();
                $user->name = trim($row[0]);
                $user->email = $email;
                $user->role = $role;
                $user->password = Hash::make($password);
                
                if ($role === 'guru') {
                    $user->is_pengampu_umum = isset($row[4]) ? filter_var(trim($row[4]), FILTER_VALIDATE_BOOLEAN) : true;
                    $user->is_pengampu_kejuruan = isset($row[5]) ? filter_var(trim($row[5]), FILTER_VALIDATE_BOOLEAN) : false;
                } else {
                    $user->is_pengampu_umum = false;
                    $user->is_pengampu_kejuruan = false;
                }

                $user->save();
                $imported++;
            }
            
            \Illuminate\Support\Facades\DB::commit();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            fclose($handle);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengimpor data. Tidak ada data yang tersimpan. Error: ' . $e->getMessage()
            ], 500);
        }
        
        fclose($handle);

        return response()->json([
            'success' => true,
            'message' => "Import selesai. Berhasil: $imported, Gagal/Duplikat: $failed."
        ]);
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $user->password = Hash::make('12345678');
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil direset menjadi 12345678'
        ]);
    }
}
