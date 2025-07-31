<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class DosenTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new DosenTemplateSheet(),
            new InstruksiDosenSheet(),
        ];
    }
}

class DosenTemplateSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        // Buat 50 baris kosong untuk admin isi sendiri
        $emptyRows = collect();
        for ($i = 1; $i <= 50; $i++) {
            $emptyRows->push(['row' => $i]);
        }
        return $emptyRows;
    }

    public function headings(): array
    {
        $headings = [
            'NAMA',
            'NIP'
        ];

        // Tambahkan header informasi di baris pertama
        $infoHeader = [
            'TEMPLATE IMPORT DOSEN',
            '',
            'Format: Nama lengkap dengan gelar (contoh: Prof.Dr. Ir. Rusnam, MS)',
            'NIP: 18 digit angka',
            'Email akan otomatis dibuat: namatanpagelar@ae.unand.ac.id',
            'Password otomatis: NIP',
        ];

        return [
            $infoHeader, // Baris 1: Info template
            [], // Baris 2: Kosong
            $headings, // Baris 3: Header kolom
        ];
    }

    public function map($emptyRow): array
    {
        $row = [
            '', // NAMA kosong (untuk diisi admin)
            '', // NIP kosong (untuk diisi admin)
        ];

        return $row;
    }

    public function title(): string
    {
        return 'Template Dosen';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk baris header info (baris 1)
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE6F3FF'],
                ],
            ],
            // Style untuk header kolom (baris 3)
            3 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => Color::COLOR_WHITE],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4472C4'],
                ],
            ],
        ];
    }
}

class InstruksiDosenSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    public function collection()
    {
        return collect([
            ['INSTRUKSI PENGISIAN TEMPLATE DOSEN'],
            [''],
            ['1. Isi kolom NAMA dengan nama lengkap dosen beserta gelar'],
            ['   Contoh: Prof.Dr. Ir. Rusnam, MS'],
            ['   Contoh: Ir. Ayendra Asmuti, M.Si'],
            ['   Contoh: Dr. Renny Eka Putri, S.TP, MP'],
            [''],
            ['2. Isi kolom NIP dengan 18 digit angka NIP dosen'],
            ['   Contoh: 196309041989031002'],
            [''],
            ['3. Sistem akan otomatis:'],
            ['   - Membuat email: namatanpagelar@ae.unand.ac.id'],
            ['   - Menghilangkan gelar dari email (tanpa Prof., Dr., Ir., dll)'],
            ['   - Membuat password sama dengan NIP'],
            ['   - Membuat akun user dengan role dosen'],
            [''],
            ['4. Format email yang dihasilkan:'],
            ['   Nama: Prof.Dr. Ir. Rusnam, MS → Email: rusnam@ae.unand.ac.id'],
            ['   Nama: Dr. Renny Eka Putri, S.TP, MP → Email: rennyekaputri@ae.unand.ac.id'],
            ['   Nama: Ir. Ayendra Asmuti, M.Si → Email: ayendraasmuti@ae.unand.ac.id'],
            [''],
            ['5. Setelah mengisi, simpan file dan upload kembali ke sistem'],
            ['6. Tersedia 50 baris kosong untuk diisi'],
            [''],
            ['Contoh pengisian:'],
            ['NAMA: Prof.Dr. Ir. Rusnam, MS, NIP: 196309041989031002'],
            ['NAMA: Dr. Renny Eka Putri, S.TP, MP, NIP: 198006212006042016'],
            ['NAMA: Ir. Ayendra Asmuti, M.Si, NIP: 196504051990101001'],
        ]);
    }

    public function headings(): array
    {
        return [
            'INSTRUKSI PENGISIAN TEMPLATE DOSEN',
        ];
    }

    public function title(): string
    {
        return 'Instruksi';
    }
} 