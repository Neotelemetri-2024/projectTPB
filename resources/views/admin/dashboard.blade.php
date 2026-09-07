@extends('layouts.main')

@push('head')
    @vite('resources/js/charts.js')
@endpush

@section('content')
<div class="p-4 md:p-6 space-y-4">
    <!-- Header with Filter -->
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 tracking-tight">Dashboard Administrator</h1>
            <p class="text-sm text-gray-500 mt-1">Ringkasan data akademik dan capaian pembelajaran</p>
        </div>

        <div>
            <label for="tahun-ajaran-filter" class="block text-[11px] font-medium text-gray-500 mb-1">Tahun Ajaran</label>
            <select id="tahun-ajaran-filter" onchange="filterDashboard()"
                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg px-3 py-2 min-w-[180px] shadow-sm">
                <option value="">Semua Tahun Ajaran</option>
                @foreach($tahunAjaranList as $tahunAjaran)
                    <option value="{{ $tahunAjaran->id }}" {{ $selectedTahunAjaranId == $tahunAjaran->id ? 'selected' : '' }}>
                        {{ $tahunAjaran->tahun }} - {{ ucfirst($tahunAjaran->periode) }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
            <p class="text-[11px] uppercase tracking-wide text-gray-500">Total Mahasiswa</p>
            <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ number_format($statistics['totalMahasiswa']) }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
            <p class="text-[11px] uppercase tracking-wide text-gray-500">Total Dosen</p>
            <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ number_format($statistics['totalDosen']) }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
            <p class="text-[11px] uppercase tracking-wide text-gray-500">Total Mata Kuliah</p>
            <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ number_format($statistics['totalMataKuliah']) }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ number_format($statistics['activeCourses']) }} aktif semester ini</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
            <p class="text-[11px] uppercase tracking-wide text-gray-500">CPL & CPMK</p>
            <p class="text-2xl font-semibold text-gray-900 mt-0.5">
                {{ number_format($statistics['totalCPL']) }}
                <span class="text-gray-400 font-normal">|</span>
                {{ number_format($statistics['totalCPMK']) }}
            </p>
        </div>
    </div>

    <!-- Main Charts Section -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div class="mb-3">
                <h3 class="text-base font-semibold text-gray-900">Tren Historis Top 5 Mata Kuliah</h3>
                <p class="text-sm text-gray-500">Semester Ganjil - Berdasarkan jumlah mahasiswa terbanyak</p>
            </div>
            <div class="relative h-80 w-full px-0 mx-0">
                <div id="historyChartGanjil" class="h-full w-full"></div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div class="mb-3">
                <h3 class="text-base font-semibold text-gray-900">Tren Historis Top 5 Mata Kuliah</h3>
                <p class="text-sm text-gray-500">Semester Genap - Berdasarkan jumlah mahasiswa terbanyak</p>
            </div>
            <div class="relative h-80 w-full px-0 mx-0">
                <div id="historyChartGenap" class="h-full w-full"></div>
            </div>
        </div>
    </div>

    <!-- CPL Achievement Chart -->
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <div class="mb-3">
            <h3 class="text-base font-semibold text-gray-900">Capaian Pembelajaran Lulusan</h3>
            <p class="text-sm text-gray-500">Rata-rata nilai per CPL (kode + deskripsi singkat)</p>
        </div>
        <div class="relative h-80">
            <div id="cplChart" class="h-full w-full"></div>
        </div>
    </div>

    <!-- Secondary Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div class="mb-3">
                <h3 class="text-base font-semibold text-gray-900">Distribusi Grade</h3>
                <p class="text-sm text-gray-500">Sebaran nilai per mata kuliah (Top 5)</p>
            </div>
            <div class="relative h-64">
                <div id="gradeChart" class="h-full w-full"></div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div class="mb-3">
                <h3 class="text-base font-semibold text-gray-900">Jenis Mata Kuliah</h3>
                <p class="text-sm text-gray-500">Distribusi wajib vs pilihan (Master Data)</p>
            </div>
            <div class="relative h-64">
                <div id="courseTypeChart" class="h-full w-full"></div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div class="mb-3">
                <h3 class="text-base font-semibold text-gray-900">Tingkat Kelulusan</h3>
                <p class="text-sm text-gray-500">Per mata kuliah</p>
            </div>
            <div class="relative h-64">
                <div id="completionChart" class="h-full w-full"></div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div class="mb-3">
                <h3 class="text-base font-semibold text-gray-900">Top 10 Mahasiswa</h3>
                <p class="text-sm text-gray-500">Berdasarkan rata-rata nilai</p>
            </div>
            <div class="relative h-64">
                <div id="topStudentsChart" class="h-full w-full"></div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <h3 class="text-base font-semibold text-gray-900 mb-3">Aksi Cepat</h3>
        <div class="flex flex-wrap gap-x-4 gap-y-2">
            <a href="{{ route('admin.mahasiswa.create') }}" class="text-sm text-gray-700 hover:text-amber-700">Tambah Mahasiswa</a>
            <a href="{{ route('admin.mata-kuliah.index') }}" class="text-sm text-gray-700 hover:text-amber-700">Kelola Mata Kuliah</a>
            <a href="{{ route('admin.tahun-ajaran.index') }}" class="text-sm text-gray-700 hover:text-amber-700">Buat Tahun Ajaran</a>
            <a href="{{ route('admin.cpl.index') }}" class="text-sm text-gray-700 hover:text-amber-700">Lihat Laporan CPL</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
});

