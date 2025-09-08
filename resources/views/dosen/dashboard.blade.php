@extends('layouts.main')

@section('content')
<div class="p-6">
    <!-- Loading Skeleton testing -->
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

        <!-- Charts Section Skeleton -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8">
            @for($i = 0; $i < 2; $i++)
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <div class="h-6 bg-gray-200 rounded w-48 mb-2"></div>
                        <div class="h-4 bg-gray-200 rounded w-64"></div>
                    </div>
                    <div class="w-6 h-6 bg-gray-200 rounded"></div>
                </div>
                <div class="relative h-80">
                    <div class="w-full h-full bg-gray-200 rounded"></div>
                </div>
            </div>
            @endfor
        </div>

        <!-- Line Chart Skeleton -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <div class="h-6 bg-gray-200 rounded w-64 mb-2"></div>
                    <div class="h-4 bg-gray-200 rounded w-48"></div>
                </div>
                <div class="w-6 h-6 bg-gray-200 rounded"></div>
            </div>
            <div class="relative h-80">
                <div class="w-full h-full bg-gray-200 rounded"></div>
            </div>
        </div>

        <!-- Grade Distribution Skeleton -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="h-6 bg-gray-200 rounded w-64 mb-6"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @for($i = 0; $i < 6; $i++)
                <div class="border rounded-lg p-4">
                    <div class="flex justify-between items-center mb-3">
                        <div class="h-4 bg-gray-200 rounded w-20"></div>
                        <div class="h-3 bg-gray-200 rounded w-16"></div>
                    </div>
                    <div class="h-3 bg-gray-200 rounded w-32 mb-3"></div>
                    <div class="space-y-2">
                        @for($j = 0; $j < 4; $j++)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 bg-gray-200 rounded-full"></div>
                                <div class="h-3 bg-gray-200 rounded w-4"></div>
                            </div>
            <div class="flex items-center gap-2">
                                <div class="w-16 bg-gray-200 rounded-full h-2"></div>
                                <div class="h-3 bg-gray-200 rounded w-4"></div>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>
                @endfor
            </div>
        </div>

        <!-- Table Skeleton -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="h-6 bg-gray-200 rounded w-64 mb-6"></div>
            <div class="overflow-x-auto">
                <div class="min-w-full">
                    <div class="bg-gray-50 px-4 py-3">
                        <div class="grid grid-cols-4 gap-4">
                            @for($i = 0; $i < 4; $i++)
                            <div class="h-4 bg-gray-200 rounded w-20"></div>
                            @endfor
                        </div>
                    </div>
                    @for($i = 0; $i < 5; $i++)
                    <div class="px-4 py-3 border-b">
                        <div class="grid grid-cols-4 gap-4">
                            @for($j = 0; $j < 4; $j++)
                            <div class="h-4 bg-gray-200 rounded w-24"></div>
                            @endfor
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- Actual Dashboard Content -->
    <div id="dashboard-content" class="hidden">
        <!-- Header with Filter -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard Dosen</h1>
                    <p class="mt-2 text-gray-600">Selamat datang di Portal TPB!</p>
                </div>

                <!-- Filter Tahun Ajaran -->
                <div class="flex items-center gap-4">
                    <label for="tahun-ajaran-filter" class="text-sm font-medium text-gray-700">Filter Tahun Ajaran:</label>
                    <select id="tahun-ajaran-filter" onchange="filterDashboard()"
                            class="block w-64 px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500">
                    @foreach($tahunAjaranList as $ta)
                        <option value="{{ $ta->id }}" {{ $selectedTahunAjaranId == $ta->id ? 'selected' : '' }}>
                            {{ $ta->tahun }} - {{ ucfirst($ta->periode) }}
                        </option>
                    @endforeach
                </select>
            </div>
    </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Mata Kuliah -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Mata Kuliah</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($jumlahMK) }}</p>
                        <p class="text-xs text-blue-600 mt-1">Yang diampu</p>
                    </div>
                </div>
            </div>

            <!-- Total Kelas -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Kelas</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($jumlahKelas) }}</p>
                        <p class="text-xs text-amber-600 mt-1">Aktif semester ini</p>
                    </div>
                </div>
            </div>

            <!-- Total Mahasiswa -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Mahasiswa</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($jumlahMahasiswa) }}</p>
                        <p class="text-xs text-green-600 mt-1">Total mahasiswa</p>
                    </div>
                </div>
            </div>

            <!-- Progress Input Nilai -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Progress Input Nilai</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $progressPersen }}%</p>
                        <p class="text-xs text-purple-600 mt-1">{{ $jumlahKelasLengkap }} dari {{ $kelasList->count() }} kelas lengkap</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Charts Section -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8">
            <!-- Jumlah Mahasiswa per Mata Kuliah -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Jumlah Mahasiswa per Mata Kuliah</h3>
                        <p class="text-sm text-gray-600">Distribusi mahasiswa di setiap mata kuliah</p>
                    </div>
                    <button onclick="maximizeChart('barChart', 'Jumlah Mahasiswa per Mata Kuliah')"
                            class="text-amber-600 hover:text-amber-700 p-1 rounded transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                        </svg>
                    </button>
        </div>
                <div class="relative h-80">
                    <canvas id="barChart"></canvas>
        </div>
    </div>

            <!-- Distribusi Grade -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Distribusi Grade</h3>
                        <p class="text-sm text-gray-600">Sebaran nilai mahasiswa</p>
                    </div>
                    <button onclick="maximizeChart('pieChart', 'Distribusi Grade')"
                            class="text-amber-600 hover:text-amber-700 p-1 rounded transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                        </svg>
                    </button>
        </div>
                <div class="relative h-80">
            <canvas id="pieChart"></canvas>
        </div>
    </div>
        </div>

        <!-- Progress Rata-rata Nilai per MK -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Progress Rata-rata Nilai per MK</h3>
                    <p class="text-sm text-gray-600">Tren nilai per mata kuliah tiap tahun ajaran</p>
                </div>
                <button onclick="maximizeChart('lineChart', 'Progress Rata-rata Nilai per MK')"
                        class="text-amber-600 hover:text-amber-700 p-1 rounded transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                    </svg>
                </button>
            </div>
            <div class="relative h-80">
        <canvas id="lineChart"></canvas>
            </div>
    </div>

    <!-- Distribusi Nilai per Mata Kuliah -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Distribusi Nilai per Mata Kuliah</h3>
                <p class="text-sm text-gray-600">Detail sebaran grade untuk setiap mata kuliah</p>
            </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($gradeDistributionPerMK as $mk)
                @if($mk['totalMahasiswa'] > 0)
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-center mb-3">
                                <h4 class="font-medium text-sm text-gray-900">{{ $mk['kodeMatkul'] }}-{{ $mk['kurikulum'] }}</h4>
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ $mk['totalMahasiswa'] }} mahasiswa</span>
                            </div>
                            <p class="text-xs text-gray-600 mb-3">{{ $mk['mataKuliah'] }}</p>

                            <div class="space-y-2">
                                @php
                                    $grades = ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'D', 'E'];
                                    $colors = ['#10B981', '#34D399', '#60A5FA', '#3B82F6', '#6366F1', '#F59E0B', '#F97316', '#EF4444', '#DC2626'];
                                @endphp
                                @foreach($grades as $index => $grade)
                                    @if($mk['gradeCounts'][$grade] > 0)
                                        @php
                                            $percentage = $mk['totalMahasiswa'] > 0 ? round(($mk['gradeCounts'][$grade] / $mk['totalMahasiswa']) * 100, 1) : 0;
                                            $color = $colors[$index];
                                        @endphp
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <div class="w-3 h-3 rounded-full" style="background-color: {{ $color }};"></div>
                                                <span class="text-xs font-medium text-gray-700">{{ $grade }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="w-16 bg-gray-200 rounded-full h-2">
                                                <div class="h-2 rounded-full" style="width: {{ $percentage }}%; background-color: {{ $color }};"></div>
                                            </div>
                                            <span class="text-xs text-gray-600 w-8 text-right">{{ $mk['gradeCounts'][$grade] }}</span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

        <!-- Daftar Mata Kuliah Diampu -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Mata Kuliah Diampu</h3>
                <p class="text-sm text-gray-600">Status input nilai untuk semester aktif</p>
            </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Kuliah</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mahasiswa</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Nilai</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($kelasList as $kelas)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $kelas->tahunAjaranMatkul->mataKuliah->namaMatkul ?? '-' }}</div>
                                        <div class="text-sm text-gray-500">{{ $kelas->tahunAjaranMatkul->mataKuliah->kodeMatkul ?? '-' }}{{ $kelas->tahunAjaranMatkul->mataKuliah->kurikulum ? '-' . $kelas->tahunAjaranMatkul->mataKuliah->kurikulum : '' }}</div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $kelas->namaKelas }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $kelas->kelasMahasiswa->count() }}</td>
                                <td class="px-4 py-3">
                                @php
                                    $mahasiswaCount = $kelas->kelasMahasiswa->count();
                                    $sudahNilai = $kelas->kelasMahasiswa->whereNotNull('totalNilai')->count();
                                @endphp
                                @if($mahasiswaCount > 0 && $mahasiswaCount == $sudahNilai)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Lengkap
                                        </span>
                                @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            Belum Lengkap
                                        </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

