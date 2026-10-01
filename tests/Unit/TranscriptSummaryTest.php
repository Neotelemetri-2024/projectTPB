<?php

namespace Tests\Unit;

use App\Http\Controllers\KHSController;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class TranscriptSummaryTest extends TestCase
{
    public function test_summary_uses_only_courses_with_final_grades_and_weights_them_by_sks(): void
    {
        $summarize = new ReflectionMethod(KHSController::class, 'summarizeRows');

        $result = $summarize->invoke(new KHSController(), [
            ['grade' => 'A', 'sks' => 3],
            ['grade' => 'B', 'sks' => 2],
            ['grade' => null, 'sks' => 4],
        ]);

        $this->assertSame(5, $result['totalSks']);
        $this->assertSame(3.6, $result['ipk']);
    }

    public function test_summary_without_final_grades_has_no_ipk(): void
    {
        $summarize = new ReflectionMethod(KHSController::class, 'summarizeRows');

        $result = $summarize->invoke(new KHSController(), [
            ['grade' => null, 'sks' => 3],
        ]);

        $this->assertSame(0, $result['totalSks']);
        $this->assertNull($result['ipk']);
    }
}
