@extends('layouts.main')

@section('title', 'Mata Kuliah yang Diampu')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Mata Kuliah yang Diampu</h2>
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
            <form method="GET" action="{{ route('dosen.mata-kuliah.index') }}">
                <div class="flex flex-col lg:flex-row gap-4 lg:items-center lg:justify-between">
                    <!-- Filter dropdowns -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="w-full sm:w-48">
                            <select name="tahun_ajaran_id" id="filter-tahun-ajaran" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" onchange="this.form.submit()">
                                <option value="">Semua Tahun Ajaran</option>
                                @foreach($tahunAjaranList as $ta)
                                    <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>
                                        {{ $ta->tahun }} - {{ $ta->periode }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full sm:w-32">
                            <select name="jenis" id="filter-jenis" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" onchange="this.form.submit()">
                                <option value="">Semua Jenis</option>
                                <option value="wajib" {{ request('jenis') == 'wajib' ? 'selected' : '' }}>Wajib</option>
                                <option value="pilihan" {{ request('jenis') == 'pilihan' ? 'selected' : '' }}>Pilihan</option>
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
                            <a href="{{ route('dosen.mata-kuliah.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2.5 rounded-lg flex items-center justify-center whitespace-nowrap">
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
            </div>                @if ($mataKuliahDiampu->isEmpty())
                    <div id="empty-state" class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Mata Kuliah</h3>
                        <p class="text-gray-500">Anda belum ditugaskan untuk mengampu mata kuliah apapun.</p>
                    </div>
                @else
                    <div id="mata-kuliah-grid" class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($mataKuliahDiampu as $mataKuliah)
                            <div class="mata-kuliah-card data-item bg-gradient-to-br from-white to-gray-50 border border-gray-200 rounded-xl p-6 hover:shadow-xl hover:border-gray-300 transition-all duration-300"
                             data-nama="{{ strtolower($mataKuliah->mataKuliah->namaMatkul ?? '') }}"
                             data-kode="{{ strtolower($mataKuliah->mataKuliah->kodeMatkul ?? '') }}"
                             data-tahun-ajaran="{{ $mataKuliah->tahunAjaran->tahun ?? '' }}"
                             data-tahun-ajaran-periode="{{ $mataKuliah->tahunAjaran->periode ?? '' }}"
                             data-jenis="{{ $mataKuliah->mataKuliah->jenis ?? '' }}"
                             data-sks="{{ $mataKuliah->mataKuliah->sks ?? '' }}"
                             data-created="{{ $mataKuliah->created_at ?? '' }}">

                            <!-- Header Card -->
                            <div class="flex items-start justify-between mb-6">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-bold text-gray-900 mb-2 leading-tight">
                                        {{ $mataKuliah->mataKuliah->namaMatkul ?? 'Nama tidak tersedia' }}
                                    </h3>
                                    <div class="space-y-2">
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium text-gray-700">Kode:</span>
                                            <span class="text-gray-900 font-mono">{{ $mataKuliah->mataKuliah->kodeMatkul ?? '-' }}</span>
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium text-gray-700">SKS:</span>
                                            <span class="text-gray-900 font-semibold">{{ $mataKuliah->mataKuliah->sks ?? '-' }}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 ml-4">
                                    @if($mataKuliah->mataKuliah->jenis)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                            {{ $mataKuliah->mataKuliah->jenis == 'wajib' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ ucfirst($mataKuliah->mataKuliah->jenis) }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Detail Info -->
                            <div class="space-y-3 mb-6">
                                <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm font-medium text-gray-700">Kelas:</span>
                                    <span class="text-sm font-bold text-gray-900">
                                        @if(isset($mataKuliah->allKelas) && $mataKuliah->allKelas->count() > 0)
                                            {{ App\Models\TahunAjaranMatkul::convertKelasToHuruf($mataKuliah->allKelas)->implode(', ') }}
                                        @else
                                            {{ $mataKuliah->kelasHuruf ?: '-' }}
                                        @endif
                                    </span>
                                </div>
                                <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm font-medium text-gray-700">Tahun Ajaran:</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $mataKuliah->tahunAjaran->tahun ?? '-' }} - {{ $mataKuliah->tahunAjaran->periode ?? '-' }}</span>
                                </div>
                            </div>

                            <!-- Stats -->
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div class="text-center py-3 bg-blue-50 rounded-lg border border-blue-200">
                                    <div class="flex items-center justify-center mb-1">
                                        <svg class="w-4 h-4 text-blue-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                                        </svg>
                                        <span class="text-xs font-medium text-blue-700">Mahasiswa</span>
                                    </div>
                                    <span class="text-lg font-bold text-blue-900">{{ $mataKuliah->kelasMahasiswa->count() }}</span>
                                </div>
                                <div class="text-center py-3 bg-purple-50 rounded-lg border border-purple-200">
                                    <div class="flex items-center justify-center mb-1">
                                        <svg class="w-4 h-4 text-purple-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"></path>
                                        </svg>
                                        <span class="text-xs font-medium text-purple-700">Dosen</span>
                                    </div>
                                    <span class="text-lg font-bold text-purple-900">{{ $mataKuliah->dosenPengampu->unique('dosenId')->count() }}</span>
                                </div>
                            </div>

                            <!-- Tombol aksi -->
                            <div class="pt-4 border-t border-gray-200">
                                <a href="{{ route('dosen.mata-kuliah.show', $mataKuliah->id) }}"
                                   class="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white text-center px-4 py-3 rounded-lg text-sm font-semibold transition-all duration-200 flex items-center justify-center shadow-md hover:shadow-lg">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script src="{{ asset('assets/js/search-filter.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize search and filter
    new SearchFilter({
        searchInput: '#search-input',
        filterSelectors: ['#filter-tahun-ajaran', '#filter-jenis'],
        itemSelector: '.data-item',
        noResultsElement: '#no-results',
        emptyStateElement: '#empty-state',
        totalCountElement: '#total-count',
        containerElement: '#mata-kuliah-grid'
    });
});
</script>
@endpush
