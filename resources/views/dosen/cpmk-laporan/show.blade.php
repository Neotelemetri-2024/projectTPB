@extends('layouts.main')

@section('title', 'Laporan Detail CPMK - ' . $tahunAjaranMatkul->mataKuliah->namaMatkul)

@section('content')
<div class="p-6">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li>
                <a href="{{ route('dosen.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
            </li>
            <li>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </li>
            <li>
                <a href="{{ route('dosen.cpmk-laporan.index') }}" class="hover:text-gray-700">Laporan CPMK</a>
            </li>
            <li>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </li>
            <li class="text-gray-900 font-medium">{{ $tahunAjaranMatkul->mataKuliah->namaMatkul }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Pengukuran CPMK {{ $tahunAjaranMatkul->mataKuliah->namaMatkul }}</h1>
                    <p class="text-gray-600 mt-1">{{ $tahunAjaranMatkul->mataKuliah->kodeMatkul }} • {{ $tahunAjaranMatkul->tahunAjaran->tahun }} - {{ ucfirst($tahunAjaranMatkul->tahunAjaran->periode) }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('dosen.cpmk-laporan.export-pdf', $tahunAjaranMatkul->id) }}"
                       class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export PDF
                    </a>
                    <a href="{{ route('dosen.cpmk-laporan.index') }}"
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(count($cpmkData) > 0)
        @foreach($cpmkData as $index => $data)
            <div class="bg-white rounded-lg shadow-md mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">{{ $data['cpmk']->kodeCpmk }} - {{ $data['cpmk']->deskripsi }}</h2>
                    <div class="flex items-center gap-4 mt-2 text-sm text-gray-600">
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">Total: {{ $data['totalMahasiswa'] }} Mahasiswa</span>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded">Dengan Nilai: {{ $data['mahasiswaDenganNilai'] }} Mahasiswa</span>
                        <span class="bg-amber-100 text-amber-800 px-2 py-1 rounded">Rata-rata: {{ $data['averageNilai'] }}</span>
                        <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded">Kompeten: {{ $data['competentPercentage'] }}%</span>
                    </div>
                </div>

                <div class="p-6">
                    <!-- Charts Row -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <!-- Pie Chart -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Persentase Pengukuran CPMK</h3>
                                    <p class="text-sm text-gray-600 mt-1">Distribusi persentase berdasarkan grade pencapaian</p>
                                </div>
                                <button onclick="maximizeChart('pie-chart-{{ $index }}', 'Persentase Pengukuran CPMK - {{ $data['cpmk']->kodeCpmk }}')" 
                                        class="text-amber-600 hover:text-amber-700 p-1 rounded">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="relative" style="height: 256px;">
                                <canvas id="pie-chart-{{ $index }}"></canvas>
                            </div>
                        </div>

                        <!-- Histogram -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Histogram Pengukuran CPMK</h3>
                                    <p class="text-sm text-gray-600 mt-1">Distribusi frekuensi nilai dengan pengelompokan grade (U, C, E, X)</p>
                                </div>
                                <button onclick="maximizeChart('histogram-{{ $index }}', 'Histogram Pengukuran CPMK - {{ $data['cpmk']->kodeCpmk }}')" 
                                        class="text-amber-600 hover:text-amber-700 p-1 rounded">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="relative" style="height: 256px;">
                                <canvas id="histogram-{{ $index }}"></canvas>
                            </div>
                            <!-- Legend untuk histogram -->
                            <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-blue-500 mr-2 rounded"></div>
                                    <span>U (0-59): Uncompetence</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-red-500 mr-2 rounded"></div>
                                    <span>C (60-74): Competence</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-green-500 mr-2 rounded"></div>
                                    <span>E (75-89): Excellent</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-3 h-3 bg-purple-500 mr-2 rounded"></div>
                                    <span>X (90-100): Extraordinary</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">Nilai Angka (NA)</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">Nilai Mutu (NM)</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">Sebutan Mutu</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">Persentase</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">Competen (%)</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">Tidak Competen (%)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200">Nilai < 60</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200">U</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200">Uncompetence</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200">{{ $data['distribution']['U']['percentage'] }}%</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200"></td>
                                    <td class="px-4 py-3 text-sm text-gray-900"></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200">60 ≤ Nilai < 75</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200">C</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200">Competence</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200">{{ $data['distribution']['C']['percentage'] }}%</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200" rowspan="3">{{ $data['competentPercentage'] }}%</td>
                                    <td class="px-4 py-3 text-sm text-gray-900" rowspan="3">{{ $data['notCompetentPercentage'] }}%</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200">75 ≤ Nilai < 90</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200">E</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200">Excellent</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200">{{ $data['distribution']['E']['percentage'] }}%</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200">Nilai ≥ 90</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200">X</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200">Extraordinary</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200">{{ $data['distribution']['X']['percentage'] }}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>


                </div>
            </div>
        @endforeach
    @else
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Data CPMK</h3>
            <p class="text-gray-600 mb-4">
                Belum ada data nilai CPMK untuk mata kuliah ini.
            </p>
        </div>
    @endif
</div>

<!-- Chart Modal -->
<div id="chartModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 id="chartModalTitle" class="text-xl font-semibold text-gray-900"></h3>
                <button onclick="closeChartModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <div class="relative w-full" style="height: 60vh;">
                    <canvas id="chartModalCanvas" width="800" height="400"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Chart data untuk setiap CPMK
