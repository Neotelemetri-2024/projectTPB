<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TahunAjaran;

class TahunAjaranSeeder extends Seeder
{
    public function run(): void
    {
        $tahunAjarans = [
            ['tahun' => 2024, 'periode' => 'Ganjil'],
            ['tahun' => 2024, 'periode' => 'Genap'],
            ['tahun' => 2023, 'periode' => 'Ganjil'],
            ['tahun' => 2023, 'periode' => 'Genap'],
            ['tahun' => 2022, 'periode' => 'Ganjil'],
            ['tahun' => 2022, 'periode' => 'Genap'],
        ];

        foreach ($tahunAjarans as $ta) {
            TahunAjaran::create($ta);
        }
    }
} 