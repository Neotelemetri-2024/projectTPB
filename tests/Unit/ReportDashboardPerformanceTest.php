<?php

namespace Tests\Unit;

use App\Services\CplAssessmentCalculator;
use App\Models\MataKuliah;
use App\Models\TahunAjaranMatkul;
use Tests\TestCase;

class ReportDashboardPerformanceTest extends TestCase
{
    public function test_pimpinan_dashboard_uses_lazy_chart_endpoint(): void
    {
        $controller = $this->source('app/Http/Controllers/DashboardController.php');
        $routes = $this->source('routes/web.php');
        $view = $this->source('resources/views/pimpinan/dashboard.blade.php');

        $this->assertStringContainsString('function pimpinanDashboardChartData', $controller);
        $this->assertStringContainsString("pimpinan/dashboard/chart-data", $routes);
        $this->assertStringContainsString("route('pimpinan.dashboard.chart-data')", $view);
        $this->assertStringContainsString('fetch(url', $view);
    }

    public function test_dashboard_master_counts_are_aggregated_in_one_round_trip(): void
    {
        $controller = $this->source('app/Http/Controllers/DashboardController.php');

        $this->assertStringContainsString("selectSub(DB::table('mahasiswa')", $controller);
        $this->assertStringContainsString("selectSub(DB::table('cpmk')", $controller);
        $this->assertStringNotContainsString('$dosenStats = DB::table', $controller);
    }

    public function test_cpl_report_caches_selected_filter_query(): void
    {
        $controller = $this->source('app/Http/Controllers/CplLaporanController.php');

        $this->assertStringContainsString('cachedDetailRows($selectedTahunAjaranId, $selectedKurikulum)', $controller);
        $this->assertStringContainsString("where('tam.tahunAjaranId', \$tahunAjaranId)", $controller);
        $this->assertStringContainsString("where('mk.kurikulumId', \$kurikulumId)", $controller);
    }

    public function test_cpl_achievement_build_and_cache_are_filter_scoped(): void
    {
        $controller = $this->source('app/Http/Controllers/Pimpinan/CplAchievementController.php');

        $this->assertStringContainsString('buildAchievementRows($tahunAjaranId, $kurikulum)', $controller);
        $this->assertStringContainsString("->when(\$tahunAjaranId", $controller);
        $this->assertStringContainsString("->where('kurikulumId', \$kurikulumId)", $controller);
    }

    public function test_assessment_scope_is_based_on_explicit_cpl_matkul_pairs(): void
    {
        $scope = $this->source('app/Services/CplAssessmentScope.php');

        $this->assertStringContainsString('cpl_mata_kuliah_asesmen', $scope);
        $this->assertStringContainsString('applyAssessedPairConstraint', $scope);
        $this->assertStringContainsString('hasExplicitAssessment', $scope);
        $this->assertStringNotContainsString("where('isAsesmen', true)", $scope);
    }

    public function test_aggregate_reports_no_longer_filter_jenis_wajib(): void
    {
        $laporan = $this->source('app/Http/Controllers/CplLaporanController.php');
        $achievement = $this->source('app/Http/Controllers/Pimpinan/CplAchievementController.php');

        $this->assertStringNotContainsString("where('mk.jenis', 'wajib')", $laporan);
        $this->assertStringNotContainsString("where('jenis', 'wajib')", $achievement);
    }

    public function test_curriculum_screen_saves_cpl_matkul_pairs_scoped_to_its_own_mata_kuliah(): void
    {
        $controller = $this->source('app/Http/Controllers/Admin/KurikulumController.php');

        $this->assertStringContainsString("cpl_mata_kuliah_asesmen", $controller);
        $this->assertStringContainsString("whereIn('mataKuliahId', \$mkIds)", $controller);
        $this->assertStringContainsString('asesmen[', $this->source('resources/views/admin/kurikulum/matkul-asesmen.blade.php'));
    }