const chartData = @json($cpmkData);

// Inisialisasi chart untuk setiap CPMK
chartData.forEach((data, index) => {
    // Pie Chart
    const pieCtx = document.getElementById(`pie-chart-${index}`);
    if (pieCtx) {
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: ['U', 'C', 'E', 'X'],
                datasets: [{
                    data: [
                        data.distribution.U.percentage,
                        data.distribution.C.percentage,
                        data.distribution.E.percentage,
                        data.distribution.X.percentage
                    ],
                    backgroundColor: [
                        data.distribution.U.color,
                        data.distribution.C.color,
                        data.distribution.E.color,
                        data.distribution.X.color
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    }

    // Histogram
    const histogramCtx = document.getElementById(`histogram-${index}`);
    if (histogramCtx) {
        new Chart(histogramCtx, {
            type: 'line',
            data: {
                labels: data.histogramData.map(item => item.range),
                datasets: [
                    {
                        label: 'Frekuensi',
                        data: data.histogramData.map(item => item.count),
                        borderColor: '#F97316',
                        borderWidth: 3,
                        backgroundColor: 'rgba(249, 115, 22, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#F97316',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Frekuensi'
                        },
                        max: Math.max(...data.histogramData.map(item => item.count)) + 5
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Range Nilai CPMK'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    annotation: {
                        annotations: {
                            // Background zone untuk U (0-59)
                            zoneU: {
                                type: 'box',
                                xMin: -0.5,
                                xMax: 2.5, // 0-19, 20-39, 40-59
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                borderColor: 'rgba(59, 130, 246, 0.3)',
                                borderWidth: 1,
                                label: {
                                    content: 'U (0-59)',
                                    position: 'start',
                                    yAdjust: -10,
                                    color: '#3B82F6',
                                    font: {
                                        weight: 'bold',
                                        size: 12
                                    }
                                }
                            },
                            // Background zone untuk C (60-74)
                            zoneC: {
                                type: 'box',
                                xMin: 2.5,
                                xMax: 4.5, // 60-69, 70-79
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                borderColor: 'rgba(239, 68, 68, 0.3)',
                                borderWidth: 1,
                                label: {
                                    content: 'C (60-74)',
                                    position: 'start',
                                    yAdjust: -10,
                                    color: '#EF4444',
                                    font: {
                                        weight: 'bold',
                                        size: 12
                                    }
                                }
                            },
                            // Background zone untuk E (75-89)
                            zoneE: {
                                type: 'box',
                                xMin: 4.5,
                                xMax: 5.5, // 80-89
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                borderColor: 'rgba(16, 185, 129, 0.3)',
                                borderWidth: 1,
                                label: {
                                    content: 'E (75-89)',
                                    position: 'start',
                                    yAdjust: -10,
                                    color: '#10B981',
                                    font: {
                                        weight: 'bold',
                                        size: 12
                                    }
                                }
                            },
                            // Background zone untuk X (90-100)
                            zoneX: {
                                type: 'box',
                                xMin: 5.5,
                                xMax: 6.5, // 90-100
                                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                                borderColor: 'rgba(139, 92, 246, 0.3)',
                                borderWidth: 1,
                                label: {
                                    content: 'X (90-100)',
                                    position: 'start',
                                    yAdjust: -10,
                                    color: '#8B5CF6',
                                    font: {
                                        weight: 'bold',
                                        size: 12
                                    }
                                }
                            }
                        }
                    }
                }
            }
        });
    }
});

// Fungsi untuk maximize chart
function maximizeChart(chartId, title) {
    const modal = document.getElementById('chartModal');
    const modalTitle = document.getElementById('chartModalTitle');
    const modalCanvas = document.getElementById('chartModalCanvas');
    
    // Cek apakah element ada
    if (!modal || !modalTitle || !modalCanvas) {
        console.error('Modal elements not found');
        return;
    }
    
    modalTitle.textContent = title;
    modal.classList.remove('hidden');
    
    // Destroy previous chart if exists
    if (window.modalChart) {
        window.modalChart.destroy();
    }
    
    // Find the original chart data
    const originalChart = Chart.getChart(chartId);
    if (originalChart) {
        console.log('Original chart found:', originalChart);
        console.log('Chart data:', originalChart.config.data);
        
        // Wait a bit for modal to be visible
        setTimeout(() => {
            try {
                // Create new chart in modal with full configuration
                window.modalChart = new Chart(modalCanvas, {
                    type: originalChart.config.type,
                    data: JSON.parse(JSON.stringify(originalChart.config.data)), // Deep copy
                    options: {
                        ...JSON.parse(JSON.stringify(originalChart.config.options)), // Deep copy
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            ...originalChart.config.options.plugins,
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        }
                    }
                });
                console.log('Modal chart created successfully');
            } catch (error) {
                console.error('Error creating modal chart:', error);
            }
        }, 200);
    } else {
        console.error('Original chart not found for ID:', chartId);
    }
}

// Close modal function
function closeChartModal() {
    const modal = document.getElementById('chartModal');
    if (modal) {
        modal.classList.add('hidden');
    }
    
    if (window.modalChart) {
        window.modalChart.destroy();
        window.modalChart = null;
    }
}
</script>
@endsection
