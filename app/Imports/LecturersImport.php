<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Lecturer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LecturersImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (User::where('email', $row['email'])->exists()) {
                continue;
            }

            $user = User::create([
                'name' => $row['nama'],
                'email' => $row['email'],
                'role' => 'dosen',
            ]);

            Lecturer::create([
                'user_id' => $user->id,
                'nip' => $row['nip'],
            ]);
        }
    }
}