    public function test_cpmk_distribution_uses_single_classification_pass(): void
    {
        $controller = $this->source('app/Http/Controllers/Pimpinan/CpmkReportController.php');

        $this->assertStringContainsString('foreach ($nilaiPerMahasiswa as $nilai)', $controller);
        $this->assertStringContainsString('$distributionCounts[$grade]++', $controller);
        $this->assertStringContainsString('$histogramCounts[$index]++', $controller);
        $this->assertStringNotContainsString('count(array_filter($nilaiPerMahasiswa', $controller);
    }

    public function test_student_cpl_uses_complete_assessed_course_scores_without_integer_rounding(): void
    {
        $controller = $this->source('app/Http/Controllers/CapaianController.php');
        $calculator = $this->source('app/Services/CplAssessmentCalculator.php');
        $view = $this->source('resources/views/mahasiswa/capaian.blade.php');

        $this->assertStringContainsString("whereIn('tahunAjaranMatkulId', \$tamIds)", $controller);
        $this->assertStringContainsString('$assessedCourses[]', $controller);
        $this->assertStringContainsString('array_sum($courseScores) / count($courseScores)', $calculator);
        $this->assertStringContainsString("'nilai_surat' => " . '$total_cpl', $controller);
        $this->assertStringNotContainsString('(int) round($total_cpl)', $controller);
        $this->assertStringContainsString("'Belum lengkap'", $calculator);
        $this->assertStringContainsString('rata-rata nilai akhir seluruh mata kuliah asesmen', $view);
    }

    public function test_student_cpl_uses_average_instead_of_highest_course_score(): void
    {
        $controller = $this->source('app/Http/Controllers/CapaianController.php');
        $calculator = $this->source('app/Services/CplAssessmentCalculator.php');

        $this->assertStringNotContainsString('max($courseScores)', $calculator);
        $this->assertStringNotContainsString('$totalCpmkArr[]', $controller);
        $this->assertStringContainsString('array_sum($courseScores) / count($courseScores)', $calculator);
    }

    public function test_student_dashboard_loads_cpl_chart_bundle(): void
    {
        $app = $this->source('resources/js/app.js');
        $dashboard = $this->source('resources/views/mahasiswa/dashboard.blade.php');

        $this->assertStringContainsString('window.cplCpmkData', $dashboard);
        $this->assertStringContainsString('if (window.cplCpmkData)', $app);
        $charts = $this->source('resources/js/charts.js');
        $this->assertStringContainsString('window.loadChartModule().catch(() => {});', $app);
        $this->assertStringContainsString('onDomReady(renderCplCharts);', $charts);
        $this->assertStringNotContainsString("document.addEventListener('DOMContentLoaded', renderCplCharts", $charts);
    }

    public function test_student_cpl_requires_non_null_assessment_values_and_rounds_results(): void
    {
        $controller = $this->source('app/Http/Controllers/CapaianController.php');

        $calculator = $this->source('app/Services/CplAssessmentCalculator.php');
        $this->assertStringContainsString('CplAssessmentCalculator::class)->calculate($assessedCourses, $nilaiMinimal)', $controller);
        $this->assertStringContainsString("\$component['score'] !== null", $calculator);
        $this->assertStringContainsString('round($nilaiCpmkTotal / $bobotCpmkTotal, 2)', $controller);
        $this->assertStringContainsString('round($weightedTotal / $totalWeight, 2)', $calculator);
        $this->assertStringContainsString('round(array_sum($courseScores) / count($courseScores), 2)', $calculator);
        $this->assertStringContainsString('number_format((float) $cpl[\'nilai_surat\'], 2)', $this->source('resources/views/exports/capaian-pdf.blade.php'));
    }

    public function test_cpl_calculator_requires_every_assessment_component_and_rounds_to_two_decimals(): void
    {
        $calculator = new CplAssessmentCalculator();
        $complete = $calculator->calculate([
            [
                ['weight' => 30, 'score' => 81],
                ['weight' => 70, 'score' => 80],
            ],
            [
                ['weight' => 100, 'score' => 80.2],
            ],
        ], 80);

        $this->assertSame(80.25, $complete['score']);
        $this->assertSame('Tercapai', $complete['status']);
        $this->assertTrue($complete['complete']);
        $this->assertSame(0, $complete['missing_course_count']);
    }

