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

class MataKuliahTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Template' => new MataKuliahTemplateSheet(),
            'Instruksi' => new InstruksiMataKuliahSheet(),
        ];
    }
}

class MataKuliahTemplateSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        // Generate 50 empty rows
        $rows = [];
        for ($i = 1; $i <= 50; $i++) {
            $rows[] = [
                'nama_mata_kuliah' => '',
                'kode' => '',
                'kurikulum' => '',
                'sks' => '',
                'jenis' => ''
            ];
        }
        return collect($rows);
    }

    public function headings(): array
    {
        return [
            ['FORMAT IMPORT DATA MATA KULIAH'],
            [''],
            ['NAMA_MATA_KULIAH', 'KODE', 'KURIKULUM', 'SKS', 'JENIS'],
            ['Contoh: Matematika Dasar', 'Contoh: TPB001', 'Contoh: 2020', 'Contoh: 3', 'Contoh: wajib']
        ];
    }

    public function map($row): array
    {
        return [
            $row['nama_mata_kuliah'],
            $row['kode'],
            $row['kurikulum'],
            $row['sks'],
            $row['jenis']
        ];
    }

    public function title(): string
    {
        return 'Template';
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk header utama
        $sheet->getStyle('A1:E1')->applyFromArray([
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
        $sheet->mergeCells('A1:E1');

        // Style untuk header kolom
        $sheet->getStyle('A3:E3')->applyFromArray([
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
        $sheet->getStyle('A4:E4')->applyFromArray([
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
        $sheet->getStyle('A3:E53')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
    }
}

class InstruksiMataKuliahSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    public function collection()
    {
        return collect([
            ['INSTRUKSI PENGISIAN TEMPLATE IMPORT MATA KULIAH'],
            [''],
            ['1. Format File:', 'Excel (.xlsx atau .xls)'],
            ['2. Kolom yang Harus Diisi:', ''],
            ['   - NAMA_MATA_KULIAH', 'Nama lengkap mata kuliah'],
            ['   - KODE', 'Kode mata kuliah'],
            ['   - KURIKULUM', 'Kode kurikulum (mis. 2020). Dibuat otomatis bila belum ada'],
            ['   - SKS', 'Jumlah SKS (1-6)'],
            ['   - JENIS', 'Jenis mata kuliah (wajib/pilihan)'],
            [''],
            ['3. Aturan Pengisian:', ''],
            ['   - NAMA_MATA_KULIAH', 'Wajib diisi, maksimal 255 karakter'],
            ['   - KODE', 'Wajib diisi, maksimal 20 karakter'],
            ['   - KURIKULUM', 'Wajib diisi, kode kurikulum yang terdaftar di menu Kurikulum'],
            ['   - SKS', 'Wajib diisi, angka positif'],
            ['   - JENIS', 'Wajib diisi, pilih: wajib atau pilihan'],
            [''],
            ['4. Contoh Pengisian:', ''],
            ['   NAMA_MATA_KULIAH', 'Matematika Dasar'],
            ['   KODE', 'TPB001'],
            ['   KURIKULUM', '2020'],
            ['   SKS', '3'],
            ['   JENIS', 'wajib'],
            [''],
            ['5. Catatan:', ''],
            ['   - Jika mata kuliah sudah ada (berdasarkan kode dan kurikulum), data akan diupdate'],
            ['   - Jika mata kuliah belum ada, akan dibuat baru'],
            ['   - Mata kuliah baru tidak otomatis menjadi matkul asesmen; atur di menu Kurikulum'],
            [''],
            ['6. Error yang Mungkin Terjadi:', ''],
            ['   - Kode sudah digunakan', 'Gunakan kode yang berbeda atau kurikulum yang berbeda'],
            ['   - SKS tidak valid', 'Gunakan angka positif'],
            ['   - Jenis tidak valid', 'Gunakan: wajib atau pilihan'],
            ['   - Nama kosong', 'Nama mata kuliah wajib diisi'],
            ['   - Kode kosong', 'Kode mata kuliah wajib diisi'],
            ['   - Kurikulum kosong', 'Kode kurikulum wajib diisi'],
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