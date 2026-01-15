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

class TahunAjaranMatkulTemplateExport implements WithMultipleSheets
{
    protected $tahunAjaranId;

    public function __construct($tahunAjaranId = null)
    {
        $this->tahunAjaranId = $tahunAjaranId;
    }

    public function sheets(): array
    {
        return [
            'Template' => new TahunAjaranMatkulTemplateSheet($this->tahunAjaranId),
            'Instruksi' => new InstruksiTahunAjaranMatkulSheet(),
        ];
    }
}

class TahunAjaranMatkulTemplateSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    protected $tahunAjaranId;
    protected $tahunAjaranText;

    public function __construct($tahunAjaranId = null)
    {
        $this->tahunAjaranId = $tahunAjaranId;
        $this->tahunAjaranText = $this->getTahunAjaranText();
    }

    protected function getTahunAjaranText()
    {
        if (!$this->tahunAjaranId) {
            return '2025 Ganjil'; // Default
        }

        $tahunAjaran = \App\Models\TahunAjaran::find($this->tahunAjaranId);
        if ($tahunAjaran) {
            return $tahunAjaran->tahun . ' - ' . $tahunAjaran->periode;
        }

        return '2025/2026 - Ganjil'; // Fallback
    }

    public function collection()
    {
        // Generate 50 empty rows
        $rows = [];
        
        // Tambahkan beberapa contoh data dengan tahun ajaran yang dipilih
        $rows[] = [
            'tahun_ajaran' => $this->tahunAjaranText,
            'kode_matkul' => 'MTK101',
            'mata_kuliah' => 'Matematika Dasar',
            'semester' => '1',
            'nama_kelas' => 'A',
            'nama_dosen' => 'Dr. John Doe',
            'nip_dosen' => '197304131998022001',
            'email_dosen' => 'johndoe@ae.unand.ac.id'
        ];
        
        $rows[] = [
            'tahun_ajaran' => $this->tahunAjaranText,
            'kode_matkul' => 'MTK101',
            'mata_kuliah' => 'Matematika Dasar',
            'semester' => '1',
            'nama_kelas' => 'A',
            'nama_dosen' => 'Dr. Jane Smith',
            'nip_dosen' => '197304131998022002',
            'email_dosen' => 'janesmith@ae.unand.ac.id'
        ];
        
        $rows[] = [
            'tahun_ajaran' => $this->tahunAjaranText,
            'kode_matkul' => 'MTK101',
            'mata_kuliah' => 'Matematika Dasar',
            'semester' => '1',
            'nama_kelas' => 'B',
            'nama_dosen' => 'Dr. John Doe',
            'nip_dosen' => '197304131998022001',
            'email_dosen' => 'johndoe@ae.unand.ac.id'
        ];
        
        // Generate empty rows dengan tahun ajaran yang dipilih
        for ($i = 1; $i <= 47; $i++) {
            $rows[] = [
                'tahun_ajaran' => $this->tahunAjaranText,
                'kode_matkul' => '',
                'mata_kuliah' => '',
                'semester' => '',
                'nama_kelas' => '',
                'nama_dosen' => '',
                'nip_dosen' => '',
                'email_dosen' => ''
            ];
        }
        return collect($rows);
    }

    public function headings(): array
    {
        return [
            ['FORMAT IMPORT MATA KULIAH TAHUN AJARAN (PER KELAS)'],
            [''],
            ['TAHUN_AJARAN', 'KODE_MATKUL', 'MATA_KULIAH', 'SEMESTER', 'NAMA_KELAS', 'NAMA_DOSEN', 'NIP_DOSEN', 'EMAIL_DOSEN'],
            ['Contoh: 2025 - Ganjil', 'Contoh: MTK101', 'Contoh: Matematika Dasar', 'Contoh: 1', 'Contoh: A', 'Contoh: Dr. John Doe', 'Contoh: 197304131998022001', 'Contoh: johndoe@ae.unand.ac.id']
        ];
    }

    public function map($row): array
    {
        return [
            $row['tahun_ajaran'],
            $row['kode_matkul'],
            $row['mata_kuliah'],
            $row['semester'],
            $row['nama_kelas'],
            $row['nama_dosen'],
            $row['nip_dosen'],
            $row['email_dosen']
        ];
    }

    public function title(): string
    {
        return 'Template';
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk header utama
        $sheet->getStyle('A1:H1')->applyFromArray([
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
        $sheet->mergeCells('A1:H1');

        // Style untuk header kolom
        $sheet->getStyle('A3:H3')->applyFromArray([
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
        $sheet->getStyle('A4:H4')->applyFromArray([
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
        $sheet->getStyle('A3:H53')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
    }
}

class InstruksiTahunAjaranMatkulSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    public function collection()
    {
        return collect([
            ['INSTRUKSI PENGISIAN TEMPLATE IMPORT MATA KULIAH TAHUN AJARAN (PER KELAS)'],
            [''],
            ['1. Format File:', 'Excel (.xlsx atau .xls)'],
            ['2. Kolom yang Harus Diisi:', ''],
            ['   - TAHUN_AJARAN', 'Tahun ajaran (contoh: 2025 - Ganjil, 2025 - Genap)'],
            ['   - KODE_MATKUL', 'Kode mata kuliah (harus sudah ada di database)'],
            ['   - MATA_KULIAH', 'Nama mata kuliah (harus sudah ada di database)'],
            ['   - SEMESTER', 'Semester (1-8)'],
            ['   - NAMA_KELAS', 'Nama kelas (contoh: A, B, C, atau 1, 2, 3)'],
            ['   - NAMA_DOSEN', 'Nama lengkap dosen (termasuk gelar jika ada)'],
            ['   - NIP_DOSEN', 'Nomor Induk Pegawai dosen (18 digit)'],
            ['   - EMAIL_DOSEN', 'Alamat email dosen'],
            [''],
            ['3. Aturan Pengisian:', ''],
            ['   - TAHUN_AJARAN', 'Sudah diisi otomatis sesuai tahun ajaran yang dipilih'],
            ['   - KODE_MATKUL', 'Wajib diisi, harus sesuai dengan kode di database'],
            ['   - MATA_KULIAH', 'Wajib diisi, harus sesuai dengan nama di database'],
            ['   - SEMESTER', 'Wajib diisi, angka 1-8'],
            ['   - NAMA_KELAS', 'Wajib diisi, nama kelas (A, B, C, atau 1, 2, 3)'],
            ['   - NAMA_DOSEN', 'Wajib diisi, maksimal 255 karakter'],
            ['   - NIP_DOSEN', 'Wajib diisi, harus 18 digit angka'],
            ['   - EMAIL_DOSEN', 'Wajib diisi, format email yang valid'],
            [''],
            ['4. Contoh Pengisian:', ''],
            ['   TAHUN_AJARAN', 'Sudah diisi otomatis (contoh: 2025 - Ganjil)'],
            ['   KODE_MATKUL', 'MTK101'],
            ['   MATA_KULIAH', 'Matematika Dasar'],
            ['   SEMESTER', '1'],
            ['   NAMA_KELAS', 'A'],
            ['   NAMA_DOSEN', 'Dr. John Doe'],
            ['   NIP_DOSEN', '197304131998022001'],
            ['   EMAIL_DOSEN', 'johndoe@ae.unand.ac.id'],
            [''],
            ['5. Contoh Multiple Dosen:', ''],
            ['   Cara 1 - Baris Terpisah:', ''],
            ['   Baris 1:', '2025 - Ganjil | MTK101 | Matematika Dasar | 1 | A | Dr. John Doe | 197304131998022001 | johndoe@ae.unand.ac.id'],
            ['   Baris 2:', '2025 - Ganjil | MTK101 | Matematika Dasar | 1 | A | Dr. Jane Smith | 197304131998022002 | janesmith@ae.unand.ac.id'],
            ['   Baris 3:', '2025 - Ganjil | MTK101 | Matematika Dasar | 1 | B | Dr. John Doe | 197304131998022001 | johndoe@ae.unand.ac.id'],
            [''],
            ['   Cara 2 - Satu Baris (dengan titik koma):', ''],
            ['   Baris 1:', '2025 - Ganjil | MTK101 | Matematika Dasar | 1 | A | Dr. John Doe; Dr. Jane Smith | 197304131998022001; 197304131998022002 | johndoe@ae.unand.ac.id; janesmith@ae.unand.ac.id'],
            [''],
            ['6. Catatan:', ''],
            ['   - Satu baris = satu kelas untuk mata kuliah tertentu'],
            ['   - Jika mata kuliah sama, semester sama, buat baris terpisah per kelas'],
            ['   - Untuk multiple dosen: buat baris terpisah per dosen per kelas'],
            ['   - Jika dosen belum ada, akan dibuat akun baru dengan password = NIP'],
            ['   - Jika dosen sudah ada (berdasarkan NIP), data akan diupdate'],
            ['   - Pastikan email tidak duplikat dengan dosen lain'],
            ['   - Mata kuliah harus sudah terdaftar di database'],
            [''],
            ['7. Error yang Mungkin Terjadi:', ''],
            ['   - Mata kuliah tidak ditemukan', 'Periksa nama dan kode mata kuliah di database'],
            ['   - Kode mata kuliah tidak ditemukan', 'Periksa kode mata kuliah di database'],
            ['   - Nama kelas kosong', 'Nama kelas wajib diisi'],
            ['   - NIP tidak 18 digit', 'Periksa kembali jumlah digit NIP'],
            ['   - Email sudah digunakan', 'Gunakan email yang berbeda'],
            ['   - Format email tidak valid', 'Pastikan format email benar'],
            ['   - Tahun ajaran tidak valid', 'Gunakan format YYYY - Ganjil/Genap'],
            ['   - Semester tidak valid', 'Gunakan angka 1-8'],
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