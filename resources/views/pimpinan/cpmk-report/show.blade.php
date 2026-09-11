@extends('layouts.main')

@section('title', 'Laporan Detail CPMK - ' . $tahunAjaranMatkul->mataKuliah->namaMatkul)

@section('content')
<div class="p-4 md:p-6 space-y-4">
    <nav class="text-sm text-gray-500">
        <a href="{{ route('pimpinan.dashboard') }}" class="text-amber-700 hover:underline">Dashboard</a>
        <span class="mx-1.5 text-gray-400">/</span>
        <a href="{{ route('pimpinan.cpmk-report.index') }}" class="text-amber-700 hover:underline">Laporan CPMK</a>
        <span class="mx-1.5 text-gray-400">/</span>
        <span class="text-gray-900">{{ $tahunAjaranMatkul->mataKuliah->namaMatkul }}</span>
    </nav>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-gray-900 tracking-tight">Pengukuran CPMK {{ $tahunAjaranMatkul->mataKuliah->namaMatkul }}</h1>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $tahunAjaranMatkul->mataKuliah->kodeMatkul }}-{{ $tahunAjaranMatkul->mataKuliah->kurikulum }}
                    · {{ $tahunAjaranMatkul->tahunAjaran->tahun }} - {{ ucfirst($tahunAjaranMatkul->tahunAjaran->periode) }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('pimpinan.cpmk-report.export-pdf', $tahunAjaranMatkul->id) }}"
                   class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md text-sm">
                    Export PDF
                </a>
                <a href="{{ route('pimpinan.cpmk-report.index') }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-md text-sm">
                    Kembali
                </a>
            </div>
        </div>
    </div>

    @if(count($cpmkData) > 0)
        @php
            $summaryCpmk = count($cpmkData);
            $summaryAvg = round(collect($cpmkData)->avg('average_nilai'), 2);
            $summaryKompeten = round(collect($cpmkData)->avg('competent_percentage'), 1);
            $summaryDenganNilai = collect($cpmkData)->max('mahasiswa_dengan_nilai') ?? 0;
            $summaryTotalMhs = collect($cpmkData)->max('total_mahasiswa') ?? 0;
        @endphp

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-gray-500">Jumlah CPMK</p>
                <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ $summaryCpmk }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-gray-500">Mahasiswa</p>
                <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ $summaryDenganNilai }} / {{ $summaryTotalMhs }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-gray-500">Rata-rata Nilai</p>
                <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ $summaryAvg }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-gray-500">Rata-rata Kompeten</p>
                <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ $summaryKompeten }}%</p>
            </div>
        </div>

        @foreach($cpmkData as $index => $data)
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h2 class="text-base font-semibold text-gray-900">{{ $data['cpmk']->kodeCpmk }} — {{ $data['cpmk']->deskripsi }}</h2>
                    <p class="mt-2 text-sm text-gray-600 flex flex-wrap items-center gap-x-3 gap-y-1">
                        <span>Total: {{ $data['total_mahasiswa'] }} mahasiswa</span>
                        <span class="text-gray-300">|</span>
                        <span>Dengan nilai: {{ $data['mahasiswa_dengan_nilai'] }}</span>
                        <span class="text-gray-300">|</span>
                        <span>Rata-rata: {{ $data['average_nilai'] }}</span>
                        <span class="text-gray-300">|</span>
                        <span>Kompeten: {{ $data['competent_percentage'] }}%</span>
                        <span class="text-gray-300">|</span>
                        <span>Tidak kompeten: {{ $data['not_competent_percentage'] }}%</span>
                    </p>
                </div>

                <div class="p-5 space-y-5">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">Persentase Pengukuran CPMK</h3>
                            <p class="text-xs text-gray-500 mt-0.5 mb-3">Distribusi persentase berdasarkan grade pencapaian</p>
                            <div class="relative h-64">
                                <div id="pie-chart-{{ $index }}" class="h-full w-full"></div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">Histogram Pengukuran CPMK</h3>
                            <p class="text-xs text-gray-500 mt-0.5 mb-3">Distribusi frekuensi nilai dengan pengelompokan grade (U, C, E, X)</p>
                            <div class="relative h-64">
                                <div id="histogram-{{ $index }}" class="h-full w-full"></div>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-600">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                                    <span>U (0-59): Uncompetence</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                                    <span>C (60-74): Competence</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                                    <span>E (75-89): Excellent</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                    <span>X (90-100): Extraordinary</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse text-sm">
                            <thead>
                                <tr class="text-left text-[11px] uppercase tracking-wide text-gray-500">
                                    <th class="border border-gray-300 px-3 py-2.5 font-semibold">Nilai Angka (NA)</th>
                                    <th class="border border-gray-300 px-3 py-2.5 font-semibold">Nilai Mutu (NM)</th>
                                    <th class="border border-gray-300 px-3 py-2.5 font-semibold">Sebutan Mutu</th>
                                    <th class="border border-gray-300 px-3 py-2.5 font-semibold">Persentase</th>
                                    <th class="border border-gray-300 px-3 py-2.5 font-semibold">Kompeten (%)</th>
                                    <th class="border border-gray-300 px-3 py-2.5 font-semibold">Tidak Kompeten (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border border-gray-300 px-3 py-2 text-gray-900">0 ≤ Nilai &lt; 60</td>
                                    <td class="border border-gray-300 px-3 py-2 text-gray-900">U</td>
                                    <td class="border border-gray-300 px-3 py-2 text-gray-900">Uncompetence</td>
                                    <td class="border border-gray-300 px-3 py-2 text-gray-900">{{ $data['distribution']['U']['percentage'] }}%</td>
                                    <td class="border border-gray-300 px-3 py-2 text-gray-900" rowspan="4">{{ $data['competent_percentage'] }}%</td>
                                    <td class="border border-gray-300 px-3 py-2 text-gray-900" rowspan="4">{{ $data['not_competent_percentage'] }}%</td>
                                </tr>
                                <tr>
                                    <td class="border border-gray-300 px-3 py-2 text-gray-900">60 ≤ Nilai &lt; 75</td>
                                    <td class="border border-gray-300 px-3 py-2 text-gray-900">C</td>
                                    <td class="border border-gray-300 px-3 py-2 text-gray-900">Competence</td>
                                    <td class="border border-gray-300 px-3 py-2 text-gray-900">{{ $data['distribution']['C']['percentage'] }}%</td>
                                </tr>
                                <tr>
                                    <td class="border border-gray-300 px-3 py-2 text-gray-900">75 ≤ Nilai &lt; 90</td>
                                    <td class="border border-gray-300 px-3 py-2 text-gray-900">E</td>
                                    <td class="border border-gray-300 px-3 py-2 text-gray-900">Excellent</td>
                                    <td class="border border-gray-300 px-3 py-2 text-gray-900">{{ $data['distribution']['E']['percentage'] }}%</td>
                                </tr>
                                <tr>
                                    <td class="border border-gray-300 px-3 py-2 text-gray-900">90 ≤ Nilai ≤ 100</td>
                                    <td class="border border-gray-300 px-3 py-2 text-gray-900">X</td>
                                    <td class="border border-gray-300 px-3 py-2 text-gray-900">Extraordinary</td>
                                    <td class="border border-gray-300 px-3 py-2 text-gray-900">{{ $data['distribution']['X']['percentage'] }}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="bg-white border border-gray-200 rounded-xl px-5 py-10 text-center">
            <p class="text-sm font-medium text-gray-900">Belum ada data CPMK</p>
            <p class="text-sm text-gray-500 mt-1">Belum ada data nilai CPMK untuk mata kuliah ini.</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
window.__chartReadyQueue = window.__chartReadyQueue || [];

const chartData = @json($chartPayload);

function findCategoryLabel(categories, candidates, fallback) {
    for (const label of candidates) {
        if (categories.includes(label)) {
            return label;
        }
    }
    return fallback;
}

function buildGradeBandAnnotations(categories) {
    if (!categories.length) {
        return { xaxis: [] };
    }

    const first = categories[0];
    const last = categories[categories.length - 1];

    const bands = [
        {
            x: findCategoryLabel(categories, ['0-19'], first),
            x2: findCategoryLabel(categories, ['40-59'], categories[Math.min(2, categories.length - 1)]),
            fillColor: '#3B82F6',
            label: 'U (0-59)',
            color: '#3B82F6',
        },
        {
            x: findCategoryLabel(categories, ['60-69'], categories[Math.min(3, categories.length - 1)]),
            x2: findCategoryLabel(categories, ['70-79'], categories[Math.min(4, categories.length - 1)]),
            fillColor: '#EF4444',
            label: 'C (60-74)',
            color: '#EF4444',
        },
        {
            x: findCategoryLabel(categories, ['80-89'], categories[Math.min(5, categories.length - 1)]),
            x2: findCategoryLabel(categories, ['80-89'], categories[Math.min(5, categories.length - 1)]),
            fillColor: '#10B981',
            label: 'E (75-89)',
            color: '#10B981',
        },
        {
            x: findCategoryLabel(categories, ['90-100'], last),
            x2: findCategoryLabel(categories, ['90-100'], last),
            fillColor: '#F59E0B',
            label: 'X (90-100)',
            color: '#F59E0B',
        },
    ];

    return {
        xaxis: bands.map((band) => ({
            x: band.x,
            x2: band.x2,
            fillColor: band.fillColor,
            opacity: 0.1,
            borderColor: band.fillColor,
            label: {
                text: band.label,
                style: {
                    color: band.color,
                    background: 'transparent',
                    fontSize: '12px',
                    fontWeight: 'bold',
                },
            },
        })),
    };
}

window.__chartReadyQueue.push(function () {
    if (!chartData || !chartData.length) {
        console.warn('Tidak ada data chart CPMK');
        return;
    }

    chartData.forEach((data, index) => {
        const pieId = `pie-chart-${index}`;
        const pieEl = document.getElementById(pieId);
        if (pieEl) {
            window.renderApexChart(pieEl, {
                chart: {
                    type: 'pie',
                    height: '100%',
                    toolbar: { show: false },
                },
                series: [
                    data.distribution.U.percentage,
                    data.distribution.C.percentage,
                    data.distribution.E.percentage,
                    data.distribution.X.percentage,
                ],
                labels: ['U', 'C', 'E', 'X'],
                colors: [
                    data.distribution.U.color,
                    data.distribution.C.color,
                    data.distribution.E.color,
                    data.distribution.X.color,
                ],
                legend: {
                    position: 'right',
                },
                stroke: {
                    width: 2,
                    colors: ['#ffffff'],
                },
                dataLabels: {
                    enabled: true,
                },
            }, pieId);
        }

        const histogramId = `histogram-${index}`;
        const histogramEl = document.getElementById(histogramId);
        if (histogramEl) {
            const histogramRows = data.histogram_data || [];
            const categories = histogramRows.map((item) => item.range);
            const counts = histogramRows.map((item) => item.count);
            const maxCount = counts.length ? Math.max(...counts) : 0;

            window.renderApexChart(histogramEl, {
                chart: {
                    type: 'area',
                    height: '100%',
                    toolbar: { show: false },
                },
                series: [{
                    name: 'Frekuensi',
                    data: counts,
                }],
                colors: ['#F97316'],
                stroke: {
                    curve: 'smooth',
                    width: 3,
                    colors: ['#F97316'],
                },
                fill: {
                    type: 'solid',
                    opacity: 0.1,
                },
                markers: {
                    size: 4,
                    colors: ['#F97316'],
                    strokeColors: '#ffffff',
                    strokeWidth: 2,
                },
                xaxis: {
                    categories,
                    title: { text: 'Range Nilai CPMK' },
                },
                yaxis: {
                    min: 0,
                    max: maxCount + 5,
                    title: { text: 'Frekuensi' },
                },
                legend: { show: false },
                dataLabels: { enabled: false },
                annotations: buildGradeBandAnnotations(categories),
            }, histogramId);
        }
    });
});
</script>
@endpush
