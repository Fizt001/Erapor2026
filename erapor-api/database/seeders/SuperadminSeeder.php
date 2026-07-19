<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@erapor.com'],
            [
                'name' => 'Superadmin Utama',
                'password' => Hash::make('sadmin123'),
                'role' => 'superadmin',
            ]
        );
    }
}