@include('components.chart-modal')

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Show loading skeleton initially
document.addEventListener('DOMContentLoaded', function() {
    // Hide skeleton and show content after a short delay
    setTimeout(function() {
        document.getElementById('loading-skeleton').classList.add('hidden');
        document.getElementById('dashboard-content').classList.remove('hidden');

        // Initialize charts
        initializeCharts();
    }, 1000);
});

function initializeCharts() {
// Bar Chart
const barChart = new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
        labels: @json($barChartLabels),
        datasets: [{
            label: 'Jumlah Mahasiswa',
            data: @json($barChartData),
            backgroundColor: '#3B82F6',
                borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#3B82F6',
                    borderWidth: 1,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)',
                    }
                },
                x: {
                    grid: {
                        display: false,
                    }
                }
            }
        }
    });

// Pie Chart
const pieChart = new Chart(document.getElementById('pieChart'), {
    type: 'pie',
    data: {
        labels: @json($pieChartLabels),
        datasets: [{
            data: @json($pieChartData),
            backgroundColor: ['#10B981','#34D399','#60A5FA','#3B82F6','#6366F1','#F59E0B','#F97316','#EF4444','#DC2626'],
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const data = context.chart.data.datasets[0].data;
                            const total = data.reduce((sum, val) => sum + val, 0);
                            const percent = total ? ((value / total) * 100).toFixed(1) : 0;
                            return `${label}: ${value} (${percent}%)`;
                        }
                    }
                }
            }
        }
    });

// Line Chart
const lineChart = new Chart(document.getElementById('lineChart'), {
    type: 'line',
    data: {
        labels: @json($lineChartLabels),
        datasets: @json($lineChartDatasets),
    },
    options: {
        responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)',
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)',
                    }
                }
            },
            elements: {
                line: {
                    tension: 0.4,
                },
                point: {
                    radius: 4,
                    hoverRadius: 6,
                }
            }
        }
    });

    // Store charts globally for maximize functionality
    window.barChart = barChart;
    window.pieChart = pieChart;
    window.lineChart = lineChart;
}

function filterDashboard() {
    const tahunAjaranId = document.getElementById('tahun-ajaran-filter').value;
    window.location.href = `{{ route('dosen.dashboard') }}?tahun_ajaran_id=${tahunAjaranId}`;
}
</script>
@endsection
