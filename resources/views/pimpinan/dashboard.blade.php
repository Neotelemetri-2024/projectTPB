@extends('layouts.main')

@section('content')
<div class="p-6">
    <!-- Loading Skeleton -->
    <div id="loading-skeleton" class="animate-pulse">
        <!-- Header Skeleton -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <div class="h-8 bg-gray-200 rounded w-80 mb-2"></div>
                    <div class="h-4 bg-gray-200 rounded w-64"></div>
                </div>

                <!-- Filter Skeleton -->
                <div class="flex items-center gap-4">
                    <div class="h-4 bg-gray-200 rounded w-32"></div>
                    <div class="h-10 bg-gray-200 rounded w-64"></div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards Skeleton -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            @for($i = 0; $i < 4; $i++)
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gray-200 rounded-lg"></div>
                    </div>
                    <div class="ml-4 flex-1">
                        <div class="h-4 bg-gray-200 rounded w-24 mb-2"></div>
                        <div class="h-8 bg-gray-200 rounded w-16 mb-1"></div>
                        <div class="h-3 bg-gray-200 rounded w-20"></div>
                    </div>
                </div>
            </div>
            @endfor
        </div>

        <!-- Main Charts Section Skeleton -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8">
            <!-- History Chart Skeleton -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <div class="h-6 bg-gray-200 rounded w-48 mb-2"></div>
                        <div class="h-4 bg-gray-200 rounded w-64"></div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-gray-200 rounded-full"></div>
                        <div class="h-4 bg-gray-200 rounded w-24"></div>
                    </div>
                </div>
                <div class="relative h-80">
                    <div class="w-full h-full bg-gray-200 rounded"></div>
                </div>
            </div>

            <!-- CPL Chart Skeleton -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <div class="h-6 bg-gray-200 rounded w-48 mb-2"></div>
                        <div class="h-4 bg-gray-200 rounded w-40"></div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-gray-200 rounded-full"></div>
                        <div class="h-4 bg-gray-200 rounded w-20"></div>
                    </div>
                </div>
                <div class="relative h-80">
                    <div class="w-full h-full bg-gray-200 rounded"></div>
                </div>
            </div>
        </div>

        <!-- Secondary Charts Row Skeleton -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 mb-8">
            @for($i = 0; $i < 4; $i++)
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <div class="h-6 bg-gray-200 rounded w-32 mb-2"></div>
                        <div class="h-4 bg-gray-200 rounded w-24"></div>
                    </div>
                </div>
                <div class="relative h-64">
                    <div class="w-full h-full bg-gray-200 rounded"></div>
                </div>
            </div>
            @endfor
        </div>
    </div>

    <!-- Actual Dashboard Content -->
    <div id="dashboard-content" class="hidden">
        <!-- Header with Filter -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard Pimpinan</h1>
                    <p class="mt-2 text-gray-600">Selamat datang di Portal TPB!</p>
                </div>

                <!-- Filter Tahun Ajaran -->
                <div class="flex items-center gap-4">
                    <label for="tahun-ajaran-filter" class="text-sm font-medium text-gray-700">Filter Tahun Ajaran:</label>
                    <select id="tahun-ajaran-filter" onchange="filterDashboard()"
                            class="block w-64 px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500">
                        <option value="">Semua Tahun Ajaran</option>
                        @foreach($tahunAjaranList as $tahunAjaran)
                            <option value="{{ $tahunAjaran->id }}" {{ $selectedTahunAjaranId == $tahunAjaran->id ? 'selected' : '' }}>
                                {{ $tahunAjaran->tahun }} - {{ ucfirst($tahunAjaran->periode) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Mahasiswa -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Mahasiswa</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($statistics['totalMahasiswa']) }}</p>
                        <p class="text-xs text-blue-600 mt-1">Aktif dalam sistem</p>
                    </div>
                </div>
            </div>

            <!-- Total Dosen -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Dosen</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($statistics['totalDosen']) }}</p>
                        <p class="text-xs text-green-600 mt-1">Pengampu aktif</p>
                    </div>
                </div>
            </div>

            <!-- Total Mata Kuliah -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Mata Kuliah</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($statistics['totalMataKuliah']) }}</p>
                        <p class="text-xs text-purple-600 mt-1">{{ number_format($statistics['activeCourses']) }} aktif semester ini</p>
                    </div>
                </div>
            </div>

            <!-- CPL & CPMK -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">CPL & CPMK</p>
                        <div class="flex items-center space-x-2">
                            <span class="text-xl font-bold text-gray-900">{{ number_format($statistics['totalCPL']) }}</span>
                            <span class="text-gray-400">|</span>
                            <span class="text-xl font-bold text-gray-900">{{ number_format($statistics['totalCPMK']) }}</span>
                        </div>
                        <p class="text-xs text-amber-600 mt-1">CPL | CPMK tersedia</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Charts Section -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8">
            <!-- History Chart - Semester Ganjil -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Tren Historis Top 5 Mata Kuliah</h3>
                        <p class="text-sm text-gray-600">Semester Ganjil - Berdasarkan jumlah mahasiswa terbanyak</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <span class="text-sm text-gray-600">Per Tahun Ajaran</span>
                        <button onclick="maximizeChart('historyChartGanjil', 'Tren Historis Top 5 Mata Kuliah - Semester Ganjil')"
                                class="p-1 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="relative h-80 w-full px-0 mx-0">
                    <canvas id="historyChartGanjil" class="w-full h-full"></canvas>
                </div>
            </div>

            <!-- History Chart - Semester Genap -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Tren Historis Top 5 Mata Kuliah</h3>
                        <p class="text-sm text-gray-600">Semester Genap - Berdasarkan jumlah mahasiswa terbanyak</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-sm text-gray-600">Per Tahun Ajaran</span>
                        <button onclick="maximizeChart('historyChartGenap', 'Tren Historis Top 5 Mata Kuliah - Semester Genap')"
                                class="p-1 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="relative h-80 w-full px-0 mx-0">
                    <canvas id="historyChartGenap" class="w-full h-full"></canvas>
                </div>
            </div>
        </div>

        <!-- CPL Achievement Chart - Full Width -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Capaian Pembelajaran Lulusan</h3>
                        <p class="text-sm text-gray-600">Rata-rata pencapaian per CPL</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-sm text-gray-600">Rata-rata (%)</span>
                        <button onclick="maximizeChart('cplChart', 'Capaian Pembelajaran Lulusan')"
                                class="p-1 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="relative h-80">
                    <canvas id="cplChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Secondary Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 mb-8">
            <!-- Grade Distribution -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Distribusi Grade</h3>
                        <p class="text-sm text-gray-600">Sebaran nilai per mata kuliah (Top 5)</p>
                    </div>
                    <button onclick="maximizeChart('gradeChart', 'Distribusi Grade')"
                            class="p-1 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                        </svg>
                    </button>
                </div>
                <div class="relative h-64">
                    <canvas id="gradeChart"></canvas>
                </div>
            </div>

            <!-- Course Type Distribution -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Jenis Mata Kuliah</h3>
                        <p class="text-sm text-gray-600">Distribusi wajib vs pilihan (Master Data)</p>
                    </div>
                    <button onclick="maximizeChart('courseTypeChart', 'Jenis Mata Kuliah')"
                            class="p-1 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                        </svg>
                    </button>
                </div>
                <div class="relative h-64">
                    <canvas id="courseTypeChart"></canvas>
                </div>
            </div>

            <!-- Course Completion Rate -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Tingkat Kelulusan</h3>
                        <p class="text-sm text-gray-600">Per mata kuliah</p>
                    </div>
                    <button onclick="maximizeChart('completionChart', 'Tingkat Kelulusan')"
                            class="p-1 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                        </svg>
                    </button>
                </div>
                <div class="relative h-64">
                    <canvas id="completionChart"></canvas>
                </div>
            </div>

            <!-- Top Students -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Top 10 Mahasiswa</h3>
                        <p class="text-sm text-gray-600">Berdasarkan rata-rata nilai</p>
                    </div>
                    <button onclick="maximizeChart('topStudentsChart', 'Top 10 Mahasiswa')"
                            class="p-1 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                        </svg>
                    </button>
                </div>
                <div class="relative h-64">
                    <canvas id="topStudentsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart Modal Component -->
