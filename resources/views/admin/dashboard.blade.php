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

        <!-- Quick Actions Skeleton -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="h-6 bg-gray-200 rounded w-32 mb-4"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @for($i = 0; $i < 4; $i++)
                <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                    <div class="w-6 h-6 bg-gray-200 rounded mr-3"></div>
                    <div class="h-4 bg-gray-200 rounded w-24"></div>
                </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Actual Dashboard Content -->
    <div id="dashboard-content" class="hidden">
        <!-- Header with Filter -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard Administrator</h1>
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
            <!-- History Chart - Top 5 Courses -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Tren Historis Top 5 Mata Kuliah</h3>
                        <p class="text-sm text-gray-600">Berdasarkan jumlah mahasiswa terbanyak</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <span class="text-sm text-gray-600">Per Tahun Ajaran</span>
                        <button onclick="maximizeChart('historyChart', 'Tren Historis Top 5 Mata Kuliah')" 
                                class="p-1 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="relative h-80">
                    <canvas id="historyChart"></canvas>
                </div>
            </div>

            <!-- CPL Achievement Chart -->
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

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('admin.mahasiswa.create') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <svg class="h-6 w-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span class="text-blue-700 font-medium">Tambah Mahasiswa</span>
                </a>

                <a href="{{ route('admin.mata-kuliah.index') }}" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <svg class="h-6 w-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span class="text-green-700 font-medium">Kelola Mata Kuliah</span>
                </a>

                <a href="{{ route('admin.tahun-ajaran.index') }}" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                    <svg class="h-6 w-6 text-purple-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-purple-700 font-medium">Buat Tahun Ajaran</span>
                </a>

                <a href="{{ route('admin.cpl.index') }}" class="flex items-center p-4 bg-amber-50 rounded-lg hover:bg-amber-100 transition-colors">
                    <svg class="h-6 w-6 text-amber-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span class="text-amber-700 font-medium">Lihat Laporan CPL</span>
                </a>
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
const cplAchievementData = @json($cplAchievementData);
const matkulPerformanceData = @json($matkulPerformanceData);
const courseCompletionData = @json($courseCompletionData);
const courseTypeData = @json($courseTypeData);
const topStudentsData = @json($topStudentsData);

// Make chart data globally accessible for maximize function
window.chartData = chartData;
window.cplAchievementData = cplAchievementData;
window.matkulPerformanceData = matkulPerformanceData;
window.courseCompletionData = courseCompletionData;
window.courseTypeData = courseTypeData;
window.topStudentsData = topStudentsData;

// Initialize all charts
function initializeCharts() {
    try {
        // Initialize History Chart (Line Chart)
        const historyCtx = document.getElementById('historyChart');
        if (historyCtx) {
            new Chart(historyCtx.getContext('2d'), {
                type: 'line',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
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
@endsection
