<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Ambil NIM dari email (bagian sebelum '_')
            $emailParts = explode('_', $row['email']);
            $nimFromEmail = $emailParts[0];

            // Cek apakah nim di email cocok dengan kolom nim
            $nimFromEmail = trim($emailParts[0]);
            $actualNim = trim($row['nim']);

            if ($nimFromEmail !== $actualNim) {
                continue;
            }


            // Cek jika user dengan email ini sudah ada, skip
            if (User::where('email', $row['email'])->exists()) {
                continue;
            }

            $user = User::create([
                'name' => $row['nama'],
                'email' => $row['email'],
                'role' => 'mahasiswa',
                'status' => 'aktif'
            ]);

            Student::create([
                'user_id' => $user->id,
                'nim' => $row['nim'],
                'tahun_masuk' => $row['tahun_masuk'],
            ]);
        }
    }
}