    public function test_cpl_calculator_hides_partial_or_missing_assessment_results(): void
    {
        $calculator = new CplAssessmentCalculator();
        $partial = $calculator->calculate([
            [
                ['weight' => 50, 'score' => 100],
                ['weight' => 50, 'score' => null],
            ],
        ], 55);
        $empty = $calculator->calculate([], 55);

        $this->assertSame('-', $partial['score']);
        $this->assertSame('Belum lengkap', $partial['status']);
        $this->assertFalse($partial['complete']);
        $this->assertSame(1, $partial['missing_course_count']);
        $this->assertSame('-', $empty['score']);
        $this->assertSame('Belum lengkap', $empty['status']);
    }

    public function test_cpl_calculator_treats_zero_as_a_valid_score_and_checks_minimum(): void
    {
        $calculator = new CplAssessmentCalculator();
        $atMinimum = $calculator->calculate([[['weight' => 100, 'score' => 60]]], 60);
        $zero = $calculator->calculate([[['weight' => 100, 'score' => 0]]], 60);

        $this->assertSame(60.0, $atMinimum['score']);
        $this->assertSame('Tercapai', $atMinimum['status']);
        $this->assertSame(0.0, $zero['score']);
        $this->assertSame('Belum tercapai', $zero['status']);
        $this->assertTrue($zero['complete']);
    }

    public function test_sks_falls_back_to_master_course_when_tam_value_is_missing(): void
    {
        $model = $this->source('app/Models/TahunAjaranMatkul.php');

        $this->assertStringContainsString('$tamSks = is_numeric($this->sks) ? (int) $this->sks : 0;', $model);
        $this->assertStringContainsString('if ($tamSks > 0)', $model);
        $this->assertStringContainsString('$masterSks = $this->mataKuliah?->sks;', $model);
    }

    public function test_sks_accessor_prioritizes_valid_tam_and_falls_back_for_zero_or_null(): void
    {
        $master = new MataKuliah(['sks' => 3]);

        $validTam = new TahunAjaranMatkul(['sks' => 4]);
        $validTam->setRelation('mataKuliah', $master);
        $this->assertSame(4, $validTam->getSks());

        $zeroTam = new TahunAjaranMatkul(['sks' => 0]);
        $zeroTam->setRelation('mataKuliah', $master);
        $this->assertSame(3, $zeroTam->getSks());

        $nullTam = new TahunAjaranMatkul(['sks' => null]);
        $nullTam->setRelation('mataKuliah', $master);
        $this->assertSame(3, $nullTam->getSks());

        $emptyTam = new TahunAjaranMatkul(['sks' => 0]);
        $this->assertSame(0, $emptyTam->getSks());
    }

    public function test_all_tam_write_paths_copy_or_repair_sks(): void
    {
        $controller = $this->source('app/Http/Controllers/Admin/TahunAjaranMatkulController.php');
        $import = $this->source('app/Imports/TahunAjaranMatkulImport.php');
        $migration = $this->source('database/migrations/2026_09_28_000001_backfill_missing_sks_on_tahun_ajaran_matkul_table.php');

        $this->assertSame(2, substr_count($controller, "'sks' => \$mataKuliah->sks"));
        $this->assertStringContainsString("'sks' => \$sourceMatkul->getSks()", $controller);
        $this->assertStringContainsString('$tahunAjaranMatkul->update([\'sks\' => $mataKuliahModel->sks])', $import);
        $this->assertStringContainsString('(tam.sks IS NULL OR tam.sks = 0)', $migration);
        $this->assertStringContainsString('SET tam.sks = mk.sks', $migration);
    }

    private function source(string $path): string
    {
        return file_get_contents(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path));
    }
}