// Chart data from backend
const chartData = @json($chartData);
const chartDataGanjil = @json($chartDataGanjil ?? []);
const chartDataGenap = @json($chartDataGenap ?? []);

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
const cplAchievementData = @json($cplAchievementData);
const matkulPerformanceData = @json($matkulPerformanceData);
const courseCompletionData = @json($courseCompletionData);
const courseTypeData = @json($courseTypeData);
const topStudentsData = @json($topStudentsData);

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

// Global variables untuk chart instances
let historyChartGanjilInstance = null;
let historyChartGenapInstance = null;
let cplChartInstance = null;
let gradeChartInstance = null;
let courseTypeChartInstance = null;
let completionChartInstance = null;
let topStudentsChartInstance = null;


function registerApexInstance(id, instance) {
    window.__apexInstances = window.__apexInstances || {};
    if (id && instance) {
        window.__apexInstances[id] = instance;
    }
}

function destroyApexChart(instance) {
    if (instance && typeof instance.destroy === 'function') {
        instance.destroy();
    }
}

function chartjsToSeries(datasets) {
    return (datasets || []).map((d) => ({
        name: d.label || '',
        data: d.data || []
    }));
}

function chartjsSeriesColors(datasets, preferBorder = false) {
    return (datasets || []).map((d) => {
        const color = preferBorder
            ? (d.borderColor || d.backgroundColor)
            : (d.backgroundColor || d.borderColor);
        return Array.isArray(color) ? color[0] : color;
    }).filter(Boolean);
}

function chartjsBarColors(data) {
    const datasets = (data && data.datasets) || [];
    if (datasets.length === 1 && Array.isArray(datasets[0].backgroundColor)) {
        return datasets[0].backgroundColor;
    }
    return chartjsSeriesColors(datasets);
}

function isDistributedBar(data) {
    const datasets = (data && data.datasets) || [];
    return datasets.length === 1 && Array.isArray(datasets[0].backgroundColor);
}

function chartjsPieSeries(data) {
    const dataset = (data && data.datasets && data.datasets[0]) || {};
    return {
        series: dataset.data || [],
        labels: (data && data.labels) || [],
        colors: Array.isArray(dataset.backgroundColor)
            ? dataset.backgroundColor
            : (dataset.backgroundColor ? [dataset.backgroundColor] : undefined)
    };
}

