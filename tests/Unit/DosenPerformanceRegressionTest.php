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
        $this->assertStringContainsString('$currentPage = min($requestedPage, $lastPage);', $showMethod);
        $this->assertStringContainsString("paginate(\$perPage, ['*'], 'page', \$currentPage)", $showMethod);
        $this->assertStringNotContainsString('->sync(', $showMethod);
        $this->assertStringNotContainsString("foreach (\$cpmkList as \$cpmk) {\n            \$bobotData =", $showMethod);
    }

    public function test_cpmk_view_has_explicit_empty_states_for_both_tabs(): void
    {
        $view = $this->projectFile('resources/views/dosen/cpmk/show.blade.php');

        $this->assertStringContainsString('@forelse($mainCpmkList as $cpmk)', $view);
        $this->assertStringContainsString('Tidak ada CPMK utama pada hasil filter ini.', $view);
        $this->assertStringContainsString('@forelse($subCpmkList as $subCpmk)', $view);
        $this->assertStringContainsString('Tidak ada Sub-CPMK pada hasil filter ini.', $view);
        $this->assertStringContainsString('$cpmkList->links()', $view);
        $this->assertStringNotContainsString('id="form-bobot-cpmk" style="display:none"', $view);
    }

    public function test_cpmk_tabs_are_split_from_complete_filtered_result(): void
    {
        $controller = $this->projectFile('app/Http/Controllers/Dosen/CpmkController.php');

        $this->assertStringContainsString('$allCpmkForTabs = (clone $query)', $controller);
        $this->assertStringContainsString('$mainCpmkList = $allCpmkForTabs->filter', $controller);
        $this->assertStringContainsString('$subCpmkList = $allCpmkForTabs->filter', $controller);
        $this->assertStringContainsString("->orWhere('deskripsi', 'like'", $controller);
    }

    public function test_cpmk_report_requests_lazy_chart_loader(): void
    {
        $view = $this->projectFile('resources/views/dosen/cpmk-laporan/show.blade.php');

        $this->assertStringContainsString('window.whenChartReady(function () {', $view);
        $this->assertStringContainsString('window.renderApexChart', $view);
        $this->assertStringNotContainsString('window.__chartReadyQueue.push(function () {', $view);
    }

    public function test_global_chart_and_modal_drivers_support_legacy_and_daisyui_markup(): void
    {
        $app = $this->projectFile('resources/js/app.js');
        $charts = $this->projectFile('resources/js/charts.js');

        $this->assertStringContainsString("import('./charts.js')", $app);
        $this->assertStringContainsString('[data-modal-toggle], [data-modal-target]', $app);
        $this->assertStringContainsString("modal.classList.remove('hidden')", $app);
        $this->assertStringContainsString("modal.classList.add('hidden')", $app);
        $this->assertStringContainsString('if (typeof window.whenChartReady !== \'function\')', $charts);
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
