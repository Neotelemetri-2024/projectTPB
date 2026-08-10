<?php

namespace App\Imports;

use App\Models\TahunAjaranMatkul;
use App\Models\TahunAjaran;
use App\Models\MataKuliah;
use App\Models\Dosen;
use App\Models\User;
use App\Models\Kelas;
use App\Models\DosenPengampuKelas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class TahunAjaranMatkulImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, WithChunkReading, ShouldQueue, WithEvents
{
    private $results = [
        'success' => 0,
        'created' => 0,
        'updated' => 0,
        'errors' => []
    ];

    public $jobId;

    public function __construct($jobId = null)
    {
        $this->jobId = $jobId;
    }

    public function model(array $row)
    {
        try {
            // Log untuk debugging
            Log::info('Processing row:', $row);

            // Ambil data dari row dengan case insensitive
            $tahunAjaran = trim($row['tahun_ajaran'] ?? $row['TAHUN_AJARAN'] ?? $row['Tahun_Ajaran'] ?? '');
            $kodeMatkul = trim($row['kode_matkul'] ?? $row['KODE_MATKUL'] ?? $row['Kode_Matkul'] ?? '');
            $mataKuliah = trim($row['mata_kuliah'] ?? $row['MATA_KULIAH'] ?? $row['Mata_Kuliah'] ?? '');
            $semester = trim($row['semester'] ?? $row['SEMESTER'] ?? '');
            $namaKelas = trim($row['nama_kelas'] ?? $row['NAMA_KELAS'] ?? $row['Nama_Kelas'] ?? '');
            $namaDosen = trim($row['nama_dosen'] ?? $row['NAMA_DOSEN'] ?? $row['Nama_Dosen'] ?? '');
            $nipDosen = trim($row['nip_dosen'] ?? $row['NIP_DOSEN'] ?? $row['Nip_Dosen'] ?? '');
            $emailDosen = trim($row['email_dosen'] ?? $row['EMAIL_DOSEN'] ?? $row['Email_Dosen'] ?? '');

            // Skip baris kosong ATAU baris contoh ATAU baris yang hanya berisi instruksi
            if (empty($kodeMatkul) || empty($mataKuliah) || strpos($tahunAjaran, 'Contoh:') !== false) {
                return null;
            }

            // Validasi data wajib
            if (empty($tahunAjaran)) {
                $this->results['errors'][] = "Tahun ajaran wajib diisi";
                return null;
            }

            if (empty($kodeMatkul)) {
                $this->results['errors'][] = "Kode mata kuliah wajib diisi";
                return null;
            }

            if (empty($mataKuliah)) {
                $this->results['errors'][] = "Mata kuliah wajib diisi";
                return null;
            }

            if (empty($semester)) {
                $this->results['errors'][] = "Semester wajib diisi";
                return null;
            }

            if (empty($namaKelas)) {
                $this->results['errors'][] = "Nama kelas wajib diisi";
                return null;
            }

            if (empty($namaDosen)) {
                $this->results['errors'][] = "Nama dosen wajib diisi";
                return null;
            }


            // Validasi format tahun ajaran (YYYY - Periode atau YYYY/YYYY - Periode)
            if (!preg_match('/^(\d{4}(?:\/\d{4})?)\s*-\s*(Ganjil|Genap)$/i', $tahunAjaran, $matches)) {
                $this->results['errors'][] = "Format tahun ajaran '{$tahunAjaran}' tidak valid (gunakan format: YYYY - Ganjil atau YYYY/YYYY - Ganjil)";
                return null;
            }

            $parsedTahun = $matches[1];
            $parsedPeriode = ucfirst(strtolower($matches[2]));

            // Validasi semester
            if (!is_numeric($semester) || $semester < 1 || $semester > 8) {
                $this->results['errors'][] = "Semester harus berupa angka 1-8";
                return null;
            }


            // Cari atau buat tahun ajaran
            $tahunAjaranModel = TahunAjaran::firstOrCreate(
                ['tahun' => $parsedTahun, 'periode' => $parsedPeriode]
            );

            // Cari mata kuliah berdasarkan kode dan nama
            $mataKuliahModel = MataKuliah::where('kodeMatkul', $kodeMatkul)
                ->where('namaMatkul', $mataKuliah)
                ->first();
            if (!$mataKuliahModel) {
                $this->results['errors'][] = "Mata kuliah dengan kode '{$kodeMatkul}' dan nama '{$mataKuliah}' tidak ditemukan di database";
                return null;
            }

            // Handle multiple dosen dengan titik koma
            $namaDosenArray = array_map('trim', explode(';', $namaDosen));
            $nipDosenArray = array_map('trim', explode(';', $nipDosen));
            $emailDosenArray = array_map('trim', explode(';', $emailDosen));

            // Cari atau buat tahun ajaran mata kuliah
            // NOTE: 'semester' sengaja TIDAK dimasukkan ke kunci pencarian firstOrCreate
            // agar tidak membuat duplikat TahunAjaranMatkul untuk mata kuliah + tahun ajaran
            // yang sama hanya karena perbedaan semester (lihat issue duplikat TAM).
            // Semester akan di-set hanya saat record baru dibuat.
            $tahunAjaranMatkul = TahunAjaranMatkul::firstOrCreate([
                'tahunAjaranId' => $tahunAjaranModel->id,
                'mataKuliahId' => $mataKuliahModel->id,
            ], [
                'sks' => $mataKuliahModel->sks,
                'semester' => $semester,
            ]);

            // Cari atau buat kelas
            $kelas = Kelas::firstOrCreate([
                'tahunAjaranMatkulId' => $tahunAjaranMatkul->id,
                'namaKelas' => $namaKelas,
            ]);

            // Proses setiap dosen
            foreach ($namaDosenArray as $index => $namaDosenSingle) {
                if (empty($namaDosenSingle)) continue;

                $nipDosenSingle = isset($nipDosenArray[$index]) && !empty($nipDosenArray[$index]) ? $nipDosenArray[$index] : '';
                $emailDosenSingle = isset($emailDosenArray[$index]) && !empty($emailDosenArray[$index]) ? $emailDosenArray[$index] : '';
                
                $isNipGenerated = false;

                if (empty($nipDosenSingle)) {
                    // Auto-generate NIP (e.g. 20260802134703 + 4 digits = 18 digits)
                    $nipDosenSingle = date('YmdHis') . rand(1000, 9999);
                    $isNipGenerated = true;
                } else {
                    // Bersihkan NIP dari format scientific notation
                    if (strpos($nipDosenSingle, 'E') !== false || strpos($nipDosenSingle, 'e') !== false) {
                        $nipDosenSingle = number_format((float)$nipDosenSingle, 0, '', '');
                    }
                    
                    // Validasi NIP (harus 15-20 digit)
                    if (strlen($nipDosenSingle) < 15 || strlen($nipDosenSingle) > 20 || !is_numeric($nipDosenSingle)) {
                        $this->results['errors'][] = "NIP {$namaDosenSingle} harus 15-20 digit angka (ditemukan: " . strlen($nipDosenSingle) . " digit)";
                        continue;
                    }
                }

                if (empty($emailDosenSingle)) {
                    // Auto-generate email based on name and random number
                    $cleanName = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $namaDosenSingle));
                    $emailDosenSingle = $cleanName . rand(100, 999) . '@generated.com';
                } else {
                    // Validasi format email
                    if (!filter_var($emailDosenSingle, FILTER_VALIDATE_EMAIL)) {
                        $this->results['errors'][] = "Format email '{$emailDosenSingle}' untuk dosen '{$namaDosenSingle}' tidak valid";
                        continue;
                    }
                }

                // Cari atau buat dosen
                $dosen = Dosen::where('nip', $nipDosenSingle)->first();
            
            if (!$dosen) {
                    // Cek apakah email sudah digunakan
                    $existingUser = User::where('email', $emailDosenSingle)->first();
                    if ($existingUser) {
                        $this->results['errors'][] = "Email {$emailDosenSingle} sudah digunakan oleh user lain";
                        continue;
                    }

                    // Tentukan password (jika NIP digenerate maka "password123", jika tidak maka gunakan NIP)
                    $password = $isNipGenerated ? 'password123' : $nipDosenSingle;

                    // Buat user baru
                    $user = User::create([
                        'name' => $namaDosenSingle,
                        'email' => $emailDosenSingle,
                        'password' => Hash::make($password), 
                    'role' => 'dosen',
                ]);

                    // Buat dosen baru
                $dosen = Dosen::create([
                        'nama' => $namaDosenSingle,
                        'nip' => $nipDosenSingle,
                    'userId' => $user->id,
                    ]);

                    Log::info("Dosen created: {$namaDosenSingle} (NIP: {$nipDosenSingle}, Email: {$emailDosenSingle})");
                } else {
                    // Update nama dosen jika berbeda
                    if ($dosen->nama !== $namaDosenSingle) {
                        $dosen->update(['nama' => $namaDosenSingle]);
                        
                        // Update nama user juga
                        if ($dosen->user) {
                            $dosen->user->update(['name' => $namaDosenSingle]);
                        }
                    }
            }
            
                // Cari atau buat dosen pengampu kelas
                $dosenPengampuKelas = DosenPengampuKelas::firstOrCreate([
                    'dosenId' => $dosen->id,
                    'kelasId' => $kelas->id,
                ]);

                $this->results['success']++;
            }

            if ($this->jobId) {
                Cache::increment('import_progress_' . $this->jobId . '_processed');
            }

            Log::info("TahunAjaranMatkul processed: {$mataKuliah} - {$tahunAjaran} - {$namaKelas}");

            return $tahunAjaranMatkul;

        } catch (\Exception $e) {
            $this->results['errors'][] = "Error processing row: " . $e->getMessage();
            Log::error("Error importing TahunAjaranMatkul: " . $e->getMessage(), ['row' => $row]);
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'tahun_ajaran' => 'nullable|string|max:20',
            'kode_matkul' => 'nullable|string|max:20',
            'mata_kuliah' => 'nullable|string|max:255',
            'semester' => 'nullable|numeric|min:1|max:8',
            'nama_kelas' => 'nullable|string|max:10',
            'nama_dosen' => 'nullable|string|max:255',
            'nip_dosen' => 'nullable|max:20',
            'email_dosen' => 'nullable|max:255',
            'TAHUN_AJARAN' => 'nullable|string|max:20',
            'KODE_MATKUL' => 'nullable|string|max:20',
            'MATA_KULIAH' => 'nullable|string|max:255',
            'SEMESTER' => 'nullable|numeric|min:1|max:8',
            'NAMA_KELAS' => 'nullable|string|max:10',
            'NAMA_DOSEN' => 'nullable|string|max:255',
            'NIP_DOSEN' => 'nullable|max:20',
            'EMAIL_DOSEN' => 'nullable|max:255',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'tahun_ajaran.string' => 'Tahun ajaran harus berupa teks',
            'tahun_ajaran.max' => 'Tahun ajaran maksimal 20 karakter',
            'kode_matkul.string' => 'Kode mata kuliah harus berupa teks',
            'kode_matkul.max' => 'Kode mata kuliah maksimal 20 karakter',
            'mata_kuliah.string' => 'Mata kuliah harus berupa teks',
            'mata_kuliah.max' => 'Mata kuliah maksimal 255 karakter',
            'semester.numeric' => 'Semester harus berupa angka',
            'semester.min' => 'Semester minimal 1',
            'semester.max' => 'Semester maksimal 8',
            'nama_kelas.string' => 'Nama kelas harus berupa teks',
            'nama_kelas.max' => 'Nama kelas maksimal 10 karakter',
            'nama_dosen.string' => 'Nama dosen harus berupa teks',
            'nama_dosen.max' => 'Nama dosen maksimal 255 karakter',
            'nip_dosen.max' => 'NIP dosen maksimal 20 karakter',
            'email_dosen.max' => 'Email dosen maksimal 255 karakter',
        ];
    }

    public function onError(\Throwable $e)
    {
        $this->results['errors'][] = $e->getMessage();
        Log::error("Import error: " . $e->getMessage());
    }

    public function getImportResults()
    {
        return $this->results;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => [$this, 'beforeImport'],
        ];
    }

    public function beforeImport(BeforeImport $event)
    {
        if ($this->jobId) {
            $totalRows = array_sum($event->reader->getTotalRows());
            // Substract 3 for the heading rows we skip
            $totalRows = max(0, $totalRows - 3);
            Cache::put('import_progress_' . $this->jobId . '_total', $totalRows, 3600);
            Cache::put('import_progress_' . $this->jobId . '_processed', 0, 3600);
        }
    }

    /**
     * Custom heading row untuk menangani berbagai format header
     */
    public function headingRow(): int
    {
        return 3; // Skip 2 baris pertama (info template), header di baris 3
    }

    /**
     * Transform heading untuk menangani case sensitivity
     */
    public function transformHeading(string $heading): string
    {
        return strtolower(trim($heading));
    }

    /**
     * Skip empty rows
     */
    public function startRow(): int
    {
        return 4; // Mulai dari baris 4 (setelah header)
    }
} 