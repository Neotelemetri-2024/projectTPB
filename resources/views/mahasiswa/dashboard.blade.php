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

    <!-- Distribusi Nilai CPL (Stack Bar Chart) + Radar Chart -->
    @if(count($cpl_cpmk_data) > 0)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 mb-8">
        <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-3 md:mb-4">Distribusi Nilai CPL</h3>
        <div class="w-full flex flex-col flex-1 gap-6">
            <div class="flex-1 flex items-center justify-center">
                <canvas id="cplDistribusiBarChart" class="w-full" style="max-width:100%; min-height:380px; height:380px;"></canvas>
            </div>
            <div class="flex-1 flex items-center justify-center">
                <canvas id="cplRadarChartDistribusi" class="w-full" style="max-width:100%; min-height:380px; height:380px;"></canvas>
            </div>
        </div>
    </div>
    @endif

    <!-- Grafik Nilai Per CPL (Bar Chart per CPL) -->
    @php
        $chartCount = count($cpl_cpmk_data);
    @endphp
    @if($chartCount > 0)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-8">
        @foreach($cpl_cpmk_data as $idx => $cpl)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 flex flex-col justify-between h-full">
            <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-3 md:mb-4">Capaian {{ $cpl['cpl_label'] }}</h3>
            <div class="w-full flex flex-col flex-1">
                <div class="flex-1 flex items-center justify-center">
                    <canvas id="cplBarChart{{ $idx }}" class="w-full" style="max-width:100%;"></canvas>
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
