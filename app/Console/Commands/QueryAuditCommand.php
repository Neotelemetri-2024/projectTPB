<?php

namespace App\Console\Commands;

use App\Http\Controllers\CapaianController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Dosen\CpmkLaporanController;
use App\Http\Controllers\Pimpinan\CplAchievementController;
use App\Models\TahunAjaran;
use App\Models\TahunAjaranMatkul;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use ReflectionClass;
use ReflectionMethod;
use Throwable;

class QueryAuditCommand extends Command
{
    protected $signature = 'query:audit
                            {--cold : Flush dashboard cache before measuring (default: true)}
                            {--no-cold : Keep existing cache}
                            {--explain-limit=8 : How many slowest queries to EXPLAIN}
                            {--output=storage/app/query-audit-report.json : Report path relative to base}';

    protected $description = 'Profile dashboard / heavy-page SQL and write a JSON audit report';

    /** @var array<int, array<string, mixed>> */
    private array $queries = [];

    private ?string $currentScenario = null;

    public function handle(): int
    {
        // Heavy paths (CPL achievement) may load large nilai result sets.
        @ini_set('memory_limit', '1024M');

        $cold = !$this->option('no-cold');
        $explainLimit = (int) $this->option('explain-limit');
        $outputRel = $this->option('output');
        $outputPath = base_path($outputRel);

        $this->info('Query audit starting…');
        $this->line('DB: ' . config('database.connections.' . config('database.default') . '.database'));
        $this->line('memory_limit: ' . ini_get('memory_limit'));

        if ($cold) {
            Cache::flush();
            $this->warn('Cache flushed (cold path).');
        }

        DB::listen(function ($query) {
            $this->queries[] = [
                'scenario' => $this->currentScenario,
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time_ms' => $query->time,
            ];
        });

        $meta = [
            'generated_at' => now()->toIso8601String(),
            'app_env' => config('app.env'),
            'db_connection' => config('database.default'),
            'db_driver' => config('database.connections.' . config('database.default') . '.driver'),
            'db_database' => config('database.connections.' . config('database.default') . '.database'),
            'cold_cache' => $cold,
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ];

        $tableStats = $this->collectTableStats();
        $indexes = $this->collectIndexes([
            'nilai',
            'cpmk',
            'mahasiswa',
            'bobot',
            'cpmk_cpl',
            'cpmk_mat_kul',
            'kelas_mahasiswa',
            'tahun_ajaran_matkul',
            'kelas',
            'dosen_pengampu_kelas',
        ]);

        $scenarios = [];
        $scenarios['admin_dashboard_charts'] = $this->runScenario('admin_dashboard_charts', function () {
            return $this->measureAdminDashboardCharts();
        });
        $scenarios['dosen_dashboard'] = $this->runScenario('dosen_dashboard', function () {
            return $this->measureDosenDashboard();
        });
        $scenarios['mahasiswa_dashboard'] = $this->runScenario('mahasiswa_dashboard', function () {
            return $this->measureMahasiswaDashboard();
        });
        $scenarios['cpmk_laporan_show'] = $this->runScenario('cpmk_laporan_show', function () {
            return $this->measureCpmkLaporanShow();
        });
        $scenarios['capaian_index'] = $this->runScenario('capaian_index', function () {
            return $this->measureCapaian();
        });
        $scenarios['cpl_achievement'] = $this->runScenario('cpl_achievement', function () {
            return $this->measureCplAchievement();
        });

        $allQueries = $this->queries;
        usort($allQueries, fn ($a, $b) => $b['time_ms'] <=> $a['time_ms']);

        $slowest = array_slice($allQueries, 0, max(1, $explainLimit));
        $explains = [];
        if (config('database.default') === 'mysql' || ($meta['db_driver'] ?? '') === 'mysql') {
            foreach ($slowest as $i => $q) {
                $explains[] = $this->explainQuery($q, $i + 1);
            }
        }

        $report = [
            'meta' => $meta,
            'table_stats' => $tableStats,
            'indexes' => $indexes,
            'scenarios' => $scenarios,
            'summary' => [
                'total_queries' => count($this->queries),
                'total_time_ms' => round(array_sum(array_column($this->queries, 'time_ms')), 2),
                'slowest_queries' => array_map(function ($q) {
                    return [
                        'scenario' => $q['scenario'],
                        'time_ms' => $q['time_ms'],
                        'sql' => $q['sql'],
                        'bindings' => $q['bindings'],
                    ];
                }, $slowest),
            ],
            'explains' => $explains,
            'all_queries' => $this->queries,
            'recommendations' => $this->buildRecommendations($tableStats, $allQueries, $scenarios),
        ];

        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($outputPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $this->newLine();
        $this->info('Report written to: ' . $outputPath);
        $this->table(
            ['Scenario', 'Queries', 'Time (ms)', 'Notes'],
            collect($scenarios)->map(fn ($s, $name) => [
                $name,
                $s['query_count'] ?? 0,
                $s['elapsed_ms'] ?? 0,
                $s['error'] ?? ($s['note'] ?? 'ok'),
            ])->values()->all()
        );

        $this->newLine();
        $this->info('Top slow queries:');
        foreach (array_slice($slowest, 0, 10) as $i => $q) {
            $this->line(sprintf(
                '  #%d [%.2f ms] (%s) %s',
                $i + 1,
                $q['time_ms'],
                $q['scenario'] ?? '-',
                $this->truncateSql($q['sql'], 160)
            ));
        }

        return self::SUCCESS;
    }

    private function runScenario(string $name, callable $fn): array
    {
        $beforeCount = count($this->queries);
        $start = microtime(true);
        $this->currentScenario = $name;
        $result = ['name' => $name, 'error' => null, 'note' => null];

        try {
            $extra = $fn();
            if (is_array($extra)) {
                $result = array_merge($result, $extra);
            }
        } catch (Throwable $e) {
            $result['error'] = $e->getMessage();
            $this->error("Scenario {$name} failed: " . $e->getMessage());
        } finally {
            $this->currentScenario = null;
        }

        $elapsedMs = round((microtime(true) - $start) * 1000, 2);
        $scenarioQueries = array_slice($this->queries, $beforeCount);
        $sqlTime = round(array_sum(array_column($scenarioQueries, 'time_ms')), 2);

        $result['elapsed_ms'] = $elapsedMs;
        $result['query_count'] = count($scenarioQueries);
        $result['sql_time_ms'] = $sqlTime;
        $result['top_queries'] = array_values(array_slice(
            collect($scenarioQueries)->sortByDesc('time_ms')->take(5)->map(fn ($q) => [
                'time_ms' => $q['time_ms'],
                'sql' => $q['sql'],
            ])->all(),
            0
        ));

        $this->line(sprintf(
            '  ✓ %-28s %4d queries, wall %.0f ms, SQL %.0f ms',
            $name,
            $result['query_count'],
            $elapsedMs,
            $sqlTime
        ));

        return $result;
    }

    private function measureAdminDashboardCharts(): array
    {
        $controller = app(DashboardController::class);
        $latest = TahunAjaran::select('id', 'tahun', 'periode')
            ->orderBy('tahun', 'desc')
            ->orderByRaw("FIELD(periode, 'ganjil', 'genap') DESC")
            ->first();
        $tahunId = $latest?->id;

        // Mirror adminDashboard data path without rendering the view.
        $this->callPrivate($controller, 'getDashboardStatistics', [$tahunId]);
        $this->callPrivate($controller, 'getAverageScoreHistoryData', []);
        $this->callPrivate($controller, 'getCPLAchievementData', []);
        $this->callPrivate($controller, 'getMatkulPerformanceData', [$tahunId]);
        $this->callPrivate($controller, 'getCourseCompletionData', [$tahunId]);
        $this->callPrivate($controller, 'getCourseTypeDistribution', []);
        $this->callPrivate($controller, 'getTopStudentsData', [$tahunId]);

        return ['note' => 'private chart helpers (cold, no Cache::remember)', 'tahun_ajaran_id' => $tahunId];
    }

    private function measureDosenDashboard(): array
    {
        $user = User::where('role', 'dosen')->whereHas('dosen')->first();
        if (!$user) {
            return ['note' => 'skipped: no dosen user'];
        }

        Auth::login($user);
        $request = Request::create('/dosen/dashboard', 'GET');
        app()->instance('request', $request);

        try {
            $controller = app(DashboardController::class);
            $controller->dosenDashboard($request);
        } finally {
            Auth::logout();
        }

        return ['note' => 'full dosenDashboard()', 'user_id' => $user->id];
    }

    private function measureMahasiswaDashboard(): array
    {
        $user = User::where('role', 'mahasiswa')->whereHas('mahasiswa')->first();
        if (!$user) {
            return ['note' => 'skipped: no mahasiswa user'];
        }

        Auth::login($user);
        try {
            $controller = app(DashboardController::class);
            $controller->mahasiswaDashboard();
        } finally {
            Auth::logout();
        }

        return ['note' => 'full mahasiswaDashboard()', 'user_id' => $user->id, 'mahasiswa_id' => $user->mahasiswa->id];
    }

    private function measureCpmkLaporanShow(): array
    {
        $user = User::where('role', 'dosen')->whereHas('dosen')->first();
        if (!$user) {
            return ['note' => 'skipped: no dosen user'];
        }

        $dosenId = $user->dosen->id;
        $tam = TahunAjaranMatkul::whereHas('kelas.dosenPengampuKelas', function ($q) use ($dosenId) {
            $q->where('dosenId', $dosenId);
        })->orderByDesc('id')->first();

        if (!$tam) {
            // Fall back to any TAM with nilai rows
            $tam = TahunAjaranMatkul::whereHas('nilai')->orderByDesc('id')->first();
            if (!$tam) {
                return ['note' => 'skipped: no tahun_ajaran_matkul with nilai'];
            }

            // Bypass dosen ownership check by calling getCpmkData via reflection
            Auth::login($user);
            try {
                $controller = app(CpmkLaporanController::class);
                $tam->load(['mataKuliah', 'kelas.kelasMahasiswa.mahasiswa', 'cpmkMatKul.cpmk']);
                $this->callPrivate($controller, 'getCpmkData', [$tam]);
            } finally {
                Auth::logout();
            }

            return ['note' => 'getCpmkData only (no ownership match)', 'tahun_ajaran_matkul_id' => $tam->id];
        }

        Auth::login($user);
        $request = Request::create('/dosen/cpmk-laporan/' . $tam->id, 'GET');
        try {
            $controller = app(CpmkLaporanController::class);
            $controller->show($request, $tam->id);
        } finally {
            Auth::logout();
        }

        return ['note' => 'CpmkLaporanController::show', 'tahun_ajaran_matkul_id' => $tam->id];
    }

    private function measureCapaian(): array
    {
        $user = User::where('role', 'mahasiswa')->whereHas('mahasiswa')->first();
        if (!$user) {
            return ['note' => 'skipped: no mahasiswa user'];
        }

        Auth::login($user);
        try {
            $controller = app(CapaianController::class);
            $this->callPrivate($controller, 'buildCplData', [$user->mahasiswa->id, null, null, true]);
        } finally {
            Auth::logout();
        }

        return ['note' => 'CapaianController::buildCplData', 'mahasiswa_id' => $user->mahasiswa->id];
    }

    private function measureCplAchievement(): array
    {
        Cache::forget('pimpinan.cpl-achievement.rows');

        // Measure the heavy Nilai load separately so OOM mid-PHP still leaves SQL timings.
        $tamIds = TahunAjaranMatkul::whereHas('mataKuliah', fn ($q) => $q->where('jenis', 'wajib'))
            ->pluck('id');
        $cpmkIds = DB::table('cpmk_cpl')->distinct()->pluck('cpmkId');

        $nilaiQueryMs = null;
        $nilaiRows = null;
        try {
            $t0 = microtime(true);
            $nilaiRows = DB::table('nilai')
                ->whereIn('tahunAjaranMatkulId', $tamIds)
                ->when($cpmkIds->isNotEmpty(), fn ($q) => $q->whereIn('cpmkId', $cpmkIds))
                ->count();
            $nilaiQueryMs = round((microtime(true) - $t0) * 1000, 2);
        } catch (Throwable $e) {
            $nilaiQueryMs = null;
        }

        try {
            $controller = app(CplAchievementController::class);
            $this->callPrivate($controller, 'buildAchievementRows', []);
            $note = 'CplAchievementController::buildAchievementRows (cache bypassed)';
        } catch (Throwable $e) {
            return [
                'note' => 'partial: buildAchievementRows failed — ' . $e->getMessage(),
                'nilai_matching_count' => $nilaiRows,
                'nilai_count_ms' => $nilaiQueryMs,
                'tam_count' => $tamIds->count(),
            ];
        }

        return [
            'note' => $note,
            'nilai_matching_count' => $nilaiRows,
            'nilai_count_ms' => $nilaiQueryMs,
            'tam_count' => $tamIds->count(),
        ];
    }

    private function callPrivate(object $instance, string $method, array $args = []): mixed
    {
        $ref = new ReflectionMethod($instance, $method);
        $ref->setAccessible(true);

        return $ref->invokeArgs($instance, $args);
    }

    private function collectTableStats(): array
    {
        $tables = [
            'nilai',
            'mahasiswa',
            'dosen',
            'cpmk',
            'cpl',
            'bobot',
            'cpmk_cpl',
            'cpmk_mat_kul',
            'kelas_mahasiswa',
            'tahun_ajaran_matkul',
            'kelas',
            'mata_kuliah',
            'dosen_pengampu_kelas',
        ];

        $stats = [];
        foreach ($tables as $table) {
            try {
                $stats[$table] = (int) DB::table($table)->count();
            } catch (Throwable $e) {
                $stats[$table] = ['error' => $e->getMessage()];
            }
        }

        return $stats;
    }

    private function collectIndexes(array $tables): array
    {
        $driver = config('database.connections.' . config('database.default') . '.driver');
        if ($driver !== 'mysql') {
            return ['note' => 'SHOW INDEX only supported for mysql, driver=' . $driver];
        }

        $out = [];
        foreach ($tables as $table) {
            try {
                $out[$table] = DB::select('SHOW INDEX FROM `' . str_replace('`', '', $table) . '`');
            } catch (Throwable $e) {
                $out[$table] = ['error' => $e->getMessage()];
            }
        }

        return $out;
    }

    private function explainQuery(array $q, int $rank): array
    {
        $sql = $q['sql'];
        $bindings = $q['bindings'] ?? [];

        // Only EXPLAIN SELECT-like statements
        $trimmed = ltrim($sql);
        if (!preg_match('/^(select|with)\b/i', $trimmed)) {
            return [
                'rank' => $rank,
                'time_ms' => $q['time_ms'],
                'scenario' => $q['scenario'],
                'sql' => $sql,
                'skipped' => 'not a SELECT',
            ];
        }

        try {
            $explain = DB::select('EXPLAIN ' . $sql, $bindings);

            return [
                'rank' => $rank,
                'time_ms' => $q['time_ms'],
                'scenario' => $q['scenario'],
                'sql' => $sql,
                'bindings' => $bindings,
                'explain' => json_decode(json_encode($explain), true),
            ];
        } catch (Throwable $e) {
            return [
                'rank' => $rank,
                'time_ms' => $q['time_ms'],
                'scenario' => $q['scenario'],
                'sql' => $sql,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function buildRecommendations(array $tableStats, array $allQueries, array $scenarios): array
    {
        $nilaiCount = is_int($tableStats['nilai'] ?? null) ? $tableStats['nilai'] : null;
        $recs = [];

        $recs[] = [
            'priority' => 'high',
            'area' => 'admin/pimpinan dashboard',
            'issue' => 'Multiple full-table aggregations on nilai (AVG/COUNT DISTINCT group by) for chart builders; getCPLAchievementData joins cpl→cpmk_cpl→cpmk→nilai without tahun filter.',
            'fix' => 'Keep Cache::remember (already 10m). Add covering indexes; rewrite completion/grade charts to SQL aggregations instead of loading all rows into PHP.',
            'suggested_indexes' => [
                'nilai (tahunAjaranMatkulId, nilai)',
                'nilai (cpmkId, nilai)',
                'nilai (mahasiswaId, tahunAjaranMatkulId, nilai)',
                'tahun_ajaran_matkul (tahunAjaranId, mataKuliahId)',
            ],
        ];

        $recs[] = [
            'priority' => 'high',
            'area' => 'getCourseCompletionData / getMatkulPerformanceData',
            'issue' => 'Per-course loop: SELECT * FROM nilai WHERE tahunAjaranMatkulId=? then group in PHP (N+1 style, up to 8–5 courses).',
            'fix' => 'Replace with single grouped SQL: SELECT mahasiswaId, AVG(nilai) … GROUP BY tahunAjaranMatkulId, mahasiswaId, then bucket grades in PHP or CASE.',
        ];

        $recs[] = [
            'priority' => 'high',
            'area' => 'mahasiswaDashboard',
            'issue' => 'Classic N+1: for each CPMK under each CPL, query Nilai + Bobot::whereHas. Scales with #CPMK.',
            'fix' => 'Batch like CapaianController::buildCplData — one Nilai::where(mahasiswaId) + Bobot map; reuse that for dashboard.',
        ];

        $recs[] = [
            'priority' => 'medium',
            'area' => 'dosenDashboard',
            'issue' => 'Line chart loads TahunAjaranMatkul twice per MK (all years) with nested kelas.kelasMahasiswa — duplicate queries and heavy eager loads.',
            'fix' => 'One query for all TAMs of dosen MKs; aggregate avg(totalNilai) in SQL via kelas_mahasiswa.',
        ];

        $recs[] = [
            'priority' => 'medium',
            'area' => 'cpl_achievement',
            'issue' => 'Loads all nilai for all wajib TAMs into memory (with bobot). Fine with cache, painful on cold miss when nilai ~' . ($nilaiCount ?? '?') . ' rows.',
            'fix' => 'Pre-aggregate in SQL or materialized summary table; keep cache; consider filtering by latest kurikulum/tahun.',
        ];

        $recs[] = [
            'priority' => 'low',
            'area' => 'indexes',
            'issue' => 'nilai FK columns get automatic indexes; composite nilai_tam_mahasiswa_cpmk_index exists but AVG(nilai)>0 scans may not use it well without nilai in key or filter selectivity.',
            'fix' => 'Add (tahunAjaranMatkulId, nilai) and (mahasiswaId, cpmkId); ensure migration 2026_03_23 composite is applied.',
        ];

        if (($scenarios['mahasiswa_dashboard']['query_count'] ?? 0) > 50) {
            $recs[] = [
                'priority' => 'critical',
                'area' => 'mahasiswa_dashboard query volume',
                'issue' => 'Observed ' . $scenarios['mahasiswa_dashboard']['query_count'] . ' queries — strong N+1 signal.',
                'fix' => 'Immediate: reuse Capaian batching; cache per mahasiswaId for 5–10 minutes.',
            ];
        }

        return $recs;
    }

    private function truncateSql(string $sql, int $len): string
    {
        $sql = preg_replace('/\s+/', ' ', $sql) ?? $sql;

        return strlen($sql) > $len ? substr($sql, 0, $len - 3) . '...' : $sql;
    }
}
