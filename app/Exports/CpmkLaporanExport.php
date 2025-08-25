<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CpmkLaporanExport implements WithMultipleSheets
{
    protected $tahunAjaranMatkul;
    protected $cpmkData;

    public function __construct($tahunAjaranMatkul, $cpmkData)
    {
        $this->tahunAjaranMatkul = $tahunAjaranMatkul;
        $this->cpmkData = $cpmkData;
    }

    public function sheets(): array
    {
        $sheets = [];
        
        // Sheet Ringkasan
        $sheets[] = new CpmkRingkasanSheet($this->tahunAjaranMatkul, $this->cpmkData);
        
        // Sheet Detail per CPMK
        foreach ($this->cpmkData as $index => $data) {
            $sheets[] = new CpmkDetailSheet($data, $index + 1);
        }
        
        return $sheets;
    }
}

class CpmkRingkasanSheet implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $tahunAjaranMatkul;
    protected $cpmkData;

    public function __construct($tahunAjaranMatkul, $cpmkData)
    {
        $this->tahunAjaranMatkul = $tahunAjaranMatkul;
        $this->cpmkData = $cpmkData;
    }

    public function title(): string
    {
        return 'Ringkasan CPMK';
    }

    public function collection()
    {
        $data = [];
        
        // Header informasi mata kuliah
        $data[] = ['LAPORAN PENGUKURAN CPMK'];
        $data[] = ['Mata Kuliah', $this->tahunAjaranMatkul->mataKuliah->namaMatkul];
        $data[] = ['Kode', $this->tahunAjaranMatkul->mataKuliah->kodeMatkul];
        $data[] = ['Tahun Ajaran', $this->tahunAjaranMatkul->tahunAjaran->tahun . ' - ' . ucfirst($this->tahunAjaranMatkul->tahunAjaran->periode)];
        $data[] = ['Dicetak pada', now()->format('d/m/Y H:i')];
        $data[] = []; // Empty row
        
        // Header tabel
        $data[] = ['CPMK', 'Total Mahasiswa', 'Dengan Nilai', 'Rata-rata', 'Kompeten (%)', 'Tidak Kompeten (%)'];
        
        // Data CPMK
        foreach ($this->cpmkData as $dataItem) {
            $data[] = [
                $dataItem['cpmk']->kodeCpmk . ' - ' . $dataItem['cpmk']->deskripsi,
                $dataItem['totalMahasiswa'],
                $dataItem['mahasiswaDenganNilai'],
                $dataItem['averageNilai'],
                $dataItem['competentPercentage'],
                $dataItem['notCompetentPercentage']
            ];
        }
        
        return collect($data);
    }

    public function headings(): array
    {
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 50,
            'B' => 15,
            'C' => 15,
            'D' => 15,
            'E' => 15,
            'F' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = count($this->cpmkData) + 8;
        
        // Style header laporan
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Style informasi mata kuliah
        $sheet->getStyle('A2:A6')->getFont()->setBold(true);
        
        // Style header tabel
        $sheet->getStyle('A8:F8')->getFont()->setBold(true);
        $sheet->getStyle('A8:F8')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E5E7EB');
        $sheet->getStyle('A8:F8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Style data
        $sheet->getStyle('A9:F' . $lastRow)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        
        // Border
        $sheet->getStyle('A8:F' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        return $sheet;
    }
}

class CpmkDetailSheet implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $data;
    protected $sheetNumber;

    public function __construct($data, $sheetNumber)
    {
        $this->data = $data;
        $this->sheetNumber = $sheetNumber;
    }

    public function title(): string
    {
        return 'CPMK-' . $this->sheetNumber;
    }

    public function collection()
    {
        $rows = [];
        
        // Header CPMK
        $rows[] = [$this->data['cpmk']->kodeCpmk . ' - ' . $this->data['cpmk']->deskripsi];
        $rows[] = [];
        
        // Statistik
        $rows[] = ['Total Mahasiswa', $this->data['totalMahasiswa']];
        $rows[] = ['Dengan Nilai', $this->data['mahasiswaDenganNilai']];
        $rows[] = ['Rata-rata', $this->data['averageNilai']];
        $rows[] = ['Kompeten (%)', $this->data['competentPercentage']];
        $rows[] = ['Tidak Kompeten (%)', $this->data['notCompetentPercentage']];
        $rows[] = [];
        
        // Distribusi Nilai
        $rows[] = ['DISTRIBUSI NILAI'];
        $rows[] = ['Nilai Angka', 'Nilai Mutu', 'Sebutan Mutu', 'Persentase'];
        $rows[] = ['Nilai < 60', 'U', 'Uncompetence', $this->data['distribution']['U']['percentage'] . '%'];
        $rows[] = ['60 ≤ Nilai < 75', 'C', 'Competence', $this->data['distribution']['C']['percentage'] . '%'];
        $rows[] = ['75 ≤ Nilai < 90', 'E', 'Excellent', $this->data['distribution']['E']['percentage'] . '%'];
        $rows[] = ['Nilai ≥ 90', 'X', 'Extraordinary', $this->data['distribution']['X']['percentage'] . '%'];
        $rows[] = [];
        
        // Histogram
        $rows[] = ['HISTOGRAM NILAI'];
        $rows[] = ['Range Nilai', 'Jumlah Mahasiswa', 'Persentase'];
        foreach ($this->data['histogramData'] as $histogram) {
            $rows[] = [
                $histogram['range'],
                $histogram['count'],
                $histogram['percentage'] . '%'
            ];
        }
        
        return collect($rows);
    }

    public function headings(): array
    {
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 15,
            'C' => 20,
            'D' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        
        // Style header CPMK
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
        
        // Style judul section
        $sheet->getStyle('A8')->getFont()->setBold(true);
        $sheet->getStyle('A16')->getFont()->setBold(true);
        
        // Style header tabel
        $sheet->getStyle('A9:D9')->getFont()->setBold(true);
        $sheet->getStyle('A9:D9')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E5E7EB');
        $sheet->getStyle('A17:C17')->getFont()->setBold(true);
        $sheet->getStyle('A17:C17')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E5E7EB');
        
        // Border untuk tabel distribusi
        $sheet->getStyle('A9:D14')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Border untuk tabel histogram
        $sheet->getStyle('A17:C' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        return $sheet;
    }
}
