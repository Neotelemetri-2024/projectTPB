<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MataKuliah;

class MataKuliahSeeder extends Seeder
{
    public function run(): void
    {
        $mataKuliahs = [
            ['kodeMatkul' => 'MK001', 'namaMatkul' => 'Pemrograman Web', 'jenis' => 'Teori & Praktikum', 'sks' => 3],
            ['kodeMatkul' => 'MK002', 'namaMatkul' => 'Basis Data', 'jenis' => 'Teori & Praktikum', 'sks' => 3],
            ['kodeMatkul' => 'MK003', 'namaMatkul' => 'Algoritma dan Pemrograman', 'jenis' => 'Teori & Praktikum', 'sks' => 4],
            ['kodeMatkul' => 'MK004', 'namaMatkul' => 'Struktur Data', 'jenis' => 'Teori & Praktikum', 'sks' => 3],
            ['kodeMatkul' => 'MK005', 'namaMatkul' => 'Jaringan Komputer', 'jenis' => 'Teori & Praktikum', 'sks' => 3],
            ['kodeMatkul' => 'MK006', 'namaMatkul' => 'Sistem Operasi', 'jenis' => 'Teori', 'sks' => 2],
            ['kodeMatkul' => 'MK007', 'namaMatkul' => 'Pemrograman Mobile', 'jenis' => 'Praktikum', 'sks' => 2],
            ['kodeMatkul' => 'MK008', 'namaMatkul' => 'Kecerdasan Buatan', 'jenis' => 'Teori', 'sks' => 3],
            ['kodeMatkul' => 'MK009', 'namaMatkul' => 'Pengembangan Aplikasi', 'jenis' => 'Praktikum', 'sks' => 2],
            ['kodeMatkul' => 'MK010', 'namaMatkul' => 'Keamanan Sistem', 'jenis' => 'Teori', 'sks' => 2],
        ];

        foreach ($mataKuliahs as $mk) {
            MataKuliah::create($mk);
        }
    }
} 