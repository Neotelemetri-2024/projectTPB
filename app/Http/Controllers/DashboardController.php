<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        
        return view('admin.dashboard', compact(
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
    
    /**
     * Get average score history data for line chart - SHOW ALL YEARS (no filter) - LIMIT TO TOP 5
     */
    private function getAverageScoreHistoryData()
    {
        // Get top 5 courses by student enrollment to avoid clutter
        $topCourses = \App\Models\TahunAjaranMatkul::with(['mataKuliah', 'kelasMahasiswa'])
            ->get()
            ->groupBy('mataKuliahId')
            ->map(function($courses) {
                return [
                    'mataKuliah' => $courses->first()->mataKuliah,
                    'totalStudents' => $courses->sum(function($course) {
                        return $course->kelasMahasiswa->count();
                    })
                ];
            })
            ->sortByDesc('totalStudents')
            ->take(5)
            ->pluck('mataKuliah.id');
        
        // Get data for top courses only
        $data = \App\Models\TahunAjaranMatkul::with(['tahunAjaran', 'mataKuliah', 'kelasMahasiswa'])
            ->whereIn('mataKuliahId', $topCourses)
            ->get()
            ->groupBy('mataKuliahId');
        
        $chartLabels = [];
        $chartDatasets = [];
        
        foreach ($data as $matkulId => $tahunAjaranMatkuls) {
            $matkulName = $tahunAjaranMatkuls->first()->mataKuliah->namaMatkul;
            
            $averagesByYear = [];
            
            foreach ($tahunAjaranMatkuls as $tam) {
                $yearLabel = $tam->tahunAjaran->tahun . ' - ' . ucfirst($tam->tahunAjaran->periode);
                
                // Calculate average score for this mata kuliah in this year
                $totalScore = $tam->kelasMahasiswa->where('totalNilai', '!=', null)->avg('totalNilai');
                
                if ($totalScore !== null) {
                    $averagesByYear[$yearLabel] = round($totalScore, 2);
                    
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
     * Get detailed course charts - individual chart per course
     */
    private function getDetailedCourseCharts($selectedTahunAjaranId = null)
    {
        $query = \App\Models\TahunAjaranMatkul::with(['tahunAjaran', 'mataKuliah', 'kelasMahasiswa']);
        
        if ($selectedTahunAjaranId) {
            $query->where('tahunAjaranId', $selectedTahunAjaranId);
        }
        
        // Get top 4 courses for individual detailed charts
        $courses = $query->get()
            ->groupBy('mataKuliahId')
            ->map(function($courseGroup) {
                $firstCourse = $courseGroup->first();
                $totalStudents = $courseGroup->sum(function($course) {
                    return $course->kelasMahasiswa->count();
                });
                
                return [
                    'mataKuliah' => $firstCourse->mataKuliah,
                    'courses' => $courseGroup,
                    'totalStudents' => $totalStudents
                ];
            })
            ->sortByDesc('totalStudents')
            ->take(4);
        
        $detailedCharts = [];
        
        foreach ($courses as $courseData) {
            $mataKuliah = $courseData['mataKuliah'];
            $courseGroup = $courseData['courses'];
            
            $labels = [];
            $avgScores = [];
            $studentCounts = [];
            
            foreach ($courseGroup as $course) {
                $yearLabel = $course->tahunAjaran->tahun . '-' . $course->tahunAjaran->periode;
                $avgScore = $course->kelasMahasiswa->where('totalNilai', '!=', null)->avg('totalNilai');
                $studentCount = $course->kelasMahasiswa->count();
                
                $labels[] = $yearLabel;
                $avgScores[] = round($avgScore ?? 0, 2);
                $studentCounts[] = $studentCount;
            }
            
            $detailedCharts[] = [
                'courseCode' => $mataKuliah->kodeMatkul,
                'courseName' => $mataKuliah->namaMatkul,
                'labels' => $labels,
                'avgScores' => $avgScores,
                'studentCounts' => $studentCounts,
                'color' => $this->getRandomColor()
            ];
        }
        
        return $detailedCharts;
    }
    
    /**
     * Get CPL achievement data for bar chart - FIXED
     */
    private function getCPLAchievementData($selectedTahunAjaranId = null)
    {
        $cpls = \App\Models\Cpl::with(['cpmk'])->get();
        
        $labels = [];
        $data = [];
        $backgroundColors = [];
        
        foreach ($cpls as $cpl) {
            $labels[] = $cpl->kodeCpl;
            
            // Calculate average achievement for this CPL
            $cpmkScores = [];
            
            foreach ($cpl->cpmk as $cpmk) {
                // Get all nilai for this CPMK
                $nilaiQuery = \App\Models\Nilai::where('cpmkId', $cpmk->id);
                
                if ($selectedTahunAjaranId) {
                    $nilaiQuery->whereHas('tahunAjaranMatkul', function($q) use ($selectedTahunAjaranId) {
                        $q->where('tahunAjaranId', $selectedTahunAjaranId);
                    });
                }
                
                $avgScore = $nilaiQuery->avg('nilai');
                if ($avgScore !== null && $avgScore > 0) {
                    $cpmkScores[] = $avgScore;
                }
            }
            
            $cplAverage = count($cpmkScores) > 0 ? array_sum($cpmkScores) / count($cpmkScores) : 0;
            $data[] = round($cplAverage, 2);
            $backgroundColors[] = $this->getRandomColor(true);
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
            'backgroundColor' => $backgroundColors
        ];
    }
    
    /**
     * Get mata kuliah performance data for donut chart - FIXED
     */
    private function getMatkulPerformanceData($selectedTahunAjaranId = null)
    {
        $query = \App\Models\KelasMahasiswa::with(['kelas.tahunAjaranMatkul.mataKuliah', 'kelas.tahunAjaranMatkul.tahunAjaran']);
        
        if ($selectedTahunAjaranId) {
            $query->whereHas('kelas.tahunAjaranMatkul', function($q) use ($selectedTahunAjaranId) {
                $q->where('tahunAjaranId', $selectedTahunAjaranId);
            });
        }
        
        // Get all grades with data
        $grades = $query->whereNotNull('grade')
            ->whereNotNull('totalNilai')
            ->where('totalNilai', '>', 0)
            ->get()
            ->groupBy('grade');
        
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
            $count = $grades->has($grade) ? $grades[$grade]->count() : 0;
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
     * Get course completion rate data - FIXED
     */
    private function getCourseCompletionData($selectedTahunAjaranId = null)
    {
        $query = \App\Models\TahunAjaranMatkul::with(['mataKuliah', 'kelasMahasiswa']);
        
        if ($selectedTahunAjaranId) {
            $query->where('tahunAjaranId', $selectedTahunAjaranId);
        }
        
        $courses = $query->whereHas('kelasMahasiswa', function($q) {
            $q->whereNotNull('totalNilai');
        })->get()->take(8); // Limit for readability
        
        $labels = [];
        $completionRates = [];
        $backgroundColors = [];
        
        foreach ($courses as $course) {
            $labels[] = $course->mataKuliah->kodeMatkul;
            
            $totalStudents = $course->kelasMahasiswa->whereNotNull('totalNilai')->count();
            $passedStudents = $course->kelasMahasiswa
                ->whereNotNull('grade')
                ->whereNotIn('grade', ['E'])
                ->count();
            
            $completionRate = $totalStudents > 0 ? ($passedStudents / $totalStudents) * 100 : 0;
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
     * Get course type distribution (Pie Chart) - FIXED
     */
    private function getCourseTypeDistribution($selectedTahunAjaranId = null)
    {
        $query = \App\Models\TahunAjaranMatkul::with(['mataKuliah']);
        
        if ($selectedTahunAjaranId) {
            $query->where('tahunAjaranId', $selectedTahunAjaranId);
        }
        
        $courseTypes = $query->get()
            ->groupBy('mataKuliah.jenis')
            ->map(function($courses) {
                return $courses->count();
            });
        
        $labels = [];
        $data = [];
        $backgroundColors = ['#3B82F6', '#10B981', '#F59E0B', '#8B5CF6'];
        
        // Ensure we have at least some data
        if ($courseTypes->count() > 0) {
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
     * Get top performing students (Bar Chart)
     */
    private function getTopStudentsData($selectedTahunAjaranId = null)
    {
        $query = \App\Models\KelasMahasiswa::with(['mahasiswa', 'kelas.tahunAjaranMatkul'])
            ->whereNotNull('totalNilai');
        
        if ($selectedTahunAjaranId) {
            $query->whereHas('kelas.tahunAjaranMatkul', function($q) use ($selectedTahunAjaranId) {
                $q->where('tahunAjaranId', $selectedTahunAjaranId);
            });
        }
        
        $topStudents = $query->get()
            ->groupBy('mahasiswaId')
            ->map(function($studentGrades, $mahasiswaId) {
                $student = $studentGrades->first()->mahasiswa;
                $avgScore = $studentGrades->avg('totalNilai');
                
                return [
                    'nama' => $student->nama,
                    'nim' => $student->nim,
                    'avgScore' => round($avgScore, 2)
                ];
            })
            ->sortByDesc('avgScore')
            ->take(10);
        
        $labels = [];
        $data = [];
        $backgroundColors = [];
        
        foreach ($topStudents as $student) {
            $labels[] = $student['nim'];
            $data[] = $student['avgScore'];
            $backgroundColors[] = $this->getRandomColor(true);
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
            'backgroundColor' => $backgroundColors,
            'students' => $topStudents->values()->toArray()
        ];
    }
    
    /**
     * Get recent activities
     */
    private function getRecentActivities()
    {
        $activities = [];
        
        // Recent students added
        $recentStudents = \App\Models\Mahasiswa::latest()->limit(3)->get();
        foreach ($recentStudents as $student) {
            $activities[] = [
                'type' => 'student',
                'message' => "Mahasiswa {$student->nama} ditambahkan",
                'time' => $student->created_at->diffForHumans(),
                'icon' => 'user-plus'
            ];
        }
        
        // Recent courses added
        $recentCourses = \App\Models\TahunAjaranMatkul::with(['mataKuliah'])->latest()->limit(3)->get();
        foreach ($recentCourses as $course) {
            $activities[] = [
                'type' => 'course',
                'message' => "Mata kuliah {$course->mataKuliah->namaMatkul} dibuka",
                'time' => $course->created_at->diffForHumans(),
                'icon' => 'book'
            ];
        }
        
        // Recent CPMK updates
        $recentCPMK = \App\Models\Cpmk::latest('updated_at')->limit(2)->get();
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
                $cpmks = $cpl->cpmk;
                $cpmk_data = [];
                $totalBobotCpl = 0;
                $totalNilaiCpl = 0;
                foreach ($cpmks as $cpmk) {
                                    // Ambil nilai per komponen untuk CPMK ini melalui bobot
                $bobotIds = \App\Models\Bobot::where('cpmkId', $cpmk->id)->pluck('id');
                $nilaiPerKomponen = \App\Models\Nilai::where('mahasiswaId', $mahasiswaId)
                    ->whereIn('bobotId', $bobotIds)
                    ->with('bobot.komponen')
                    ->get()
                    ->groupBy('bobot.komponenId')
                    ->map(function($nilaiGroup) {
                        return $nilaiGroup->avg('nilai');
                    });

                $cpmkMatKul = $cpmk->cpmkMatKul->first();
                $kodeMataKuliah = $cpmkMatKul->tahunAjaranMatkul->mataKuliah->kodeMatkul ?? '';
                $label = $cpmk->kodeCpmk . ' - ' . $kodeMataKuliah;

                // Hitung nilai CPMK dengan bobot yang benar
                $nilaiCpmkTotal = 0;
                $bobotCpmkTotal = 0;
                
                $nilaiRecords = \App\Models\Nilai::where('mahasiswaId', $mahasiswaId)
                    ->whereIn('bobotId', $bobotIds)
                    ->with('bobot')
                    ->get();
                
                foreach ($nilaiRecords as $nilai) {
                    if ($nilai->bobot && $nilai->bobot->bobot > 0) {
                        $nilaiCpmkTotal += ($nilai->nilai * $nilai->bobot->bobot);
                        $bobotCpmkTotal += $nilai->bobot->bobot;
                    }
                }
                
                $totalNilai = $bobotCpmkTotal > 0 ? $nilaiCpmkTotal / $bobotCpmkTotal : 0;
                $tahunAjaranMatkulId = $cpmkMatKul->tahunAjaranMatkul->id ?? null;
                $totalBobot = \App\Models\Bobot::where('cpmkId', $cpmk->id)
                    ->whereHas('tahunAjaranMatkul', function($q) use ($tahunAjaranMatkulId) {
                        $q->where('id', $tahunAjaranMatkulId);
                    })
                    ->sum('bobot');
                $nilaiNormal = round($totalNilai, 2);

                    $cpmk_data[] = [
                        'label' => $label,
                        'komponen_nilai' => $nilaiPerKomponen->toArray(),
                        'total_nilai' => $totalNilai,
                        'total_bobot' => $totalBobot,
                        'nilai_normal' => $nilaiNormal
                    ];
                    $totalBobotCpl += $totalBobot;
                    $totalNilaiCpl += $totalNilai;
                }
                // Hitung nilai CPL (maksimal 100)
                $nilai_cpl = ($totalBobotCpl > 0) ? round(($totalNilaiCpl / $totalBobotCpl) * 100, 2) : 0;
                $cpl_cpmk_data[] = [
                    'cpl_label' => $cpl->kodeCpl,
                    'cpmk_data' => $cpmk_data,
                    'nilai_cpl' => $nilai_cpl,
                    'total_bobot_cpl' => $totalBobotCpl,
                    'total_nilai_cpl' => $totalNilaiCpl
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
