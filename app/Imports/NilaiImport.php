<?php

namespace App\Imports;

use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\TahunAjaranMatkul;
use App\Models\Kelas;
use App\Models\KelasMahasiswa;
use App\Models\Komponen;
use App\Models\Bobot;
use App\Models\Nilai;
use App\Models\DosenPengampuKelas;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class NilaiImport implements WithMultipleSheets
{
    protected $tahunAjaranMatkulId;
    protected $dosenId;
    protected $importSheet;

    public function __construct($tahunAjaranMatkulId, $dosenId)
    {
        $this->tahunAjaranMatkulId = $tahunAjaranMatkulId;
        $this->dosenId = $dosenId;
        $this->importSheet = new NilaiImportSheet($tahunAjaranMatkulId, $dosenId);
    }

    public function sheets(): array
    {
        return [
            'Template Nilai' => $this->importSheet,
        ];
    }

    public function getImportResults()
    {
        return $this->importSheet->getImportResults();
    }
}

class NilaiImportSheet implements ToCollection
{
    protected $tahunAjaranMatkulId;
    protected $dosenId;
    protected $tahunAjaranMatkul;
    protected $komponen;
    protected $importResults = [
        'success' => 0,
        'created_students' => 0,
        'updated_grades' => 0,
        'errors' => [],
        'created_student_list' => []
    ];

    public function __construct($tahunAjaranMatkulId, $dosenId)
    {
        $this->tahunAjaranMatkulId = $tahunAjaranMatkulId;
        $this->dosenId = $dosenId;
        
        // Load data yang diperlukan
        $this->tahunAjaranMatkul = TahunAjaranMatkul::with(['mataKuliah', 'tahunAjaran'])
            ->findOrFail($tahunAjaranMatkulId);
        
        // Load komponen yang sudah ada bobot di mata kuliah ini (tidak semua komponen)
        $this->komponen = Komponen::whereHas('bobot', function($query) use ($tahunAjaranMatkulId) {
            $query->where('tahunAjaranMatkulId', $tahunAjaranMatkulId);
        })->orderBy('nama')->get()->keyBy('nama');
    }

