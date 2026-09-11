<?php

namespace Tests\Unit;

use App\Models\KelasMahasiswa;
use PHPUnit\Framework\TestCase;

class KelasMahasiswaTest extends TestCase
{
    public function test_total_nilai_and_grade_are_plain_materialized_attributes(): void
    {
        $kelasMahasiswa = new KelasMahasiswa([
            'mahasiswaId' => 10,
            'kelasId' => 20,
        ]);

        $this->assertNull($kelasMahasiswa->totalNilai);
        $this->assertNull($kelasMahasiswa->grade);

        $kelasMahasiswa->forceFill([
            'totalNilai' => 79.5,
            'grade' => 'A-',
        ]);

        $this->assertSame(79.5, $kelasMahasiswa->totalNilai);
        $this->assertSame('A-', $kelasMahasiswa->grade);
    }
}
