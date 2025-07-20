<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PimpinanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Pimpinan users
        $pimpinanUsers = [
            [
                'name' => 'Prof. Dr. Ir. Bambang Supriyadi, M.T.',
                'email' => 'pimpinan@tpb.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'pimpinan',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dr. Ir. Siti Nurhaliza, M.Si.',
                'email' => 'wakil.pimpinan@tpb.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'pimpinan',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ir. Ahmad Hidayat, M.Eng.',
                'email' => 'kajur@tpb.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'pimpinan',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dr. Ir. Rina Marlina, M.T.',
                'email' => 'kaprodi@tpb.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'pimpinan',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ir. Muhammad Fadli, M.Sc.',
                'email' => 'sekjur@tpb.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'pimpinan',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($pimpinanUsers as $userData) {
            // Check if user already exists
            $existingUser = User::where('email', $userData['email'])->first();
            
            if (!$existingUser) {
                User::create($userData);
                $this->command->info("Pimpinan user created: {$userData['name']} ({$userData['email']})");
            } else {
                $this->command->warn("Pimpinan user already exists: {$userData['email']}");
            }
        }

        $this->command->info('Pimpinan users seeding completed!');
        $this->command->info('Default password for all pimpinan accounts: password123');
    }
} 