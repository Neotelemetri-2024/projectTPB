<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // Load chart data efficiently
        $chartData = $this->getAverageScoreHistoryData(); // Remove filter parameter
        $cplAchievementData = $this->getCPLAchievementData(); // Remove filter parameter
        $matkulPerformanceData = $this->getMatkulPerformanceData($selectedTahunAjaranId);
        $courseCompletionData = $this->getCourseCompletionData($selectedTahunAjaranId);
        $courseTypeData = $this->getCourseTypeDistribution();
        $topStudentsData = $this->getTopStudentsData($selectedTahunAjaranId);

        return view('admin.dashboard', compact(
            'statistics',
            'tahunAjaranList',
            'selectedTahunAjaranId',
            'chartData',
            'cplAchievementData',
            'matkulPerformanceData',
            'courseCompletionData',
            'courseTypeData',
            'topStudentsData'
        ));
    }

    /**
     * OPTIMIZED: Get all dashboard statistics in batch
     */
    private function getDashboardStatistics($selectedTahunAjaranId = null)
    {
        // OPTIMIZED: Single query for all statistics
        $stats = DB::table('mahasiswa')
            ->selectRaw('COUNT(*) as total_mahasiswa')
            ->first();

        $dosenStats = DB::table('dosen')
            ->selectRaw('COUNT(*) as total_dosen')
            ->first();

        $matkulStats = DB::table('mata_kuliah')
            ->selectRaw('COUNT(*) as total_mata_kuliah')
            ->first();

        $cplStats = DB::table('cpl')
            ->selectRaw('COUNT(*) as total_cpl')
            ->first();

        $cpmkStats = DB::table('cpmk')
            ->selectRaw('COUNT(*) as total_cpmk')
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
            'totalMahasiswa' => $stats->total_mahasiswa ?? 0,
            'totalDosen' => $dosenStats->total_dosen ?? 0,
            'totalMataKuliah' => $matkulStats->total_mata_kuliah ?? 0,
            'totalCPL' => $cplStats->total_cpl ?? 0,
            'totalCPMK' => $cpmkStats->total_cpmk ?? 0,
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
        // OPTIMIZED: Single query with proper joins and aggregation using pivot table
        $query = \App\Models\Cpl::select([
                'cpl.id',
                'cpl.kodeCpl'
            ])
            ->selectRaw('AVG(n.nilai) as avg_score')
            ->join('cpmk_cpl', 'cpl.id', '=', 'cpmk_cpl.cplId')
            ->join('cpmk', 'cpmk_cpl.cpmkId', '=', 'cpmk.id')
            ->join('nilai as n', 'cpmk.id', '=', 'n.cpmkId')
            ->where('n.nilai', '>', 0);

        $cplData = $query->groupBy('cpl.id', 'cpl.kodeCpl')
            ->get();

        $labels = [];
        $data = [];
        $backgroundColors = [];

        foreach ($cplData as $index => $cpl) {
            $labels[] = $cpl->kodeCpl;
            $data[] = round($cpl->avg_score, 2);
            $backgroundColors[] = $this->getChartColors($index, true);
        }

        // If no data, show placeholder
        if (empty($labels)) {
            $labels = ['Belum Ada Data'];
            $data = [0];
            $backgroundColors = ['#9CA3AF'];
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
            ]
        ];
    }

    /**
     * OPTIMIZED: Get grade distribution data per mata kuliah (Bar Chart) - FIXED
     */
    private function getMatkulPerformanceData($selectedTahunAjaranId = null)
    {
        // Get top 5 mata kuliah with most students
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

        // Define all possible grades
        $allGrades = ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'D', 'E'];
        $labels = $allGrades;

        $datasets = [];
        $colors = ['#10B981', '#34D399', '#60A5FA', '#3B82F6', '#6366F1', '#F59E0B', '#F97316', '#EF4444', '#DC2626'];

        foreach ($topMatkul as $index => $matkul) {
            // Get grade distribution for this mata kuliah
            $gradeDistribution = DB::table('nilai as n')
                ->select(DB::raw('
                    CASE
                        WHEN AVG(n.nilai) >= 80 THEN "A"
                        WHEN AVG(n.nilai) >= 75 THEN "A-"
                        WHEN AVG(n.nilai) >= 70 THEN "B+"
                        WHEN AVG(n.nilai) >= 65 THEN "B"
                        WHEN AVG(n.nilai) >= 60 THEN "B-"
                        WHEN AVG(n.nilai) >= 55 THEN "C+"
                        WHEN AVG(n.nilai) >= 50 THEN "C"
                        WHEN AVG(n.nilai) >= 40 THEN "D"
                        ELSE "E"
                    END as grade
                '))
                ->selectRaw('COUNT(*) as count')
                ->where('n.tahunAjaranMatkulId', $matkul->tamId)
                ->where('n.nilai', '>', 0)
                ->groupBy('n.mahasiswaId')
                ->get()
                ->groupBy('grade');

            // Prepare data for this mata kuliah
            $data = [];
            foreach ($allGrades as $grade) {
                $data[] = $gradeDistribution->get($grade)?->sum('count') ?? 0;
            }

            $datasets[] = [
                'label' => $matkul->kodeMatkul . ' - ' . $matkul->namaMatkul,
                'data' => $data,
                'backgroundColor' => $this->getChartColors($index, true),
                'borderColor' => $this->getChartColors($index),
                'borderWidth' => 1
            ];
        }

        // If no data, show placeholder
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
        // Get all courses with student count
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

        $labels = [];
        $completionRates = [];
        $backgroundColors = [];

        foreach ($courses as $course) {
            $labels[] = $course->kodeMatkul;

            // Get all nilai for this course
            $nilaiData = DB::table('nilai')
                ->where('tahunAjaranMatkulId', $course->id)
                ->where('nilai', '>', 0)
                ->get()
                ->groupBy('mahasiswaId');

            // Calculate passed students (average >= 40)
            $passedStudents = 0;
            foreach ($nilaiData as $mahasiswaId => $nilaiRecords) {
                $avgNilai = $nilaiRecords->avg('nilai');
                if ($avgNilai >= 40) {
                    $passedStudents++;
                }
            }

            $completionRate = $course->total_students > 0 ?
                ($passedStudents / $course->total_students) * 100 : 0;
            $completionRates[] = round($completionRate, 2);

            // Color based on completion rate
            if ($completionRate >= 80) {
                $backgroundColors[] = '#10B981'; // Green
            } elseif ($completionRate >= 60) {
                $backgroundColors[] = '#F59E0B'; // Yellow
            } else {
                $backgroundColors[] = '#EF4444'; // Red
            }
        }

        // If no data, show placeholder
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
        ->with(['mataKuliah', 'kelas.kelasMahasiswa'])
        ->get();

        // Jumlah MK, kelas, mahasiswa
        $jumlahMK = $mataKuliahDiampu->count();
        $jumlahKelas = $mataKuliahDiampu->flatMap->kelas->count();
        $jumlahMahasiswa = $mataKuliahDiampu->flatMap->kelas->flatMap->kelasMahasiswa->pluck('mahasiswaId')->unique()->count();

        // Progress input nilai: kelas yang sudah lengkap input nilai (semua mahasiswa punya totalNilai)
        $kelasList = $mataKuliahDiampu->flatMap->kelas;
        $kelasProgress = $kelasList->map(function($kelas) {
            $mahasiswaCount = $kelas->kelasMahasiswa->count();
            $sudahNilai = $kelas->kelasMahasiswa->whereNotNull('totalNilai')->count();
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
        $barChartLabels = $mataKuliahDiampu->map(fn($mk) => $mk->mataKuliah->namaMatkul)->toArray();
        $barChartData = $mataKuliahDiampu->map(function($mk) {
            $mahasiswaIds = $mk->kelas->flatMap->kelasMahasiswa->map(function($km) {
                return $km->mahasiswaId;
            })->unique()->values();
            return $mahasiswaIds->count();
        })->toArray();

        // Pie chart: distribusi grade per mata kuliah
        $gradeDistributionPerMK = [];
        foreach ($mataKuliahDiampu as $mk) {
            $mkKelasMahasiswa = $mk->kelas->flatMap->kelasMahasiswa;
            $gradeCounts = ['A'=>0,'A-'=>0,'B+'=>0,'B'=>0,'B-'=>0,'C+'=>0,'C'=>0,'D'=>0,'E'=>0];

            foreach ($mkKelasMahasiswa as $km) {
                $grade = $km->grade;
                if ($grade && array_key_exists($grade, $gradeCounts)) {
                    $gradeCounts[$grade]++;
                }
            }

            $gradeDistributionPerMK[] = [
                'mataKuliah' => $mk->mataKuliah->namaMatkul,
                'kodeMatkul' => $mk->mataKuliah->kodeMatkul,
                'gradeCounts' => $gradeCounts,
                'totalMahasiswa' => array_sum($gradeCounts)
            ];
        }

        // Pie chart: distribusi grade keseluruhan (untuk chart utama)
        $allKelasMahasiswa = $mataKuliahDiampu->flatMap->kelas->flatMap->kelasMahasiswa;
        $overallGradeCounts = ['A'=>0,'A-'=>0,'B+'=>0,'B'=>0,'B-'=>0,'C+'=>0,'C'=>0,'D'=>0,'E'=>0];
        foreach ($allKelasMahasiswa as $km) {
            $grade = $km->grade;
            if ($grade && array_key_exists($grade, $overallGradeCounts)) {
                $overallGradeCounts[$grade]++;
            }
        }
        $pieChartLabels = array_keys($overallGradeCounts);
        $pieChartData = array_values($overallGradeCounts);

        // Line chart: progress rata-rata nilai per MK dari waktu ke waktu (per tahun ajaran)
        $lineChartLabels = [];
        $lineChartDatasets = [];
        foreach ($mataKuliahDiampu as $mk) {
            $nilaiPerTahun = \App\Models\TahunAjaranMatkul::where('mataKuliahId', $mk->mataKuliahId)
                ->with(['tahunAjaran', 'kelas.kelasMahasiswa'])
                ->get()
                ->groupBy('tahunAjaranId')
                ->map(function($group) {
                    $tahunAjaran = $group->first()->tahunAjaran;
                    $label = $tahunAjaran->tahun . ' - ' . ucfirst($tahunAjaran->periode);
                    $allNilai = $group->flatMap->kelas->flatMap->kelasMahasiswa->map(function($km) {
                        return $km->totalNilai;
                    })->filter();
                    $avg = $allNilai->count() > 0 ? round($allNilai->avg(),2) : null;
                    return ['label' => $label, 'avg' => $avg];
                });
            foreach ($nilaiPerTahun as $row) {
                if (!in_array($row['label'], $lineChartLabels)) {
                    $lineChartLabels[] = $row['label'];
                }
            }
            $lineChartDatasets[] = [
                'label' => $mk->mataKuliah->namaMatkul,
                'data' => $nilaiPerTahun->map(function($item) {
                    return $item['avg'];
                })->toArray(),
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

        $cpls = \App\Models\Cpl::with(['cpmk' => function($q) use ($mahasiswaId) {
            $q->whereHas('nilai', function($n) use ($mahasiswaId) {
                $n->where('mahasiswaId', $mahasiswaId);
            })->with(['cpmkMatKul.tahunAjaranMatkul.mataKuliah']);
        }])->get();

        $cpl_cpmk_data = [];
        $realCplLabels = [];
        if ($cpls->count() > 0) {
            foreach ($cpls as $cpl) {
                $cpmks = $cpl->cpmk;

                $cpmk_data = [];
                $totalBobotCpl = 0;
                $totalNilaiCpl = 0;

                foreach ($cpmks as $cpmk) {

                // 1. Ambil seluruh nilai dengan cpmkId yang sama untuk 1 mahasiswa
                $allNilaiForCpmk = \App\Models\Nilai::where('mahasiswaId', $mahasiswaId)
                    ->where('cpmkId', $cpmk->id)
                    ->with(['bobot.komponen'])
                    ->get();

                // 2. Kalikan nilai dengan bobot, 3. Ambil komponenId, 4. Simpan ke array
                $nilaiPerKomponenRaw = [];
                $komponenInfo = []; // Array to store komponen info

                foreach ($allNilaiForCpmk as $index => $nilaiRecord) {
                    $nilaiMentah = $nilaiRecord->nilai;
                    $bobotPengali = $nilaiRecord->bobot->bobot;
                    $komponenId = $nilaiRecord->bobot->komponenId;
                    $namaKomponen = $nilaiRecord->bobot->komponen->nama ?? 'Unknown';

                    // Simpan ke array berdasarkan komponenId asli (tidak perlu mapping)
                    if (!isset($nilaiPerKomponenRaw[$komponenId])) {
                        $nilaiPerKomponenRaw[$komponenId] = 0;
                    }
                    $nilaiPerKomponenRaw[$komponenId] += $nilaiMentah * ($bobotPengali / 100);

                    // Simpan info komponen
                    $komponenInfo[$komponenId] = [
                        'nama' => $namaKomponen,
                        'bobot' => $bobotPengali
                    ];
                }

                // 1. Cari total bobot untuk 1 CPMK dari tabel bobot
                $cpmkMatKul = $cpmk->cpmkMatKul->first();
                $tahunAjaranMatkulId = $cpmkMatKul->tahunAjaranMatkul->id ?? null;
                $totalBobotCpmk = \App\Models\Bobot::where('cpmkId', $cpmk->id)
                    ->whereHas('tahunAjaranMatkul', function($q) use ($tahunAjaranMatkulId) {
                        $q->where('id', $tahunAjaranMatkulId);
                    })
                    ->sum('bobot');

                // 2. Normalisasi nilai komponen: (nilai_komponen / total_bobot) * 100
                $nilaiPerKomponenNormalized = [];
                foreach ($nilaiPerKomponenRaw as $kompId => $nilaiKomp) {
                    $nilaiNormalized = $totalBobotCpmk > 0 ? ($nilaiKomp / $totalBobotCpmk) * 100 : 0;
                    $nilaiPerKomponenNormalized[$kompId] = round($nilaiNormalized, 2);
                }

                // Gunakan nilai yang sudah dinormalisasi
                $nilaiPerKomponen = $nilaiPerKomponenNormalized;

                $kodeMataKuliah = $cpmkMatKul->tahunAjaranMatkul->mataKuliah->kodeMatkul ?? '';
                $namaMataKuliah = $cpmkMatKul->tahunAjaranMatkul->mataKuliah->namaMatkul ?? '';
                $label = $cpmk->kodeCpmk . ' - ' . $namaMataKuliah;

                // Hitung nilai CPMK total (sudah dalam persentase 0-100)
                $nilaiCpmkTotal = array_sum($nilaiPerKomponen);

                // Nilai normal adalah total nilai komponen yang sudah dinormalisasi
                $nilaiNormal = round($nilaiCpmkTotal, 2);

                $cpmk_data[] = [
                    'label' => $label,
                    'komponen_nilai' => $nilaiPerKomponen,
                    'komponen_info' => $komponenInfo, // Tambah info komponen
                    'total_nilai' => $nilaiNormal, // Gunakan nilai yang sudah dibulatkan untuk konsistensi
                    'total_bobot' => $totalBobotCpmk,
                    'nilai_normal' => $nilaiNormal
                ];
                $totalBobotCpl += $totalBobotCpmk;
                $totalNilaiCpl += $nilaiNormal; // Gunakan nilai yang sudah dibulatkan untuk konsistensi
                }

                // Hitung nilai CPL berdasarkan rata-rata semua CPMK (gunakan nilai_normal untuk konsistensi)
                $nilaiCpmkRataRata = 0;
                $cpmkTertinggi = '';
                $nilaiCpmkTertinggi = 0;

                if (!empty($cpmk_data)) {
                    // Hitung rata-rata
                    $nilaiCpmkRataRata = array_sum(array_column($cpmk_data, 'nilai_normal')) / count($cpmk_data);

                    // Cari CPMK tertinggi
                    $maxNilai = max(array_column($cpmk_data, 'nilai_normal'));
                    $nilaiCpmkTertinggi = $maxNilai;

                    foreach ($cpmk_data as $cpmk_item) {
                        if ($cpmk_item['nilai_normal'] == $maxNilai) {
                            $cpmkTertinggi = $cpmk_item['label'];
                            break;
                        }
                    }
                }

                $nilai_cpl = round($nilaiCpmkTertinggi, 2); // Gunakan nilai CPMK tertinggi, bukan rata-rata

                $cpl_cpmk_data[] = [
                    'cpl_label' => $cpl->kodeCpl,
                    'cpmk_data' => $cpmk_data,
                    'nilai_cpl' => $nilai_cpl,
                    'total_bobot_cpl' => $totalBobotCpl,
                    'total_nilai_cpl' => $totalNilaiCpl,
                    'nilai_cpmk_rata_rata' => $nilaiCpmkRataRata,
                    'cpmk_tertinggi' => $cpmkTertinggi,
                    'nilai_cpmk_tertinggi' => $nilaiCpmkTertinggi
                ];
                $realCplLabels[] = $cpl->kodeCpl;
            }
        }

        // Statistik Akademik
        $stat = [
            'jumlah_mk' => 0,
            'jumlah_sks' => 0,
            'ipk' => null,
            'cpl_tercapai' => 0
        ];
        if ($mahasiswa) {
            // Jumlah MK dan SKS
            $mkDiambil = $mahasiswa->kelasMahasiswa()->with('kelas.tahunAjaranMatkul.mataKuliah')->get();
            $stat['jumlah_mk'] = $mkDiambil->count();
            $stat['jumlah_sks'] = $mkDiambil->sum(function($km) {
                return $km->kelas->tahunAjaranMatkul->mataKuliah->sks ?? 0;
            });
            // IPK (standar Unand: konversi nilai akhir ke bobot, lalu (bobot x sks) / total sks)
            $totalSks = 0;
            $totalNilaiBobot = 0;
            foreach ($mkDiambil as $km) {
                $nilaiAkhir = $km->totalNilai; // final grade per MK
                $sks = $km->kelas->tahunAjaranMatkul->mataKuliah->sks ?? 0;
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
            // CPL tercapai (nilai CPL > 55)
            $stat['cpl_tercapai'] = collect($cpl_cpmk_data)->filter(function($cpl) {
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
        $user = Auth::user();

        // Get filter parameter
        $selectedTahunAjaranId = $request->get('tahun_ajaran_filter');

        // Statistics Cards Data
        $totalMahasiswa = \App\Models\Mahasiswa::count();
        $totalDosen = \App\Models\Dosen::count();
        $totalMataKuliah = \App\Models\MataKuliah::count();
        $totalCPL = \App\Models\Cpl::count();
        $totalCPMK = \App\Models\Cpmk::count();

        // Active courses this academic year
        $latestTahunAjaran = \App\Models\TahunAjaran::orderBy('tahun', 'desc')->orderBy('periode', 'desc')->first();
        $activeCourses = \App\Models\TahunAjaranMatkul::where('tahunAjaranId', $latestTahunAjaran->id ?? 0)->count();

        // Get all tahun ajaran for filter dropdown
        $tahunAjaranList = \App\Models\TahunAjaran::orderBy('tahun', 'desc')->orderBy('periode', 'desc')->get();

        // Data for Average Score History Chart (Line Chart) - NO FILTER, show all years
        $chartData = $this->getAverageScoreHistoryData();

        // Data for CPL Achievement Chart (Bar Chart) - FIXED
        $cplAchievementData = $this->getCPLAchievementData($selectedTahunAjaranId);

        // Data for Grade Distribution Chart (Donut Chart) - FIXED
        $matkulPerformanceData = $this->getMatkulPerformanceData($selectedTahunAjaranId);

        // Data for Course Completion Rate - FIXED
        $courseCompletionData = $this->getCourseCompletionData($selectedTahunAjaranId);

        // Data for Course Type Distribution (Pie Chart) - FIXED
        $courseTypeData = $this->getCourseTypeDistribution();

        // Data for Top Performing Students (Bar Chart)
        $topStudentsData = $this->getTopStudentsData($selectedTahunAjaranId);

        return view('pimpinan.dashboard', compact(
            'user',
            'totalMahasiswa',
            'totalDosen',
            'totalMataKuliah',
            'totalCPL',
            'totalCPMK',
            'activeCourses',
            'tahunAjaranList',
            'selectedTahunAjaranId',
            'chartData',
            'cplAchievementData',
            'matkulPerformanceData',
            'courseCompletionData',
            'courseTypeData',
            'topStudentsData'
        ));
    }
}
