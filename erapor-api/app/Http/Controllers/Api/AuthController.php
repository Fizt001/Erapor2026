<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Siswa;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string', // username can be email or NIS
            'password' => 'required|string',
        ]);

        $username = $request->username;
        $user = null;
        $nis = null;

        // Check if username is an email
        if (str_contains($username, '@')) {
            $user = User::where('email', $username)->first();
            
            // Aturan: Siswa tidak boleh login pakai email
            if ($user && $user->role === 'siswa') {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa harus login menggunakan NIS, bukan Email.'
                ], 403);
            }
        } else {
            // Asumsikan ini adalah NIS
            $siswa = Siswa::where('nis', $username)->first();
            if ($siswa) {
                $user = $siswa->user;
                $nis = $siswa->nis;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'NIS tidak ditemukan di sistem.'
                ], 404);
            }
        }

        // Cek password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password salah atau kredensial tidak valid.'
            ], 401);
        }

        // Create Sanctum Token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'is_pengampu_umum' => $user->is_pengampu_umum,
                    'is_pengampu_kejuruan' => $user->is_pengampu_kejuruan,
                    'nis' => $nis
                ]
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    }
}
