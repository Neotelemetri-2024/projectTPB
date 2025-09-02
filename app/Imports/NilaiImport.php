<?php

namespace App\Imports;

use App\Models\Nilai;
use App\Models\Bobot;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\DosenPengampuKelas;
use App\Models\KelasMahasiswa;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class NilaiImport implements ToCollection, WithHeadingRow
{
    protected $tahunAjaranMatkulId;
    protected $dosenId;
    protected $relatedClasses;
    protected $results = [
        'success' => true,
        'message' => '',
        'total_processed' => 0,
        'total_success' => 0,
        'total_errors' => 0,
        'errors' => [],
        'processed_nims' => [] // Added for tracking unique NIMs
    ];

    public function __construct($tahunAjaranMatkulId, $dosenId, $relatedClasses)
    {
        $this->tahunAjaranMatkulId = $tahunAjaranMatkulId;
        $this->dosenId = $dosenId;
        $this->relatedClasses = $relatedClasses;
    }

    public function collection(Collection $rows)
    {
        DB::beginTransaction();

        try {
            // Debug: Log total rows received
            Log::info('Total rows received from Excel:', ['count' => $rows->count()]);

            // Debug: Log first few rows to see structure
            Log::info('First 6 rows structure:', [
                'row_1' => $rows->get(0) ? $rows->get(0)->toArray() : 'empty',
                'row_2' => $rows->get(1) ? $rows->get(1)->toArray() : 'empty',
                'row_3' => $rows->get(2) ? $rows->get(2)->toArray() : 'empty',
                'row_4' => $rows->get(3) ? $rows->get(3)->toArray() : 'empty',
                'row_5' => $rows->get(4) ? $rows->get(4)->toArray() : 'empty',
                'row_6' => $rows->get(5) ? $rows->get(5)->toArray() : 'empty',
            ]);

            // Get header from row 2 (index 1) - header yang benar
            $headerRow = $rows->get(1);
            if (!$headerRow) {
                throw new \Exception("Header row tidak ditemukan di baris 2");
            }

            Log::info('Header row found:', $headerRow->toArray());

            // Skip first 2 rows (info mata kuliah, header) dan mulai dari baris 3
            $dataRows = $rows->skip(2);

            Log::info('Data rows to process:', ['count' => $dataRows->count()]);

            foreach ($dataRows as $index => $row) {
                $this->results['total_processed']++;

                try {
                    // Baris data dimulai dari index 2 (baris ke-3 di Excel)
                    $this->processRowWithHeader($row, $headerRow, $index + 3);
                } catch (\Exception $e) {
                    $this->results['total_errors']++;
                    $this->results['errors'][] = [
                        'row' => $index + 3,
                        'error' => $e->getMessage(),
                        'data' => $row->toArray()
                    ];

                    // Log error but continue processing other rows
                    Log::warning("Error processing row " . ($index + 3) . ": " . $e->getMessage());
                }
            }

            // If there are errors, rollback and set success to false
            if ($this->results['total_errors'] > 0) {
                DB::rollBack();
                $this->results['success'] = false;
                $this->results['message'] = "Import selesai dengan {$this->results['total_errors']} error dari {$this->results['total_processed']} baris data.";
            } else {
                DB::commit();
                $this->results['message'] = "Berhasil mengimport {$this->results['total_success']} data mahasiswa.";
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->results['success'] = false;
            $this->results['message'] = 'Terjadi kesalahan fatal: ' . $e->getMessage();
            Log::error('Fatal error during import: ' . $e->getMessage());
        }
    }

    protected function processRowWithHeader($row, $headerRow, $rowNumber)
    {
        // Debug: Log the row being processed
        Log::info("Processing row {$rowNumber}:", [
            'row_data' => $row->toArray(),
            'header_row' => $headerRow->toArray()
        ]);

        // Convert row to associative array using header
        $data = [];
        foreach ($headerRow as $index => $header) {
            $data[$header] = $row[$index] ?? null;
        }

        Log::info("Processed data:", $data);

        // Validate required fields
        if (empty($data['NIM']) || empty($data['Nama Mahasiswa'])) {
            return;
        }

        // Increment total_success untuk NIM unik yang berhasil diproses
        $nim = trim($data['NIM']);
        if (!in_array($nim, $this->results['processed_nims'])) {
            $this->results['processed_nims'][] = $nim;
            $this->results['total_success']++;
            Log::info("NIM {$nim} berhasil diproses. Total mahasiswa: {$this->results['total_success']}");
        }

        // Find mahasiswa by NIM
        $mahasiswa = Mahasiswa::where('nim', trim($data['NIM']))->first();
        if (!$mahasiswa) {
            // Create new mahasiswa if NIM not found (like in job version)
            $tahunMasukFromNim = substr($data['NIM'], 0, 2);
        $tahunMasuk = 2000 + intval($tahunMasukFromNim);

            $namaAwal = strtolower(explode(' ', trim($data['Nama Mahasiswa']))[0]);
            $email = strtolower($data['NIM'] . '_' . $namaAwal . '@student.unand.ac.id');

            // Buat user dengan hashing yang lebih cepat untuk menghindari timeout
        $user = User::create([
                'name' => $data['Nama Mahasiswa'],
            'email' => $email,
                'password' => Hash::make($data['NIM'], ['rounds' => 4]), // Reduce bcrypt rounds for faster hashing
                'role' => 'mahasiswa',
        ]);

        // Buat mahasiswa
        $mahasiswa = Mahasiswa::create([
            'userId' => $user->id,
                'nim' => $data['NIM'],
                'nama' => $data['Nama Mahasiswa'],
            'tahunMasuk' => $tahunMasuk,
                'jenisKelamin' => 'L', // Default
                'tempatLahir' => 'Unknown',
                'tanggalLahir' => now(),
                'alamat' => 'Unknown',
                'noTelp' => '0000000000000000',
                'agama' => 'Islam',
                'status' => 'Aktif',
            ]);

            Log::info("Created new mahasiswa: {$data['NIM']} - {$data['Nama Mahasiswa']}");
        }

        // Find student's class
        $studentClass = $this->findStudentClass($mahasiswa->id);
        if (!$studentClass) {
            throw new \Exception("Mahasiswa {$mahasiswa->nama} tidak terdaftar di kelas manapun untuk mata kuliah ini");
        }

        // Get dosen pengampu for this class
        $dosenPengampuKelas = DosenPengampuKelas::where('dosenId', $this->dosenId)
            ->whereHas('kelas', function($query) use ($studentClass) {
                $query->where('tahunAjaranMatkulId', $studentClass->id);
            })
            ->first();

        if (!$dosenPengampuKelas) {
            throw new \Exception("Dosen tidak memiliki akses ke kelas mahasiswa {$mahasiswa->nama}");
        }

        // Process each komponen column
        $komponenColumns = $this->getKomponenColumnsFromData($data);

        foreach ($komponenColumns as $komponenId => $nilai) {
            if ($nilai !== null && $nilai !== '') {
                // Validate nilai range
                if (!is_numeric($nilai) || $nilai < 0 || $nilai > 100) {
                    throw new \Exception("Nilai untuk komponen harus antara 0-100, ditemukan: {$nilai}");
                }

                // Get all bobot for this komponen in this student's specific class
                $bobotList = Bobot::where('komponenId', $komponenId)
                    ->where('tahunAjaranMatkulId', $studentClass->id)
                    ->with('cpmk')
                    ->get();

                if ($bobotList->isEmpty()) {
                    throw new \Exception("Tidak ada bobot yang ditemukan untuk komponen ID {$komponenId} di kelas ini");
                }

                // Create nilai entry for each bobot
                foreach ($bobotList as $bobot) {
                    Nilai::updateOrCreate([
                        'mahasiswaId' => $mahasiswa->id,
                        'tahunAjaranMatkulId' => $studentClass->id,
                        'bobotId' => $bobot->id,
                    ], [
                        'cpmkId' => $bobot->cpmkId,
                        'dosenPengampuKelasId' => $dosenPengampuKelas->id,
                        'nilai' => $nilai,
                    ]);
                }
            } else {
                // Delete if value is empty - remove all nilai for this komponen
                $bobotIds = Bobot::where('komponenId', $komponenId)
                    ->where('tahunAjaranMatkulId', $studentClass->id)
                    ->pluck('id');

                if ($bobotIds->isNotEmpty()) {
                    Nilai::where('mahasiswaId', $mahasiswa->id)
                        ->where('tahunAjaranMatkulId', $studentClass->id)
                        ->whereIn('bobotId', $bobotIds)
                        ->delete();
                }
            }
        }

        // Calculate and store total for this student
        $this->calculateAndStoreTotal($mahasiswa->id, $studentClass->id);
    }

    protected function findStudentClass($mahasiswaId)
    {
        foreach ($this->relatedClasses as $class) {
            $studentInClass = $class->kelas->flatMap(function($kelas) use ($mahasiswaId) {
                return $kelas->kelasMahasiswa->where('mahasiswaId', $mahasiswaId);
            })->first();

            if ($studentInClass) {
                return $class;
            }
        }

        // If student not found in any class, auto-create in the first available class
        Log::info("Mahasiswa ID {$mahasiswaId} tidak terdaftar di kelas manapun, akan dibuatkan otomatis");

        $firstClass = $this->relatedClasses->first();
        if (!$firstClass) {
            throw new \Exception("Tidak ada kelas yang tersedia untuk mata kuliah ini");
        }

        // Get the first available kelas
        $firstKelas = $firstClass->kelas->first();
        if (!$firstKelas) {
            throw new \Exception("Tidak ada kelas yang tersedia untuk mata kuliah ini");
        }

                        // Create KelasMahasiswa - menggunakan kelasId yang benar
        $kelasMahasiswa = KelasMahasiswa::create([
            'mahasiswaId' => $mahasiswaId,
            'kelasId' => $firstKelas->id,
        ]);

        Log::info("Created KelasMahasiswa: Mahasiswa ID {$mahasiswaId} - Kelas ID {$firstKelas->id}");

        return $firstClass;
    }

    protected function getKomponenColumnsFromData($data)
    {
        $komponenColumns = [];

        // Get all komponen that have bobot in this mata kuliah
        $komponenIds = Bobot::whereIn('tahunAjaranMatkulId', $this->relatedClasses->pluck('id'))
            ->pluck('komponenId')
            ->unique();

        // Debug: Log komponen yang ditemukan
        Log::info('Komponen IDs found:', $komponenIds->toArray());

        foreach ($komponenIds as $komponenId) {
            $komponen = \App\Models\Komponen::find($komponenId);
            if ($komponen) {
                // Check if komponen name exists in data
                if (isset($data[$komponen->nama]) && $data[$komponen->nama] !== null && $data[$komponen->nama] !== '') {
                    $komponenColumns[$komponenId] = $data[$komponen->nama];
                    Log::info("Found komponen '{$komponen->nama}' with value: {$data[$komponen->nama]}");
                }
            }
        }

        // Debug: Log final komponen columns found
        Log::info('Final komponen columns found:', $komponenColumns);

        return $komponenColumns;
    }

    protected function calculateAndStoreTotal($mahasiswaId, $matkulId)
    {
        try {
            // Get all nilai for this student in this specific class only
            $allNilai = Nilai::where('mahasiswaId', $mahasiswaId)
                ->where('tahunAjaranMatkulId', $matkulId)
                ->with(['bobot.cpmk', 'bobot.komponen'])
                ->get();

            // Only calculate and update if there are actual grades
            if ($allNilai->isNotEmpty()) {
                // Group nilai by CPMK
                $nilaiPerCpmk = $allNilai->groupBy('cpmkId');
                $totalNilaiKeseluruhan = 0;
                $totalBobotKeseluruhan = 0;

                // Calculate nilai per CPMK
                foreach ($nilaiPerCpmk as $cpmkId => $nilaiCpmk) {
                    $nilaiCpmkTotal = 0;
                    $bobotCpmkTotal = 0;

                    foreach ($nilaiCpmk as $nilai) {
                        if ($nilai->bobot && $nilai->bobot->bobot > 0) {
                            $nilaiCpmkTotal += ($nilai->nilai * $nilai->bobot->bobot);
                            $bobotCpmkTotal += $nilai->bobot->bobot;
                        }
                    }

                    // Jika bobot CPMK > 0, hitung rata-rata terbobot
                    if ($bobotCpmkTotal > 0) {
                        $nilaiRataRataCpmk = $nilaiCpmkTotal / $bobotCpmkTotal;
                        $totalNilaiKeseluruhan += $nilaiRataRataCpmk;
                        $totalBobotKeseluruhan += 1; // Setiap CPMK dihitung sebagai 1 unit
                    }
                }

                // Hitung nilai akhir (rata-rata dari semua CPMK)
                $totalNilai = $totalBobotKeseluruhan > 0 ? $totalNilaiKeseluruhan / $totalBobotKeseluruhan : 0;

                // Determine grade based on total score
                $grade = $this->calculateGrade($totalNilai);

                // Find the specific KelasMahasiswa record for this student in this class
                $kelasMahasiswa = \App\Models\KelasMahasiswa::where('mahasiswaId', $mahasiswaId)
                    ->whereHas('kelas', function($query) use ($matkulId) {
                        $query->where('tahunAjaranMatkulId', $matkulId);
                    })
                    ->first();

                if ($kelasMahasiswa) {
                    $kelasMahasiswa->update([
                        'totalNilai' => $totalNilai,
                        'grade' => $grade,
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::error('Error calculating total score: ' . $e->getMessage());
        }
    }

    protected function calculateGrade($totalScore)
    {
        // Don't assign grade if total score is null (no grades inputted)
        if ($totalScore === null) {
            return null;
        }

        // If total score is 0 or negative, assign grade E
        if ($totalScore <= 0) {
            return 'E';
        }

        if ($totalScore >= 80) {
            return 'A';
        } elseif ($totalScore >= 75) {
            return 'A-';
        } elseif ($totalScore >= 70) {
            return 'B+';
        } elseif ($totalScore >= 65) {
            return 'B';
        } elseif ($totalScore >= 60) {
            return 'B-';
        } elseif ($totalScore >= 55) {
            return 'C+';
        } elseif ($totalScore >= 50) {
            return 'C';
        } elseif ($totalScore >= 45) {
            return 'D';
        } else {
            return 'E';
        }
    }

    public function getResults()
    {
        return $this->results;
    }
}
