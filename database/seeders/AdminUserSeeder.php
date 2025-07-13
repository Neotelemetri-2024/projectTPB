<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin2@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'isAktif' => true,
            'email_verified_at' => now(),
        ]);

        // Create additional admin users if needed
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin2@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'isAktif' => true,
            'email_verified_at' => now(),
        ]);
    }
}