    public function collection(Collection $rows)
    {
        try {
            DB::beginTransaction();

            // Skip header rows (baris 1-3)
            $dataRows = $rows->skip(3);

            foreach ($dataRows as $rowIndex => $row) {
                $actualRowNumber = $rowIndex + 4; // Karena skip 3 baris

                try {
                    $this->processRow($row, $actualRowNumber);
                } catch (\Exception $e) {
                    $this->importResults['errors'][] = "Baris {$actualRowNumber}: " . $e->getMessage();
                    \Log::error("Import error at row {$actualRowNumber}: " . $e->getMessage(), ['row' => $row->toArray()]);
                }
            }

            DB::commit();
            \Log::info('Import completed', $this->importResults);
            
        } catch (\Exception $e) {
            DB::rollback();
            $this->importResults['errors'][] = "Error umum: " . $e->getMessage();
            \Log::error('Import failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }
    }

    private function processRow($row, $rowNumber)
    {
        // Ambil data dari row
        $nim = trim($row[0] ?? '');
        $nama = trim($row[1] ?? '');
        $kelasNama = trim($row[2] ?? '');

        // Skip jika NIM kosong
        if (empty($nim)) {
            return;
        }

        // Validate NIM format (exactly 10 digits)
        if (!preg_match('/^\d{10}$/', $nim)) {
            throw new \Exception("NIM harus 10 digit angka (sekarang: {$nim})");
        }

        // Validate required fields
        if (empty($nama)) {
            throw new \Exception("Nama mahasiswa tidak boleh kosong");
        }
        if (empty($kelasNama)) {
            throw new \Exception("Kelas tidak boleh kosong");
        }

        // Cari atau buat mahasiswa
        $mahasiswa = $this->findOrCreateMahasiswa($nim, $nama);

        // Pastikan mahasiswa terdaftar di kelas yang sesuai
        $this->ensureStudentInClass($mahasiswa, $kelasNama);

        // Proses nilai untuk setiap komponen
        $komponenIndex = 3; // Kolom komponen mulai dari index 3
        foreach ($this->komponen as $komponenNama => $komponen) {
            $nilaiValue = $row[$komponenIndex] ?? '';
            
            if (!empty($nilaiValue) && is_numeric($nilaiValue)) {
                $nilai = (float)$nilaiValue;
                
                // Validate nilai range (0-100)
                if ($nilai < 0 || $nilai > 100) {
                    throw new \Exception("Nilai {$komponenNama} harus antara 0-100 (sekarang: {$nilai})");
                }
                
                $this->saveNilai($mahasiswa->id, $komponen->id, $nilai);
                $this->importResults['updated_grades']++;
            }
            
            $komponenIndex++;
        }

        $this->importResults['success']++;
    }

    private function findOrCreateMahasiswa($nim, $nama)
    {
        // Cari mahasiswa berdasarkan NIM
        $mahasiswa = Mahasiswa::where('nim', $nim)->first();

        if ($mahasiswa) {
            // Jika mahasiswa ada, update nama jika perlu
            if (!empty($nama) && $mahasiswa->nama !== $nama) {
                $mahasiswa->update(['nama' => $nama]);
            }
            return $mahasiswa;
        }

        // Jika mahasiswa belum ada, buat baru
        if (empty($nama)) {
            throw new \Exception("Nama mahasiswa tidak boleh kosong untuk NIM baru: {$nim}");
        }

        // Ambil tahun masuk dari 2 digit pertama NIM
        $tahunMasukFromNim = substr($nim, 0, 2);
        $tahunMasuk = 2000 + intval($tahunMasukFromNim); // 22 -> 2022

        // Generate email format: nim_namaawal@student.unand.ac.id (lowercase)
        $namaAwal = strtolower(explode(' ', trim($nama))[0]); // Ambil kata pertama dari nama dan lowercase
        $email = strtolower($nim . '_' . $namaAwal . '@student.unand.ac.id');

        // Buat user terlebih dahulu
        $user = User::create([
            'name' => $nama,
            'email' => $email,
            'password' => Hash::make($nim), // Password = NIM
            'role' => 'mahasiswa'
        ]);

        // Buat mahasiswa
        $mahasiswa = Mahasiswa::create([
            'userId' => $user->id,
            'nim' => $nim,
            'nama' => $nama,
            'tahunMasuk' => $tahunMasuk,
        ]);

        $this->importResults['created_students']++;
        $this->importResults['created_student_list'][] = [
            'nim' => $nim,
            'nama' => $nama,
            'email' => $user->email,
            'password' => $nim,
            'tahun_masuk' => $tahunMasuk
        ];

        return $mahasiswa;
    }

    private function ensureStudentInClass($mahasiswa, $kelasNama)
    {
        // Cari semua tahun ajaran matkul untuk mata kuliah dan tahun ajaran yang sama
        $allTahunAjaranMatkul = TahunAjaranMatkul::where('mataKuliahId', $this->tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $this->tahunAjaranMatkul->tahunAjaranId)
            ->with('kelas')
            ->get();

        $kelas = null;
        $tahunAjaranMatkulId = null;

        // Cari kelas berdasarkan nama di semua tahun ajaran matkul
        foreach ($allTahunAjaranMatkul as $tam) {
            $foundKelas = $tam->kelas->where('namaKelas', $kelasNama)->first();
            if ($foundKelas) {
                $kelas = $foundKelas;
                $tahunAjaranMatkulId = $tam->id;
                break;
            }
        }

        if (!$kelas) {
            // Jika kelas tidak ditemukan, gunakan kelas pertama yang tersedia
            foreach ($allTahunAjaranMatkul as $tam) {
                if ($tam->kelas->isNotEmpty()) {
                    $kelas = $tam->kelas->first();
                    $tahunAjaranMatkulId = $tam->id;
                    break;
                }
            }
            
            if (!$kelas) {
                throw new \Exception("Tidak ada kelas tersedia untuk mata kuliah ini");
            }
        }

        // Cek apakah mahasiswa sudah terdaftar di kelas
        $kelasMahasiswa = KelasMahasiswa::where('mahasiswaId', $mahasiswa->id)
            ->where('kelasId', $kelas->id)
            ->first();

        if (!$kelasMahasiswa) {
            // Daftarkan mahasiswa ke kelas
            KelasMahasiswa::create([
                'mahasiswaId' => $mahasiswa->id,
                'kelasId' => $kelas->id,
                'tahunAjaranMatkulId' => $tahunAjaranMatkulId,
            ]);
        }
    }

    private function saveNilai($mahasiswaId, $komponenId, $nilaiValue)
    {
        // Cari semua tahun ajaran matkul untuk mata kuliah dan tahun ajaran yang sama
        $allTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $this->tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $this->tahunAjaranMatkul->tahunAjaranId)
            ->pluck('id');

        // Cari semua bobot untuk komponen ini di semua kelas mata kuliah ini
        $bobotList = Bobot::where('komponenId', $komponenId)
            ->whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds)
            ->with('cpmk')
            ->get();

        if ($bobotList->isEmpty()) {
            // Jika belum ada bobot, skip (harus setup CPMK dan bobot dulu)
            return;
        }

        // Untuk setiap bobot, simpan nilai
        foreach ($bobotList as $bobot) {
            // Get dosen pengampu kelas untuk tahun ajaran matkul ini
            $dosenPengampuKelas = DosenPengampuKelas::where('dosenId', $this->dosenId)
                ->whereHas('kelas', function($query) use ($bobot) {
                    $query->where('tahunAjaranMatkulId', $bobot->tahunAjaranMatkulId);
                })
                ->first();

            if ($dosenPengampuKelas) {
                Nilai::updateOrCreate([
                    'mahasiswaId' => $mahasiswaId,
                    'tahunAjaranMatkulId' => $bobot->tahunAjaranMatkulId,
                    'bobotId' => $bobot->id,
                ], [
                    'cpmkId' => $bobot->cpmkId,
                    'dosenPengampuKelasId' => $dosenPengampuKelas->id,
                    'nilai' => $nilaiValue, // Simpan nilai apa adanya
                ]);
            }
        }
    }

    public function getImportResults()
    {
        return $this->importResults;
    }
} 