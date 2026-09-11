<?php

namespace Database\Seeders;

use App\Models\Kurikulum;
use Illuminate\Database\Seeder;

class KurikulumSeeder extends Seeder
{
    public function run(): void
    {
        Kurikulum::firstOrCreate(
            ['kode' => '2020'],
            [
                'nama' => 'Kurikulum 2020',
                'tahun' => 2020,
                'isAktif' => true,
            ]
        );
    }
}