@include('components.chart-modal')

<script>
// Loading state management
document.addEventListener('DOMContentLoaded', function() {
    const loadingSkeleton = document.getElementById('loading-skeleton');
    const dashboardContent = document.getElementById('dashboard-content');

    // Simulate loading time (remove this in production)
    setTimeout(() => {
        loadingSkeleton.classList.add('hidden');
        dashboardContent.classList.remove('hidden');
        initializeCharts();
    }, 1500);
});

// Chart data from backend
const chartData = @json($chartData);
const chartDataGanjil = @json($chartDataGanjil ?? []);
const chartDataGenap = @json($chartDataGenap ?? []);
const cplAchievementData = @json($cplAchievementData);
const matkulPerformanceData = @json($matkulPerformanceData);
const courseCompletionData = @json($courseCompletionData);
const courseTypeData = @json($courseTypeData);
const topStudentsData = @json($topStudentsData);

// Debug: Log data untuk troubleshooting
console.log('Chart Data Ganjil:', chartDataGanjil);
console.log('Chart Data Genap:', chartDataGenap);
console.log('Chart Data All:', chartData);

// Fallback data sementara jika backend belum menyediakan data terpisah
// TODO: Hapus ini setelah backend menyediakan data terpisah
let fallbackGanjilData = null;
let fallbackGenapData = null;

