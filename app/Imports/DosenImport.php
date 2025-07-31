<?php

namespace App\Imports;

use App\Models\Dosen;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class DosenImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
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
            $nama = trim($row['nama'] ?? $row['NAMA'] ?? $row['Nama'] ?? '');
            $nip = trim($row['nip'] ?? $row['NIP'] ?? $row['Nip'] ?? '');
            $email = trim($row['email'] ?? $row['EMAIL'] ?? $row['Email'] ?? '');

            // Skip baris kosong
            if (empty($nama) && empty($nip) && empty($email)) {
                return null;
            }

            // Validasi data wajib
            if (empty($nama)) {
                $this->results['errors'][] = "Nama dosen wajib diisi";
                return null;
            }

            if (empty($nip)) {
                $this->results['errors'][] = "NIP dosen wajib diisi";
                return null;
            }

            if (empty($email)) {
                $this->results['errors'][] = "Email dosen wajib diisi";
                return null;
            }

            // Bersihkan NIP dari format scientific notation dan spasi
            $nip = trim($nip);
            if (strpos($nip, 'E') !== false || strpos($nip, 'e') !== false) {
                // Convert scientific notation to full number
                $nip = number_format((float)$nip, 0, '', '');
            }
            
            // Validasi NIP (harus 18 digit)
            if (strlen($nip) !== 18 || !is_numeric($nip)) {
                $this->results['errors'][] = "NIP {$nama} harus 18 digit angka (ditemukan: " . strlen($nip) . " digit)";
                return null;
            }

            // Validasi format email - lebih fleksibel
            $email = trim($email);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->results['errors'][] = "Format email '{$email}' untuk dosen '{$nama}' tidak valid";
                return null;
            }

            // Cek apakah dosen sudah ada berdasarkan NIP
            $existingDosen = Dosen::where('nip', $nip)->first();

            if ($existingDosen) {
                // Update dosen yang sudah ada
                $existingDosen->update([
                    'nama' => $nama,
                ]);

                // Update user jika ada
                $user = User::where('email', $existingDosen->user->email)->first();
                if ($user) {
                    $user->update([
                        'name' => $nama,
                    ]);
                }

                $this->results['updated']++;
                $this->results['success']++;

                Log::info("Dosen updated: {$nama} (NIP: {$nip})");

                return null; // Return null karena ini update, bukan create
            }

            // Cek apakah email sudah digunakan
            $existingUser = User::where('email', $email)->first();
            if ($existingUser) {
                $this->results['errors'][] = "Email {$email} sudah digunakan oleh user lain";
                return null;
            }

            // Buat user baru
            $user = User::create([
                'name' => $nama,
                'email' => $email,
                'password' => Hash::make($nip), // Password = NIP
                'role' => 'dosen',
            ]);

            // Buat dosen baru
            $dosen = Dosen::create([
                'nama' => $nama,
                'nip' => $nip,
                'userId' => $user->id,
            ]);

            $this->results['created']++;
            $this->results['success']++;

            Log::info("Dosen created: {$nama} (NIP: {$nip}, Email: {$email})");

            return $dosen;

        } catch (\Exception $e) {
            $this->results['errors'][] = "Error processing {$nama}: " . $e->getMessage();
            Log::error("Error importing dosen: " . $e->getMessage(), ['row' => $row]);
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'nama' => 'nullable|string|max:255',
            'nip' => 'nullable|max:20',
            'email' => 'nullable|max:255',
            'NAMA' => 'nullable|string|max:255',
            'NIP' => 'nullable|max:20',
            'EMAIL' => 'nullable|max:255',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama.string' => 'Nama dosen harus berupa teks',
            'nama.max' => 'Nama dosen maksimal 255 karakter',
            'nip.max' => 'NIP maksimal 20 karakter',
            'email.max' => 'Email maksimal 255 karakter',
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