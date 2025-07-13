<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            $user = User::create([
                'name' => 'Mahasiswa ' . $i,
                'email' => 'mahasiswa' . $i . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'isAktif' => true,
            ]);

            Mahasiswa::create([
                'userId' => $user->id,
                'nama' => 'Mahasiswa ' . $i,
                'nim' => 'MHS2024' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'tahunMasuk' => 2024,
            ]);
        }
    }
} 