if (chartDataGanjil.length === 0 && chartDataGenap.length === 0 && chartData && chartData.labels) {
    // Buat data terpisah dari chartData yang ada
    const allLabels = chartData.labels || [];
    const allDatasets = chartData.datasets || [];

    // Filter untuk semester ganjil (asumsi label mengandung "Ganjil")
    const ganjilLabels = allLabels.filter(label => label.toLowerCase().includes('ganjil'));
    const genapLabels = allLabels.filter(label => label.toLowerCase().includes('genap'));

    if (ganjilLabels.length > 0) {
        fallbackGanjilData = {
            labels: ganjilLabels,
            datasets: allDatasets.map(dataset => ({
                ...dataset,
                data: dataset.data.slice(0, ganjilLabels.length)
            }))
        };
    }

    if (genapLabels.length > 0) {
        fallbackGenapData = {
            labels: genapLabels,
            datasets: allDatasets.map(dataset => ({
                ...dataset,
                data: dataset.data.slice(ganjilLabels.length, ganjilLabels.length + genapLabels.length)
            }))
        };
    }

    console.log('Fallback Ganjil Data:', fallbackGanjilData);
    console.log('Fallback Genap Data:', fallbackGenapData);
}

// Make chart data globally accessible for maximize function
window.chartData = chartData;
window.chartDataGanjil = chartDataGanjil;
window.chartDataGenap = chartDataGenap;
window.fallbackGanjilData = fallbackGanjilData;
window.fallbackGenapData = fallbackGenapData;
window.cplAchievementData = cplAchievementData;
window.matkulPerformanceData = matkulPerformanceData;
window.courseCompletionData = courseCompletionData;
window.courseTypeData = courseTypeData;
window.topStudentsData = topStudentsData;

// Global variables untuk history chart instances
let historyChartGanjilInstance = null;
let historyChartGenapInstance = null;

