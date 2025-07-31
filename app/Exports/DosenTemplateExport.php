<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DosenTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Template' => new DosenTemplateSheet(),
            'Instruksi' => new InstruksiDosenSheet(),
        ];
    }
}

class DosenTemplateSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        // Generate 50 empty rows
        $rows = [];
        for ($i = 1; $i <= 50; $i++) {
            $rows[] = [
                'nama' => '',
                'nip' => '',
                'email' => ''
            ];
        }
        return collect($rows);
    }

    public function headings(): array
    {
        return [
            ['FORMAT IMPORT DATA DOSEN'],
            [''],
            ['NAMA', 'NIP', 'EMAIL'],
            ['Contoh: Dr. John Doe', 'Contoh: 197304131998022001', 'Contoh: johndoe@ae.unand.ac.id']
        ];
    }

    public function map($row): array
    {
        return [
            $row['nama'],
            $row['nip'],
            $row['email']
        ];
    }

    public function title(): string
    {
        return 'Template';
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk header utama
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2EFDA'],
            ],
        ]);

        // Merge cell untuk judul
        $sheet->mergeCells('A1:C1');

        // Style untuk header kolom
        $sheet->getStyle('A3:C3')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Style untuk contoh data
        $sheet->getStyle('A4:C4')->applyFromArray([
            'font' => [
                'italic' => true,
                'color' => ['rgb' => '7F7F7F'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F2F2F2'],
            ],
        ]);

        // Border untuk semua data
        $sheet->getStyle('A3:C53')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
    }
}

class InstruksiDosenSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    public function collection()
    {
        return collect([
            ['INSTRUKSI PENGISIAN TEMPLATE IMPORT DOSEN'],
            [''],
            ['1. Format File:', 'Excel (.xlsx atau .xls)'],
            ['2. Kolom yang Harus Diisi:', ''],
            ['   - NAMA', 'Nama lengkap dosen (termasuk gelar jika ada)'],
            ['   - NIP', 'Nomor Induk Pegawai (18 digit)'],
            ['   - EMAIL', 'Alamat email dosen'],
            [''],
            ['3. Aturan Pengisian:', ''],
            ['   - NAMA', 'Wajib diisi, maksimal 255 karakter'],
            ['   - NIP', 'Wajib diisi, harus 18 digit angka'],
            ['   - EMAIL', 'Wajib diisi, format email yang valid'],
            [''],
            ['4. Contoh Pengisian:', ''],
            ['   NAMA', 'Dr. John Doe'],
            ['   NIP', '197304131998022001'],
            ['   EMAIL', 'johndoe@ae.unand.ac.id'],
            [''],
            ['5. Catatan:', ''],
            ['   - Password akun akan otomatis diset sama dengan NIP'],
            ['   - Jika dosen sudah ada (berdasarkan NIP), data akan diupdate'],
            ['   - Jika dosen belum ada, akan dibuat akun baru'],
            ['   - Pastikan email tidak duplikat dengan dosen lain'],
            [''],
            ['6. Error yang Mungkin Terjadi:', ''],
            ['   - NIP tidak 18 digit', 'Periksa kembali jumlah digit NIP'],
            ['   - Email sudah digunakan', 'Gunakan email yang berbeda'],
            ['   - Format email tidak valid', 'Pastikan format email benar'],
            ['   - Nama kosong', 'Nama dosen wajib diisi'],
        ]);
    }

    public function headings(): array
    {
        return ['KETERANGAN', 'DETAIL'];
    }

    public function title(): string
    {
        return 'Instruksi';
    }
} 