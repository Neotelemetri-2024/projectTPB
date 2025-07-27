<?php

namespace App\Exports;

use App\Models\TahunAjaranMatkul;
use App\Models\Komponen;
use App\Models\Mahasiswa;
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

class NilaiTemplateExport implements WithMultipleSheets
{
    protected $tahunAjaranMatkulId;
    protected $tahunAjaranMatkul;
    protected $komponen;
    protected $mahasiswa;

    public function __construct($tahunAjaranMatkulId)
    {
        $this->tahunAjaranMatkulId = $tahunAjaranMatkulId;
        
        // Load data mata kuliah dan tahun ajaran
        $this->tahunAjaranMatkul = TahunAjaranMatkul::with(['mataKuliah', 'tahunAjaran'])
            ->findOrFail($tahunAjaranMatkulId);
        
        // Load komponen yang sudah ada bobot di mata kuliah ini (tidak semua komponen)
        $this->komponen = \App\Models\Komponen::whereHas('bobot', function($query) use ($tahunAjaranMatkulId) {
            $query->where('tahunAjaranMatkulId', $tahunAjaranMatkulId);
        })->orderBy('nama')->get();
        
        // Load semua mahasiswa dari semua kelas di mata kuliah dan tahun ajaran yang sama
        $allTahunAjaranMatkul = \App\Models\TahunAjaranMatkul::where('mataKuliahId', $this->tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $this->tahunAjaranMatkul->tahunAjaranId)
            ->with('kelas.kelasMahasiswa.mahasiswa')
            ->get();
        
        $this->mahasiswa = collect();
        foreach ($allTahunAjaranMatkul as $tam) {
            foreach ($tam->kelas as $kelas) {
                foreach ($kelas->kelasMahasiswa as $kelasMahasiswa) {
                    $this->mahasiswa->push([
                        'mahasiswa' => $kelasMahasiswa->mahasiswa,
                        'kelas' => $kelas->namaKelas
                    ]);
                }
            }
        }
        $this->mahasiswa = $this->mahasiswa->unique(function($item) {
            return $item['mahasiswa']->id;
        })->sortBy(function($item) {
            return $item['mahasiswa']->nim;
        })->values();
    }

    public function sheets(): array
    {
        return [
            new NilaiTemplateSheet($this->tahunAjaranMatkul, $this->komponen, $this->mahasiswa),
            new InstruksiSheet(),
        ];
    }
}

class NilaiTemplateSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    protected $tahunAjaranMatkul;
    protected $komponen;
    protected $mahasiswa;

    public function __construct($tahunAjaranMatkul, $komponen, $mahasiswa)
    {
        $this->tahunAjaranMatkul = $tahunAjaranMatkul;
        $this->komponen = $komponen;
        $this->mahasiswa = $mahasiswa;
    }

    public function collection()
    {
        // Buat 50 baris kosong untuk dosen isi sendiri
        $emptyRows = collect();
        for ($i = 1; $i <= 50; $i++) {
            $emptyRows->push(['row' => $i]);
        }
        return $emptyRows;
    }

    public function headings(): array
    {
        $headings = [
            'NIM',
            'Nama Mahasiswa',
            'Kelas'
        ];

        // Tambahkan kolom untuk setiap komponen
        foreach ($this->komponen as $komponen) {
            $headings[] = $komponen->nama;
        }

        // Tambahkan header informasi mata kuliah di baris pertama
        $infoHeader = [
            'TEMPLATE INPUT NILAI',
            '',
            '',
            'Mata Kuliah: ' . $this->tahunAjaranMatkul->mataKuliah->namaMatkul,
            'Kode: ' . $this->tahunAjaranMatkul->mataKuliah->kodeMatkul,
            'Tahun Ajaran: ' . $this->tahunAjaranMatkul->tahunAjaran->tahun . ' - ' . $this->tahunAjaranMatkul->tahunAjaran->periode,
            'SKS: ' . $this->tahunAjaranMatkul->mataKuliah->sks,
        ];

        return [
            $infoHeader, // Baris 1: Info mata kuliah
            [], // Baris 2: Kosong
            $headings, // Baris 3: Header kolom
        ];
    }

    public function map($emptyRow): array
    {
        $row = [
            '', // NIM kosong (untuk diisi dosen)
            '', // Nama mahasiswa kosong (untuk diisi dosen)
            ''  // Kelas kosong (untuk diisi dosen)
        ];

        // Tambahkan kolom kosong untuk setiap komponen (untuk diisi dosen)
        foreach ($this->komponen as $komponen) {
            $row[] = ''; // Kosong untuk diisi
        }

        return $row;
    }

    public function title(): string
    {
        return 'Template Nilai';
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

class InstruksiSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    public function collection()
    {
        return collect([
            ['Isi semua kolom: NIM, Nama Mahasiswa, Kelas, dan nilai pada kolom komponen'],
            ['Nilai harus berupa angka antara 0-100'],
            ['Jika mahasiswa tidak ada nilai untuk komponen tertentu, biarkan kosong'],
            ['Semua kolom harus diisi oleh dosen (NIM, Nama, Kelas, Nilai)'],
            ['Jika ada mahasiswa baru (NIM belum terdaftar), sistem akan otomatis membuat akun dengan password = NIM'],
            ['Setelah mengisi, simpan file dan upload kembali ke sistem'],
            ['Tersedia 50 baris kosong untuk diisi'],
            [''],
            ['Contoh pengisian:'],
            ['NIM: 1410250635, Nama: FIFI SUSANTI, Kelas: A, UAS: 85, UTS: 80, TUGAS: 90'],
            ['NIM: 1410250642, Nama: RIZKY RAMADHANI, Kelas: B, UAS: 78, UTS: 85, TUGAS: 88'],
        ]);
    }

    public function headings(): array
    {
        return [
            'INSTRUKSI PENGISIAN TEMPLATE NILAI',
        ];
    }

    public function title(): string
    {
        return 'Instruksi';
    }
}
