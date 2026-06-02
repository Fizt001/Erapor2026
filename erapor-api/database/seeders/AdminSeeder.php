<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
{
    // Admin
    \App\Models\User::create([
        'name' => 'Administrator',
        'email' => 'admin@erapor.com',
        'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
        'role' => 'admin',
    ]);

    // Kurikulum
    \App\Models\User::create([
        'name' => 'Kurikulum',
        'email' => 'kurikulum@erapor.com',
        'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
        'role' => 'kurikulum',
    ]);

    // BK (Bimbingan Konseling)
    \App\Models\User::create([
        'name' => 'Guru BK',
        'email' => 'bk@erapor.com',
        'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
        'role' => 'bk',
    ]);

    // Guru / Walas
    \App\Models\User::create([
        'name' => 'Guru Pengajar',
        'email' => 'guru@erapor.com',
        'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
        'role' => 'guru',
    ]);
}
}
