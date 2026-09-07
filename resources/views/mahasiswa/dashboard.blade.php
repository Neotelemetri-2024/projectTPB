@extends('layouts.main')

@push('head')
    @vite('resources/js/charts.js')
@endpush

@section('content')
<div class="p-4 md:p-6 space-y-4">
    <!-- Header -->
    <div>
        <h1 class="text-xl font-semibold text-gray-900 tracking-tight">Dashboard Mahasiswa</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $user->name }}</p>
    </div>

    <!-- Statistik Akademik -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
            <p class="text-[11px] uppercase tracking-wide text-gray-500">Mata Kuliah Diambil</p>
            <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ $stat['jumlah_mk'] ?? '-' }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
            <p class="text-[11px] uppercase tracking-wide text-gray-500">SKS Terpenuhi</p>
            <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ $stat['jumlah_sks'] ?? '-' }}/144</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
            <p class="text-[11px] uppercase tracking-wide text-gray-500">IPK</p>
            <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ $stat['ipk'] ?? '-' }}/4</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
            <p class="text-[11px] uppercase tracking-wide text-gray-500">CPL Tercapai</p>
            <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ $stat['cpl_tercapai'] ?? '-' }}</p>
        </div>
    </div>

    <!-- Distribusi Nilai CPL (Stack Bar Chart) + Radar Chart -->
    @if(count($cpl_cpmk_data) > 0)
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <h3 class="text-base font-semibold text-gray-900 mb-3">Distribusi Nilai CPL</h3>
        <div class="w-full flex flex-col flex-1 gap-6">
            <div class="flex-1 flex items-center justify-center">
                <div id="cplDistribusiBarChart" class="w-full" style="max-width:100%; min-height:380px; height:380px;"></div>
            </div>
            <div class="flex-1 flex items-center justify-center">
                <div id="cplRadarChartDistribusi" class="w-full" style="max-width:100%; min-height:380px; height:380px;"></div>
            </div>
        </div>
    </div>
    @endif

    <!-- Grafik Nilai Per CPL (Bar Chart per CPL) -->
    @php
    $chartCount = count($cpl_cpmk_data);
    @endphp
    @if($chartCount > 0)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @foreach($cpl_cpmk_data as $idx => $cpl)
        <div class="bg-white border border-gray-200 rounded-xl p-5 flex flex-col justify-between h-full">
            <h3 class="text-base font-semibold text-gray-900 mb-3">Capaian {{ $cpl['cpl_label'] }}</h3>
            <div class="w-full flex flex-col flex-1">
                <div class="flex-1 flex items-center justify-center min-h-[280px]">
                    <div id="cplBarChart{{ $idx }}" class="w-full h-full" style="min-height:280px;"></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

@push('scripts')
<script>
    window.cplCpmkData = JSON.parse('{!! addslashes(json_encode($cpl_cpmk_data)) !!}');
</script>
@endpush
@endsection
