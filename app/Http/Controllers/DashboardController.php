<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        $user = Auth::user();

        // Get filter parameter
        $selectedTahunAjaranId = $request->get('tahun_ajaran_filter');

        // OPTIMIZED: Batch load all statistics in single queries
        $statistics = $this->getDashboardStatistics();

        // Get all tahun ajaran for filter dropdown
        $tahunAjaranList = \App\Models\TahunAjaran::orderBy('tahun', 'desc')->orderBy('periode', 'desc')->get();

        // OPTIMIZED: Load all chart data with efficient queries
        $chartData = $this->getAverageScoreHistoryData();
        $detailedCourseCharts = $this->getDetailedCourseCharts($selectedTahunAjaranId);
        $cplAchievementData = $this->getCPLAchievementData($selectedTahunAjaranId);
        $matkulPerformanceData = $this->getMatkulPerformanceData($selectedTahunAjaranId);
        $courseCompletionData = $this->getCourseCompletionData($selectedTahunAjaranId);
        $courseTypeData = $this->getCourseTypeDistribution($selectedTahunAjaranId);
        $topStudentsData = $this->getTopStudentsData($selectedTahunAjaranId);
        $recentActivities = $this->getRecentActivities();
        $systemHealth = $this->getSystemHealthData();

        return view('admin.dashboard', compact(
            'user',
            'statistics',
            'tahunAjaranList',
            'selectedTahunAjaranId',
            'chartData',
            'detailedCourseCharts',
            'cplAchievementData',
            'matkulPerformanceData',
            'courseCompletionData',
            'courseTypeData',
            'topStudentsData',
            'recentActivities',
            'systemHealth'
        ));
    }

    /**
     * OPTIMIZED: Get all dashboard statistics in batch
     */
    private function getDashboardStatistics()
    {
        // Get latest tahun ajaran once
        $latestTahunAjaran = \App\Models\TahunAjaran::orderBy('tahun', 'desc')->orderBy('periode', 'desc')->first();

        return [
            'totalMahasiswa' => \App\Models\Mahasiswa::count(),
            'totalDosen' => \App\Models\Dosen::count(),
            'totalMataKuliah' => \App\Models\MataKuliah::count(),
            'totalCPL' => \App\Models\Cpl::count(),
            'totalCPMK' => \App\Models\Cpmk::count(),
            'activeCourses' => \App\Models\TahunAjaranMatkul::where('tahunAjaranId', $latestTahunAjaran->id ?? 0)->count(),
        ];
    }

    /**
     * OPTIMIZED: Get average score history data for line chart - SHOW ALL YEARS (no filter) - LIMIT TO TOP 5
     */
    private function getAverageScoreHistoryData()
    {
        // OPTIMIZED: Use single query with proper joins and aggregation
        $topCourses = \App\Models\TahunAjaranMatkul::select('mataKuliahId')
            ->selectRaw('COUNT(DISTINCT km.mahasiswaId) as total_students')
            ->join('kelas as k', 'tahun_ajaran_matkul.id', '=', 'k.tahunAjaranMatkulId')
            ->join('kelas_mahasiswa as km', 'k.id', '=', 'km.kelasId')
            ->groupBy('mataKuliahId')
            ->orderByDesc('total_students')
            ->limit(5)
            ->pluck('mataKuliahId');

        // OPTIMIZED: Single query with all necessary data
        $data = \App\Models\TahunAjaranMatkul::select([
                'tahun_ajaran_matkul.id',
                'tahun_ajaran_matkul.mataKuliahId',
                'mata_kuliah.namaMatkul',
                'tahun_ajaran.tahun',
                'tahun_ajaran.periode'
            ])
            ->selectRaw('AVG(km.totalNilai) as avg_score')
            ->join('mata_kuliah', 'tahun_ajaran_matkul.mataKuliahId', '=', 'mata_kuliah.id')
            ->join('tahun_ajaran', 'tahun_ajaran_matkul.tahunAjaranId', '=', 'tahun_ajaran.id')
            ->join('kelas as k', 'tahun_ajaran_matkul.id', '=', 'k.tahunAjaranMatkulId')
            ->join('kelas_mahasiswa as km', 'k.id', '=', 'km.kelasId')
            ->whereIn('tahun_ajaran_matkul.mataKuliahId', $topCourses)
            ->whereNotNull('km.totalNilai')
            ->groupBy('tahun_ajaran_matkul.id', 'tahun_ajaran_matkul.mataKuliahId', 'mata_kuliah.namaMatkul', 'tahun_ajaran.tahun', 'tahun_ajaran.periode')
            ->get()
            ->groupBy('mataKuliahId');

        $chartLabels = [];
        $chartDatasets = [];

        foreach ($data as $matkulId => $records) {
            $matkulName = $records->first()->namaMatkul;
            $averagesByYear = [];

            foreach ($records as $record) {
                $yearLabel = $record->tahun . ' - ' . ucfirst($record->periode);
                $avgScore = round($record->avg_score, 2);

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
                    'borderColor' => $this->getRandomColor(),
                    'backgroundColor' => $this->getRandomColor(true),
                    'tension' => 0.4
                ];
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

        return [
            'labels' => $chartLabels,
            'datasets' => $chartDatasets
        ];
    }

    /**
     * OPTIMIZED: Get detailed course charts - individual chart per course
     */
    private function getDetailedCourseCharts($selectedTahunAjaranId = null)
    {
        // OPTIMIZED: Single query with proper joins and aggregation
        $query = \App\Models\TahunAjaranMatkul::select([
                'tahun_ajaran_matkul.mataKuliahId',
                'mata_kuliah.kodeMatkul',
                'mata_kuliah.namaMatkul',
                'tahun_ajaran.tahun',
                'tahun_ajaran.periode'
            ])
            ->selectRaw('AVG(km.totalNilai) as avg_score')
            ->selectRaw('COUNT(DISTINCT km.mahasiswaId) as student_count')
            ->join('mata_kuliah', 'tahun_ajaran_matkul.mataKuliahId', '=', 'mata_kuliah.id')
            ->join('tahun_ajaran', 'tahun_ajaran_matkul.tahunAjaranId', '=', 'tahun_ajaran.id')
            ->join('kelas as k', 'tahun_ajaran_matkul.id', '=', 'k.tahunAjaranMatkulId')
            ->join('kelas_mahasiswa as km', 'k.id', '=', 'km.kelasId')
            ->whereNotNull('km.totalNilai');

        if ($selectedTahunAjaranId) {
            $query->where('tahun_ajaran_matkul.tahunAjaranId', $selectedTahunAjaranId);
        }

        // Get top 4 courses by student enrollment
        $topCourses = \App\Models\TahunAjaranMatkul::select('mataKuliahId')
            ->selectRaw('COUNT(DISTINCT km.mahasiswaId) as total_students')
            ->join('kelas as k', 'tahun_ajaran_matkul.id', '=', 'k.tahunAjaranMatkulId')
            ->join('kelas_mahasiswa as km', 'k.id', '=', 'km.kelasId')
            ->groupBy('mataKuliahId')
            ->orderByDesc('total_students')
            ->limit(4)
            ->pluck('mataKuliahId');

        $courses = $query->whereIn('tahun_ajaran_matkul.mataKuliahId', $topCourses)
            ->groupBy('tahun_ajaran_matkul.mataKuliahId', 'mata_kuliah.kodeMatkul', 'mata_kuliah.namaMatkul', 'tahun_ajaran.tahun', 'tahun_ajaran.periode')
            ->get()
            ->groupBy('mataKuliahId');

        $detailedCharts = [];

        foreach ($courses as $mataKuliahId => $courseGroup) {
            $firstCourse = $courseGroup->first();

            $labels = [];
            $avgScores = [];
            $studentCounts = [];

            foreach ($courseGroup as $course) {
                $yearLabel = $course->tahun . '-' . $course->periode;
                $labels[] = $yearLabel;
                $avgScores[] = round($course->avg_score ?? 0, 2);
                $studentCounts[] = $course->student_count;
            }

            $detailedCharts[] = [
                'courseCode' => $firstCourse->kodeMatkul,
                'courseName' => $firstCourse->namaMatkul,
                'labels' => $labels,
                'avgScores' => $avgScores,
                'studentCounts' => $studentCounts,
                'color' => $this->getRandomColor()
            ];
        }

        return $detailedCharts;
    }

    /**
     * OPTIMIZED: Get CPL achievement data for bar chart - FIXED
     */
    private function getCPLAchievementData($selectedTahunAjaranId = null)
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

        if ($selectedTahunAjaranId) {
            $query->join('tahun_ajaran_matkul as tam', 'n.tahunAjaranMatkulId', '=', 'tam.id')
                  ->where('tam.tahunAjaranId', $selectedTahunAjaranId);
        }

        $cplData = $query->groupBy('cpl.id', 'cpl.kodeCpl')
            ->get();

        $labels = [];
        $data = [];
        $backgroundColors = [];

        foreach ($cplData as $cpl) {
            $labels[] = $cpl->kodeCpl;
            $data[] = round($cpl->avg_score, 2);
            $backgroundColors[] = $this->getRandomColor(true);
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'backgroundColor' => $backgroundColors
        ];
    }

    /**
     * OPTIMIZED: Get mata kuliah performance data for donut chart - FIXED
     */
    private function getMatkulPerformanceData($selectedTahunAjaranId = null)
    {
        // OPTIMIZED: Single query with proper joins and aggregation
        $query = \App\Models\KelasMahasiswa::select('grade')
            ->selectRaw('COUNT(*) as count')
            ->join('kelas', 'kelas_mahasiswa.kelasId', '=', 'kelas.id')
            ->join('tahun_ajaran_matkul as tam', 'kelas.tahunAjaranMatkulId', '=', 'tam.id')
            ->whereNotNull('kelas_mahasiswa.grade')
            ->whereNotNull('kelas_mahasiswa.totalNilai')
            ->where('kelas_mahasiswa.totalNilai', '>', 0);

        if ($selectedTahunAjaranId) {
            $query->where('tam.tahunAjaranId', $selectedTahunAjaranId);
        }

        $grades = $query->groupBy('grade')
            ->pluck('count', 'grade')
            ->toArray();

        $labels = [];
        $data = [];
        $backgroundColors = [];

        $gradeColors = [
            'A' => '#10B981',
            'A-' => '#34D399',
            'B+' => '#60A5FA',
            'B' => '#3B82F6',
            'B-' => '#6366F1',
            'C+' => '#F59E0B',
            'C' => '#F97316',
            'D' => '#EF4444',
            'E' => '#DC2626'
        ];

        // Define all possible grades to show complete distribution
        $allGrades = ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'D', 'E'];

        foreach ($allGrades as $grade) {
            $labels[] = "Grade {$grade}";
            $count = $grades[$grade] ?? 0;
            $data[] = $count;
            $backgroundColors[] = $gradeColors[$grade];
        }

        // If all data is 0, show a placeholder
        if (array_sum($data) == 0) {
            $labels = ['Belum Ada Data'];
            $data = [1];
            $backgroundColors = ['#9CA3AF'];
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'backgroundColor' => $backgroundColors
        ];
    }

    /**
     * OPTIMIZED: Get course completion rate data - FIXED
     */
    private function getCourseCompletionData($selectedTahunAjaranId = null)
    {
        // OPTIMIZED: Single query with proper joins and aggregation
        $query = \App\Models\TahunAjaranMatkul::select([
                'tahun_ajaran_matkul.id',
                'mata_kuliah.kodeMatkul'
            ])
            ->selectRaw('COUNT(CASE WHEN km.totalNilai IS NOT NULL THEN 1 END) as total_students')
            ->selectRaw('COUNT(CASE WHEN km.grade IS NOT NULL AND km.grade != "E" THEN 1 END) as passed_students')
            ->join('mata_kuliah', 'tahun_ajaran_matkul.mataKuliahId', '=', 'mata_kuliah.id')
            ->join('kelas as k', 'tahun_ajaran_matkul.id', '=', 'k.tahunAjaranMatkulId')
            ->join('kelas_mahasiswa as km', 'k.id', '=', 'km.kelasId')
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

            $completionRate = $course->total_students > 0 ?
                ($course->passed_students / $course->total_students) * 100 : 0;
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

        return [
            'labels' => $labels,
            'data' => $completionRates,
            'backgroundColor' => $backgroundColors
        ];
    }

    /**
     * OPTIMIZED: Get course type distribution (Pie Chart) - FIXED
     */
    private function getCourseTypeDistribution($selectedTahunAjaranId = null)
    {
        // OPTIMIZED: Single query with proper joins and aggregation
        $query = \App\Models\TahunAjaranMatkul::select('mata_kuliah.jenis')
            ->selectRaw('COUNT(*) as count')
            ->join('mata_kuliah', 'tahun_ajaran_matkul.mataKuliahId', '=', 'mata_kuliah.id')
            ->groupBy('mata_kuliah.jenis');

        if ($selectedTahunAjaranId) {
            $query->where('tahun_ajaran_matkul.tahunAjaranId', $selectedTahunAjaranId);
        }

        $courseTypes = $query->pluck('count', 'jenis')->toArray();

        $labels = [];
        $data = [];
        $backgroundColors = ['#3B82F6', '#10B981', '#F59E0B', '#8B5CF6'];

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
            'data' => $data,
            'backgroundColor' => array_slice($backgroundColors, 0, count($labels))
        ];
    }

    /**
     * OPTIMIZED: Get top performing students (Bar Chart)
     */
    private function getTopStudentsData($selectedTahunAjaranId = null)
    {
        // OPTIMIZED: Single query with proper joins and aggregation
        $query = \App\Models\KelasMahasiswa::select([
                'kelas_mahasiswa.mahasiswaId',
                'mahasiswa.nama',
                'mahasiswa.nim'
            ])
            ->selectRaw('AVG(kelas_mahasiswa.totalNilai) as avg_score')
            ->join('mahasiswa', 'kelas_mahasiswa.mahasiswaId', '=', 'mahasiswa.id')
            ->join('kelas', 'kelas_mahasiswa.kelasId', '=', 'kelas.id')
            ->join('tahun_ajaran_matkul as tam', 'kelas.tahunAjaranMatkulId', '=', 'tam.id')
            ->whereNotNull('kelas_mahasiswa.totalNilai');

        if ($selectedTahunAjaranId) {
            $query->where('tam.tahunAjaranId', $selectedTahunAjaranId);
        }

        $topStudents = $query->groupBy('kelas_mahasiswa.mahasiswaId', 'mahasiswa.nama', 'mahasiswa.nim')
            ->orderByDesc('avg_score')
            ->limit(10)
            ->get();

        $labels = [];
        $data = [];
        $backgroundColors = [];
        $students = [];

        foreach ($topStudents as $student) {
            $labels[] = $student->nim;
            $data[] = round($student->avg_score, 2);
            $backgroundColors[] = $this->getRandomColor(true);
            $students[] = [
                'nama' => $student->nama,
                'nim' => $student->nim,
                'avgScore' => round($student->avg_score, 2)
            ];
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'backgroundColor' => $backgroundColors,
            'students' => $students
        ];
    }

    /**
     * OPTIMIZED: Get recent activities
     */
    private function getRecentActivities()
    {
        $activities = [];

        // OPTIMIZED: Single query for recent students
        $recentStudents = \App\Models\Mahasiswa::select('nama', 'created_at')
            ->latest()
            ->limit(3)
            ->get();

        foreach ($recentStudents as $student) {
            $activities[] = [
                'type' => 'student',
                'message' => "Mahasiswa {$student->nama} ditambahkan",
                'time' => $student->created_at->diffForHumans(),
                'icon' => 'user-plus'
            ];
        }

        // OPTIMIZED: Single query for recent courses
        $recentCourses = \App\Models\TahunAjaranMatkul::select('mata_kuliah.namaMatkul', 'tahun_ajaran_matkul.created_at')
            ->join('mata_kuliah', 'tahun_ajaran_matkul.mataKuliahId', '=', 'mata_kuliah.id')
            ->latest('tahun_ajaran_matkul.created_at')
            ->limit(3)
            ->get();

        foreach ($recentCourses as $course) {
            $activities[] = [
                'type' => 'course',
                'message' => "Mata kuliah {$course->namaMatkul} dibuka",
                'time' => $course->created_at->diffForHumans(),
                'icon' => 'book'
            ];
        }

        // OPTIMIZED: Single query for recent CPMK updates
        $recentCPMK = \App\Models\Cpmk::select('kodeCpmk', 'updated_at')
            ->latest('updated_at')
            ->limit(2)
            ->get();

        foreach ($recentCPMK as $cpmk) {
            $activities[] = [
                'type' => 'cpmk',
                'message' => "CPMK {$cpmk->kodeCpmk} diperbarui",
                'time' => $cpmk->updated_at->diffForHumans(),
                'icon' => 'star'
            ];
        }

        // Sort by created_at/updated_at
        usort($activities, function($a, $b) {
            return strtotime($b['time']) <=> strtotime($a['time']);
        });

        return array_slice($activities, 0, 6);
    }

    /**
     * Get system health data
     */
    private function getSystemHealthData()
    {
        $pendingGrades = \App\Models\KelasMahasiswa::whereNull('totalNilai')->count();
        $incompleteCPMK = \App\Models\TahunAjaranMatkul::whereDoesntHave('cpmkMatKul')->count();

        return [
            'database' => 'healthy',
            'storage' => '78',
            'users_online' => \App\Models\User::where('updated_at', '>=', now()->subMinutes(15))->count(),
            'uptime' => '99.8',
            'response_time' => '120ms',
            'pending_grades' => $pendingGrades,
            'incomplete_cpmk' => $incompleteCPMK
        ];
    }

    /**
     * Generate random color for charts
     */
    private function getRandomColor($withAlpha = false)
    {
        $colors = [
            '#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6',
            '#EC4899', '#06B6D4', '#84CC16', '#F97316', '#6366F1'
        ];

        $color = $colors[array_rand($colors)];

        if ($withAlpha) {
            return $color . '80'; // Add 50% transparency
        }

        return $color;
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
                // Log 1: CPL apa
                \Illuminate\Support\Facades\Log::info("=== PROCESSING CPL: {$cpl->kodeCpl} ===");

                $cpmks = $cpl->cpmk;

                // Log 2: CPMK nya apa saja
                $cpmkList = $cpmks->pluck('kodeCpmk')->toArray();
                \Illuminate\Support\Facades\Log::info("CPMK dalam CPL {$cpl->kodeCpl}: " . implode(', ', $cpmkList));
                $cpmk_data = [];
                $totalBobotCpl = 0;
                $totalNilaiCpl = 0;

                foreach ($cpmks as $cpmk) {
                \Illuminate\Support\Facades\Log::info("--- Processing CPMK: {$cpmk->kodeCpmk} ---");

                // 1. Ambil seluruh nilai dengan cpmkId yang sama untuk 1 mahasiswa
                $allNilaiForCpmk = \App\Models\Nilai::where('mahasiswaId', $mahasiswaId)
                    ->where('cpmkId', $cpmk->id)
                    ->with(['bobot.komponen'])
                    ->get();

                \Illuminate\Support\Facades\Log::info("Total nilai records untuk CPMK {$cpmk->kodeCpmk}: " . $allNilaiForCpmk->count());

                // 2. Kalikan nilai dengan bobot, 3. Ambil komponenId, 4. Simpan ke array
                $nilaiPerKomponenRaw = [];

                foreach ($allNilaiForCpmk as $index => $nilaiRecord) {
                    $nilaiMentah = $nilaiRecord->nilai;
                    $bobotPengali = $nilaiRecord->bobot->bobot;
                    $komponenId = $nilaiRecord->bobot->komponenId;
                    $namaKomponen = $nilaiRecord->bobot->komponen->namaKomponen ?? 'Unknown';

                    // Log 3, 4, 5: Nilai mentah, bobot pengali, hasil perkalian
                    \Illuminate\Support\Facades\Log::info("Record #{$index} - Komponen: {$namaKomponen} (ID: {$komponenId})");
                    \Illuminate\Support\Facades\Log::info("  - Nilai mentah: {$nilaiMentah}");
                    \Illuminate\Support\Facades\Log::info("  - Bobot pengali: {$bobotPengali}%");

                    // Hasil perkalian
                    $hasilPerkalian = $nilaiMentah * ($bobotPengali / 100);
                    \Illuminate\Support\Facades\Log::info("  - Hasil perkalian: {$nilaiMentah} × ({$bobotPengali}/100) = {$hasilPerkalian}");

                    // Simpan ke array berdasarkan komponenId asli (tidak perlu mapping)
                    if (!isset($nilaiPerKomponenRaw[$komponenId])) {
                        $nilaiPerKomponenRaw[$komponenId] = 0;
                    }
                    $nilaiPerKomponenRaw[$komponenId] += $hasilPerkalian;
                    \Illuminate\Support\Facades\Log::info("  - Total untuk komponen {$komponenId}: {$nilaiPerKomponenRaw[$komponenId]}");
                }

                // 1. Cari total bobot untuk 1 CPMK dari tabel bobot
                $cpmkMatKul = $cpmk->cpmkMatKul->first();
                $tahunAjaranMatkulId = $cpmkMatKul->tahunAjaranMatkul->id ?? null;
                $totalBobotCpmk = \App\Models\Bobot::where('cpmkId', $cpmk->id)
                    ->whereHas('tahunAjaranMatkul', function($q) use ($tahunAjaranMatkulId) {
                        $q->where('id', $tahunAjaranMatkulId);
                    })
                    ->sum('bobot');

                \Illuminate\Support\Facades\Log::info("Total bobot untuk CPMK {$cpmk->kodeCpmk}: {$totalBobotCpmk}%");

                // 2. Normalisasi nilai komponen: (nilai_komponen / total_bobot) * 100
                $nilaiPerKomponenNormalized = [];
                foreach ($nilaiPerKomponenRaw as $kompId => $nilaiKomp) {
                    $nilaiNormalized = $totalBobotCpmk > 0 ? ($nilaiKomp / $totalBobotCpmk) * 100 : 0;
                    $nilaiPerKomponenNormalized[$kompId] = round($nilaiNormalized, 2);
                }

                // Gunakan nilai yang sudah dinormalisasi
                $nilaiPerKomponen = $nilaiPerKomponenNormalized;

                // Log final component values dengan nilai normalisasi
                \Illuminate\Support\Facades\Log::info("Normalized component values untuk CPMK {$cpmk->kodeCpmk}:");
                foreach ($nilaiPerKomponen as $kompId => $nilaiKomp) {
                    // Ambil nama komponen dari database berdasarkan ID asli
                    $komponen = \App\Models\Komponen::find($kompId);
                    $namaKomp = $komponen ? $komponen->namaKomponen : "Komponen {$kompId}";
                    \Illuminate\Support\Facades\Log::info("  - {$namaKomp} (ID: {$kompId}): {$nilaiKomp}% (dari raw: {$nilaiPerKomponenRaw[$kompId]} / {$totalBobotCpmk} * 100)");
                }

                $kodeMataKuliah = $cpmkMatKul->tahunAjaranMatkul->mataKuliah->kodeMatkul ?? '';
                $namaMataKuliah = $cpmkMatKul->tahunAjaranMatkul->mataKuliah->namaMatkul ?? '';
                $label = $cpmk->kodeCpmk . ' - ' . $namaMataKuliah;

                // Hitung nilai CPMK total (sudah dalam persentase 0-100)
                $nilaiCpmkTotal = array_sum($nilaiPerKomponen);

                // Nilai normal adalah total nilai komponen yang sudah dinormalisasi
                $nilaiNormal = round($nilaiCpmkTotal, 2);

                \Illuminate\Support\Facades\Log::info("Total nilai CPMK {$cpmk->kodeCpmk} setelah normalisasi: {$nilaiNormal}%");

                $cpmk_data[] = [
                    'label' => $label,
                    'komponen_nilai' => $nilaiPerKomponen,
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

                    // Logging detail perhitungan
                    \Illuminate\Support\Facades\Log::info("=== PERHITUNGAN CPL {$cpl->kodeCpl} ===");
                    \Illuminate\Support\Facades\Log::info("Jumlah CPMK: " . count($cpmk_data));
                    foreach ($cpmk_data as $i => $cpmk_item) {
                        \Illuminate\Support\Facades\Log::info("CPMK #{$i}: {$cpmk_item['label']} = {$cpmk_item['nilai_normal']}%");
                    }
                    \Illuminate\Support\Facades\Log::info("Total nilai CPMK: " . array_sum(array_column($cpmk_data, 'nilai_normal')));
                    \Illuminate\Support\Facades\Log::info("Rata-rata CPMK: {$nilaiCpmkRataRata}");
                    \Illuminate\Support\Facades\Log::info("CPMK Tertinggi: {$cpmkTertinggi} = {$nilaiCpmkTertinggi}%");
                    \Illuminate\Support\Facades\Log::info("Nilai CPL yang digunakan: {$nilaiCpmkTertinggi}% (berdasarkan CPMK tertinggi)");
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

        // Data for detailed course charts (individual courses)
        $detailedCourseCharts = $this->getDetailedCourseCharts($selectedTahunAjaranId);

        // Data for CPL Achievement Chart (Bar Chart) - FIXED
        $cplAchievementData = $this->getCPLAchievementData($selectedTahunAjaranId);

        // Data for Grade Distribution Chart (Donut Chart) - FIXED
        $matkulPerformanceData = $this->getMatkulPerformanceData($selectedTahunAjaranId);

        // Data for Course Completion Rate - FIXED
        $courseCompletionData = $this->getCourseCompletionData($selectedTahunAjaranId);

        // Data for Course Type Distribution (Pie Chart) - FIXED
        $courseTypeData = $this->getCourseTypeDistribution($selectedTahunAjaranId);

        // Data for Top Performing Students (Bar Chart)
        $topStudentsData = $this->getTopStudentsData($selectedTahunAjaranId);

        // Recent Activities Data
        $recentActivities = $this->getRecentActivities();

        // System Health Data
        $systemHealth = $this->getSystemHealthData();

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
            'detailedCourseCharts',
            'cplAchievementData',
            'matkulPerformanceData',
            'courseCompletionData',
            'courseTypeData',
            'topStudentsData',
            'recentActivities',
            'systemHealth'
        ));
    }
}
