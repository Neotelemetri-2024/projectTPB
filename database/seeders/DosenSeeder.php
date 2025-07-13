<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dosen;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DosenSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $user = User::create([
                'name' => 'Dosen ' . $i,
                'email' => 'dosen' . $i . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'dosen',
                'isAktif' => true,
            ]);

            Dosen::create([
                'userId' => $user->id,
                'nama' => 'Dosen ' . $i,
                'nip' => 'DSN2024' . str_pad($i, 3, '0', STR_PAD_LEFT),
            ]);
        }
    }
} 