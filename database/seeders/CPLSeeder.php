<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cpl;

class CPLSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'kodeCpl' => 'CPL-01',
                'deskripsi' => 'Kemampuan menerapkan pengetahuan matematika, ilmu pengetahuan  alam dan/atau material, teknologi informasi dan keteknikan untuk menyelesaikan permasalahan dalam bidang teknik pertanian dan biosistem.'
            ],
            [
                'kodeCpl' => 'CPL-02',
                'deskripsi' => 'Kemampuan mendesain komponen, sistem dan/atau proses untuk memenuhi kebutuhan yang diharapkan didalam batasan-batasan realistis, misalnya hukum, ekonomi, lingkungan, sosial, politik, kesehatan dan keselamatan, keberlanjutan pertanian serta untuk mengenali dan/atau memanfaatkan potensi sumber daya lokal dan nasional dengan wawasan global'
            ],
            [
                'kodeCpl' => 'CPL-03',
                'deskripsi' => 'Kemampuan mendesain dan melaksanakan eksperimen laboratorium dan/atau lapangan serta menganalisis dan mengartikan data untuk memperkuat penilaian teknik pertanian dan biosistem'
            ],
            [
                'kodeCpl' => 'CPL-04',
                'deskripsi' => 'Kemampuan menganalisis dan menyelesaikan permasalahan teknik pertanian dan biosistem'
            ],
            [
                'kodeCpl' => 'CPL-05',
                'deskripsi' => 'Kemampuan menerapkan metode, keterampilan dan piranti teknik yang modern yang diperlukan untuk praktek keteknikan pertanian dan biosistem'
            ],
            [
                'kodeCpl' => 'CPL-06',
                'deskripsi' => 'Kemampuan berkomunikasi secara efektif baik lisan maupun  tulisan'
            ],
            [
                'kodeCpl' => 'CPL-07',
                'deskripsi' => 'Kemampuan merencanakan, menyelesaikan dan mengevaluasi tugas di dalam batasan-batasan yang ada.'
            ],
            [
                'kodeCpl' => 'CPL-08',
                'deskripsi' => 'Kemampuan untuk mendemonstrasikan bekerja dalam tim lintas disiplin dan lintas budaya'
            ],
            [
                'kodeCpl' => 'CPL-09',
                'deskripsi' => 'Kemampuan  untuk bertanggung jawab kepada masyarakat dan mematuhi etika profesi dalam menyelesaikan permasalahan teknik pertanian dan biosistem'
            ],
            [
                'kodeCpl' => 'CPL-10',
                'deskripsi' => 'Kemampuan menerapkan akan kebutuhan pembelajaran sepanjang hayat, termasuk akses terhadap pengetahuan terkait isu-isu terkini yang relevan'
            ],
            [
                'kodeCpl' => 'CPL-11',
                'deskripsi' => 'Mampu mengaplikasikan prinsip kewirausahaan untuk dapat berkontribusi pada pembangunan'
            ],
        ];
        foreach ($data as $cpl) {
            Cpl::create($cpl);
        }
    }
}
