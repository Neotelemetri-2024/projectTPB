<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MataKuliah;

class MataKuliahSeeder extends Seeder
{
    public function run(): void
    {
        $mataKuliahs = [
            ['kodeMatkul' => 'MK001', 'namaMatkul' => 'Pemrograman Web', 'jenis' => 'wajib', 'sks' => 3],
            ['kodeMatkul' => 'MK002', 'namaMatkul' => 'Basis Data', 'jenis' => 'wajib', 'sks' => 3],
            ['kodeMatkul' => 'MK003', 'namaMatkul' => 'Algoritma dan Pemrograman', 'jenis' => 'wajib', 'sks' => 4],
            ['kodeMatkul' => 'MK004', 'namaMatkul' => 'Struktur Data', 'jenis' => 'wajib', 'sks' => 3],
            ['kodeMatkul' => 'MK005', 'namaMatkul' => 'Jaringan Komputer', 'jenis' => 'wajib', 'sks' => 3],
            ['kodeMatkul' => 'MK006', 'namaMatkul' => 'Sistem Operasi', 'jenis' => 'pilihan', 'sks' => 2],
            ['kodeMatkul' => 'MK007', 'namaMatkul' => 'Pemrograman Mobile', 'jenis' => 'pilihan', 'sks' => 2],
            ['kodeMatkul' => 'MK008', 'namaMatkul' => 'Kecerdasan Buatan', 'jenis' => 'pilihan', 'sks' => 3],
            ['kodeMatkul' => 'MK009', 'namaMatkul' => 'Pengembangan Aplikasi', 'jenis' => 'pilihan', 'sks' => 2],
            ['kodeMatkul' => 'MK010', 'namaMatkul' => 'Keamanan Sistem', 'jenis' => 'pilihan', 'sks' => 2],
        ];

        foreach ($mataKuliahs as $mk) {
            MataKuliah::create($mk);
        }
    }
}