// Initialize all charts
function initializeCharts() {
    const run = () => {
        try {
            const ApexCharts = window.ApexCharts;
            if (!ApexCharts) {
                console.error('ApexCharts is not available');
                return;
            }

            // Initialize History Chart Ganjil (Line Chart)
            const historyGanjilEl = document.getElementById('historyChartGanjil');
            if (historyGanjilEl) {
                let ganjilData;
                if (chartDataGanjil && chartDataGanjil.labels) {
                    ganjilData = chartDataGanjil;
                } else if (chartDataGanjil && chartDataGanjil.length > 0) {
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
                            backgroundColor: 'rgba(156, 163, 175, 0.1)'
                        }]
                    };
                }

                destroyApexChart(historyChartGanjilInstance);
                historyChartGanjilInstance = new ApexCharts(historyGanjilEl, {
                    chart: { type: 'line', height: '100%', toolbar: { show: false } },
                    series: chartjsToSeries(ganjilData.datasets),
                    xaxis: {
                        categories: ganjilData.labels || [],
                        title: { text: 'Tahun Ajaran' }
                    },
                    yaxis: {
                        min: 0,
                        max: 100,
                        title: { text: 'Rata-rata Nilai' }
                    },
                    colors: chartjsSeriesColors(ganjilData.datasets, true),
                    stroke: { curve: 'smooth', width: 2 },
                    markers: { size: 3 },
                    legend: { position: 'bottom' }
                });
                historyChartGanjilInstance.render();
                window.historyChartGanjilInstance = historyChartGanjilInstance;
                registerApexInstance('historyChartGanjil', historyChartGanjilInstance);
            }

            // Initialize History Chart Genap (Line Chart)
            const historyGenapEl = document.getElementById('historyChartGenap');
            if (historyGenapEl) {
                let genapData;
                if (chartDataGenap && chartDataGenap.labels) {
                    genapData = chartDataGenap;
                } else if (chartDataGenap && chartDataGenap.length > 0) {
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
                            backgroundColor: 'rgba(156, 163, 175, 0.1)'
                        }]
                    };
                }

                destroyApexChart(historyChartGenapInstance);
                historyChartGenapInstance = new ApexCharts(historyGenapEl, {
                    chart: { type: 'line', height: '100%', toolbar: { show: false } },
                    series: chartjsToSeries(genapData.datasets),
                    xaxis: {
                        categories: genapData.labels || [],
                        title: { text: 'Tahun Ajaran' }
                    },
                    yaxis: {
                        min: 0,
                        max: 100,
                        title: { text: 'Rata-rata Nilai' }
                    },
                    colors: chartjsSeriesColors(genapData.datasets, true),
                    stroke: { curve: 'smooth', width: 2 },
                    markers: { size: 3 },
                    legend: { position: 'bottom' }
                });
                historyChartGenapInstance.render();
                window.historyChartGenapInstance = historyChartGenapInstance;
                registerApexInstance('historyChartGenap', historyChartGenapInstance);
            }

            // Initialize CPL Achievement Chart (Bar Chart)
            const cplEl = document.getElementById('cplChart');
            if (cplEl && cplAchievementData) {
                destroyApexChart(cplChartInstance);
                cplChartInstance = new ApexCharts(cplEl, {
                    chart: { type: 'bar', height: '100%', toolbar: { show: false } },
                    series: chartjsToSeries(cplAchievementData.datasets),
                    xaxis: {
                        categories: cplAchievementData.labels || [],
                        title: { text: 'Kode CPL' }
                    },
                    yaxis: {
                        min: 0,
                        max: 100,
                        title: { text: 'Rata-rata Pencapaian (%)' }
                    },
                    colors: chartjsBarColors(cplAchievementData),
                    legend: { show: false },
                    dataLabels: { enabled: false },
                    plotOptions: {
                        bar: {
                            distributed: isDistributedBar(cplAchievementData)
                        }
                    }
                });
                cplChartInstance.render();
                window.cplChartInstance = cplChartInstance;
                registerApexInstance('cplChart', cplChartInstance);
            }

            // Initialize Grade Distribution Chart (Bar Chart)
            const gradeEl = document.getElementById('gradeChart');
            if (gradeEl && matkulPerformanceData) {
                destroyApexChart(gradeChartInstance);
                gradeChartInstance = new ApexCharts(gradeEl, {
                    chart: { type: 'bar', height: '100%', toolbar: { show: false } },
                    series: chartjsToSeries(matkulPerformanceData.datasets),
                    xaxis: {
                        categories: matkulPerformanceData.labels || [],
                        title: { text: 'Grade' }
                    },
                    yaxis: {
                        min: 0,
                        title: { text: 'Jumlah Mahasiswa' }
                    },
                    colors: chartjsSeriesColors(matkulPerformanceData.datasets),
                    legend: { position: 'bottom', fontSize: '10px' },
                    dataLabels: { enabled: false }
                });
                gradeChartInstance.render();
                window.gradeChartInstance = gradeChartInstance;
                registerApexInstance('gradeChart', gradeChartInstance);
            }

            // Initialize Course Type Chart (Pie Chart)
            const courseTypeEl = document.getElementById('courseTypeChart');
            if (courseTypeEl && courseTypeData) {
                const pie = chartjsPieSeries(courseTypeData);
                destroyApexChart(courseTypeChartInstance);
                courseTypeChartInstance = new ApexCharts(courseTypeEl, {
                    chart: { type: 'pie', height: '100%' },
                    series: pie.series,
                    labels: pie.labels,
                    colors: pie.colors,
                    legend: { position: 'bottom' }
                });
                courseTypeChartInstance.render();
                window.courseTypeChartInstance = courseTypeChartInstance;
                registerApexInstance('courseTypeChart', courseTypeChartInstance);
            }

            // Initialize Course Completion Chart
            const completionEl = document.getElementById('completionChart');
            if (completionEl && courseCompletionData) {
                destroyApexChart(completionChartInstance);
                completionChartInstance = new ApexCharts(completionEl, {
                    chart: { type: 'bar', height: '100%', toolbar: { show: false } },
                    series: chartjsToSeries(courseCompletionData.datasets),
                    xaxis: {
                        categories: courseCompletionData.labels || [],
                        title: { text: 'Kode Mata Kuliah' }
                    },
                    yaxis: {
                        min: 0,
                        max: 100,
                        title: { text: 'Tingkat Kelulusan (%)' }
                    },
                    colors: chartjsBarColors(courseCompletionData),
                    legend: { show: false },
                    dataLabels: { enabled: false },
                    plotOptions: {
                        bar: {
                            distributed: isDistributedBar(courseCompletionData)
                        }
                    }
                });
                completionChartInstance.render();
                window.completionChartInstance = completionChartInstance;
                registerApexInstance('completionChart', completionChartInstance);
            }

            // Initialize Top Students Chart (Horizontal Bar)
            const topStudentsEl = document.getElementById('topStudentsChart');
            if (topStudentsEl && topStudentsData) {
                destroyApexChart(topStudentsChartInstance);
                topStudentsChartInstance = new ApexCharts(topStudentsEl, {
                    chart: { type: 'bar', height: '100%', toolbar: { show: false } },
                    series: chartjsToSeries(topStudentsData.datasets),
                    xaxis: {
                        categories: topStudentsData.labels || [],
                        min: 0,
                        max: 100,
                        title: { text: 'Rata-rata Nilai' }
                    },
                    yaxis: {
                        title: { text: 'NIM Mahasiswa' }
                    },
                    colors: chartjsBarColors(topStudentsData),
                    legend: { show: false },
                    dataLabels: { enabled: false },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            distributed: isDistributedBar(topStudentsData)
                        }
                    }
                });
                topStudentsChartInstance.render();
                window.topStudentsChartInstance = topStudentsChartInstance;
                registerApexInstance('topStudentsChart', topStudentsChartInstance);
            }

        } catch (error) {
            console.error('Error initializing charts:', error);
        }
    };

    if (window.whenChartReady) {
        window.whenChartReady(run);
    } else {
        run();
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

/* Pastikan chart memenuhi lebar panel */
.bg-white.border.border-gray-200.rounded-xl [id$="Chart"],
.bg-white.border.border-gray-200.rounded-xl [id^="historyChart"] {
    width: 100% !important;
    max-width: 100% !important;
}
</style>
@endpush

@endsection
