<?php

namespace App\Imports;

use App\Models\MataKuliah;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Illuminate\Support\Facades\Log;

class MataKuliahImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    private $results = [
        'success' => 0,
        'created' => 0,
        'updated' => 0,
        'errors' => []
    ];

    public function model(array $row)
    {
        try {
            // Log untuk debugging
            Log::info('Processing row:', $row);

            // Ambil data dari row dengan case insensitive
            $namaMataKuliah = trim($row['nama_mata_kuliah'] ?? $row['NAMA_MATA_KULIAH'] ?? $row['Nama_Mata_Kuliah'] ?? '');
            $kode = trim($row['kode'] ?? $row['KODE'] ?? $row['Kode'] ?? '');
            $kurikulumKode = trim($row['kurikulum'] ?? $row['KURIKULUM'] ?? $row['Kurikulum'] ?? '');
            $sks = trim($row['sks'] ?? $row['SKS'] ?? $row['Sks'] ?? '');
            $jenis = trim($row['jenis'] ?? $row['JENIS'] ?? $row['Jenis'] ?? '');

            // Skip baris kosong
            if (empty($namaMataKuliah) && empty($kode) && empty($sks) && empty($jenis)) {
                return null;
            }

            // Validasi data wajib
            if (empty($namaMataKuliah)) {
                $this->results['errors'][] = "Nama mata kuliah wajib diisi";
                return null;
            }

            if (empty($kode)) {
                $this->results['errors'][] = "Kode mata kuliah wajib diisi";
                return null;
            }

            if (empty($kurikulumKode)) {
                $this->results['errors'][] = "Kurikulum '{$namaMataKuliah}' wajib diisi";
                return null;
            }

            if (empty($sks)) {
                $this->results['errors'][] = "SKS wajib diisi";
                return null;
            }

            if (empty($jenis)) {
                $this->results['errors'][] = "Jenis mata kuliah wajib diisi";
                return null;
            }

            // Validasi SKS (harus angka positif)
            if (!is_numeric($sks) || $sks < 0) {
                $this->results['errors'][] = "SKS '{$namaMataKuliah}' harus berupa angka positif";
                return null;
            }

            // Validasi jenis (wajib atau pilihan)
            $jenis = strtolower($jenis);
            if (!in_array($jenis, ['wajib', 'pilihan'])) {
                $this->results['errors'][] = "Jenis '{$namaMataKuliah}' harus 'wajib' atau 'pilihan'";
                return null;
            }

            // Resolve kurikulum (buat bila kode belum ada)
            $kurikulum = \App\Models\Kurikulum::firstOrCreate(
                ['kode' => $kurikulumKode],
                ['nama' => 'Kurikulum ' . $kurikulumKode, 'tahun' => is_numeric($kurikulumKode) ? (int) $kurikulumKode : null, 'isAktif' => true]
            );

            // Cek apakah mata kuliah sudah ada berdasarkan kode + kurikulum
            $existingMataKuliah = MataKuliah::where('kodeMatkul', $kode)
                ->where('kurikulumId', $kurikulum->id)
                ->first();

            if ($existingMataKuliah) {
                // Update mata kuliah yang sudah ada
                $existingMataKuliah->update([
                    'namaMatkul' => $namaMataKuliah,
                    'sks' => $sks,
                    'jenis' => $jenis,
                ]);

                $this->results['updated']++;
                $this->results['success']++;

                Log::info("MataKuliah updated: {$namaMataKuliah} (Kode: {$kode})");

                return null; // Return null karena ini update, bukan create
            }

            // Buat mata kuliah baru
            $mataKuliah = MataKuliah::create([
                'namaMatkul' => $namaMataKuliah,
                'kodeMatkul' => $kode,
                'kurikulumId' => $kurikulum->id,
                'sks' => $sks,
                'jenis' => $jenis,
            ]);

            $this->results['created']++;
            $this->results['success']++;

            Log::info("MataKuliah created: {$namaMataKuliah} (Kode: {$kode}, SKS: {$sks}, Jenis: {$jenis})");

            return $mataKuliah;

        } catch (\Exception $e) {
            $this->results['errors'][] = "Error processing {$namaMataKuliah}: " . $e->getMessage();
            Log::error("Error importing mata kuliah: " . $e->getMessage(), ['row' => $row]);
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'nama_mata_kuliah' => 'nullable|string|max:255',
            'kode' => 'nullable|max:20',
            'kurikulum' => 'nullable|max:50',
            'sks' => 'nullable|max:10',
            'jenis' => 'nullable|max:20',
            'NAMA_MATA_KULIAH' => 'nullable|string|max:255',
            'KODE' => 'nullable|max:20',
            'KURIKULUM' => 'nullable|max:50',
            'SKS' => 'nullable|max:10',
            'JENIS' => 'nullable|max:20',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_mata_kuliah.string' => 'Nama mata kuliah harus berupa teks',
            'nama_mata_kuliah.max' => 'Nama mata kuliah maksimal 255 karakter',
            'kode.max' => 'Kode mata kuliah maksimal 20 karakter',
            'sks.max' => 'SKS maksimal 10 karakter',
            'jenis.max' => 'Jenis maksimal 20 karakter',
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