// Initialize all charts
function initializeCharts() {
    try {
        // Initialize History Chart Ganjil (Line Chart)
        const historyGanjilCtx = document.getElementById('historyChartGanjil');
        if (historyGanjilCtx) {
            // Gunakan data semester ganjil, fallback ke data yang sudah difilter
            let ganjilData;
            if (chartDataGanjil.length > 0) {
                ganjilData = chartDataGanjil;
            } else if (fallbackGanjilData) {
                ganjilData = fallbackGanjilData;
            } else {
                ganjilData = {
                    labels: ['Belum ada data semester ganjil'],
                    datasets: [{
                        label: 'Data tidak tersedia',
                        data: [0],
                        borderColor: 'rgb(156, 163, 175)',
                        backgroundColor: 'rgba(156, 163, 175, 0.1)',
                        tension: 0.1,
                        pointRadius: 0,
                        borderWidth: 2
                    }]
                };
            }

            historyChartGanjilInstance = new Chart(historyGanjilCtx.getContext('2d'), {
                type: 'line',
                data: ganjilData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    aspectRatio: 2,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Rata-rata Nilai'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Tahun Ajaran'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 15
                            }
                        }
                    }
                }
            });
        }

        // Initialize History Chart Genap (Line Chart)
        const historyGenapCtx = document.getElementById('historyChartGenap');
        if (historyGenapCtx) {
            // Gunakan data semester genap, fallback ke data yang sudah difilter
            let genapData;
            if (chartDataGenap.length > 0) {
                genapData = chartDataGenap;
            } else if (fallbackGenapData) {
                genapData = fallbackGenapData;
            } else {
                genapData = {
                    labels: ['Belum ada data semester genap'],
                    datasets: [{
                        label: 'Data tidak tersedia',
                        data: [0],
                        borderColor: 'rgb(156, 163, 175)',
                        backgroundColor: 'rgba(156, 163, 175, 0.1)',
                        tension: 0.1,
                        pointRadius: 0,
                        borderWidth: 2
                    }]
                };
            }

            historyChartGenapInstance = new Chart(historyGenapCtx.getContext('2d'), {
                type: 'line',
                data: genapData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    aspectRatio: 2,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Rata-rata Nilai'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Tahun Ajaran'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 15
                            }
                        }
                    }
                }
            });
        }

        // Initialize CPL Achievement Chart (Bar Chart)
        const cplCtx = document.getElementById('cplChart');
        if (cplCtx) {
            new Chart(cplCtx.getContext('2d'), {
                type: 'bar',
                data: cplAchievementData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Rata-rata Pencapaian (%)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Kode CPL'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        // Initialize Grade Distribution Chart (Bar Chart)
        const gradeCtx = document.getElementById('gradeChart');
        if (gradeCtx) {
            new Chart(gradeCtx.getContext('2d'), {
                type: 'bar',
                data: matkulPerformanceData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jumlah Mahasiswa'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Grade'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 8,
                                font: {
                                    size: 10
                                }
                            }
                        }
                    }
                }
            });
        }

        // Initialize Course Type Chart (Pie Chart)
        const courseTypeCtx = document.getElementById('courseTypeChart');
        if (courseTypeCtx) {
            new Chart(courseTypeCtx.getContext('2d'), {
                type: 'pie',
                data: courseTypeData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 15
                            }
                        }
                    }
                }
            });
        }

        // Initialize Course Completion Chart
        const completionCtx = document.getElementById('completionChart');
        if (completionCtx) {
            new Chart(completionCtx.getContext('2d'), {
                type: 'bar',
                data: courseCompletionData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Tingkat Kelulusan (%)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Kode Mata Kuliah'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        // Initialize Top Students Chart
        const topStudentsCtx = document.getElementById('topStudentsChart');
        if (topStudentsCtx) {
            new Chart(topStudentsCtx.getContext('2d'), {
                type: 'bar',
                data: topStudentsData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Rata-rata Nilai'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'NIM Mahasiswa'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

    } catch (error) {
        console.error('Error initializing charts:', error);
    }
}

// Filter function
function filterDashboard() {
    const selectedValue = document.getElementById('tahun-ajaran-filter').value;
    const url = new URL(window.location);

    if (selectedValue) {
        url.searchParams.set('tahun_ajaran_filter', selectedValue);
    } else {
        url.searchParams.delete('tahun_ajaran_filter');
    }

    window.location.href = url.toString();
}
</script>

@push('styles')
<style>
/* Fix untuk chart agar memenuhi lebar penuh */
canvas {
    width: 100% !important;
    height: auto !important;
}

/* Pastikan container chart tidak ada padding yang mengganggu */
.relative.h-80 {
    padding: 0;
    margin: 0;
}

/* Pastikan chart responsive */
.chart-container {
    position: relative;
    width: 100%;
    height: 320px;
}

/* Pastikan chart memenuhi lebar card */
.bg-white.rounded-lg.shadow.p-6 canvas {
    width: 100% !important;
    max-width: 100% !important;
}
</style>
@endpush

@endsection
