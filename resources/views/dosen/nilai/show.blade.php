@extends('layouts.main')

@section('title', 'Nilai Mahasiswa - ' . $mataKuliahDiampu->mataKuliah->namaMatkul)

@section('content')
<div class="p-6">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li>
                <a href="{{ route('dosen.nilai.index') }}" class="hover:text-gray-700">Kelola Nilai Mahasiswa</a>
            </li>
            <li>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </li>
            <li class="text-gray-900 font-medium">{{ $mataKuliahDiampu->mataKuliah->namaMatkul }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Nilai Mahasiswa - {{ $mataKuliahDiampu->mataKuliah->namaMatkul }}</h1>
                    <p class="text-gray-600 mt-1">{{ $mataKuliahDiampu->mataKuliah->kodeMatkul }} • {{ $mataKuliahDiampu->tahunAjaran->tahun }} - {{ $mataKuliahDiampu->tahunAjaran->periode }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('dosen.nilai.index') }}"
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

    <!-- Course Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Mahasiswa</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $allMahasiswaCollection->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Kelas</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ App\Models\TahunAjaranMatkul::convertKelasToHuruf($kelasNumbers)->implode(', ') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">SKS</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $mataKuliahDiampu->mataKuliah->sks ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <div>
                    @php
                        // Get all related class IDs for this mata kuliah that are taught by this dosen
                        $relatedTahunAjaranMatkulIds = $mataKuliahClasses->pluck('id');
                        $lastNilaiUpdate = \App\Models\Nilai::whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds)->max('updated_at');
                    @endphp
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-medium text-gray-600">Last Modified</p>
                        @if($lastNilaiUpdate)
                            <span class="text-xs text-gray-500">
                                ({{ \Carbon\Carbon::parse($lastNilaiUpdate)->diffForHumans() }})
                            </span>
                        @endif
                    </div>
                    @if($lastNilaiUpdate)
                        <p class="text-sm font-bold text-gray-900" title="{{ \Carbon\Carbon::parse($lastNilaiUpdate)->format('d/m/Y H:i:s') }}">
                            {{ \Carbon\Carbon::parse($lastNilaiUpdate)->format('d/m/Y H:i') }}
                        </p>
                    @else
                        <p class="text-sm font-bold text-gray-400">-</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Students List -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Mahasiswa</h3>
                    <p class="text-gray-600 mt-1">Pilih mahasiswa untuk mengelola nilai</p>
                </div>
                <div class="flex items-center space-x-4">
                    @if(!$isBulkMode)
                        @php
                            $bulkUrl = request()->fullUrl();
                            $bulkUrl .= (strpos($bulkUrl, '?') !== false ? '&' : '?') . 'bulk=1';
                        @endphp
                        <a href="{{ $bulkUrl }}"
                           class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Aktifkan Input Sekaligus
                        </a>
                    @else
                        @php
                            $individualUrl = request()->url();
                            $currentTab = request('tab', 'all');
                            $currentSort = request('sort', 'nim');
                            $currentDirection = request('direction', 'asc');
                            $currentSearch = request('search', '');
                            $params = [
                                'tab' => $currentTab,
                                'sort' => $currentSort,
                                'direction' => $currentDirection
                            ];
                            if (!empty($currentSearch)) {
                                $params['search'] = $currentSearch;
                            }
                            $individualUrl .= '?' . http_build_query($params);
                        @endphp
                        <a href="{{ $individualUrl }}"
                           class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Kembali ke Mode Individual
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Search Form -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <form method="GET" action="{{ request()->url() }}" class="flex items-center gap-4">
                <!-- Preserve existing parameters -->
                @if(request('tab'))
                    <input type="hidden" name="tab" value="{{ request('tab') }}">
                @endif
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                @if(request('direction'))
                    <input type="hidden" name="direction" value="{{ request('direction') }}">
                @endif
                @if(request('bulk'))
                    <input type="hidden" name="bulk" value="{{ request('bulk') }}">
                @endif

                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <input type="text"
                               name="search"
                               value="{{ request('search', '') }}"
                               placeholder="Cari berdasarkan NIM atau nama mahasiswa..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Cari
                </button>

                <!-- Reset All Filters Button -->
                @if(request('sort') || request('direction') || (request('tab') && request('tab') !== 'all') || request('search'))
                    <a href="{{ request()->url() }}"
                       class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Reset
                    </a>
                @endif
            </form>

            @if(request('search'))
                <div class="mt-3 text-sm text-gray-600">
                    Menampilkan hasil pencarian untuk: <span class="font-semibold">"{{ request('search') }}"</span>
                    @if($mahasiswa->count() > 0)
                        ({{ $allMahasiswaCollection->count() }} mahasiswa ditemukan)
                    @endif
                </div>
            @endif
        </div>

        <div class="p-6">
            @if($mahasiswa->isEmpty())
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Mahasiswa</h3>
                    <p class="text-gray-500">Belum ada mahasiswa yang terdaftar di mata kuliah ini.</p>
                </div>
            @else
                <!-- Tab Navigation -->
                <div class="mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <button type="button"
                                    class="tab-button {{ (!request('tab') || request('tab') === 'all') ? 'active border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                                    data-tab="all">
                                Semua Mahasiswa
                                <span class="ml-2 {{ (!request('tab') || request('tab') === 'all') ? 'bg-amber-100 text-amber-600' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2 rounded-full text-xs font-medium">
                                    {{ $allMahasiswaCollection->count() }}
                                </span>
                            </button>
                            @if(!empty($mahasiswaByKelas))
                                @foreach($mahasiswaByKelas as $kelasHuruf => $mahasiswaInKelas)
                                    @php
                                        $kelasSlug = 'kelas-' . Str::slug($kelasHuruf);
                                        $isActive = request('tab') === $kelasSlug;
                                    @endphp
                                    <button type="button"
                                            class="tab-button {{ $isActive ? 'active border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                                            data-tab="{{ $kelasSlug }}">
                                        Kelas {{ $kelasHuruf }}
                                        <span class="ml-2 {{ $isActive ? 'bg-amber-100 text-amber-600' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2 rounded-full text-xs font-medium">{{ count($mahasiswaInKelas) }}</span>
                                    </button>
                                @endforeach
                            @endif
                        </nav>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    @if($allKomponen->isEmpty())
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c0 .621-.504 1.125-1.125 1.125H18a2.25 2.25 0 01-2.25-2.25M6.75 17.25h-.75m-.75 0h-.75m-.75 0h-.75" />
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Komponen Penilaian</h3>
                            <p class="text-gray-500">Belum ada komponen penilaian yang dikonfigurasi untuk mata kuliah ini.</p>
                        </div>
                    @else
                        @if($isBulkMode)
                            <!-- Bulk Input Form -->
                            <form id="bulk-nilai-form" action="{{ route('dosen.nilai.bulk-store', $mataKuliahDiampu->id) }}" method="POST">
                                @csrf
                                <table id="bulk-input-table" class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium {{ $sortBy === 'nama' ? ($sortDirection === 'asc' ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50') : 'text-gray-500' }} uppercase tracking-wider sticky left-0 {{ $sortBy === 'nama' ? ($sortDirection === 'asc' ? 'bg-green-50' : 'bg-red-50') : 'bg-gray-50' }} sortable-header cursor-pointer" data-sort="nama">
                                            <div class="flex items-center">
                                                Mahasiswa
                                                @if($sortBy === 'nama')
                                                    @if($sortDirection === 'asc')
                                                        <svg class="w-4 h-4 ml-1 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4 ml-1 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    @endif
                                                @else
                                                    <svg class="w-4 h-4 ml-1 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium {{ $sortBy === 'nim' ? ($sortDirection === 'asc' ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50') : 'text-gray-500' }} uppercase tracking-wider sortable-header cursor-pointer" data-sort="nim">
                                            <div class="flex items-center">
                                                NIM
                                                @if($sortBy === 'nim')
                                                    @if($sortDirection === 'asc')
                                                        <svg class="w-4 h-4 ml-1 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4 ml-1 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    @endif
                                                @else
                                                    <svg class="w-4 h-4 ml-1 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Kelas
                                        </th>
                                        @foreach($allKomponen as $komponen)
                                            <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-24">
                                                <div class="text-center">
                                                    <div class="font-semibold">{{ $komponen->nama }}</div>
                                                    @if($komponen->bobot->isNotEmpty())
                                                        <div class="text-xs text-gray-400">
                                                            Total: {{ $komponen->bobot->sum('bobot') }}%
                                                        </div>
                                                    @endif
                                                </div>
                                            </th>
                                        @endforeach
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($allMahasiswaCollection as $mhs)
                                        @php
                                            $kelasNumber = 'Tidak Ada Kelas';
                                            $studentClassId = null;

                                            // Find the class this student belongs to
                                            foreach($mataKuliahClasses as $class) {
                                                $studentInClass = $class->kelasMahasiswa->where('mahasiswaId', $mhs->id)->first();
                                                if ($studentInClass) {
                                                    $kelasNumber = $class->kelas;
                                                    $studentClassId = $class->id;
                                                    break;
                                                }
                                            }

                                            // Convert numeric class to letter for display
                                            $kelasHuruf = is_numeric($kelasNumber) ?
                                                App\Models\TahunAjaranMatkul::convertKelasToHuruf($kelasNumber) :
                                                $kelasNumber;

                                            // Get existing grades for this student
                                            $studentNilai = $existingNilai->get($mhs->id, collect());
                                        @endphp
                                        <tr class="mahasiswa-row hover:bg-gray-50" data-kelas="kelas-{{ Str::slug($kelasHuruf) }}" data-mahasiswa-id="{{ $mhs->id }}">
                                            <!-- Hidden field to identify student's class -->
                                            <input type="hidden" name="student_class_id[{{ $mhs->id }}]" value="{{ $studentClassId }}">
                                            <td class="px-6 py-4 whitespace-nowrap sticky left-0 bg-white">
                                                <div class="flex items-left">
                                                    <div class="text-sm font-medium text-gray-900">{{ $mhs->nama }}</div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-mono text-gray-900">{{ $mhs->nim }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Kelas {{ $kelasHuruf }}
                                                </span>
                                            </td>
                                            @foreach($allKomponen as $komponen)
                                                @php
                                                    // Calculate the existing component value from bobot values
                                                    $nilaiKomponen = null;
                                                    if ($studentNilai->isNotEmpty()) {
                                                        // Only use bobot from the student's class
                                                        $komponenBobot = $komponen->bobot->where('tahunAjaranMatkulId', $studentClassId) ?? collect();
                                                        if ($komponenBobot->isNotEmpty()) {
                                                            // Get values for this component's bobot
                                                            $nilaiBobot = [];
                                                            $totalBobot = 0;

                                                            foreach ($komponenBobot as $bobot) {
                                                                $existingNilaiRecord = $studentNilai->where('bobotId', $bobot->id)->first();
                                                                if ($existingNilaiRecord && $existingNilaiRecord->nilai !== null) {
                                                                    // Reverse calculate: nilai / (bobot/100) = original component value
                                                                    $originalValue = $existingNilaiRecord->nilai / ($bobot->bobot / 100);
                                                                    $nilaiBobot[] = $originalValue;
                                                                    $totalBobot += $bobot->bobot;
                                                                }
                                                            }

                                                            // If we have consistent values, use the first one (they should be the same)
                                                            if (!empty($nilaiBobot)) {
                                                                $nilaiKomponen = $nilaiBobot[0];
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                                    <input type="number"
                                                           name="nilai[{{ $mhs->id }}][{{ $komponen->id }}]"
                                                           value="{{ $nilaiKomponen !== null ? number_format($nilaiKomponen, 2, '.', '') : '' }}"
                                                           min="0"
                                                           max="100"
                                                           step="0.01"
                                                           class="bulk-input w-20 px-2 py-1 border border-gray-300 rounded text-sm text-left focus:ring-amber-500 focus:border-amber-500"
                                                           placeholder="0"
                                                           data-mahasiswa-id="{{ $mhs->id }}"
                                                           data-komponen-id="{{ $komponen->id }}"
                                                           data-original-value="{{ $nilaiKomponen !== null ? number_format($nilaiKomponen, 2, '.', '') : '' }}"
                                                           value="{{ $nilaiKomponen !== null ? number_format($nilaiKomponen, 2, '.', '') : '' }}">
                                                </td>
                                            @endforeach
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <div class="flex items-center justify-center space-x-2">
                                                    <button type="button"
                                                            onclick="saveIndividualNilai({{ $mhs->id }})"
                                                            class="individual-save-btn px-3 py-1 bg-gray-400 text-white text-xs font-medium rounded-lg transition-colors duration-200 cursor-not-allowed"
                                                            id="bulk-save-btn-{{ $mhs->id }}"
                                                            disabled>
                                                        <svg class="w-3 h-3 mr-1 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Simpan
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </form>
                        @else
                            <!-- Individual Input Mode (Default) -->
                            <table id="individual-table" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium {{ $sortBy === 'nim' ? ($sortDirection === 'asc' ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50') : 'text-gray-500' }} uppercase tracking-wider sticky left-0 {{ $sortBy === 'nim' ? ($sortDirection === 'asc' ? 'bg-green-50' : 'bg-red-50') : 'bg-gray-50' }} sortable-header cursor-pointer" data-sort="nim">
                                        <div class="flex items-center">
                                            NIM
                                            @if($sortBy === 'nim')
                                                @if($sortDirection === 'asc')
                                                    <svg class="w-4 h-4 ml-1 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4 ml-1 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                    </svg>
                                                @endif
                                            @else
                                                <svg class="w-4 h-4 ml-1 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                </svg>
                                            @endif
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium {{ $sortBy === 'nama' ? ($sortDirection === 'asc' ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50') : 'text-gray-500' }} uppercase tracking-wider sortable-header cursor-pointer" data-sort="nama">
                                        <div class="flex items-center">
                                            Mahasiswa
                                            @if($sortBy === 'nama')
                                                @if($sortDirection === 'asc')
                                                    <svg class="w-4 h-4 ml-1 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4 ml-1 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                    </svg>
                                                @endif
                                            @else
                                                <svg class="w-4 h-4 ml-1 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                </svg>
                                            @endif
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kelas
                                    </th>
                                    @foreach($allKomponen as $komponen)
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-24">
                                            <div class="text-center">
                                                <div class="font-semibold">{{ $komponen->nama }}</div>
                                                @if($komponen->bobot->isNotEmpty())
                                                    <div class="text-xs text-gray-400">
                                                        Total: {{ $komponen->bobot->sum('bobot') }}%
                                                    </div>
                                                @endif
                                            </div>
                                        </th>
                                    @endforeach
                                    <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total Nilai
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Grade
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($mahasiswa as $mhs)
                                    @php
                                        $kelasNumber = 'Tidak Ada Kelas';
                                        $studentClassId = null;

                                        // Find the class this student belongs to
                                        foreach($mataKuliahClasses as $class) {
                                            $studentInClass = $class->kelasMahasiswa->where('mahasiswaId', $mhs->id)->first();
                                            if ($studentInClass) {
                                                $kelasNumber = $class->kelas;
                                                $studentClassId = $class->id;
                                                break;
                                            }
                                        }

                                        // Convert numeric class to letter for display
                                        $kelasHuruf = is_numeric($kelasNumber) ?
                                            App\Models\TahunAjaranMatkul::convertKelasToHuruf($kelasNumber) :
                                            $kelasNumber;

                                        // Get existing grades for this student
                                        $studentNilai = $existingNilai->get($mhs->id, collect());

                                        // Get final grade from kelasMahasiswa table (passed from controller)
                                        $finalGrade = $nilaiMahasiswa->get($mhs->id);
                                    @endphp
                                    <tr class="mahasiswa-row hover:bg-gray-50" data-kelas="kelas-{{ Str::slug($kelasHuruf) }}" data-mahasiswa-id="{{ $mhs->id }}">
                                        <td class="px-6 py-4 whitespace-nowrap sticky left-0 bg-white">
                                            <div class="flex items-left">
                                                <div class="text-sm font-medium text-gray-900">{{ $mhs->nim }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-mono text-gray-900">{{ $mhs->nama }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Kelas {{ $kelasHuruf }}
                                            </span>
                                        </td>

                                        <!-- Individual Forms for Each Student -->
                                        <form id="form-{{ $mhs->id }}" action="{{ route('dosen.nilai.bulk-store', $mataKuliahDiampu->id) }}" method="POST" class="contents">
                                            @csrf
                                            <!-- Hidden field to identify student's class -->
                                            <input type="hidden" name="student_class_id[{{ $mhs->id }}]" value="{{ $studentClassId }}">
                                            @foreach($allKomponen as $komponen)
                                                @php
                                                    // Calculate the existing component value from bobot values
                                                    $nilaiKomponen = null;
                                                    if ($studentNilai->isNotEmpty()) {
                                                        // Only use bobot from the student's class
                                                        $komponenBobot = $komponen->bobot->where('tahunAjaranMatkulId', $studentClassId) ?? collect();
                                                        if ($komponenBobot->isNotEmpty()) {
                                                            // Get values for this component's bobot
                                                            $nilaiBobot = [];
                                                            $totalBobot = 0;

                                                            foreach ($komponenBobot as $bobot) {
                                                                $existingNilaiRecord = $studentNilai->where('bobotId', $bobot->id)->first();
                                                                if ($existingNilaiRecord && $existingNilaiRecord->nilai !== null) {
                                                                    // Reverse calculate: nilai / (bobot/100) = original component value
                                                                    $originalValue = $existingNilaiRecord->nilai / ($bobot->bobot / 100);
                                                                    $nilaiBobot[] = $originalValue;
                                                                    $totalBobot += $bobot->bobot;
                                                                }
                                                            }

                                                            // If we have consistent values, use the first one (they should be the same)
                                                            if (!empty($nilaiBobot)) {
                                                                $nilaiKomponen = $nilaiBobot[0];
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                                    <input type="number"
                                                           name="nilai[{{ $mhs->id }}][{{ $komponen->id }}]"
                                                           value="{{ $nilaiKomponen !== null ? number_format($nilaiKomponen, 2, '.', '') : '' }}"
                                                           min="0"
                                                           max="100"
                                                           step="0.01"
                                                           class="individual-input w-20 px-2 py-1 border-0 bg-transparent text-sm text-center focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-gray-300 focus:bg-white focus:rounded" disabled
                                                           placeholder="0"
                                                           data-original-value="{{ $nilaiKomponen !== null ? number_format($nilaiKomponen, 2, '.', '') : '' }}">
                                                </td>
                                            @endforeach
                                        </form>

                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $finalGrade && $finalGrade->totalNilai !== null ? number_format($finalGrade->totalNilai, 2) : '-' }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                            @if($finalGrade && $finalGrade->grade)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if(in_array($finalGrade->grade, ['A', 'A-'])) bg-green-100 text-green-800
                                                    @elseif(in_array($finalGrade->grade, ['B+', 'B', 'B-'])) bg-blue-100 text-blue-800
                                                    @elseif(in_array($finalGrade->grade, ['C+', 'C'])) bg-yellow-100 text-yellow-800
                                                    @elseif($finalGrade->grade == 'D') bg-orange-100 text-orange-800
                                                    @elseif($finalGrade->grade == 'E') bg-red-100 text-red-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ $finalGrade->grade }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        </form>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                    @endif
                </div>

                    <!-- Pagination for Individual Mode -->
                    @if(!$isBulkMode && $mahasiswaPaginated && $mahasiswaPaginated->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-700">
                                    Menampilkan {{ $mahasiswaPaginated->firstItem() }} - {{ $mahasiswaPaginated->lastItem() }}
                                    dari {{ $mahasiswaPaginated->total() }} mahasiswa
                                </div>
                                <div>
                                    {{ $mahasiswaPaginated->links() }}
                                </div>
                            </div>
                        </div>
                    @endif

                <!-- Bulk Save Controls (Bottom) -->
                @if($isBulkMode)
                    <div id="bulk-actions" class="mt-6 p-4 bg-gray-50 rounded-lg border-t border-gray-200">
                    <div class="flex justify-end">
                        <div class="text-right space-y-3">
                            <!-- First row: Checkbox -->
                            <div>
                                <label class="flex items-center justify-end">
                                    <span class="mr-2 text-sm text-gray-700">Saya yakin untuk menyimpan semua nilai sekaligus</span>
                                    <input type="checkbox" id="confirm-bulk-save" class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                                </label>
                            </div>
                            <!-- Second row: Save button -->
                            <div>
                                <button type="button"
                                        id="bulk-save-btn"
                                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed"
                                        disabled>
                                    <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Simpan Semua Nilai
                                </button>
                            </div>
                        </div>
                    </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- Modal Peringatan Perubahan Belum Disimpan -->
<x-confirm-modal
    id="unsaved-changes-modal"
    title="Perubahan Belum Disimpan"
    message="Anda memiliki perubahan nilai yang belum disimpan. Jika Anda meninggalkan halaman ini, semua perubahan akan hilang."
    type="warning"
    action="#"
    confirmText="Tinggalkan Halaman"
    cancelText="Tetap di Halaman"
/>

@if($isBulkMode)
<x-confirm-modal
    id="confirm-bobot-warning"
    title="Peringatan Bobot Tidak 100%"
    message="Pastikan total bobot CPMK dan bobot setiap komponen penilaian sudah 100%. Apakah Anda yakin ingin tetap mengaktifkan input sekaligus?"
    type="warning"
    action="#"
    confirmText="Tetap Lanjutkan"
    cancelText="Batal"
/>
@endif
<script>
window.totalBobotCpmk = {{ $totalBobotCpmk ?? 0 }};
window.totalBobotKomponen = @json($totalBobotKomponen ?? []);
</script>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables to track changes
    let hasUnsavedChanges = false;
    let allowNavigation = false;
    let pendingNavigation = null;

    // Function to check for unsaved changes
    function checkForUnsavedChanges() {
        const inputs = document.querySelectorAll('.individual-input, .bulk-input');
        hasUnsavedChanges = false;

        inputs.forEach(input => {
            const originalValue = input.getAttribute('data-original-value') || '';
            const currentValue = input.value || '';

            if (originalValue !== currentValue) {
                hasUnsavedChanges = true;
            }
        });

        // Update UI indicators if needed
        updateSaveButtonStates();
    }

    // Function to update save button states
    function updateSaveButtonStates() {
        // For bulk mode
        const bulkSaveBtn = document.getElementById('bulk-save-btn');
        const confirmCheckbox = document.getElementById('confirm-bulk-save');

        if (bulkSaveBtn && confirmCheckbox) {
            const canSave = hasUnsavedChanges && confirmCheckbox.checked;
            bulkSaveBtn.disabled = !canSave;

            if (canSave) {
                bulkSaveBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                bulkSaveBtn.classList.add('bg-green-600', 'hover:bg-green-700');
            } else {
                bulkSaveBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
                bulkSaveBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
            }
        }

        // For individual mode
        const individualSaveBtns = document.querySelectorAll('.individual-save-btn');
        individualSaveBtns.forEach(btn => {
            const mahasiswaId = btn.id.replace('bulk-save-btn-', '');
            const hasDataForStudent = checkForData(mahasiswaId);
            const hasChangesForStudent = checkForChanges(mahasiswaId);

            btn.disabled = !hasDataForStudent || !hasChangesForStudent;

            if (hasDataForStudent && hasChangesForStudent) {
                btn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                btn.classList.add('bg-green-600', 'hover:bg-green-700');
            } else {
                btn.classList.add('bg-gray-400', 'cursor-not-allowed');
                btn.classList.remove('bg-green-600', 'hover:bg-green-700');
            }
        });
    }

    // Function to show unsaved changes modal
    function showUnsavedChangesModal() {
        const modal = document.getElementById('unsaved-changes-modal');
        const modalContent = modal.querySelector('[data-modal-content]');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Trigger animation
        setTimeout(() => {
            modal.classList.remove('bg-opacity-0');
            modal.classList.add('bg-opacity-10');
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    // Function to hide unsaved changes modal
    function hideUnsavedChangesModal() {
        const modal = document.getElementById('unsaved-changes-modal');
        const modalContent = modal.querySelector('[data-modal-content]');

        modalContent.classList.add('scale-95', 'opacity-0');
        modalContent.classList.remove('scale-100', 'opacity-100');
        modal.classList.remove('bg-opacity-10');
        modal.classList.add('bg-opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    // Handle beforeunload event (page refresh, close tab, etc.)
    window.addEventListener('beforeunload', function(e) {
        if (hasUnsavedChanges && !allowNavigation) {
            e.preventDefault();
            e.returnValue = 'Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman?';
            return e.returnValue;
        }
    });

    // Handle navigation attempts (internal links)
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (link && link.href && hasUnsavedChanges && !allowNavigation) {
            e.preventDefault();
            pendingNavigation = link.href;
            showUnsavedChangesModal();
        }
    });    // Handle form submissions that might navigate away
    document.addEventListener('submit', function(e) {
        // Skip if it's our save forms
        if (e.target.id === 'bulk-nilai-form' || e.target.id.startsWith('form-')) {
            return;
        }

        // Handle modal form submission (Tinggalkan Halaman)
        if (e.target.closest('#unsaved-changes-modal')) {
            e.preventDefault();
            allowNavigation = true;
            hideUnsavedChangesModal();

            if (pendingNavigation) {
                window.location.href = pendingNavigation;
            }
            return;
        }

        if (hasUnsavedChanges && !allowNavigation) {
            e.preventDefault();
            pendingNavigation = e.target.action;
            showUnsavedChangesModal();
        }
    });    // Modal button handlers
    document.addEventListener('click', function(e) {
        // Handle cancel button (Tetap di Halaman)
        if (e.target.matches('[data-modal-hide="unsaved-changes-modal"]')) {
            hideUnsavedChangesModal();
            pendingNavigation = null;
        }
    });

    // Listen for input changes
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('individual-input') || e.target.classList.contains('bulk-input')) {
            checkForUnsavedChanges();
        }
    });

    // Listen for successful saves to reset the unsaved changes flag
    document.addEventListener('valuesSaved', function() {
        hasUnsavedChanges = false;
        allowNavigation = false;

        // Update original values
        const inputs = document.querySelectorAll('.individual-input, .bulk-input');
        inputs.forEach(input => {
            input.setAttribute('data-original-value', input.value || '');
        });

        updateSaveButtonStates();
    });

    // Tab functionality for students with pagination support
    const tabButtons = document.querySelectorAll('.tab-button');

    function switchTab(activeTab) {
        // Get current URL and update tab parameter
        const url = new URL(window.location);
        url.searchParams.set('tab', activeTab);
        url.searchParams.set('sort', '{{ $sortBy }}');
        url.searchParams.set('direction', '{{ $sortDirection }}');
        @if(request('search'))
        url.searchParams.set('search', '{{ request('search') }}');
        @endif
        url.searchParams.delete('page'); // Reset to page 1 when switching tabs

        // Navigate to new URL (this will reload the page with correct pagination)
        window.location.href = url.toString();
    }    // Add click event listeners to tab buttons
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.dataset.tab;
            switchTab(targetTab);
        });
    });

    // Initialize with current active tab from server
    const currentTab = '{{ $activeTab ?? "all" }}';

    // Update pagination links to maintain current tab and sorting
    const paginationLinks = document.querySelectorAll('.pagination a');
    paginationLinks.forEach(link => {
        if (link.href) {
            const url = new URL(link.href);
            url.searchParams.set('tab', currentTab);
            url.searchParams.set('sort', '{{ $sortBy }}');
            url.searchParams.set('direction', '{{ $sortDirection }}');
            @if(request('search'))
            url.searchParams.set('search', '{{ request('search') }}');
            @endif
            @if($isBulkMode)
                url.searchParams.set('bulk', '1');
            @endif
            link.href = url.toString();
        }
    });

    // Sorting functionality
    const sortableHeaders = document.querySelectorAll('.sortable-header');
    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const sortBy = this.getAttribute('data-sort');
            const currentSort = '{{ $sortBy }}';
            const currentDirection = '{{ $sortDirection }}';

            let newDirection = 'asc';
            if (currentSort === sortBy && currentDirection === 'asc') {
                newDirection = 'desc';
            }

            // Get current URL and update sort parameters
            const url = new URL(window.location);
            url.searchParams.set('sort', sortBy);
            url.searchParams.set('direction', newDirection);
            @if(request('search'))
            url.searchParams.set('search', '{{ request('search') }}');
            @endif
            url.searchParams.delete('page'); // Reset to page 1 when sorting

            // Navigate to new URL
            window.location.href = url.toString();
        });
    });

    // Initialize bulk input functionality if in bulk mode
    @if($isBulkMode)
        initializeBulkInput();
    @endif

    // Bulk mode functionality
    @if($isBulkMode)
        const bulkForm = document.getElementById('bulk-nilai-form');
        const confirmCheckbox = document.getElementById('confirm-bulk-save');
        const bulkSaveBtn = document.getElementById('bulk-save-btn');

        if (bulkForm) {
            const bulkInputs = bulkForm.querySelectorAll('input[type="number"]');

            // Listen for changes
            bulkInputs.forEach(input => {
                input.addEventListener('input', function() {
                    checkForBulkChanges();
                    // Validate input
                    let value = parseFloat(this.value);
                    if (isNaN(value) || value < 0) {
                        this.value = '';
                    } else if (value > 100) {
                        this.value = '100';
                    }
                });
            });

            function checkForBulkChanges() {
                checkForUnsavedChanges(); // Use the global function
                updateBulkSaveState();
            }

            function updateBulkSaveState() {
                const canSave = hasUnsavedChanges && confirmCheckbox.checked;
                bulkSaveBtn.disabled = !canSave;

                if (canSave) {
                    bulkSaveBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                    bulkSaveBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                } else {
                    bulkSaveBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
                    bulkSaveBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                }
            }

            // Checkbox change listener
            if (confirmCheckbox) {
                confirmCheckbox.addEventListener('change', updateBulkSaveState);
            }

            // Bulk save button
            if (bulkSaveBtn) {
                bulkSaveBtn.addEventListener('click', function() {
                    if (hasUnsavedChanges && confirmCheckbox && confirmCheckbox.checked) {
                        // Show loading state
                        bulkSaveBtn.disabled = true;
                        bulkSaveBtn.innerHTML = `
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Menyimpan...
                        `;

                        // Dispatch custom event to reset unsaved changes flag
                        document.dispatchEvent(new CustomEvent('valuesSaved'));

                        // Allow navigation since we're saving
                        allowNavigation = true;

                        // Submit the form
                        bulkForm.submit();
                    }
                });
            }

            // Initialize
            checkForBulkChanges();
        }
    @endif

    // Initialize bulk input functionality when switching to bulk mode
    function initializeBulkInput() {
        const bulkInputs = document.querySelectorAll('#bulk-input-table .bulk-input');

        bulkInputs.forEach(input => {
            // Add event listener for input changes (prevent duplicate listeners)
            if (!input.hasAttribute('data-listener-added')) {
                input.addEventListener('input', function() {
                    checkForUnsavedChanges(); // Use global function

                    const mahasiswaId = this.getAttribute('data-mahasiswa-id');
                    const bulkSaveBtn = document.getElementById('bulk-save-btn-' + mahasiswaId);

                    if (bulkSaveBtn) {
                        const hasChanges = checkForChanges(mahasiswaId);
                        const hasData = checkForData(mahasiswaId);

                        if (hasChanges || hasData) {
                            bulkSaveBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                            bulkSaveBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                            bulkSaveBtn.disabled = false;
                        } else {
                            bulkSaveBtn.classList.add('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                            bulkSaveBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                            bulkSaveBtn.disabled = true;
                        }
                    }
                });
                input.setAttribute('data-listener-added', 'true');
            }
        });
    }

    function checkForData(mahasiswaId) {
        const row = document.querySelector(`#bulk-input-table tr[data-mahasiswa-id="${mahasiswaId}"]`);
        if (!row) return false;

        const inputs = row.querySelectorAll('.bulk-input');
        let hasData = false;

        inputs.forEach(input => {
            if (input.value && input.value.trim() !== '') {
                hasData = true;
            }
        });

        return hasData;
    }

    function checkForChanges(mahasiswaId) {
        const row = document.querySelector(`#bulk-input-table tr[data-mahasiswa-id="${mahasiswaId}"]`);
        if (!row) return false;

        const inputs = row.querySelectorAll('.bulk-input');
        let hasChanges = false;

        inputs.forEach(input => {
            const originalValue = input.getAttribute('data-original-value') || '';
            const currentValue = input.value || '';

            if (originalValue !== currentValue) {
                hasChanges = true;
            }
        });

        return hasChanges;
    }

// Function to save individual student grades
    window.saveIndividualNilai = function(mahasiswaId) {
        const row = document.querySelector(`#bulk-input-table tr[data-mahasiswa-id="${mahasiswaId}"]`);
        const saveBtn = document.getElementById('bulk-save-btn-' + mahasiswaId);

        if (!row || !saveBtn) return;

        // Get all inputs for this student
        const inputs = row.querySelectorAll('.bulk-input');
        let hasData = false;

        // Check if there's any data to save
        inputs.forEach(input => {
            if (input.value && input.value.trim() !== '') {
                hasData = true;
            }
        });

        if (!hasData) {
            alert('Tidak ada nilai yang diinput untuk mahasiswa ini.');
            return;
        }

        // Show loading state
        saveBtn.disabled = true;
        saveBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-1 h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Menyimpan...
        `;

        // Create FormData with only this student's data
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        // Add student class ID
        const studentClassInput = row.querySelector(`input[name="student_class_id[${mahasiswaId}]"]`);
        if (studentClassInput) {
            formData.append(`student_class_id[${mahasiswaId}]`, studentClassInput.value);
        }

        // Add nilai array for this specific student using the correct format
        inputs.forEach(input => {
            if (input.value && input.value.trim() !== '') {
                const komponenId = input.getAttribute('data-komponen-id');
                const fieldName = `nilai[${mahasiswaId}][${komponenId}]`;
                formData.append(fieldName, input.value);
            }
        });

        // Send AJAX request
        fetch('{{ route("dosen.nilai.individual-store", $mataKuliahDiampu->id) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showNotification('Nilai berhasil disimpan!', 'success');

                // Dispatch custom event to reset unsaved changes flag
                document.dispatchEvent(new CustomEvent('valuesSaved'));

                // Refresh the page to update total nilai and grade
                setTimeout(() => {
                    allowNavigation = true;
                    window.location.reload();
                }, 1000);
            } else {
                showNotification(data.message || 'Terjadi kesalahan saat menyimpan nilai.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan saat menyimpan nilai.', 'error');
        })
        .finally(() => {
            // Reset button state
            saveBtn.disabled = false;
            saveBtn.innerHTML = `
                <svg class="w-3 h-3 mr-1 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Simpan
            `;
        });
    };

    // Function to show notification
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg text-white font-medium ${
            type === 'success' ? 'bg-green-500' :
            type === 'error' ? 'bg-red-500' :
            'bg-blue-500'
        }`;
        notification.textContent = message;

        document.body.appendChild(notification);

        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Initial check for unsaved changes
    checkForUnsavedChanges();
});
</script>

@push('styles')
<style>
.tooltip {
    position: relative;
}

.tooltip:hover::after {
    content: attr(title);
    position: absolute;
    bottom: 125%;
    left: 50%;
    transform: translateX(-50%);
    background-color: #374151;
    color: white;
    padding: 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    white-space: nowrap;
    z-index: 50;
    opacity: 1;
}

.tooltip:hover::before {
    content: '';
    position: absolute;
    bottom: 115%;
    left: 50%;
    transform: translateX(-50%);
    border: 5px solid transparent;
    border-top-color: #374151;
    z-index: 50;
}
</style>
@endpush
