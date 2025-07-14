@extends('layouts.main')

@section('title', 'Kelola Nilai Mahasiswa')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Kelola Nilai Mahasiswa</h2>
                    <p class="text-gray-600 mt-1">Kelola dan input nilai mahasiswa untuk mata kuliah yang diampu</p>
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
            <form method="GET" action="{{ route('dosen.nilai.index') }}">
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
                            <a href="{{ route('dosen.nilai.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2.5 rounded-lg flex items-center justify-center whitespace-nowrap">
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
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
                         data-tahun-ajaran="{{ $mataKuliah->tahunAjaran->id ?? '' }}"
                         data-tahun-ajaran-display="{{ $mataKuliah->tahunAjaran->tahun ?? '' }}"
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
                            <div class="text-center py-3 bg-orange-50 rounded-lg border border-orange-200">
                                <div class="flex items-center justify-center mb-1">
                                    <svg class="w-4 h-4 text-orange-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                                    </svg>
                                    <span class="text-xs font-medium text-orange-700">Mahasiswa</span>
                                </div>
                                <span class="text-lg font-bold text-orange-900">{{ $mataKuliah->kelasMahasiswa->count() }}</span>
                            </div>
                            <div class="text-center py-3 bg-green-50 rounded-lg border border-green-200">
                                <div class="flex items-center justify-center mb-1">
                                    <svg class="w-4 h-4 text-green-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c0 .621-.504 1.125-1.125 1.125H18a2.25 2.25 0 01-2.25-2.25M6.75 17.25h-.75m-.75 0h-.75m-.75 0h-.75" />
                                    </svg>
                                    <span class="text-xs font-medium text-green-700">CPMK</span>
                                </div>
                                <span class="text-lg font-bold text-green-900">{{ $mataKuliah->cpmkMatKul->count() ?? 0 }}</span>
                            </div>
                        </div>

                        <!-- Tombol aksi -->
                        <div class="pt-4 border-t border-gray-200">
                            <a href="{{ route('dosen.nilai.show', $mataKuliah->id) }}"
                               class="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white text-center px-4 py-3 rounded-lg text-sm font-semibold transition-all duration-200 flex items-center justify-center shadow-md hover:shadow-lg">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
                                </svg>
                                Kelola Nilai
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
    // Only client-side filter search input and jenis - tahun_ajaran handled by server-side
    new SearchFilter({
        searchInput: '#search-input',
        filterSelectors: ['#filter-jenis'], // Remove tahun ajaran from client-side filtering
        itemSelector: '.data-item',
        noResultsElement: '#no-results',
        emptyStateElement: '#empty-state',
        totalCountElement: '#total-count',
        containerElement: '#mata-kuliah-grid'
    });
});
</script>
@endpush
