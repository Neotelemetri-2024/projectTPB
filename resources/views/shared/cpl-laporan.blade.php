@extends('layouts.main')

@section('title', 'Laporan CPL')

@push('head')
    @vite('resources/js/charts.js')
@endpush

@section('content')
@php
    $items = collect($detailRows)->values();
    $cplSpans = [];
    $i = 0;
    $n = $items->count();
    while ($i < $n) {
        $cplId = $items[$i]['cpl_id'];
        $j = $i;
        while ($j < $n && $items[$j]['cpl_id'] === $cplId) {
            $j++;
        }
        $cplSpans[$i] = $j - $i;
        for ($k = $i + 1; $k < $j; $k++) {
            $cplSpans[$k] = 0;
        }
        $i = $j;
    }
@endphp
<div class="p-4 md:p-6 space-y-4">
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-gray-900 tracking-tight">Laporan CPL</h1>
                <p class="text-sm text-gray-500 mt-1">Capaian CPL → CPMK → mata kuliah. Nilai min & target dari master CPL.</p>
            </div>
            <div class="flex flex-wrap items-end gap-2">
                <form method="GET" action="{{ route($rolePrefix . '.cpl-laporan.index') }}" class="flex flex-wrap items-end gap-2">
                    <div>
                        <label class="block text-[11px] font-medium text-gray-500 mb-1">Tahun Ajaran</label>
                        <select name="tahun_ajaran_id" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg px-3 py-2 min-w-[180px] shadow-sm">
                            <option value="">Semua</option>
                            @foreach($tahunAjaranList as $ta)
                                <option value="{{ $ta->id }}" @selected((string)$selectedTahunAjaranId === (string)$ta->id)>
                                    {{ $ta->tahun }} - {{ ucfirst($ta->periode) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-medium text-gray-500 mb-1">Kurikulum</label>
                        <select name="kurikulum" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg px-3 py-2 min-w-[160px] shadow-sm">
                            <option value="">Semua</option>
                            @foreach($kurikulumList as $kur)
                                <option value="{{ $kur }}" @selected($selectedKurikulum === $kur)>{{ $kur }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-3 py-2 rounded-md text-sm">Terapkan</button>
                    <a href="{{ route($rolePrefix . '.cpl-laporan.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-md text-sm">Reset</a>
                </form>
                <a href="{{ route($rolePrefix . '.cpl-laporan.export-pdf', request()->only(['tahun_ajaran_id', 'kurikulum'])) }}"
                   class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md text-sm">
                    Export PDF
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
            <p class="text-[11px] uppercase tracking-wide text-gray-500">CPL</p>
            <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ $summary['total_cpl'] }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
            <p class="text-[11px] uppercase tracking-wide text-gray-500">CPMK</p>
            <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ $summary['total_cpmk'] }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
            <p class="text-[11px] uppercase tracking-wide text-gray-500">Mata Kuliah</p>
            <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ $summary['total_mk'] }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
            <p class="text-[11px] uppercase tracking-wide text-gray-500">Rata-rata Capaian</p>
            <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ $summary['avg_capaian'] }}%</p>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <h2 class="text-base font-semibold text-gray-900">Target vs Capaian per CPL</h2>
        <p class="text-sm text-gray-500 mb-3">Rata-rata % capaian CPMK dalam setiap CPL</p>
        <div class="relative h-72">
            <div id="cplLaporanChart" class="h-full w-full"></div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-base font-semibold text-gray-900">Isi laporan</h2>
                <p class="text-xs text-gray-500">{{ $summary['total_baris'] }} baris</p>
            </div>
        </div>

        <div class="overflow-auto max-h-[70vh]">
            <table class="min-w-[1100px] w-full border-collapse text-xs">
                <thead>
                    <tr class="text-left text-[11px] uppercase tracking-wide text-gray-500">
                        <th class="sticky top-0 z-20 bg-white border border-gray-300 px-3 py-2.5 font-semibold">CPL</th>
                        <th class="sticky top-0 z-20 bg-white border border-gray-300 px-3 py-2.5 font-semibold">CPMK</th>
                        <th class="sticky top-0 z-20 bg-white border border-gray-300 px-3 py-2.5 font-semibold">Mata Kuliah</th>
                        <th class="sticky top-0 z-20 bg-white border border-gray-300 px-3 py-2.5 font-semibold">Dosen Pengampu</th>
                        <th class="sticky top-0 z-20 bg-white border border-gray-300 px-3 py-2.5 font-semibold">Sumber Penilaian</th>
                        <th class="sticky top-0 z-20 bg-white border border-gray-300 px-2 py-2.5 font-semibold text-center">Nilai Min</th>
                        <th class="sticky top-0 z-20 bg-white border border-gray-300 px-2 py-2.5 font-semibold text-center">Target</th>
                        <th class="sticky top-0 z-20 bg-white border border-gray-300 px-2 py-2.5 font-semibold text-center">Capaian</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $idx => $row)
                        <tr class="align-top">
                            @if(($cplSpans[$idx] ?? 0) > 0)
                                <td class="border border-gray-300 bg-white px-3 py-2.5 max-w-[220px]" rowspan="{{ $cplSpans[$idx] }}">
                                    <div class="sticky top-12 bg-white py-1">
                                        <p class="font-semibold text-gray-900 leading-snug">{{ $row['kode_cpl'] }}</p>
                                        <p class="mt-1 text-[11px] text-gray-500 leading-snug whitespace-normal">{{ $row['cpl_deskripsi'] }}</p>
                                    </div>
                                </td>
                            @endif
                            <td class="border border-gray-300 px-3 py-2.5 max-w-[240px]">
                                <p class="font-medium text-gray-900 leading-snug">{{ $row['kode_cpmk'] }}</p>
                                <p class="mt-1 text-[11px] text-gray-500 leading-snug whitespace-normal">{{ $row['cpmk_deskripsi'] }}</p>
                            </td>
                            <td class="border border-gray-300 px-3 py-2.5 max-w-[200px]">
                                @if($rolePrefix === 'pimpinan')
                                    <p class="font-medium text-gray-900 leading-snug">{{ $row['nama_mk'] }}</p>
                                    <a href="{{ route('pimpinan.cpmk-report.show', $row['tahun_ajaran_matkul_id']) }}"
                                       class="inline-flex items-center mt-1.5 bg-amber-600 hover:bg-amber-700 text-white px-2.5 py-1 rounded-md text-xs font-medium">
                                        Lihat
                                    </a>
                                @else
                                    <p class="font-medium text-gray-900 leading-snug">{{ $row['nama_mk'] }}</p>
                                @endif
                                <p class="mt-1 text-[11px] text-gray-500">{{ $row['kode_mk'] }} · {{ $row['tahun_ajaran_label'] }}</p>
                            </td>
                            <td class="border border-gray-300 px-3 py-2.5 text-gray-700 max-w-[160px] whitespace-normal">{{ $row['dosen'] }}</td>
                            <td class="border border-gray-300 px-3 py-2.5 text-gray-700 max-w-[200px] whitespace-normal">{{ $row['sumber_penilaian'] }}</td>
                            <td class="border border-gray-300 px-2 py-2.5 text-center text-gray-800">{{ $row['nilai_minimal'] }}</td>
                            <td class="border border-gray-300 px-2 py-2.5 text-center text-gray-800">{{ $row['target_persen'] }}%</td>
                            <td class="border border-gray-300 px-2 py-2.5 text-center">
                                @if($row['capaian'] === null)
                                    <span class="text-gray-400">—</span>
                                @else
                                    <span class="font-semibold {{ $row['capaian'] >= $row['target_persen'] ? 'text-emerald-700' : 'text-red-700' }}">
                                        {{ $row['capaian'] }}%
                                    </span>
                                    <div class="text-[10px] text-gray-400 mt-0.5">{{ $row['mencapai'] }}/{{ $row['total_mhs'] }}</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="border border-gray-300 px-4 py-12 text-center text-gray-500 text-sm">
                                Tidak ada data untuk filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.__chartReadyQueue = window.__chartReadyQueue || [];
window.__chartReadyQueue.push(function () {
    const chartData = @json($chartData);
    const el = document.querySelector('#cplLaporanChart');
    if (!el || typeof ApexCharts === 'undefined') return;

    new ApexCharts(el, {
        chart: { type: 'bar', height: '100%', toolbar: { show: false }, fontFamily: 'inherit' },
        series: [
            { name: 'Capaian (%)', data: chartData.capaian },
            { name: 'Target (%)', data: chartData.target }
        ],
        xaxis: { categories: chartData.labels },
        yaxis: {
            max: 100, min: 0,
            labels: { formatter: (v) => Math.round(v) + '%' }
        },
        colors: ['#D97706', '#9CA3AF'],
        plotOptions: { bar: { columnWidth: '55%', borderRadius: 3 } },
        dataLabels: { enabled: false },
        legend: { position: 'top' },
        tooltip: { y: { formatter: (v) => v + '%' } },
        grid: { borderColor: '#E5E7EB', strokeDashArray: 4 }
    }).render();
});
</script>
@endpush
