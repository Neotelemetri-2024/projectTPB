<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class DosenPerformanceRegressionTest extends TestCase
{
    private function projectFile(string $path): string
    {
        return file_get_contents(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $path);
    }

    public function test_cpmk_show_batches_metadata_and_performs_no_cpl_sync(): void
    {
        $controller = $this->projectFile('app/Http/Controllers/Dosen/CpmkController.php');
        $showStart = strpos($controller, 'public function showMataKuliah');
        $showEnd = strpos($controller, 'public function create', $showStart);
        $showMethod = substr($controller, $showStart, $showEnd - $showStart);

        $this->assertStringContainsString("whereIn('cpmkId', \$displayedCpmkIds)", $showMethod);
        $this->assertStringContainsString("groupBy('cpmkId')", $showMethod);
        $this->assertStringNotContainsString('->sync(', $showMethod);
        $this->assertStringNotContainsString("foreach (\$cpmkList as \$cpmk) {\n            \$bobotData =", $showMethod);
    }

    public function test_grading_view_contains_no_database_queries_or_row_total_calculation(): void
    {
        $view = $this->projectFile('resources/views/dosen/nilai/show.blade.php');

        $this->assertStringNotContainsString('App\\Models\\', $view);
        $this->assertStringNotContainsString('$nilaiData->where', $view);
        $this->assertStringContainsString('$nilaiLookup->get($mhs->id)', $view);
        $this->assertStringContainsString("\$studentSummary['totalNilai']", $view);
    }

    public function test_bulk_grading_mode_remains_safely_paginated(): void
    {
        $controller = $this->projectFile('app/Http/Controllers/Dosen/NilaiController.php');

        $this->assertStringContainsString("\$displayPerPage = \$isBulkMode && !\$request->has('per_page') ? 100 : \$perPage", $controller);
        $this->assertStringContainsString('paginate($displayPerPage)->withQueryString()', $controller);
        $this->assertStringNotContainsString("if (\$isBulkMode) {\n            \$mahasiswa = \$mahasiswaQuery->get();", $controller);
    }
}
