<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

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
        $view = $this->source('resources/views/mahasiswa/capaian.blade.php');

        $this->assertStringContainsString("whereIn('tahunAjaranMatkulId', \$tamIds)", $controller);
        $this->assertStringContainsString('$courseScores[]', $controller);
        $this->assertStringContainsString('array_sum($courseScores) / count($courseScores)', $controller);
        $this->assertStringContainsString("'nilai_surat' => " . '$total_cpl', $controller);
        $this->assertStringNotContainsString('(int) round($total_cpl)', $controller);
        $this->assertStringContainsString("'Belum lengkap'", $controller);
        $this->assertStringContainsString('rata-rata nilai akhir seluruh mata kuliah asesmen', $view);
    }

    public function test_student_cpl_uses_average_instead_of_highest_course_score(): void
    {
        $controller = $this->source('app/Http/Controllers/CapaianController.php');

        $this->assertStringNotContainsString('max($courseScores)', $controller);
        $this->assertStringNotContainsString('$totalCpmkArr[]', $controller);
        $this->assertStringContainsString('$weightTotal = $bobot->sum(\'bobot\')', $controller);
    }

    public function test_sks_falls_back_to_master_course_when_tam_value_is_missing(): void
    {
        $model = $this->source('app/Models/TahunAjaranMatkul.php');

        $this->assertStringContainsString('return $this->sks ?? $this->mataKuliah?->sks ?? 0;', $model);
    }

    private function source(string $path): string
    {
        return file_get_contents(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path));
    }
}
