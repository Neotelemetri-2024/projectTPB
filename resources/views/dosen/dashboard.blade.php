@extends('layouts.main')

@push('head')
    @vite('resources/js/charts.js')
@endpush

@section('content')
<div class="p-4 md:p-6 space-y-4">
    <!-- Header with Filter -->
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 tracking-tight">Dashboard Dosen</h1>
            <p class="text-sm text-gray-500 mt-1">Ringkasan kelas diampu dan status input nilai</p>
        </div>

        <div>
            <label for="tahun-ajaran-filter" class="block text-[11px] font-medium text-gray-500 mb-1">Tahun Ajaran</label>
            <select id="tahun-ajaran-filter" onchange="filterDashboard()"
                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg px-3 py-2 min-w-[180px] shadow-sm">
                @foreach($tahunAjaranList as $ta)
                <option value="{{ $ta->id }}" {{ $selectedTahunAjaranId == $ta->id ? 'selected' : '' }}>
                    {{ $ta->tahun }} - {{ ucfirst($ta->periode) }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
            <p class="text-[11px] uppercase tracking-wide text-gray-500">Mata Kuliah</p>
            <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ number_format($jumlahMK) }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
            <p class="text-[11px] uppercase tracking-wide text-gray-500">Kelas</p>
            <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ number_format($jumlahKelas) }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
            <p class="text-[11px] uppercase tracking-wide text-gray-500">Mahasiswa</p>
            <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ number_format($jumlahMahasiswa) }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
            <p class="text-[11px] uppercase tracking-wide text-gray-500">Progress Input Nilai</p>
            <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ $progressPersen }}%</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ $jumlahKelasLengkap }} dari {{ $kelasList->count() }} kelas lengkap</p>
        </div>
    </div>

    <!-- Main Charts Section -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div class="mb-3">
                <h3 class="text-base font-semibold text-gray-900">Jumlah Mahasiswa per Mata Kuliah</h3>
                <p class="text-sm text-gray-500">Distribusi mahasiswa di setiap mata kuliah</p>
            </div>
            <div class="relative h-80">
                <div id="barChart" class="h-full w-full"></div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <div class="mb-3">
                <h3 class="text-base font-semibold text-gray-900">Distribusi Grade</h3>
                <p class="text-sm text-gray-500">Sebaran nilai mahasiswa</p>
            </div>
            <div class="relative h-80">
                <div id="pieChart" class="h-full w-full"></div>
            </div>
        </div>
    </div>

    <!-- Progress Rata-rata Nilai per MK -->
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <div class="mb-3">
            <h3 class="text-base font-semibold text-gray-900">Progress Rata-rata Nilai per MK</h3>
            <p class="text-sm text-gray-500">Tren nilai per mata kuliah tiap tahun ajaran</p>
        </div>
        <div class="relative h-80">
            <div id="lineChart" class="h-full w-full"></div>
        </div>
    </div>

    <!-- Distribusi Nilai per Mata Kuliah -->
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <div class="mb-4">
            <h3 class="text-base font-semibold text-gray-900">Distribusi Nilai per Mata Kuliah</h3>
            <p class="text-sm text-gray-500">Detail sebaran grade untuk setiap mata kuliah</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($gradeDistributionPerMK as $mk)
            @if($mk['totalMahasiswa'] > 0)
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="font-medium text-sm text-gray-900">{{ $mk['kodeMatkul'] }}-{{ $mk['kurikulum'] }}</h4>
                    <span class="text-xs text-gray-500">{{ $mk['totalMahasiswa'] }} mahasiswa</span>
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
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-900">Daftar Mata Kuliah Diampu</h3>
            <p class="text-sm text-gray-500">Status input nilai untuk semester aktif</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-[11px] font-medium text-gray-500 uppercase tracking-wider">Mata Kuliah</th>
                        <th class="px-4 py-3 text-left text-[11px] font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                        <th class="px-4 py-3 text-left text-[11px] font-medium text-gray-500 uppercase tracking-wider">Mahasiswa</th>
                        <th class="px-4 py-3 text-left text-[11px] font-medium text-gray-500 uppercase tracking-wider">Status Nilai</th>
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

<script>
    const barChartLabels = @json($barChartLabels);
    const barChartData = @json($barChartData);
    const pieChartLabels = @json($pieChartLabels);
    const pieChartData = @json($pieChartData);
    const lineChartLabels = @json($lineChartLabels);
    const lineChartDatasets = @json($lineChartDatasets);

    document.addEventListener('DOMContentLoaded', function() {
        initializeCharts();
    });

    function initializeCharts() {
        const run = () => {
            const ApexCharts = window.ApexCharts;
            if (!ApexCharts) {
                console.error('ApexCharts is not available');
                return;
            }

            const barEl = document.getElementById('barChart');
            if (barEl) {
                window.destroyApexChart?.('barChart');
                window.barChart = new ApexCharts(barEl, {
                    chart: { type: 'bar', height: '100%', toolbar: { show: false } },
                    series: [{ name: 'Jumlah Mahasiswa', data: barChartData }],
                    colors: ['#3B82F6'],
                    xaxis: { categories: barChartLabels },
                    yaxis: { min: 0, title: { text: 'Jumlah Mahasiswa' } },
                    legend: { show: false },
                    dataLabels: { enabled: false },
                    plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
                });
                window.barChart.render();
                window.__apexInstances = window.__apexInstances || {};
                window.__apexInstances.barChart = window.barChart;
            }

            const pieEl = document.getElementById('pieChart');
            if (pieEl) {
                window.destroyApexChart?.('pieChart');
                window.pieChart = new ApexCharts(pieEl, {
                    chart: { type: 'pie', height: '100%' },
                    series: pieChartData,
                    labels: pieChartLabels,
                    colors: ['#10B981', '#34D399', '#60A5FA', '#3B82F6', '#6366F1', '#F59E0B', '#F97316', '#EF4444', '#DC2626'],
                    legend: { position: 'bottom' },
                });
                window.pieChart.render();
                window.__apexInstances = window.__apexInstances || {};
                window.__apexInstances.pieChart = window.pieChart;
            }

            const lineEl = document.getElementById('lineChart');
            if (lineEl) {
                const series = (lineChartDatasets || []).map((d) => ({
                    name: d.label || d.name || 'Series',
                    data: d.data || [],
                }));
                window.destroyApexChart?.('lineChart');
                window.lineChart = new ApexCharts(lineEl, {
                    chart: { type: 'line', height: '100%', toolbar: { show: false } },
                    series,
                    xaxis: { categories: lineChartLabels, title: { text: 'Tahun Ajaran' } },
                    yaxis: { min: 0, max: 100, title: { text: 'Rata-rata Nilai' } },
                    stroke: { curve: 'smooth', width: 3 },
                    markers: { size: 4 },
                    legend: { position: 'top' },
                });
                window.lineChart.render();
                window.__apexInstances = window.__apexInstances || {};
                window.__apexInstances.lineChart = window.lineChart;
            }
        };

        if (window.whenChartReady) {
            window.whenChartReady(run);
        } else {
            run();
        }
    }

    function filterDashboard() {
        const tahunAjaranId = document.getElementById('tahun-ajaran-filter').value;
        window.location.href = `{{ route('dosen.dashboard') }}?tahun_ajaran_id=${tahunAjaranId}`;
    }
</script>
@endsection
