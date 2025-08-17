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

        <!-- Detail Course Charts Section Skeleton -->
        <div class="mb-8">
            <div class="h-6 bg-gray-200 rounded w-64 mb-6"></div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @for($i = 0; $i < 4; $i++)
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <div class="h-6 bg-gray-200 rounded w-32 mb-2"></div>
                            <div class="h-4 bg-gray-200 rounded w-48"></div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-gray-200 rounded-full"></div>
                            <div class="h-4 bg-gray-200 rounded w-28"></div>
                        </div>
                    </div>
                    <div class="relative h-64">
                        <div class="w-full h-full bg-gray-200 rounded"></div>
                    </div>
                </div>
                @endfor
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

        <!-- System Status Row Skeleton -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- System Health Skeleton -->
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="h-6 bg-gray-200 rounded w-32 mb-4"></div>
                    <div class="space-y-4">
                        @for($i = 0; $i < 5; $i++)
                        <div class="flex items-center justify-between">
                            <div class="h-4 bg-gray-200 rounded w-24"></div>
                            <div class="h-4 bg-gray-200 rounded w-16"></div>
                        </div>
                        @endfor
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="h-6 bg-gray-200 rounded w-32 mb-4"></div>
                    <div class="space-y-4">
                        @for($i = 0; $i < 2; $i++)
                        <div class="flex items-center justify-between">
                            <div class="h-4 bg-gray-200 rounded w-28"></div>
                            <div class="h-4 bg-gray-200 rounded w-8"></div>
                        </div>
                        @endfor
                    </div>
                </div>
            </div>

            <!-- Recent Activities Skeleton -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="h-6 bg-gray-200 rounded w-40 mb-4"></div>
                <div class="space-y-4">
                    @for($i = 0; $i < 6; $i++)
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gray-200 rounded-full"></div>
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="h-4 bg-gray-200 rounded w-full mb-1"></div>
                            <div class="h-3 bg-gray-200 rounded w-20"></div>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
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
                    <p class="mt-2 text-gray-600">Selamat datang, {{ $user->name }}!</p>
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
                    </div>
                </div>
                <div class="relative h-80">
                    <canvas id="cplChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Detail Course Charts Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-900 mb-6">Detail Tren Per Mata Kuliah</h3>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach($detailedCourseCharts as $index => $courseChart)
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900">{{ $courseChart['courseCode'] }}</h4>
                            <p class="text-sm text-gray-600">{{ $courseChart['courseName'] }}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 rounded-full" style="background-color: {{ $courseChart['color'] }}"></div>
                            <span class="text-sm text-gray-600">Rata-rata & Jumlah Mhs</span>
                        </div>
                    </div>
                    <div class="relative h-64">
                        <canvas id="courseChart{{ $index }}"></canvas>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Secondary Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 mb-8">
            <!-- Grade Distribution -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Distribusi Grade</h3>
                        <p class="text-sm text-gray-600">Sebaran nilai mahasiswa</p>
                    </div>
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
                        <p class="text-sm text-gray-600">Distribusi wajib vs pilihan</p>
                    </div>
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
                </div>
                <div class="relative h-64">
                    <canvas id="topStudentsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- System Status Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- System Health & Info -->
            <div class="space-y-6">
                <!-- System Health -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Sistem</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Database</span>
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                                <span class="text-sm font-medium text-gray-900">{{ ucfirst($systemHealth['database']) }}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Storage</span>
                            <span class="text-sm font-medium text-gray-900">{{ $systemHealth['storage'] }}% Used</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Users Online</span>
                            <span class="text-sm font-medium text-gray-900">{{ $systemHealth['users_online'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Uptime</span>
                            <span class="text-sm font-medium text-gray-900">{{ $systemHealth['uptime'] }}%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Response Time</span>
                            <span class="text-sm font-medium text-gray-900">{{ $systemHealth['response_time'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- System Alerts -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Sistem Alert</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Nilai Pending</span>
                            <span class="text-sm font-medium text-orange-600">{{ $systemHealth['pending_grades'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">CPMK Belum Lengkap</span>
                            <span class="text-sm font-medium text-red-600">{{ $systemHealth['incomplete_cpmk'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Aktivitas Terbaru</h3>
                <div class="space-y-4">
                    @forelse($recentActivities as $activity)
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                @if($activity['icon'] == 'user-plus')
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                @elseif($activity['icon'] == 'book')
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                @elseif($activity['icon'] == 'star')
                                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm text-gray-900">{{ $activity['message'] }}</p>
                                <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center">Belum ada aktivitas terbaru</p>
                    @endforelse
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
const detailedCourseCharts = @json($detailedCourseCharts);
const cplAchievementData = @json($cplAchievementData);
const matkulPerformanceData = @json($matkulPerformanceData);
const courseCompletionData = @json($courseCompletionData);
const courseTypeData = @json($courseTypeData);
const topStudentsData = @json($topStudentsData);

// Initialize all charts
function initializeCharts() {
    // Initialize History Chart (Line Chart)
    const historyCtx = document.getElementById('historyChart').getContext('2d');
    const historyChart = new Chart(historyCtx, {
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

    // Initialize Detailed Course Charts
    detailedCourseCharts.forEach((courseData, index) => {
        const ctx = document.getElementById(`courseChart${index}`).getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: courseData.labels,
                datasets: [
                    {
                        label: 'Rata-rata Nilai',
                        data: courseData.avgScores,
                        borderColor: courseData.color,
                        backgroundColor: courseData.color + '20',
                        yAxisID: 'y',
                        tension: 0.4
                    },
                    {
                        label: 'Jumlah Mahasiswa',
                        data: courseData.studentCounts,
                        borderColor: '#6B7280',
                        backgroundColor: '#6B728020',
                        yAxisID: 'y1',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        max: 100,
                        title: {
                            display: true,
                            text: 'Rata-rata Nilai'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Jumlah Mahasiswa'
                        },
                        grid: {
                            drawOnChartArea: false,
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 10
                        }
                    }
                }
            }
        });
    });

    // Initialize CPL Achievement Chart (Bar Chart)
    const cplCtx = document.getElementById('cplChart').getContext('2d');
    const cplChart = new Chart(cplCtx, {
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

    // Initialize Grade Distribution Chart (Doughnut Chart)
    const gradeCtx = document.getElementById('gradeChart').getContext('2d');
    const gradeChart = new Chart(gradeCtx, {
        type: 'doughnut',
        data: matkulPerformanceData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
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

    // Initialize Course Type Chart (Pie Chart)
    const courseTypeCtx = document.getElementById('courseTypeChart').getContext('2d');
    const courseTypeChart = new Chart(courseTypeCtx, {
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

    // Initialize Course Completion Chart
    const completionCtx = document.getElementById('completionChart').getContext('2d');
    const completionChart = new Chart(completionCtx, {
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

    // Initialize Top Students Chart
    const topStudentsCtx = document.getElementById('topStudentsChart').getContext('2d');
    const topStudentsChart = new Chart(topStudentsCtx, {
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
