<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB; // Added DB facade import

class DashboardController extends Controller
{
    /**
     * Redirect user to appropriate dashboard based on their role
     */
    public function index()
    {
        $user = Auth::user();

        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'dosen':
                return redirect()->route('dosen.dashboard');
            case 'mahasiswa':
                return redirect()->route('mahasiswa.dashboard');
            case 'pimpinan':
                return redirect()->route('pimpinan.dashboard');
            default:
                return redirect()->route('login');
        }
    }

    /**
     * Admin Dashboard
     */
    public function adminDashboard(Request $request)
    {
        // Get the latest tahun ajaran as default - OPTIMIZED query
        $latestTahunAjaran = \App\Models\TahunAjaran::select('id', 'tahun', 'periode')
            ->orderBy('tahun', 'desc')
            ->orderByRaw("FIELD(periode, 'ganjil', 'genap') DESC")
            ->first();

        $selectedTahunAjaranId = $request->get('tahun_ajaran_filter', $latestTahunAjaran?->id);

        // OPTIMIZED: Load all data in parallel with efficient queries
        $statistics = $this->getDashboardStatistics($selectedTahunAjaranId);
        $tahunAjaranList = \App\Models\TahunAjaran::select('id', 'tahun', 'periode')
            ->orderBy('tahun', 'desc')
            ->orderByRaw("FIELD(periode, 'ganjil', 'genap') DESC")
            ->get();

        // Render shell dashboard immediately. Chart-heavy queries are loaded
        // asynchronously through adminDashboardChartData().
        return view('admin.dashboard', compact(
            'statistics',
            'tahunAjaranList',
            'selectedTahunAjaranId'
        ));
    }

    /**
     * Return cached chart payloads without blocking the initial dashboard HTML.
     */
    public function adminDashboardChartData(Request $request)
    {
        $selectedTahunAjaranId = $request->get('tahun_ajaran_filter');

        return response()->json([
            'chartData' => Cache::remember(
                'dashboard.avg-score-history',
                600,
                fn () => $this->getAverageScoreHistoryData()
            ),
            'cplAchievementData' => Cache::remember(
                'dashboard.cpl-achievement.v2',
                600,
                fn () => $this->getCPLAchievementData()
            ),
            'matkulPerformanceData' => Cache::remember(
                'dashboard.matkul-performance.' . ($selectedTahunAjaranId ?? 'all'),
                600,
                fn () => $this->getMatkulPerformanceData($selectedTahunAjaranId)
            ),
            'courseCompletionData' => Cache::remember(
                'dashboard.course-completion.' . ($selectedTahunAjaranId ?? 'all'),
                600,
                fn () => $this->getCourseCompletionData($selectedTahunAjaranId)
            ),
            'courseTypeData' => Cache::remember(
                'dashboard.course-type',
                600,
                fn () => $this->getCourseTypeDistribution()
            ),
            'topStudentsData' => Cache::remember(
                'dashboard.top-students.' . ($selectedTahunAjaranId ?? 'all'),
                600,
                fn () => $this->getTopStudentsData($selectedTahunAjaranId)
            ),
        ]);
    }

    /**
     * OPTIMIZED: Get all dashboard statistics in batch
     */
    private function getDashboardStatistics($selectedTahunAjaranId = null)
    {
        // Fetch the five independent master-data counts in one database round trip.
        $masterStats = DB::query()
            ->selectSub(DB::table('mahasiswa')->selectRaw('COUNT(*)'), 'total_mahasiswa')
            ->selectSub(DB::table('dosen')->selectRaw('COUNT(*)'), 'total_dosen')
            ->selectSub(DB::table('mata_kuliah')->selectRaw('COUNT(*)'), 'total_mata_kuliah')
            ->selectSub(DB::table('cpl')->selectRaw('COUNT(*)'), 'total_cpl')
            ->selectSub(DB::table('cpmk')->selectRaw('COUNT(*)'), 'total_cpmk')
            ->first();

        // OPTIMIZED: Active courses count
        $activeCoursesQuery = DB::table('tahun_ajaran_matkul')
            ->selectRaw('COUNT(DISTINCT tahun_ajaran_matkul.id) as active_courses')
            ->join('nilai', 'tahun_ajaran_matkul.id', '=', 'nilai.tahunAjaranMatkulId')
            ->where('nilai.nilai', '>', 0);

        if ($selectedTahunAjaranId) {
            $activeCoursesQuery->where('tahun_ajaran_matkul.tahunAjaranId', $selectedTahunAjaranId);
        }

        $activeCoursesStats = $activeCoursesQuery->first();

        return [
            'totalMahasiswa' => $masterStats->total_mahasiswa ?? 0,
            'totalDosen' => $masterStats->total_dosen ?? 0,
            'totalMataKuliah' => $masterStats->total_mata_kuliah ?? 0,
            'totalCPL' => $masterStats->total_cpl ?? 0,
            'totalCPMK' => $masterStats->total_cpmk ?? 0,
            'activeCourses' => $activeCoursesStats->active_courses ?? 0
        ];
    }

    /**
     * OPTIMIZED: Get average score history data for line chart - SHOW ALL YEARS (no filter) - LIMIT TO TOP 5
     */
    private function getAverageScoreHistoryData()
    {
        // Get top 5 mata kuliah with most students
        $topCourses = \App\Models\TahunAjaranMatkul::select('mataKuliahId')
            ->selectRaw('COUNT(DISTINCT n.mahasiswaId) as student_count')
            ->join('nilai as n', 'tahun_ajaran_matkul.id', '=', 'n.tahunAjaranMatkulId')
            ->where('n.nilai', '>', 0)
            ->groupBy('mataKuliahId')
            ->orderByDesc('student_count')
            ->limit(5)
            ->pluck('mataKuliahId');

        // Get data for top courses
        $data = \App\Models\TahunAjaranMatkul::select([
                'tahun_ajaran_matkul.mataKuliahId',
                'tahun_ajaran.tahun',
                'tahun_ajaran.periode',
                'mata_kuliah.namaMatkul'
            ])
            ->selectRaw('AVG(n.nilai) as avg_score')
            ->join('tahun_ajaran', 'tahun_ajaran_matkul.tahunAjaranId', '=', 'tahun_ajaran.id')
            ->join('mata_kuliah', 'tahun_ajaran_matkul.mataKuliahId', '=', 'mata_kuliah.id')
            ->join('nilai as n', 'tahun_ajaran_matkul.id', '=', 'n.tahunAjaranMatkulId')
            ->whereIn('tahun_ajaran_matkul.mataKuliahId', $topCourses)
            ->where('n.nilai', '>', 0)
            ->groupBy('tahun_ajaran_matkul.mataKuliahId', 'tahun_ajaran.tahun', 'tahun_ajaran.periode', 'mata_kuliah.namaMatkul')
            ->get()
            ->groupBy('mataKuliahId')
            ->toArray();

        $chartLabels = [];
        $chartDatasets = [];

        $index = 0;
        foreach ($data as $matkulId => $records) {
            $matkulName = $records[0]['namaMatkul'];
            $averagesByYear = [];

            foreach ($records as $record) {
                $yearLabel = $record['tahun'] . ' - ' . ucfirst($record['periode']);
                $avgScore = round($record['avg_score'], 2);

                if ($avgScore > 0) {
                    $averagesByYear[$yearLabel] = $avgScore;

                    if (!in_array($yearLabel, $chartLabels)) {
                        $chartLabels[] = $yearLabel;
                    }
                }
            }

            // Only add datasets with data
            if (!empty($averagesByYear)) {
                $chartDatasets[] = [
                    'label' => $matkulName,
                    'data' => $averagesByYear,
                    'borderColor' => $this->getChartColors($index),
                    'backgroundColor' => $this->getChartColors($index, true),
                    'tension' => 0.4
                ];
                $index++;
            }
        }

        // Sort labels chronologically
        usort($chartLabels, function($a, $b) {
            $yearA = (int)explode(' - ', $a)[0];
            $yearB = (int)explode(' - ', $b)[0];
            $periodeA = explode(' - ', $a)[1];
            $periodeB = explode(' - ', $b)[1];

            if ($yearA != $yearB) {
                return $yearA <=> $yearB;
            }

            $periodeOrder = ['ganjil' => 1, 'genap' => 2];
            return ($periodeOrder[strtolower($periodeA)] ?? 3) <=> ($periodeOrder[strtolower($periodeB)] ?? 3);
        });

        // Reorder datasets data according to sorted labels
        foreach ($chartDatasets as &$dataset) {
            $sortedData = [];
            foreach ($chartLabels as $label) {
                $sortedData[] = $dataset['data'][$label] ?? 0;
            }
            $dataset['data'] = $sortedData;
        }

        // If no data, show placeholder
        if (empty($chartLabels)) {
            $chartLabels = ['Belum Ada Data'];
            $chartDatasets = [
                [
                    'label' => 'Tidak Ada Data',
                    'data' => [0],
                    'borderColor' => '#9CA3AF',
                    'backgroundColor' => '#9CA3AF80',
                    'tension' => 0.4
                ]
            ];
        }

        return [
            'labels' => $chartLabels,
            'datasets' => $chartDatasets
        ];
    }

    /**
     * OPTIMIZED: Get CPL achievement data for bar chart - FIXED
     */
    private function getCPLAchievementData()
    {
        $scope = app(\App\Services\CplAssessmentScope::class);

        $query = DB::table('cpl')
            ->select('cpl.id', 'cpl.kodeCpl', 'cpl.deskripsi')
            ->selectRaw('AVG(n.nilai) as avg_score')
            ->join('cpmk_cpl', 'cpl.id', '=', 'cpmk_cpl.cplId')
            ->join('nilai as n', function ($join) {
                $join->on('cpmk_cpl.cpmkId', '=', 'n.cpmkId')
                    ->where('n.nilai', '>', 0);
            })
            ->join('tahun_ajaran_matkul as tam', 'tam.id', '=', 'n.tahunAjaranMatkulId');

        $scope->applyAssessedMatkulConstraint($query, null, 'tam');

        $cplData = $query
            ->groupBy('cpl.id', 'cpl.kodeCpl', 'cpl.deskripsi')
            ->orderBy('cpl.kodeCpl')
            ->get();

        $labels = [];
        $data = [];
        $backgroundColors = [];
        $descriptions = [];

        foreach ($cplData as $index => $cpl) {
            $shortDesc = $cpl->deskripsi
                ? \Illuminate\Support\Str::limit(trim($cpl->deskripsi), 40)
                : '';
            $labels[] = $shortDesc
                ? $cpl->kodeCpl . ' — ' . $shortDesc
                : $cpl->kodeCpl;
            $descriptions[] = $cpl->deskripsi ?? '';
            $data[] = round($cpl->avg_score, 2);
            $backgroundColors[] = $this->getChartColors($index, true);
        }

        if (empty($labels)) {
            $labels = ['Belum Ada Data'];
            $data = [0];
            $backgroundColors = ['#9CA3AF'];
            $descriptions = [''];
        }

        return [
            'labels' => $labels,
            'descriptions' => $descriptions,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                    'borderColor' => $backgroundColors,
                    'borderWidth' => 1
                ]
            ]
        ];
    }

    /**
     * OPTIMIZED: Get grade distribution data per mata kuliah (Bar Chart) - FIXED
     */
    private function getMatkulPerformanceData($selectedTahunAjaranId = null)
    {
        $topMatkulQuery = DB::table('nilai as n')
            ->select([
                'tahun_ajaran_matkul.id as tamId',
                'mata_kuliah.kodeMatkul',
                'mata_kuliah.namaMatkul'
            ])
            ->selectRaw('COUNT(DISTINCT n.mahasiswaId) as student_count')
            ->join('tahun_ajaran_matkul', 'n.tahunAjaranMatkulId', '=', 'tahun_ajaran_matkul.id')
            ->join('mata_kuliah', 'tahun_ajaran_matkul.mataKuliahId', '=', 'mata_kuliah.id')
            ->where('n.nilai', '>', 0);

        if ($selectedTahunAjaranId) {
            $topMatkulQuery->where('tahun_ajaran_matkul.tahunAjaranId', $selectedTahunAjaranId);
        }

        $topMatkul = $topMatkulQuery->groupBy('tahun_ajaran_matkul.id', 'mata_kuliah.kodeMatkul', 'mata_kuliah.namaMatkul')
            ->orderByDesc('student_count')
            ->limit(5)
            ->get();

        $allGrades = ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'D', 'E'];
        $labels = $allGrades;
        $datasets = [];

        $tamIds = $topMatkul->pluck('tamId')->filter()->values();
        $gradeCountsByTam = collect();

        if ($tamIds->isNotEmpty()) {
            $studentAvgs = DB::table('nilai as n')
                ->select('n.tahunAjaranMatkulId')
                ->selectRaw('AVG(n.nilai) as avg_nilai')
                ->whereIn('n.tahunAjaranMatkulId', $tamIds)
                ->where('n.nilai', '>', 0)
                ->groupBy('n.tahunAjaranMatkulId', 'n.mahasiswaId');

            $gradeCountsByTam = DB::query()
                ->fromSub($studentAvgs, 'student_avgs')
                ->select('tahunAjaranMatkulId')
                ->selectRaw('CASE
                    WHEN avg_nilai >= 80 THEN "A"
                    WHEN avg_nilai >= 75 THEN "A-"
                    WHEN avg_nilai >= 70 THEN "B+"
                    WHEN avg_nilai >= 65 THEN "B"
                    WHEN avg_nilai >= 60 THEN "B-"
                    WHEN avg_nilai >= 55 THEN "C+"
                    WHEN avg_nilai >= 50 THEN "C"
                    WHEN avg_nilai >= 40 THEN "D"
                    ELSE "E"
                END as grade')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('tahunAjaranMatkulId', 'grade')
                ->get()
                ->groupBy('tahunAjaranMatkulId');
        }

        foreach ($topMatkul as $index => $matkul) {
            $gradeRows = $gradeCountsByTam->get($matkul->tamId, collect())->keyBy('grade');
            $data = [];
            foreach ($allGrades as $grade) {
                $data[] = (int) ($gradeRows->get($grade)->count ?? 0);
            }

            $datasets[] = [
                'label' => $matkul->kodeMatkul . ' - ' . $matkul->namaMatkul,
                'data' => $data,
                'backgroundColor' => $this->getChartColors($index, true),
                'borderColor' => $this->getChartColors($index),
                'borderWidth' => 1
            ];
        }

        if (empty($datasets)) {
            $labels = ['Belum Ada Data'];
            $datasets = [
                [
                    'label' => 'Tidak Ada Data',
                    'data' => [1],
                    'backgroundColor' => '#9CA3AF80',
                    'borderColor' => '#9CA3AF',
                    'borderWidth' => 1
                ]
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets
        ];
    }

    /**
     * OPTIMIZED: Get course completion rate data - FIXED
     */
    private function getCourseCompletionData($selectedTahunAjaranId = null)
    {
        $query = \App\Models\TahunAjaranMatkul::select([
                'tahun_ajaran_matkul.id',
                'mata_kuliah.kodeMatkul'
            ])
            ->selectRaw('COUNT(DISTINCT n.mahasiswaId) as total_students')
            ->join('mata_kuliah', 'tahun_ajaran_matkul.mataKuliahId', '=', 'mata_kuliah.id')
            ->join('nilai as n', 'tahun_ajaran_matkul.id', '=', 'n.tahunAjaranMatkulId')
            ->where('n.nilai', '>', 0)
            ->groupBy('tahun_ajaran_matkul.id', 'mata_kuliah.kodeMatkul')
            ->having('total_students', '>', 0)
            ->orderByDesc('total_students')
            ->limit(8);

        if ($selectedTahunAjaranId) {
            $query->where('tahun_ajaran_matkul.tahunAjaranId', $selectedTahunAjaranId);
        }

        $courses = $query->get();
        $courseIds = $courses->pluck('id')->filter()->values();

        $labels = [];
        $completionRates = [];
        $backgroundColors = [];

        $passedByTam = collect();
        if ($courseIds->isNotEmpty()) {
            $studentAvgs = DB::table('nilai')
                ->select('tahunAjaranMatkulId', 'mahasiswaId')
                ->selectRaw('AVG(nilai) as avg_nilai')
                ->whereIn('tahunAjaranMatkulId', $courseIds)
                ->where('nilai', '>', 0)
                ->groupBy('tahunAjaranMatkulId', 'mahasiswaId');

            $passedByTam = DB::query()
                ->fromSub($studentAvgs, 'student_avgs')
                ->select('tahunAjaranMatkulId')
                ->selectRaw('SUM(CASE WHEN avg_nilai >= 40 THEN 1 ELSE 0 END) as passed_students')
                ->selectRaw('COUNT(*) as total_students')
                ->groupBy('tahunAjaranMatkulId')
                ->get()
                ->keyBy('tahunAjaranMatkulId');
        }

        foreach ($courses as $course) {
            $labels[] = $course->kodeMatkul;
            $agg = $passedByTam->get($course->id);
            $totalStudents = (int) ($agg->total_students ?? $course->total_students ?? 0);
            $passedStudents = (int) ($agg->passed_students ?? 0);

            $completionRate = $totalStudents > 0
                ? ($passedStudents / $totalStudents) * 100
                : 0;
            $completionRates[] = round($completionRate, 2);

            if ($completionRate >= 80) {
                $backgroundColors[] = '#10B981';
            } elseif ($completionRate >= 60) {
                $backgroundColors[] = '#F59E0B';
            } else {
                $backgroundColors[] = '#EF4444';
            }
        }

        if (empty($labels)) {
            $labels = ['Belum Ada Data'];
            $completionRates = [0];
            $backgroundColors = ['#9CA3AF'];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $completionRates,
                    'backgroundColor' => $backgroundColors,
                    'borderColor' => $backgroundColors,
                    'borderWidth' => 1
                ]
            ]
        ];
    }

    /**
     * OPTIMIZED: Get course type distribution from master mata kuliah (Pie Chart) - FIXED
     */
    private function getCourseTypeDistribution()
    {
        // Get distribution from master mata kuliah table
        $courseTypes = \App\Models\MataKuliah::select('jenis')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('jenis')
            ->pluck('count', 'jenis')
            ->toArray();

        $labels = [];
        $data = [];
        $backgroundColors = ['#3B82F6', '#10B981', '#F59E0B', '#8B5CF6', '#EF4444'];

        // Ensure we have at least some data
        if (count($courseTypes) > 0) {
            foreach ($courseTypes as $type => $count) {
                $labels[] = ucfirst($type ?? 'Lainnya');
                $data[] = $count;
            }
        } else {
            // If no data, show placeholder
            $labels = ['Belum Ada Data'];
            $data = [1];
            $backgroundColors = ['#9CA3AF'];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => array_slice($backgroundColors, 0, count($labels)),
                    'borderColor' => array_slice($backgroundColors, 0, count($labels)),
                    'borderWidth' => 1
                ]
            ]
        ];
    }

    /**
     * OPTIMIZED: Get top performing students (Bar Chart)
     */
    private function getTopStudentsData($selectedTahunAjaranId = null)
    {
        // OPTIMIZED: Single query with proper joins and aggregation - Calculate from nilai table
        $query = DB::table('nilai as n')
            ->select([
                'n.mahasiswaId',
                'mahasiswa.nama',
                'mahasiswa.nim'
            ])
            ->selectRaw('AVG(n.nilai) as avg_score')
            ->join('mahasiswa', 'n.mahasiswaId', '=', 'mahasiswa.id')
            ->join('tahun_ajaran_matkul as tam', 'n.tahunAjaranMatkulId', '=', 'tam.id')
            ->where('n.nilai', '>', 0)
            ->groupBy('n.mahasiswaId', 'mahasiswa.nama', 'mahasiswa.nim');

        if ($selectedTahunAjaranId) {
            $query->where('tam.tahunAjaranId', $selectedTahunAjaranId);
        }

        $topStudents = $query->orderByDesc('avg_score')
            ->limit(10)
            ->get();

        $labels = [];
        $data = [];
        $backgroundColors = [];
        $students = [];

        foreach ($topStudents as $index => $student) {
            $labels[] = $student->nim;
            $data[] = round($student->avg_score, 2);
            $backgroundColors[] = $this->getChartColors($index, true);
            $students[] = [
                'nama' => $student->nama,
                'nim' => $student->nim,
                'avgScore' => round($student->avg_score, 2)
            ];
        }

        // If no data, show placeholder
        if (empty($labels)) {
            $labels = ['Belum Ada Data'];
            $data = [0];
            $backgroundColors = ['#9CA3AF'];
            $students = [];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                    'borderColor' => $backgroundColors,
                    'borderWidth' => 1
                ]
            ],
            'students' => $students
        ];
    }

    /**
     * Get consistent colors for charts
     */
    private function getChartColors($index = 0, $alpha = false)
    {
        // Consistent color palette with good contrast
        $colors = [
            '#3B82F6', // Blue
            '#EF4444', // Red
            '#10B981', // Green
            '#F59E0B', // Yellow
            '#8B5CF6', // Purple
            '#06B6D4', // Cyan
            '#F97316', // Orange
            '#84CC16', // Lime
            '#EC4899', // Pink
            '#6366F1', // Indigo
        ];

        $color = $colors[$index % count($colors)];

        if ($alpha) {
            return $color . '80'; // Add 50% transparency
        }

        return $color;
    }

    /**
     * Get random color (deprecated - use getChartColors instead)
     */
    private function getRandomColor($alpha = false)
    {
        return $this->getChartColors(rand(0, 9), $alpha);
    }

    /**
     * Dosen Dashboard
     */
    public function dosenDashboard(Request $request)
    {
        $user = Auth::user();
        $dosen = $user->dosen;
        if (!$dosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan.');
        }

        // List tahun ajaran untuk filter
        $tahunAjaranList = \App\Models\TahunAjaran::orderBy('tahun', 'desc')->orderBy('periode', 'desc')->get();
        $selectedTahunAjaranId = $request->get('tahun_ajaran_id');
        if (!$selectedTahunAjaranId) {
            $selectedTahunAjaranId = $tahunAjaranList->first() ? $tahunAjaranList->first()->id : null;
        }

        // Mata kuliah diampu dosen (tahun ajaran terpilih)
        $mataKuliahDiampu = \App\Models\TahunAjaranMatkul::whereHas('kelas.dosenPengampuKelas', function($q) use ($dosen) {
            $q->where('dosenId', $dosen->id);
        })
        ->where('tahunAjaranId', $selectedTahunAjaranId)
        ->with(['mataKuliah.kurikulumRef', 'kelas.kelasMahasiswa'])
        ->get();

        // Jumlah MK, kelas, mahasiswa
        $jumlahMK = $mataKuliahDiampu->count();
        $jumlahKelas = $mataKuliahDiampu->flatMap->kelas->count();
        $jumlahMahasiswa = $mataKuliahDiampu->flatMap->kelas->flatMap->kelasMahasiswa->pluck('mahasiswaId')->unique()->count();

        // Progress input nilai: kelas yang sudah lengkap input nilai (semua mahasiswa punya totalNilai)
        $kelasList = $mataKuliahDiampu->flatMap->kelas;
        $kelasProgress = $kelasList->map(function ($kelas) {
            $mahasiswaCount = $kelas->kelasMahasiswa->count();
            $sudahNilai = $kelas->kelasMahasiswa->filter(function ($km) {
                return $km->getRawOriginal('totalNilai') !== null;
            })->count();
            return [
                'kelas' => $kelas,
                'mahasiswaCount' => $mahasiswaCount,
                'sudahNilai' => $sudahNilai,
                'isComplete' => $mahasiswaCount > 0 && $mahasiswaCount == $sudahNilai
            ];
        });
        $jumlahKelasLengkap = $kelasProgress->where('isComplete', true)->count();
        $progressPersen = $kelasList->count() > 0 ? round(($jumlahKelasLengkap / $kelasList->count()) * 100, 1) : 0;

        // Bar chart: jumlah mahasiswa per mata kuliah
        $barChartLabels = $mataKuliahDiampu->map(fn ($mk) => $mk->mataKuliah->namaMatkul)->toArray();
        $barChartData = $mataKuliahDiampu->map(function ($mk) {
            $mahasiswaIds = $mk->kelas->flatMap->kelasMahasiswa->map(function ($km) {
                return $km->mahasiswaId;
            })->unique()->values();
            return $mahasiswaIds->count();
        })->toArray();

        // Pie chart: distribusi grade per mata kuliah (pakai kolom tersimpan, hindari accessor N+1)
        $gradeDistributionPerMK = [];
        foreach ($mataKuliahDiampu as $mk) {
            $mkKelasMahasiswa = $mk->kelas->flatMap->kelasMahasiswa;
            $gradeCounts = ['A' => 0, 'A-' => 0, 'B+' => 0, 'B' => 0, 'B-' => 0, 'C+' => 0, 'C' => 0, 'D' => 0, 'E' => 0];

            foreach ($mkKelasMahasiswa as $km) {
                $grade = $km->getRawOriginal('grade');
                if ($grade && array_key_exists($grade, $gradeCounts)) {
                    $gradeCounts[$grade]++;
                }
            }

            $gradeDistributionPerMK[] = [
                'mataKuliah' => $mk->mataKuliah->namaMatkul,
                'kodeMatkul' => $mk->mataKuliah->kodeMatkul,
                'kurikulum' => $mk->mataKuliah->kurikulum,
                'gradeCounts' => $gradeCounts,
                'totalMahasiswa' => array_sum($gradeCounts)
            ];
        }

        // Pie chart: distribusi grade keseluruhan (untuk chart utama)
        $allKelasMahasiswa = $mataKuliahDiampu->flatMap->kelas->flatMap->kelasMahasiswa;
        $overallGradeCounts = ['A' => 0, 'A-' => 0, 'B+' => 0, 'B' => 0, 'B-' => 0, 'C+' => 0, 'C' => 0, 'D' => 0, 'E' => 0];
        foreach ($allKelasMahasiswa as $km) {
            $grade = $km->getRawOriginal('grade');
            if ($grade && array_key_exists($grade, $overallGradeCounts)) {
                $overallGradeCounts[$grade]++;
            }
        }
        $pieChartLabels = array_keys($overallGradeCounts);
        $pieChartData = array_values($overallGradeCounts);

        // Line chart: progress rata-rata nilai per MK (semua tahun, 1 query batch)
        $lineChartLabels = [];
        $lineChartDatasets = [];

        $mataKuliahSemuaTahun = \App\Models\TahunAjaranMatkul::whereHas('kelas.dosenPengampuKelas', function ($q) use ($dosen) {
            $q->where('dosenId', $dosen->id);
        })
            ->with(['mataKuliah'])
            ->get()
            ->unique('mataKuliahId');

        $mataKuliahIds = $mataKuliahSemuaTahun->pluck('mataKuliahId')->filter()->values();

        $tamAllYears = collect();
        if ($mataKuliahIds->isNotEmpty()) {
            $tamAllYears = \App\Models\TahunAjaranMatkul::whereIn('mataKuliahId', $mataKuliahIds)
                ->with(['tahunAjaran', 'kelas.kelasMahasiswa'])
                ->get()
                ->groupBy('mataKuliahId');
        }

        $allLabels = [];
        $avgByMkAndLabel = [];

        foreach ($mataKuliahSemuaTahun as $mk) {
            $groups = ($tamAllYears->get($mk->mataKuliahId) ?? collect())
                ->groupBy('tahunAjaranId');

            foreach ($groups as $group) {
                $tahunAjaran = $group->first()->tahunAjaran;
                if (!$tahunAjaran) {
                    continue;
                }
                $label = $tahunAjaran->tahun . ' - ' . ucfirst($tahunAjaran->periode);
                $allNilai = $group->flatMap->kelas->flatMap->kelasMahasiswa
                    ->map(fn ($km) => $km->getRawOriginal('totalNilai'))
                    ->filter(fn ($v) => $v !== null);
                $avg = $allNilai->count() > 0 ? round($allNilai->avg(), 2) : null;

                if (!in_array($label, $allLabels, true)) {
                    $allLabels[] = $label;
                }
                $avgByMkAndLabel[$mk->mataKuliahId][$label] = $avg;
            }
        }

        usort($allLabels, function ($a, $b) {
            $yearA = (int) explode(' - ', $a)[0];
            $yearB = (int) explode(' - ', $b)[0];
            $periodeA = explode(' - ', $a)[1];
            $periodeB = explode(' - ', $b)[1];

            if ($yearA != $yearB) {
                return $yearA <=> $yearB;
            }

            $periodeOrder = ['ganjil' => 1, 'genap' => 2];
            return ($periodeOrder[strtolower($periodeA)] ?? 3) <=> ($periodeOrder[strtolower($periodeB)] ?? 3);
        });

        $lineChartLabels = $allLabels;

        foreach ($mataKuliahSemuaTahun as $mk) {
            $perLabel = $avgByMkAndLabel[$mk->mataKuliahId] ?? [];
            $data = [];
            foreach ($lineChartLabels as $label) {
                $data[] = $perLabel[$label] ?? null;
            }

            $lineChartDatasets[] = [
                'label' => $mk->mataKuliah->namaMatkul,
                'data' => $data,
                'borderColor' => $this->getChartColors(count($lineChartDatasets)),
                'backgroundColor' => $this->getChartColors(count($lineChartDatasets), true),
                'tension' => 0.4,
                'fill' => false
            ];
        }

        return view('dosen.dashboard', compact(
            'user',
            'jumlahMK',
            'jumlahKelas',
            'jumlahMahasiswa',
            'progressPersen',
            'jumlahKelasLengkap',
            'kelasList',
            'barChartLabels',
            'barChartData',
            'pieChartLabels',
            'pieChartData',
            'lineChartLabels',
            'lineChartDatasets',
            'gradeDistributionPerMK',
            'tahunAjaranList',
            'selectedTahunAjaranId',
        ));
    }

    /**
     * Mahasiswa Dashboard
     */
    public function mahasiswaDashboard()
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;
        $mahasiswaId = $mahasiswa->id;
        $scope = app(\App\Services\CplAssessmentScope::class);

        $cpls = \App\Models\Cpl::with([
            'cpmk' => function ($q) use ($mahasiswaId) {
                $q->whereHas('nilai', function ($n) use ($mahasiswaId) {
                    $n->where('mahasiswaId', $mahasiswaId);
                })->with(['cpmkMatKul.tahunAjaranMatkul.mataKuliah']);
            },
        ])->get();

        // Matkul asesmen kini global per kurikulum; dashboard mahasiswa memakai semua yang ditandai asesmen.
        $assessedSet = array_flip($scope->assessedMataKuliahIds()->all());
        $allCpmkIds = $cpls->flatMap(fn ($cpl) => $cpl->cpmk->pluck('id'))->unique()->values();

        $nilaiByCpmk = collect();
        if ($allCpmkIds->isNotEmpty()) {
            $nilaiByCpmk = \App\Models\Nilai::where('mahasiswaId', $mahasiswaId)
                ->whereIn('cpmkId', $allCpmkIds)
                ->with(['bobot.komponen', 'tahunAjaranMatkul'])
                ->get()
                ->groupBy('cpmkId');
        }

        $tamIdsNeeded = $cpls->flatMap(function ($cpl) use ($assessedSet) {
            return $cpl->cpmk->flatMap(function ($cpmk) use ($assessedSet) {
                return $cpmk->cpmkMatKul->filter(function ($rel) use ($assessedSet) {
                    $mkId = (int) ($rel->tahunAjaranMatkul->mataKuliahId ?? 0);
                    return $mkId && isset($assessedSet[$mkId]);
                })->map(fn ($rel) => $rel->tahunAjaranMatkul->id ?? null);
            });
        })->filter()->unique()->values();

        $bobotSumByCpmkTam = collect();
        if ($allCpmkIds->isNotEmpty()) {
            $bobotSumByCpmkTam = \App\Models\Bobot::query()
                ->select('cpmkId', 'tahunAjaranMatkulId')
                ->selectRaw('SUM(bobot) as total_bobot')
                ->whereIn('cpmkId', $allCpmkIds)
                ->when($tamIdsNeeded->isNotEmpty(), fn ($q) => $q->whereIn('tahunAjaranMatkulId', $tamIdsNeeded))
                ->groupBy('cpmkId', 'tahunAjaranMatkulId')
                ->get()
                ->groupBy(fn ($row) => $row->cpmkId . ':' . $row->tahunAjaranMatkulId);
        }

        $cpl_cpmk_data = [];
        if ($cpls->count() > 0) {
            foreach ($cpls as $cpl) {
                $cpmk_data = [];
                $totalBobotCpl = 0;
                $totalNilaiCpl = 0;

                foreach ($cpl->cpmk as $cpmk) {
                    $allNilaiForCpmk = $nilaiByCpmk->get($cpmk->id, collect());

                    $assessedRels = $cpmk->cpmkMatKul->filter(function ($rel) use ($assessedSet) {
                        $mkId = (int) ($rel->tahunAjaranMatkul->mataKuliahId ?? 0);
                        return $mkId && isset($assessedSet[$mkId]);
                    });

                    if ($assessedRels->isEmpty()) {
                        continue;
                    }

                    foreach ($assessedRels as $cpmkMatKul) {
                        $tam = $cpmkMatKul->tahunAjaranMatkul;
                        $mkId = (int) ($tam->mataKuliahId ?? 0);
                        $tahunAjaranMatkulId = $tam->id ?? null;

                        $nilaiPerKomponenRaw = [];
                        $komponenInfo = [];

                        foreach ($allNilaiForCpmk as $nilaiRecord) {
                            if (!$nilaiRecord->bobot) {
                                continue;
                            }
                            if ((int) ($nilaiRecord->tahunAjaranMatkul->mataKuliahId ?? 0) !== $mkId) {
                                continue;
                            }

                            $nilaiMentah = $nilaiRecord->nilai;
                            $bobotPengali = $nilaiRecord->bobot->bobot;
                            $komponenId = $nilaiRecord->bobot->komponenId;
                            $namaKomponen = $nilaiRecord->bobot->komponen->nama ?? 'Unknown';

                            if (!isset($nilaiPerKomponenRaw[$komponenId])) {
                                $nilaiPerKomponenRaw[$komponenId] = 0;
                            }
                            $nilaiPerKomponenRaw[$komponenId] += $nilaiMentah * ($bobotPengali / 100);

                            $komponenInfo[$komponenId] = [
                                'nama' => $namaKomponen,
                                'bobot' => $bobotPengali,
                            ];
                        }

                        $bobotKey = $cpmk->id . ':' . $tahunAjaranMatkulId;
                        $totalBobotCpmk = (float) ($bobotSumByCpmkTam->get($bobotKey)?->first()->total_bobot ?? 0);

                        $nilaiPerKomponen = [];
                        foreach ($nilaiPerKomponenRaw as $kompId => $nilaiKomp) {
                            $nilaiPerKomponen[$kompId] = $totalBobotCpmk > 0
                                ? round(($nilaiKomp / $totalBobotCpmk) * 100, 2)
                                : 0;
                        }

                        $namaMataKuliah = $tam->mataKuliah->namaMatkul ?? '';
                        $label = $cpmk->kodeCpmk . ' - ' . $namaMataKuliah;
                        $nilaiNormal = round(array_sum($nilaiPerKomponen), 2);

                        $cpmk_data[] = [
                            'label' => $label,
                            'komponen_nilai' => $nilaiPerKomponen,
                            'komponen_info' => $komponenInfo,
                            'total_nilai' => $nilaiNormal,
                            'total_bobot' => $totalBobotCpmk,
                            'nilai_normal' => $nilaiNormal,
                        ];
                        $totalBobotCpl += $totalBobotCpmk;
                        $totalNilaiCpl += $nilaiNormal;
                    }
                }

                $nilaiCpmkRataRata = 0;
                $cpmkTertinggi = '';
                $nilaiCpmkTertinggi = 0;

                if (!empty($cpmk_data)) {
                    $nilaiCpmkRataRata = array_sum(array_column($cpmk_data, 'nilai_normal')) / count($cpmk_data);
                    $maxNilai = max(array_column($cpmk_data, 'nilai_normal'));
                    $nilaiCpmkTertinggi = $maxNilai;

                    foreach ($cpmk_data as $cpmk_item) {
                        if ($cpmk_item['nilai_normal'] == $maxNilai) {
                            $cpmkTertinggi = $cpmk_item['label'];
                            break;
                        }
                    }
                }

                $cpl_cpmk_data[] = [
                    'cpl_label' => $cpl->kodeCpl,
                    'cpmk_data' => $cpmk_data,
                    'nilai_cpl' => round($nilaiCpmkTertinggi, 2),
                    'total_bobot_cpl' => $totalBobotCpl,
                    'total_nilai_cpl' => $totalNilaiCpl,
                    'nilai_cpmk_rata_rata' => $nilaiCpmkRataRata,
                    'cpmk_tertinggi' => $cpmkTertinggi,
                    'nilai_cpmk_tertinggi' => $nilaiCpmkTertinggi,
                ];
            }
        }

        $stat = [
            'jumlah_mk' => 0,
            'jumlah_sks' => 0,
            'ipk' => null,
            'cpl_tercapai' => 0,
        ];

        if ($mahasiswa) {
            $mkDiambil = $mahasiswa->kelasMahasiswa()->with('kelas.tahunAjaranMatkul.mataKuliah')->get();
            $stat['jumlah_mk'] = $mkDiambil->count();
            $stat['jumlah_sks'] = $mkDiambil->sum(function ($km) {
                return $km->kelas->tahunAjaranMatkul->getSks() ?? 0;
            });

            $totalSks = 0;
            $totalNilaiBobot = 0;
            foreach ($mkDiambil as $km) {
                $nilaiAkhir = $km->totalNilai;
                $sks = $km->kelas->tahunAjaranMatkul->getSks() ?? 0;
                $bobot = 0;
                if ($nilaiAkhir !== null) {
                    if ($nilaiAkhir >= 80) $bobot = 4;
                    elseif ($nilaiAkhir >= 75) $bobot = 3.75;
                    elseif ($nilaiAkhir >= 70) $bobot = 3.5;
                    elseif ($nilaiAkhir >= 65) $bobot = 3;
                    elseif ($nilaiAkhir >= 60) $bobot = 2.75;
                    elseif ($nilaiAkhir >= 55) $bobot = 2.5;
                    elseif ($nilaiAkhir >= 50) $bobot = 2;
                    elseif ($nilaiAkhir >= 40) $bobot = 1;
                    else $bobot = 0;
                }
                $totalSks += $sks;
                $totalNilaiBobot += ($bobot * $sks);
            }
            $stat['ipk'] = ($totalSks > 0) ? round($totalNilaiBobot / $totalSks, 2) : null;
            $stat['cpl_tercapai'] = collect($cpl_cpmk_data)->filter(function ($cpl) {
                return ($cpl['nilai_cpl'] ?? 0) > 55;
            })->count();
        }

        return view('mahasiswa.dashboard', compact('user', 'cpl_cpmk_data', 'stat'));
    }



    /**
     * Pimpinan Dashboard
     */
    public function pimpinanDashboard(Request $request)
    {
        // Get the latest tahun ajaran as default - OPTIMIZED query
        $latestTahunAjaran = \App\Models\TahunAjaran::select('id', 'tahun', 'periode')
            ->orderBy('tahun', 'desc')
            ->orderByRaw("FIELD(periode, 'ganjil', 'genap') DESC")
            ->first();

        $selectedTahunAjaranId = $request->get('tahun_ajaran_filter', $latestTahunAjaran?->id);

        // OPTIMIZED: Load all data in parallel with efficient queries
        $statistics = $this->getDashboardStatistics($selectedTahunAjaranId);
        $tahunAjaranList = \App\Models\TahunAjaran::select('id', 'tahun', 'periode')
            ->orderBy('tahun', 'desc')
            ->orderByRaw("FIELD(periode, 'ganjil', 'genap') DESC")
            ->get();

        // Render the shell immediately; chart-heavy aggregates are fetched lazily.
        return view('pimpinan.dashboard', compact(
            'statistics',
            'tahunAjaranList',
            'selectedTahunAjaranId'
        ));
    }

    /**
     * Return the same cached chart payload used by the admin dashboard.
     */
    public function pimpinanDashboardChartData(Request $request)
    {
        return $this->adminDashboardChartData($request);
    }
}
