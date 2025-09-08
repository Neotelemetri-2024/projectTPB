@extends('layouts.main')

@section('title', 'Kelola CPMK')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Kelola CPMK</h2>
                    <p class="text-gray-600 mt-1">Kelola Capaian Pembelajaran Mata Kuliah dan Bobot Komponen</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-sm text-gray-500">
                        Total: <span id="total-count">{{ $mataKuliahDiampu->count() }}</span> mata kuliah
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter dan Search Form -->
        <div class="p-6 border-b border-gray-200">
            <form method="GET" action="{{ route('dosen.cpmk.index') }}">
                <div class="flex flex-col lg:flex-row gap-4 lg:items-center lg:justify-between">
                    <!-- Filter dropdowns -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="w-full sm:w-48">
                            <select name="tahun_ajaran_id" id="filter-tahun-ajaran" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" onchange="this.form.submit()">
                                <option value="">Semua Tahun Ajaran</option>
                                @foreach($tahunAjarans as $ta)
                                    <option value="{{ $ta->id }}" {{ $selectedTahunAjaranId == $ta->id ? 'selected' : '' }}>
                                        {{ $ta->tahun }} - {{ $ta->periode }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full sm:w-32">
                            <select name="jenis" id="filter-jenis" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" onchange="this.form.submit()">
                                <option value="">Semua Jenis</option>
                                @foreach($jenisOptions as $jenis)
                                    <option value="{{ $jenis }}" {{ request('jenis') == $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Search dan buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 lg:flex-1">
                        <div class="flex-1">
                            <input type="text" name="search" id="search-input" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" placeholder="Cari nama atau kode mata kuliah..." value="{{ request('search') }}">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg flex items-center justify-center whitespace-nowrap">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Cari
                            </button>
                            <a href="{{ route('dosen.cpmk.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2.5 rounded-lg flex items-center justify-center whitespace-nowrap">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="p-6">
            <!-- No Results Message (hidden by default) -->
            <div id="no-results" class="text-center py-12 hidden">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Hasil</h3>
                <p class="text-gray-500">Tidak ada mata kuliah yang sesuai dengan pencarian atau filter yang dipilih.</p>
            </div>

            @if ($mataKuliahDiampu->isEmpty())
                <div id="empty-state" class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c0 .621-.504 1.125-1.125 1.125H18a2.25 2.25 0 01-2.25-2.25M6.75 17.25h-.75m-.75 0h-.75m-.75 0h-.75" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Mata Kuliah</h3>
                    <p class="text-gray-500">Anda belum ditugaskan untuk mengampu mata kuliah apapun.</p>
                </div>
            @else
                <!-- Table List -->
                <div id="mata-kuliah-table" class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200 min-w-[100px]">
                                    Kode
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200 min-w-[200px]">
                                    Nama Mata Kuliah
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200 min-w-[60px]">
                                    SKS
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200 min-w-[80px]">
                                    Jenis
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200 min-w-[80px]">
                                    Kelas
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200 min-w-[120px]">
                                    Tahun Ajaran
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200 min-w-[150px]">
                                    Dosen Pengampu
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200 min-w-[70px]">
                                    CPMK
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200 min-w-[120px]">
                                    Last Modified
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200 min-w-[120px]">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($mataKuliahDiampu as $index => $mataKuliah)
                                <tr class="hover:bg-gray-50 transition-colors duration-200"
                                    data-nama="{{ strtolower($mataKuliah->mataKuliah->namaMatkul ?? '') }}"
                                    data-kode="{{ strtolower($mataKuliah->mataKuliah->kodeMatkul ?? '') }}"
                                    data-tahun-ajaran="{{ $mataKuliah->tahunAjaran->id ?? '' }}"
                                    data-tahun-ajaran-display="{{ $mataKuliah->tahunAjaran->tahun ?? '' }}"
                                    data-tahun-ajaran-periode="{{ $mataKuliah->tahunAjaran->periode ?? '' }}"
                                    data-jenis="{{ $mataKuliah->mataKuliah->jenis ?? '' }}"
                                    data-sks="{{ $mataKuliah->mataKuliah->sks ?? '' }}"
                                    data-created="{{ $mataKuliah->created_at ?? '' }}">

                                    <!-- Kode Column -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-mono font-medium text-gray-900">
                                            {{ $mataKuliah->mataKuliah->kodeMatkul ?? '-' }}{{ $mataKuliah->mataKuliah->kurikulum ? '-' . $mataKuliah->mataKuliah->kurikulum : '' }}
                                        </div>
                                    </td>

                                    <!-- Nama Mata Kuliah Column -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $mataKuliah->mataKuliah->namaMatkul ?? 'Nama tidak tersedia' }}
                                        </div>
                                    </td>

                                    <!-- SKS Column -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $mataKuliah->mataKuliah->sks ?? '-' }}
                                        </div>
                                    </td>

                                    <!-- Jenis Column -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($mataKuliah->mataKuliah->jenis)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                {{ $mataKuliah->mataKuliah->jenis == 'wajib' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                                {{ ucfirst($mataKuliah->mataKuliah->jenis) }}
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-500">-</span>
                                        @endif
                                    </td>

                                    <!-- Kelas Column -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="text-sm font-medium text-gray-900">
                                            @if($mataKuliah->kelas && $mataKuliah->kelas->count() > 0)
                                                {{ $mataKuliah->kelas->pluck('namaKelas')->implode(', ') }}
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Tahun Ajaran Column -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $mataKuliah->tahunAjaran->tahun ?? '-' }} - {{ $mataKuliah->tahunAjaran->periode ?? '-' }}
                                        </div>
                                    </td>

                                    <!-- Dosen Pengampu Column -->
                                    <td class="px-6 py-4 text-center">
                                        <div class="text-sm text-gray-900">
                                            @if($mataKuliah->dosenPengampuKelas && $mataKuliah->dosenPengampuKelas->count() > 0)
                                                @php
                                                    $dosenNames = $mataKuliah->dosenPengampuKelas->map(function($dosenPengampuKelas) {
                                                        return $dosenPengampuKelas->dosen->nama ?? 'Unknown';
                                                    })->unique()->values();
                                                @endphp
                                                @if($dosenNames->count() <= 2)
                                                    @foreach($dosenNames as $dosenName)
                                                        <span class="inline-block {{ $dosenName === auth()->user()->dosen->nama ? 'bg-green-100 text-green-800 font-medium' : 'bg-blue-100 text-blue-800' }} text-xs px-2 py-1 rounded-full mr-1"
                                                              title="{{ $dosenName === auth()->user()->dosen->nama ? 'Anda' : $dosenName }}">
                                                            {{ $dosenName === auth()->user()->dosen->nama ? 'Anda' : Str::limit($dosenName, 12) }}
                                                        </span>
                                                    @endforeach
                                                @else
                                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1">
                                                        {{ $dosenNames->first() === auth()->user()->dosen->nama ? 'Anda' : Str::limit($dosenNames->first(), 12) }}
                                                    </span>
                                                    <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full"
                                                          title="{{ $dosenNames->slice(1)->implode(', ') }}">
                                                        +{{ $dosenNames->count() - 1 }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-sm text-gray-500">-</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- CPMK Column -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-900">{{ $mataKuliah->cpmkMatKul->count() ?? 0 }}</span>
                                        </div>
                                    </td>

                                    <!-- Last Updated Column -->
                                    <td class="px-6 py-4 text-center">
                                        <div class="text-sm text-gray-900">
                                            @if($mataKuliah->cpmkMatKul && $mataKuliah->cpmkMatKul->count() > 0)
                                                @php
                                                    $latestUpdate = $mataKuliah->cpmkMatKul->max('updated_at');
                                                @endphp
                                                @if($latestUpdate)
                                                    <!-- Tanggal dan jam dalam 1 baris -->
                                                    <div class="text-xs text-gray-900 font-medium" title="Last updated: {{ \Carbon\Carbon::parse($latestUpdate)->format('d M Y, H:i') }}">
                                                        {{ \Carbon\Carbon::parse($latestUpdate)->format('d/m/Y H:i') }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ \Carbon\Carbon::parse($latestUpdate)->diffForHumans() }}
                                                    </div>
                                                @else
                                                    <div class="text-xs text-gray-500">
                                                        -
                                                    </div>
                                                @endif
                                            @else
                                                <div class="text-xs text-gray-500">
                                                    -
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Aksi Column -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex justify-center space-x-2">
                                            <a href="{{ route('dosen.cpmk.show', $mataKuliah->id) }}"
                                               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                               title="Kelola CPMK">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c0 .621-.504 1.125-1.125 1.125H18a2.25 2.25 0 01-2.25-2.25M6.75 17.25h-.75m-.75 0h-.75m-.75 0h-.75" />
                                                </svg>
                                                Kelola CPMK
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
    </div>
</div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form if tahun ajaran is auto-selected but not in URL
    const tahunAjaranSelect = document.querySelector('select[name="tahun_ajaran_id"]');
    const urlParams = new URLSearchParams(window.location.search);
    const hasTahunAjaranInUrl = urlParams.has('tahun_ajaran_id');
    
    if (tahunAjaranSelect && tahunAjaranSelect.value && !hasTahunAjaranInUrl) {
        // Preserve any existing search parameter
        const searchInput = document.querySelector('input[name="search"]');
        const jenisSelect = document.querySelector('select[name="jenis"]');
        
        // Auto-submit the form to update URL with the selected tahun ajaran
        tahunAjaranSelect.form.submit();
    }
});
</script>
@endpush
