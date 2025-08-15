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
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Support\Facades\Log;

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

class NilaiImportSheet implements ToCollection, WithChunkReading, WithBatchInserts
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

    // Cache untuk data yang sering diakses
    protected $mahasiswaCache = [];
    protected $kelasCache = [];
    protected $bobotCache = [];
    protected $dosenPengampuCache = [];

    public function __construct($tahunAjaranMatkulId, $dosenId)
    {
        $this->tahunAjaranMatkulId = $tahunAjaranMatkulId;
        $this->dosenId = $dosenId;
        
        // Pre-load semua data yang diperlukan untuk menghindari N+1 queries
        $this->preloadData();
    }

    /**
     * Pre-load semua data yang diperlukan
     */
    private function preloadData()
    {
        // Load tahun ajaran matkul dengan relasi
        $this->tahunAjaranMatkul = TahunAjaranMatkul::with(['mataKuliah', 'tahunAjaran'])
            ->findOrFail($this->tahunAjaranMatkulId);
        
        // Load komponen yang sudah ada bobot
        $this->komponen = Komponen::whereHas('bobot', function($query) {
            $query->where('tahunAjaranMatkulId', $this->tahunAjaranMatkulId);
        })->orderBy('nama')->get()->keyBy('nama');

        // Pre-load semua mahasiswa yang mungkin ada
        $this->mahasiswaCache = Mahasiswa::select('id', 'nim', 'nama', 'userId')
            ->get()
            ->keyBy('nim');

        // Pre-load semua kelas untuk mata kuliah ini
        $this->kelasCache = Kelas::whereHas('tahunAjaranMatkul', function($query) {
            $query->where('mataKuliahId', $this->tahunAjaranMatkul->mataKuliahId)
                  ->where('tahunAjaranId', $this->tahunAjaranMatkul->tahunAjaranId);
        })->with('tahunAjaranMatkul')->get();

        // Pre-load semua bobot untuk komponen ini
        $allTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $this->tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $this->tahunAjaranMatkul->tahunAjaranId)
            ->pluck('id');

        $this->bobotCache = Bobot::whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds)
            ->with('cpmk')
            ->get()
            ->groupBy('komponenId');

        // Pre-load dosen pengampu kelas
        $this->dosenPengampuCache = DosenPengampuKelas::where('dosenId', $this->dosenId)
            ->whereHas('kelas', function($query) use ($allTahunAjaranMatkulIds) {
                $query->whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds);
            })
            ->get()
            ->keyBy('kelasId');
    }

    /**
     * Chunk size untuk membaca Excel
     */
    public function chunkSize(): int
    {
        return 50; // Proses 50 rows per chunk
    }

    /**
     * Batch size untuk insert
     */
    public function batchSize(): int
    {
        return 100; // Insert 100 records per batch
    }

    public function collection(Collection $rows)
    {
        try {
            // Skip header rows (baris 1-3)
            $dataRows = $rows->skip(3);

            // Batch process data
            $this->processBatch($dataRows);
            
        } catch (\Exception $e) {
            $this->importResults['errors'][] = "Error dalam chunk ini: " . $e->getMessage();
            Log::error('Import chunk error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }
    }

    /**
     * Process data dalam batch untuk performa yang lebih baik
     */
    private function processBatch(Collection $dataRows)
    {
        $mahasiswaToCreate = [];
        $kelasMahasiswaToCreate = [];
        $nilaiToCreate = [];
        $nilaiToUpdate = [];

        foreach ($dataRows as $rowIndex => $row) {
            $actualRowNumber = $rowIndex + 4;

            try {
                $result = $this->processRow($row, $actualRowNumber);
                
                if ($result) {
                    // Collect data untuk batch insert/update
                    if (isset($result['mahasiswa'])) {
                        $mahasiswaToCreate[] = $result['mahasiswa'];
                    }
                    if (isset($result['kelasMahasiswa'])) {
                        $kelasMahasiswaToCreate[] = $result['kelasMahasiswa'];
                    }
                    if (isset($result['nilai'])) {
                        $nilaiToCreate[] = $result['nilai'];
                    }
                    if (isset($result['nilaiUpdate'])) {
                        $nilaiToUpdate[] = $result['nilaiUpdate'];
                    }
                }

            } catch (\Exception $e) {
                $this->importResults['errors'][] = "Baris {$actualRowNumber}: " . $e->getMessage();
                Log::error("Import error at row {$actualRowNumber}: " . $e->getMessage(), ['row' => $row->toArray()]);
            }
        }

        // Batch insert/update data
        $this->batchInsertData($mahasiswaToCreate, $kelasMahasiswaToCreate, $nilaiToCreate, $nilaiToUpdate);
    }

    /**
     * Process single row
     */
    private function processRow($row, $rowNumber)
    {
        $nim = trim($row[0] ?? '');
        $nama = trim($row[1] ?? '');
        $kelasNama = trim($row[2] ?? '');

        if (empty($nim)) {
            return null;
        }

        // Validate NIM format
        if (!preg_match('/^\d{10}$/', $nim)) {
            throw new \Exception("NIM harus 10 digit angka (sekarang: {$nim})");
        }

        if (empty($nama) || empty($kelasNama)) {
            throw new \Exception("Nama mahasiswa dan kelas tidak boleh kosong");
        }

        // Cari atau buat mahasiswa
        $mahasiswa = $this->findOrCreateMahasiswa($nim, $nama);
        
        // Pastikan mahasiswa terdaftar di kelas
        $kelasMahasiswa = $this->ensureStudentInClass($mahasiswa, $kelasNama);

        // Proses nilai untuk setiap komponen
        $komponenIndex = 3;
        $nilaiData = [];
        
        foreach ($this->komponen as $komponenNama => $komponen) {
            $nilaiValue = $row[$komponenIndex] ?? '';
            
            if (!empty($nilaiValue) && is_numeric($nilaiValue)) {
                $nilai = (float)$nilaiValue;
                
                if ($nilai < 0 || $nilai > 100) {
                    throw new \Exception("Nilai {$komponenNama} harus antara 0-100 (sekarang: {$nilai})");
                }
                
                $nilaiData[] = $this->prepareNilaiData($mahasiswa->id, $komponen->id, $nilai);
                $this->importResults['updated_grades']++;
            }
            
            $komponenIndex++;
        }

        $this->importResults['success']++;

        return [
            'mahasiswa' => isset($mahasiswa->wasRecentlyCreated) ? $mahasiswa->getAttributes() : null,
            'kelasMahasiswa' => $kelasMahasiswa,
            'nilai' => $nilaiData
        ];
    }

    /**
     * Find or create mahasiswa dengan cache
     */
    private function findOrCreateMahasiswa($nim, $nama)
    {
        // Cek cache dulu
        if (isset($this->mahasiswaCache[$nim])) {
            $mahasiswa = $this->mahasiswaCache[$nim];
            
            // Update nama jika perlu
            if ($mahasiswa->nama !== $nama) {
                $mahasiswa->update(['nama' => $nama]);
            }
            
            return $mahasiswa;
        }

        // Buat mahasiswa baru
        $tahunMasukFromNim = substr($nim, 0, 2);
        $tahunMasuk = 2000 + intval($tahunMasukFromNim);
        
        $namaAwal = strtolower(explode(' ', trim($nama))[0]);
        $email = strtolower($nim . '_' . $namaAwal . '@student.unand.ac.id');

        // Buat user
        $user = User::create([
            'name' => $nama,
            'email' => $email,
            'password' => Hash::make($nim),
            'role' => 'mahasiswa'
        ]);

        // Buat mahasiswa
        $mahasiswa = Mahasiswa::create([
            'userId' => $user->id,
            'nim' => $nim,
            'nama' => $nama,
            'tahunMasuk' => $tahunMasuk,
        ]);

        // Update cache
        $this->mahasiswaCache[$nim] = $mahasiswa;
        
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

    /**
     * Ensure student is in class
     */
    private function ensureStudentInClass($mahasiswa, $kelasNama)
    {
        // Cari kelas berdasarkan nama
        $kelas = $this->kelasCache->where('namaKelas', $kelasNama)->first();
        
        if (!$kelas) {
            // Gunakan kelas pertama yang tersedia
            $kelas = $this->kelasCache->first();
            
            if (!$kelas) {
                throw new \Exception("Tidak ada kelas tersedia untuk mata kuliah ini");
            }
        }

        // Cek apakah mahasiswa sudah terdaftar
        $kelasMahasiswa = KelasMahasiswa::where('mahasiswaId', $mahasiswa->id)
            ->where('kelasId', $kelas->id)
            ->first();

        if (!$kelasMahasiswa) {
            return [
                'mahasiswaId' => $mahasiswa->id,
                'kelasId' => $kelas->id,
                'tahunAjaranMatkulId' => $kelas->tahunAjaranMatkulId,
            ];
        }

        return null;
    }

    /**
     * Prepare nilai data untuk batch insert
     */
    private function prepareNilaiData($mahasiswaId, $komponenId, $nilaiValue)
    {
        $bobotList = $this->bobotCache->get($komponenId, collect());
        
        $nilaiData = [];
        foreach ($bobotList as $bobot) {
            $dosenPengampuKelas = $this->dosenPengampuCache->get($bobot->kelasId);
            
            if ($dosenPengampuKelas) {
                $nilaiData[] = [
                    'mahasiswaId' => $mahasiswaId,
                    'tahunAjaranMatkulId' => $bobot->tahunAjaranMatkulId,
                    'bobotId' => $bobot->id,
                    'cpmkId' => $bobot->cpmkId,
                    'dosenPengampuKelasId' => $dosenPengampuKelas->id,
                    'nilai' => $nilaiValue,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        return $nilaiData;
    }

    /**
     * Batch insert/update data
     */
    private function batchInsertData($mahasiswaToCreate, $kelasMahasiswaToCreate, $nilaiToCreate, $nilaiToUpdate)
    {
        try {
            DB::beginTransaction();

            // Batch insert mahasiswa baru
            if (!empty($mahasiswaToCreate)) {
                User::insert(array_column($mahasiswaToCreate, 'user'));
                Mahasiswa::insert(array_column($mahasiswaToCreate, 'mahasiswa'));
            }

            // Batch insert kelas mahasiswa
            if (!empty($kelasMahasiswaToCreate)) {
                KelasMahasiswa::insert($kelasMahasiswaToCreate);
            }

            // Batch insert nilai baru
            if (!empty($nilaiToCreate)) {
                // Flatten array
                $flatNilai = [];
                foreach ($nilaiToCreate as $nilaiGroup) {
                    $flatNilai = array_merge($flatNilai, $nilaiGroup);
                }
                
                if (!empty($flatNilai)) {
                    Nilai::insert($flatNilai);
                }
            }

            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function getImportResults()
    {
        return $this->importResults;
    }
} 