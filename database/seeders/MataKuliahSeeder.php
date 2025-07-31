<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MataKuliah;

class MataKuliahSeeder extends Seeder
{
    public function run(): void
    {
        $mataKuliahs = [
            [
                'kodeMatkul' => 'TPB60101',
                'namaMatkul' => 'Capstone Project',
                'jenis' => 'wajib',
                'sks' => 3
            ],
            [
                'kodeMatkul' => 'TPB62218',
                'namaMatkul' => 'Evaluasi Non-Destruktif Bahan Pertanian',
                'jenis' => 'pilihan',
                'sks' => 3
            ],
            [
                'kodeMatkul' => 'TPB62112',
                'namaMatkul' => 'Statistika I',
                'jenis' => 'wajib',
                'sks' => 3
            ],
            [
                'kodeMatkul' => 'TPB62118',
                'namaMatkul' => 'Teknik Pengolahan Hasil Pertanian dan Pangan',
                'jenis' => 'wajib',
                'sks' => 3
            ],
            [
                'kodeMatkul' => 'TPB62106',
                'namaMatkul' => 'Lingkungan Pertanian dan Biosistem',
                'jenis' => 'wajib',
                'sks' => 2
            ],
            [
                'kodeMatkul' => 'TPB62229',
                'namaMatkul' => 'Teknik Pengendalian Limbah Pertanian',
                'jenis' => 'pilihan',
                'sks' => 2
            ],
            [
                'kodeMatkul' => 'TPB62109',
                'namaMatkul' => 'Thermodinamika',
                'jenis' => 'wajib',
                'sks' => 3
            ],
            [
                'kodeMatkul' => 'TPB62115',
                'namaMatkul' => 'Energi dan Elektrifikasi',
                'jenis' => 'wajib',
                'sks' => 3
            ],
            [
                'kodeMatkul' => 'TPB62101',
                'namaMatkul' => 'Biologi',
                'jenis' => 'wajib',
                'sks' => 3
            ],
            [
                'kodeMatkul' => 'TPB62220',
                'namaMatkul' => 'Mekanika Mesin',
                'jenis' => 'pilihan',
                'sks' => 2
            ],
            [
                'kodeMatkul' => 'TPB62219',
                'namaMatkul' => 'Mekanika Mesin',
                'jenis' => 'pilihan',
                'sks' => 2
            ],
            [
                'kodeMatkul' => 'TPB62110',
                'namaMatkul' => 'Rancangan Teknik',
                'jenis' => 'wajib',
                'sks' => 3
            ],
            [
                'kodeMatkul' => 'TPB62228',
                'namaMatkul' => 'Bangunan Pertanian',
                'jenis' => 'pilihan',
                'sks' => 2
            ],
            [
                'kodeMatkul' => 'TPB62115',
                'namaMatkul' => 'Hidrologi',
                'jenis' => 'wajib',
                'sks' => 3
            ],
            [
                'kodeMatkul' => 'TPB62116',
                'namaMatkul' => 'Mekanika Fluida',
                'jenis' => 'wajib',
                'sks' => 3
            ],
            [
                'kodeMatkul' => 'TPB62108',
                'namaMatkul' => 'Mekanika Fluida',
                'jenis' => 'wajib',
                'sks' => 3
            ],
            [
                'kodeMatkul' => 'TPB62223',
                'namaMatkul' => 'Pembukaan dan Penyiapan Lahan',
                'jenis' => 'pilihan',
                'sks' => 2
            ],
            [
                'kodeMatkul' => 'TPB62220',
                'namaMatkul' => 'Pembukaan dan Penyiapan Lahan',
                'jenis' => 'pilihan',
                'sks' => 2
            ],
            [
                'kodeMatkul' => 'TPB62103',
                'namaMatkul' => 'Fisika II',
                'jenis' => 'wajib',
                'sks' => 3
            ],
            [
                'kodeMatkul' => 'TPB62108',
                'namaMatkul' => 'Etika Profesi Keteknikan',
                'jenis' => 'wajib',
                'sks' => 2
            ],
            [
                'kodeMatkul' => 'TPB62227',
                'namaMatkul' => 'Manajemen Sistem Irigasi',
                'jenis' => 'pilihan',
                'sks' => 3
            ],
            [
                'kodeMatkul' => 'TPB62225',
                'namaMatkul' => 'Manajemen Sumber Daya Lahan dan Air',
                'jenis' => 'pilihan',
                'sks' => 2
            ],
            [
                'kodeMatkul' => 'TPB62217',
                'namaMatkul' => 'Ergonomika',
                'jenis' => 'pilihan',
                'sks' => 3
            ],
            [
                'kodeMatkul' => 'TPB62102',
                'namaMatkul' => 'Kalkulus I',
                'jenis' => 'wajib',
                'sks' => 3
            ],
            [
                'kodeMatkul' => 'TPB62102',
                'namaMatkul' => 'Kalkulus II',
                'jenis' => 'wajib',
                'sks' => 3
            ],
            [
                'kodeMatkul' => 'MWU60103',
                'namaMatkul' => 'Kewarganegaraan',
                'jenis' => 'wajib',
                'sks' => 2
            ],
            [
                'kodeMatkul' => 'TPB62113',
                'namaMatkul' => 'Ilmu Ukur Wilayah',
                'jenis' => 'wajib',
                'sks' => 3
            ],
            [
                'kodeMatkul' => 'TPB62224',
                'namaMatkul' => 'Sistem Informasi Spasial Pertanian',
                'jenis' => 'pilihan',
                'sks' => 3
            ],
            [
                'kodeMatkul' => 'TPB62116',
                'namaMatkul' => 'Analisis Sistem',
                'jenis' => 'wajib',
                'sks' => 3
            ],
            [
                'kodeMatkul' => 'TPB62227',
                'namaMatkul' => 'Teknik Pengemasan',
                'jenis' => 'pilihan',
                'sks' => 3
            ],
            [
                'kodeMatkul' => 'TPB62107',
                'namaMatkul' => 'Pemprograman Komputer',
                'jenis' => 'wajib',
                'sks' => 3
            ],
            [
                'kodeMatkul' => 'TPB62117',
                'namaMatkul' => 'Sistem Kontrol',
                'jenis' => 'wajib',
                'sks' => 3
            ],
            [
                'kodeMatkul' => 'TPB62111',
                'namaMatkul' => 'Teknik Pascapanen',
                'jenis' => 'wajib',
                'sks' => 3
            ],
            [
                'kodeMatkul' => 'TPB62223',
                'namaMatkul' => 'Teknik Pengolahan Hasil Perkebunan',
                'jenis' => 'pilihan',
                'sks' => 3
            ],
            [
                'kodeMatkul' => 'TPB62104',
                'namaMatkul' => 'Kimia II',
                'jenis' => 'wajib',
                'sks' => 3
            ],
            [
                'kodeMatkul' => 'TPB62226',
                'namaMatkul' => 'Hubungan Tanah dengan Mesin Pertanian',
                'jenis' => 'pilihan',
                'sks' => 3
            ],
            [
                'kodeMatkul' => 'TPB62114',
                'namaMatkul' => 'Perbengkelan',
                'jenis' => 'wajib',
                'sks' => 3
            ],
            [
                'kodeMatkul' => 'TPB62222',
                'namaMatkul' => 'Teknik Pendinginan dan Pembekuan',
                'jenis' => 'pilihan',
                'sks' => 3
            ],
            [
                'kodeMatkul' => 'TPB62105',
                'namaMatkul' => 'Pengetahuan Bahan Teknik',
                'jenis' => 'wajib',
                'sks' => 2
            ],
            [
                'kodeMatkul' => 'TPB62221',
                'namaMatkul' => 'Teknik Konservasi Tanah dan Air',
                'jenis' => 'pilihan',
                'sks' => 3
            ]
        ];

        foreach ($mataKuliahs as $mk) {
            MataKuliah::create($mk);
        }
    }
}
