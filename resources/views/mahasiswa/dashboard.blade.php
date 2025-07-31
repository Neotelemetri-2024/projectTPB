@extends('layouts.main')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard Mahasiswa</h1>
        <p class="mt-2 text-gray-600">Selamat datang, {{ $user->name }}!</p>
    </div>

    <!-- Welcome Card -->
    <div class="bg-gradient-to-r from-amber-600 to-amber-800 rounded-lg p-6 text-white mb-8">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <h2 class="text-2xl font-bold">Sistem Telemetri Pembelajaran</h2>
                <p class="mt-1 text-amber-100">Pantau aktivitas dan capaian akademik Anda</p>
            </div>
        </div>
    </div>

    <!-- Statistik Akademik (4 Kolom) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200 flex flex-col items-center">
            <div class="flex-shrink-0 mb-2">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-500">Mata Kuliah Diambil</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stat['jumlah_mk'] ?? '-' }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200 flex flex-col items-center">
            <div class="flex-shrink-0 mb-2">
                <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-500">SKS Terpenuhi</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stat['jumlah_sks'] ?? '-' }}/144</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200 flex flex-col items-center">
            <div class="flex-shrink-0 mb-2">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-500">IPK</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stat['ipk'] ?? '-' }}/4</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200 flex flex-col items-center">
            <div class="flex-shrink-0 mb-2">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-500">CPL Tercapai</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stat['cpl_tercapai'] ?? '-' }}</p>
        </div>
    </div>

    <!-- Ringkasan CPL -->
    @if(count($cpl_cpmk_data) > 0)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 mb-8">
        <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-4">Ringkasan Capaian CPL</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($cpl_cpmk_data as $cpl)
            <div class="border rounded-lg p-4">
                <div class="flex justify-between items-start mb-2">
                    <h4 class="font-semibold text-gray-900">{{ $cpl['cpl_label'] }}</h4>
                    <span class="px-2 py-1 text-xs rounded-full {{ $cpl['status'] == 'Tercapai' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $cpl['status'] }}
                    </span>
                </div>
                <div class="mb-3">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Nilai</span>
                        <span>{{ number_format($cpl['nilai_cpl'], 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full {{ $cpl['nilai_cpl'] >= 80 ? 'bg-green-500' : ($cpl['nilai_cpl'] >= 60 ? 'bg-blue-500' : 'bg-red-500') }}" 
                             style="width: {{ min($cpl['nilai_cpl'], 100) }}%"></div>
                    </div>
                </div>
                <p class="text-xs text-gray-500">{{ $cpl['cpl_deskripsi'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Distribusi Nilai CPL (Stack Bar Chart) + Radar Chart -->
    @if(count($cpl_cpmk_data) > 0)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 mb-8">
        <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-3 md:mb-4">Visualisasi Distribusi Nilai CPL</h3>
        <div class="w-full flex flex-col lg:flex-row gap-6">
            <div class="flex-1">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Stack Bar Chart</h4>
                <div class="flex items-center justify-center">
                    <canvas id="cplDistribusiBarChart" class="w-full" style="max-width:100%; min-height:300px; height:300px;"></canvas>
                </div>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Radar Chart</h4>
                <div class="flex items-center justify-center">
                    <canvas id="cplRadarChartDistribusi" class="w-full" style="max-width:100%; min-height:300px; height:300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Detail CPMK per CPL -->
    @if($chartCount > 0)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-8">
        @foreach($cpl_cpmk_data as $idx => $cpl)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base md:text-lg font-semibold text-gray-900">{{ $cpl['cpl_label'] }}</h3>
                <span class="px-3 py-1 text-sm rounded-full {{ $cpl['status'] == 'Tercapai' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $cpl['status'] }}
                </span>
            </div>
            
            <!-- Progress Bar CPL -->
            <div class="mb-4">
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>Nilai CPL</span>
                    <span>{{ number_format($cpl['nilai_cpl'], 1) }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="h-3 rounded-full {{ $cpl['nilai_cpl'] >= 80 ? 'bg-green-500' : ($cpl['nilai_cpl'] >= 60 ? 'bg-blue-500' : 'bg-red-500') }}" 
                         style="width: {{ min($cpl['nilai_cpl'], 100) }}%"></div>
                </div>
            </div>
            
            <!-- Chart CPMK -->
            <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Nilai CPMK</h4>
                <div class="flex items-center justify-center">
                    <canvas id="cplBarChart{{ $idx }}" class="w-full" style="max-width:100%; height:200px;"></canvas>
                </div>
            </div>
            
            <!-- Detail CPMK -->
            <div class="space-y-2">
                <h4 class="text-sm font-medium text-gray-700">Detail CPMK:</h4>
                @foreach($cpl['cpmk_data'] as $cpmk)
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600">{{ $cpmk['label'] }}</span>
                    <span class="font-medium {{ $cpmk['nilai_normal'] >= 80 ? 'text-green-600' : ($cpmk['nilai_normal'] >= 60 ? 'text-blue-600' : 'text-red-600') }}">
                        {{ number_format($cpmk['nilai_normal'], 1) }}%
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    window.cplCpmkData = JSON.parse('{!! addslashes(json_encode($cpl_cpmk_data)) !!}');
    
    // Fungsi untuk mendapatkan warna berdasarkan nilai
    function getColorByValue(value) {
        if (value >= 80) return '#10B981'; // Green
        if (value >= 70) return '#34D399'; // Light Green
        if (value >= 60) return '#60A5FA'; // Blue
        if (value >= 50) return '#F59E0B'; // Yellow
        return '#EF4444'; // Red
    }
    
    // Fungsi untuk mendapatkan warna berdasarkan index
    function getColorByIndex(index) {
        const colors = [
            '#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6',
            '#06B6D4', '#F97316', '#84CC16', '#EC4899', '#6366F1'
        ];
        return colors[index % colors.length];
    }
    
            // Stack Bar Chart untuk Distribusi Nilai CPL
        if (window.cplCpmkData && window.cplCpmkData.length > 0) {
            const cplLabels = window.cplCpmkData.map(item => item.cpl_label);
            const cplValues = window.cplCpmkData.map(item => item.nilai_cpl);
            
            // Buat dataset untuk grouped bar chart (bukan stacked)
            const datasets = [{
                label: 'Nilai CPL (%)',
                data: cplValues,
                backgroundColor: cplValues.map(value => getColorByValue(value)),
                borderColor: cplValues.map(value => getColorByValue(value)),
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false,
            }];
            
            const cplDistribusiBarChart = new Chart(document.getElementById('cplDistribusiBarChart'), {
                type: 'bar',
                data: {
                    labels: cplLabels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Nilai: ' + context.parsed.y.toFixed(2) + '%';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 0
                            }
                        },
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });
        
        // Radar Chart untuk Distribusi CPL
        const radarData = {
            labels: cplLabels,
            datasets: [{
                label: 'Nilai CPL (%)',
                data: cplValues,
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 2,
                pointBackgroundColor: cplValues.map(value => getColorByValue(value)),
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6
            }]
        };
        
        const cplRadarChartDistribusi = new Chart(document.getElementById('cplRadarChartDistribusi'), {
            type: 'radar',
            data: radarData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.r.toFixed(2) + '%';
                            }
                        }
                    }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
        
        // Bar Chart untuk setiap CPL (menampilkan CPMK dalam CPL)
        window.cplCpmkData.forEach((cpl, cplIndex) => {
            const cpmkLabels = cpl.cpmk_data.map(item => item.label);
            const cpmkValues = cpl.cpmk_data.map(item => item.nilai_normal);
            
            const cplBarChart = new Chart(document.getElementById('cplBarChart' + cplIndex), {
                type: 'bar',
                data: {
                    labels: cpmkLabels,
                    datasets: [{
                        label: 'Nilai CPMK',
                        data: cpmkValues,
                        backgroundColor: cpmkValues.map(value => getColorByValue(value)),
                        borderColor: cpmkValues.map(value => getColorByValue(value)),
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Nilai: ' + context.parsed.y.toFixed(2);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });
        });
    }
</script>
@endpush
@endsection
