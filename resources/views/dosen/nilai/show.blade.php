@extends('layouts.main')

@section('title', 'Nilai Mahasiswa - ' . $tahunAjaranMatkul->mataKuliah->namaMatkul)

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
            <li class="text-gray-900 font-medium">{{ $tahunAjaranMatkul->mataKuliah->namaMatkul }}</li>
        </ol>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
        <div class="flex items-center mb-2">
            <svg class="w-5 h-5 mr-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span class="font-medium">Import Berhasil!</span>
        </div>
        <div class="text-sm whitespace-pre-line">{{ session('success') }}</div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center">
        <svg class="w-5 h-5 mr-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
        </svg>
        <span class="font-medium">{{ session('error') }}</span>
    </div>
    @endif

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Nilai Mahasiswa - {{ $tahunAjaranMatkul->mataKuliah->namaMatkul }}</h1>
                    <p class="text-gray-600 mt-1">{{ $tahunAjaranMatkul->mataKuliah->kodeMatkul }}-{{ $tahunAjaranMatkul->mataKuliah->kurikulum }} • {{ $tahunAjaranMatkul->tahunAjaran->tahun }} - {{ $tahunAjaranMatkul->tahunAjaran->periode }}</p>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
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
                        {{ $kelasNumbers->implode(', ') }}
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
                    <p class="text-2xl font-bold text-gray-900">{{ $tahunAjaranMatkul->mataKuliah->sks ?? '-' }}</p>
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
                    <p class="text-sm font-bold text-gray-400">Belum ada nilai</p>
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
                    <!-- Export Template Button -->
                    <a href="{{ route('dosen.nilai.export-template', $tahunAjaranMatkul->id) }}"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download Template Excel
                    </a>

                    <!-- Import Excel Button -->
                    <button type="button" onclick="checkImportValidation()"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        Import Excel
                    </button>

                    <!-- Toggle Edit Button -->
                    <button id="toggle-edit-nilai" type="button" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6 6M3 17v2a2 2 0 002 2h2m14-6v6a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h6"></path>
                        </svg>
                        Aktifkan Input Nilai
                    </button>

                    <!-- Reset Button -->
                    <button id="reset-nilai" type="button" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 hidden" data-modal-toggle="reset-confirm-modal">
                        <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Reset Semua Nilai
                    </button>


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

        <!-- Progress Bar Container (Hidden by default) -->
        <div id="import-progress-container" class="hidden px-6 py-4 border-b border-gray-200 bg-blue-50">
            <div class="flex justify-between mb-1">
                <span class="text-sm font-medium text-blue-700">Memproses Data Excel...</span>
                <span id="import-progress-text" class="text-sm font-medium text-blue-700">0%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div id="import-progress-bar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-500 ease-out" style="width: 0%"></div>
            </div>
            <p id="import-progress-detail" class="text-xs text-blue-600 mt-2">Menginisialisasi import...</p>
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
                        @foreach($mahasiswaByKelas as $kelasNama => $mahasiswaInKelas)
                        @php
                        $kelasSlug = 'kelas-' . Str::slug($kelasNama);
                        $isActive = request('tab') === $kelasSlug;
                        @endphp
                        <button type="button"
                            class="tab-button {{ $isActive ? 'active border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                            data-tab="{{ $kelasSlug }}">
                            Kelas {{ $kelasNama }}
                            <span class="ml-2 {{ $isActive ? 'bg-amber-100 text-amber-600' : 'bg-gray-100 text-gray-600' }} py-0.5 px-2 rounded-full text-xs font-medium">{{ count($mahasiswaInKelas) }}</span>
                        </button>
                        @endforeach
                        @endif
                    </nav>
                </div>
            </div>

            <form id="bulk-nilai-form" method="POST" action="{{ route('dosen.nilai.bulk-store', $tahunAjaranMatkul->id) }}">
                @csrf
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="nilai-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mahasiswa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                                @foreach($allKomponen as $komponen)
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-24">
                                    {{ $komponen->nama }}
                                    @php
                                    $bobotKomponen = $totalBobotKomponen[$komponen->id] ?? 0;
                                    @endphp
                                    <div class="text-xs text-gray-400 mt-1">{{ number_format($bobotKomponen, 2) }}%</div>
                                </th>
                                @endforeach
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Nilai</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider grade-column">Grade</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($mahasiswa as $mhs)
                            @php
                            $kelasNama = 'Tidak Ada Kelas';
                            $studentClassId = null;

                            // Cari kelas mahasiswa
                            foreach($mataKuliahClasses as $tam) {
                            foreach($tam->kelas as $kelas) {
                            $studentInClass = $kelas->kelasMahasiswa->where('mahasiswaId', $mhs->id)->first();
                            if ($studentInClass) {
                            $kelasNama = $kelas->namaKelas;
                            $studentClassId = $tam->id;
                            break 2;
                            }
                            }
                            }

                            // Fallback: Jika tidak ada kelas, gunakan tahunAjaranMatkul->id
                            if (!$studentClassId) {
                            $studentClassId = $tahunAjaranMatkul->id;
                            $kelasNama = 'Default';
                            }

                            // Debug: Log studentClassId
                            \Log::info("Mahasiswa {$mhs->id} ({$mhs->nama}): studentClassId = {$studentClassId}, kelasNama = {$kelasNama}");
                            @endphp
                            <tr data-mahasiswa-id="{{ $mhs->id }}">
                                <td class="px-6 py-4 whitespace-nowrap">{{ $mhs->nim }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $mhs->nama }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Kelas {{ $kelasNama }}
                                    </span>
                                </td>
                                @foreach($allKomponen as $komponen)
                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    @php
                                    $existingNilai = $nilaiData->where('mahasiswaId', $mhs->id)
                                    ->filter(function($nilai) use ($komponen) {
                                    return $nilai->bobot && $nilai->bobot->komponenId == $komponen->id;
                                    })
                                    ->first();
                                    $nilaiValue = $existingNilai ? $existingNilai->nilai : '';
                                    @endphp
                                    <span class="nilai-plain" data-mahasiswa-id="{{ $mhs->id }}" data-komponen-id="{{ $komponen->id }}">{{ $nilaiValue !== '' ? $nilaiValue : '-' }}</span>
                                    <input type="number"
                                        name="nilai[{{ $mhs->id }}][{{ $komponen->id }}]"
                                        value="{{ $nilaiValue }}"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                        class="w-20 px-2 py-1 text-sm text-center border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 nilai-input hidden"
                                        data-mahasiswa-id="{{ $mhs->id }}"
                                        data-komponen-id="{{ $komponen->id }}"
                                        data-original-value="{{ $nilaiValue }}"
                                        placeholder="0">
                                </td>
                                @endforeach
                                <!-- Kolom Total Nilai -->
                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    @php
                                    // Ambil grade dan total nilai yang sudah dihitung dari kelas_mahasiswa
                                    $kelasMahasiswa = $mhs->kelasMahasiswa->where('tahunAjaranMatkulId', $tahunAjaranMatkul->id)->first();
                                    $totalNilai = $kelasMahasiswa ? $kelasMahasiswa->totalNilai : null;
                                    $grade = $kelasMahasiswa ? $kelasMahasiswa->grade : null;

                                    // Jika belum ada nilai yang tersimpan, hitung dari bobot dan nilai yang ada
                                    if ($totalNilai === null || $grade === null) {
                                    // Hitung total nilai langsung dari semua nilai yang sudah dikalikan bobot
                                    $allNilaiMahasiswa = $nilaiData->where('mahasiswaId', $mhs->id);
                                    $totalNilai = 0;

                                    foreach ($allNilaiMahasiswa as $nilai) {
                                    if ($nilai->bobot && $nilai->bobot->bobot > 0) {
                                    $totalNilai += ($nilai->nilai * $nilai->bobot->bobot / 100);
                                    }
                                    }

                                    // Hitung grade berdasarkan total nilai terbobot
                                    if ($totalNilai >= 80) $grade = 'A';
                                    elseif ($totalNilai >= 75) $grade = 'A-';
                                    elseif ($totalNilai >= 70) $grade = 'B+';
                                    elseif ($totalNilai >= 65) $grade = 'B';
                                    elseif ($totalNilai >= 60) $grade = 'B-';
                                    elseif ($totalNilai >= 55) $grade = 'C+';
                                    elseif ($totalNilai >= 50) $grade = 'C';
                                    elseif ($totalNilai >= 45) $grade = 'D';
                                    else $grade = 'E';
                                    }
                                    @endphp
                                    <span class="text-sm font-bold text-black">
                                        {{ $totalNilai !== null ? number_format($totalNilai, 2) : '-' }}
                                    </span>
                                </td>
                                <!-- Kolom Grade -->
                                <td class="px-4 py-4 whitespace-nowrap text-center grade-column">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $grade == 'A' || $grade == 'A-' ? 'bg-green-100 text-green-800' :
                                               ($grade == 'B+' || $grade == 'B' || $grade == 'B-' ? 'bg-blue-100 text-blue-800' :
                                               ($grade == 'C+' || $grade == 'C' ? 'bg-yellow-100 text-yellow-800' :
                                               ($grade == 'D' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800'))) }}">
                                        {{ $grade ?: '-' }}
                                    </span>
                                </td>
                                <!-- Kolom Aksi -->
                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <!-- Tombol Detail (selalu terlihat) -->
                                        <button type="button"
                                            class="btn-detail-nilai px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded transition-colors duration-200"
                                            data-mahasiswa-id="{{ $mhs->id }}"
                                            data-mahasiswa-nama="{{ $mhs->nama }}"
                                            data-nim="{{ $mhs->nim }}">
                                            <svg class="w-3 h-3 mr-1 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Detail
                                        </button>

                                        <!-- Tombol Simpan (hanya muncul saat mode edit) -->
                                        <div class="aksi-column hidden">
                                            <button type="button"
                                                id="save-btn-{{ $mhs->id }}"
                                                class="btn-simpan-nilai px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed"
                                                data-mahasiswa-id="{{ $mhs->id }}"
                                                disabled>
                                                <svg class="w-3 h-3 mr-1 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Simpan
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Input hidden untuk student class ID -->
                                    <input type="hidden" name="student_class_id[{{ $mhs->id }}]" value="{{ $studentClassId }}">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>

            <!-- Bulk Save Controls (Hidden by default) - Di atas pagination -->
            <div id="bulk-actions" class="mt-6 p-4 bg-gray-50 rounded-lg btn-simpan-semua hidden">
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

            <!-- Pagination -->
            @if($mahasiswaPaginated && $mahasiswaPaginated->hasPages())
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
    cancelText="Tetap di Halaman" />

<!-- Modal Import Excel -->
<div id="import-modal" tabindex="-1" aria-hidden="true" class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center min-h-screen w-full hidden transition-opacity duration-300 ease-out" style="background: rgba(0,0,0,0.6);">
    <div class="relative p-4 w-full max-w-md max-h-full transform transition-all duration-300 ease-out scale-95 opacity-0" data-modal-content>
        <div class="relative bg-white rounded-lg shadow-xl animate-in fade-in slide-in-from-top-4 duration-300">
            <div class="flex items-center justify-between p-4 md:p-5 border-b border-gray-200 rounded-t">
                <h3 class="text-lg font-semibold text-gray-900">
                    <svg class="w-5 h-5 mr-2 inline-block text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Import Nilai dari Excel
                </h3>
                <button type="button" onclick="hideImportModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition-colors duration-200">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <form action="{{ route('dosen.nilai.import', $tahunAjaranMatkul->id) }}" method="POST" enctype="multipart/form-data" class="p-4 md:p-5">
                @csrf
                <div class="mb-4">
                    <label for="excel_file" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih File Excel
                    </label>
                    <input type="file"
                        id="excel_file"
                        name="excel_file"
                        accept=".xlsx,.xls"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-yellow-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="text-sm text-yellow-700">
                            <p class="font-semibold mb-2">Petunjuk Import:</p>
                            <ul class="space-y-1 text-xs">
                                <li class="flex items-start">
                                    <span class="mr-2">•</span>
                                    <span>Download template Excel terlebih dahulu</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">•</span>
                                    <span>Isi semua kolom: NIM, Nama, Kelas, dan nilai komponen</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">•</span>
                                    <span>Template menyediakan 50 baris kosong untuk diisi</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">•</span>
                                    <span>Mahasiswa baru otomatis dibuatkan akun (password = NIM)</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">•</span>
                                    <span>Format file: .xlsx atau .xls</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" onclick="hideImportModal()" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 transition-colors duration-200">
                        Batal
                    </button>
                    <button type="submit" id="import-submit-btn" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Detail Nilai Modal -->
<div id="detail-modal" class="fixed inset-0 overflow-y-auto overflow-x-hidden justify-center items-center min-h-screen w-full z-50 hidden" style="background: rgba(0,0,0,0.6); display: none;">
    <div class="relative p-4 w-full max-w-6xl max-h-full transform transition-all duration-300 ease-out modal-content scale-95 opacity-0">
        <div class="relative bg-white rounded-lg shadow-xl">
            <div class="flex items-center justify-between p-4 md:p-5 border-b border-gray-200 rounded-t">
                <h3 class="text-lg font-semibold text-gray-900" id="detail-modal-title">Detail Nilai Mahasiswa</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition-colors duration-200" data-modal-hide="detail-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <div class="p-4 md:p-5 overflow-y-auto max-h-[70vh]">
                <div id="detail-content-placeholder" class="space-y-4">
                    <div class="flex justify-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notifikasi Import Results -->
<!-- Reset Confirmation Modal -->
<x-confirm-modal
    id="reset-confirm-modal"
    title="Konfirmasi Reset Nilai"
    message="Apakah Anda yakin ingin menghapus SEMUA nilai dari database? Tindakan ini tidak dapat dibatalkan."
    action="{{ route('dosen.nilai.reset', $tahunAjaranMatkul->id) }}"
    method="DELETE"
    confirmText="Ya, Hapus Semua"
    cancelText="Batal"
    type="danger" />

@if(session('created_students'))
<!-- Created Students Modal -->
<div id="created-students-modal" tabindex="-1" aria-hidden="true" class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center min-h-screen w-full flex transition-opacity duration-300 ease-out" style="background: rgba(0,0,0,0.6);">
    <div class="relative p-4 w-full max-w-4xl max-h-full transform transition-all duration-300 ease-out scale-100 opacity-100">
        <div class="relative bg-white rounded-lg shadow-xl animate-in fade-in slide-in-from-top-4 duration-300">
            <div class="flex items-center justify-between p-4 md:p-5 border-b border-gray-200 rounded-t">
                <h3 class="text-lg font-semibold text-green-900">
                    <svg class="w-5 h-5 mr-2 inline-block text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                    </svg>
                    Akun Mahasiswa Baru Dibuat
                </h3>
                <button type="button" onclick="hideCreatedStudentsModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition-colors duration-200">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <div class="p-4 md:p-5">
                <p class="text-sm text-gray-600 mb-4">
                    Sistem telah membuat {{ count(session('created_students')) }} akun mahasiswa baru dengan password default = NIM:
                </p>

                <div class="overflow-x-auto max-h-64 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">NIM</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Password</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach(session('created_students') as $student)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-900">{{ $student['nim'] }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900">{{ $student['nama'] }}</td>
                                <td class="px-4 py-2 text-xs text-gray-900">{{ $student['email'] }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900 font-mono">{{ $student['password'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 p-4 md:p-5 border-t border-gray-200">
                <button type="button" onclick="hideCreatedStudentsModal()" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endif

@if(session('import_errors'))
<!-- Import Errors Modal -->
<div id="import-errors-modal" tabindex="-1" aria-hidden="true" class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center min-h-screen w-full flex transition-opacity duration-300 ease-out" style="background: rgba(0,0,0,0.6);">
    <div class="relative p-4 w-full max-w-2xl max-h-full transform transition-all duration-300 ease-out scale-100 opacity-100">
        <div class="relative bg-white rounded-lg shadow-xl animate-in fade-in slide-in-from-top-4 duration-300">
            <div class="flex items-center justify-between p-4 md:p-5 border-b border-gray-200 rounded-t">
                <h3 class="text-lg font-semibold text-red-900">
                    <svg class="w-5 h-5 mr-2 inline-block text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Error Import
                </h3>
                <button type="button" onclick="hideImportErrorsModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition-colors duration-200">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <div class="p-4 md:p-5">
                <div class="mb-4 p-3 bg-red-50 rounded-lg">
                    <p class="text-sm text-red-800 font-medium">
                        <svg class="w-4 h-4 mr-2 inline-block" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        Import gagal! Perbaiki error berikut sebelum mencoba lagi:
                    </p>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-lg p-4 max-h-64 overflow-y-auto">
                    <ul class="text-sm text-red-700 space-y-2">
                        @foreach(session('import_errors') as $error)
                        <li class="flex items-start p-2 bg-red-50 rounded border-l-4 border-red-400">
                            <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-medium">{{ $error['error'] }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <svg class="w-4 h-4 mr-2 inline-block" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <strong>Tips:</strong> Pastikan format Excel sesuai dengan template yang disediakan. Periksa nama kelas yang tersedia di sistem.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 p-4 md:p-5 border-t border-gray-200">
                <button type="button" onclick="hideImportErrorsModal()" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Validation Modal -->
<x-confirm-modal
    id="validation-modal"
    title="Validasi Diperlukan"
    message=""
    action="{{ route('dosen.cpmk.show', $tahunAjaranMatkul->id) }}"
    method="GET"
    confirmText="Atur CPMK & Bobot"
    cancelText="Tutup"
    type="warning" />

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Import Nilai dari Excel</h3>
                <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Import Form -->
            <div id="importForm">
                <form action="{{ route('dosen.nilai.import', $tahunAjaranMatkul->id) }}" method="POST" enctype="multipart/form-data" id="excelImportForm">
                    @csrf
                    <div class="mb-4">
                        <label for="excel_file" class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih File Excel
                        </label>
                        <input type="file" name="excel_file" id="excel_file" accept=".xlsx,.xls"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <p class="text-xs text-gray-500 mt-1">Format yang didukung: .xlsx, .xls (Maksimal 10MB)</p>
                    </div>

                    <div class="mb-4">
                        <a href="{{ route('dosen.nilai.export-template', $tahunAjaranMatkul->id) }}"
                            class="text-blue-600 hover:text-blue-800 text-sm underline">
                            Download Template Excel
                        </a>
                    </div>

                    <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                        <h4 class="text-sm font-medium text-blue-900 mb-2">Kelas yang Tersedia:</h4>
                        <div class="text-sm text-blue-800">
                            @php
                            $availableClasses = [];
                            foreach($tahunAjaranMatkul->kelas as $kelas) {
                            $availableClasses[] = $kelas->namaKelas;
                            }
                            @endphp
                            {{ implode(', ', $availableClasses) }}
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeImportModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                            Batal
                        </button>
                        <button type="submit" id="importSubmitBtn"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Import
                        </button>
                    </div>
                </form>
            </div>

            <!-- Loading State -->
            <div id="importLoading" class="hidden">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">Sedang Memproses Import...</h4>
                    <p class="text-sm text-gray-600 mb-4">Mohon tunggu, jangan tutup halaman ini</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
        resetImportModal();
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
        resetImportModal();
    }

    function resetImportModal() {
        document.getElementById('importForm').classList.remove('hidden');
        document.getElementById('importLoading').classList.add('hidden');
        document.getElementById('excelImportForm').reset();
    }

    // Handle form submission - direct submit without AJAX
    document.getElementById('excelImportForm').addEventListener('submit', function(e) {
        // Get submit button
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;

        // Show loading state on button
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Memproses Import...
    `;

        // Show loading state
        document.getElementById('importForm').classList.add('hidden');
        document.getElementById('importLoading').classList.remove('hidden');

        // Let form submit normally - no preventDefault
        // The page will reload after submission with flash message
    });

    // Functions removed - now using direct form submit with flash messages
</script>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Flowbite modals
        if (typeof Flowbite !== 'undefined') {
            // Force re-initialization of all modals
            const modals = document.querySelectorAll('[data-modal-toggle]');
            modals.forEach(modal => {
                if (modal.id) {
                    console.log('Initializing modal:', modal.id);
                }
            });
        }

        // Detail modal functionality
        function showDetailModal(mahasiswaId, nim, nama) {
            const modal = document.getElementById('detail-modal');
            const modalTitle = document.getElementById('detail-modal-title');

            if (modal && modalTitle) {
                modalTitle.textContent = `Detail Nilai Mahasiswa`;

                // Load detail data
                loadDetailData(mahasiswaId);

                // Show modal
                modal.classList.remove('hidden');
                modal.style.display = 'flex';

                // Add animation classes
                const modalContent = modal.querySelector('.modal-content');
                if (modalContent) {
                    modalContent.classList.add('scale-100', 'opacity-100');
                    modalContent.classList.remove('scale-95', 'opacity-0');
                }
            } else {
                console.error('Modal elements not found');
            }
        }

        function hideDetailModal() {
            const modal = document.getElementById('detail-modal');
            if (modal) {
                const modalContent = modal.querySelector('.modal-content');
                if (modalContent) {
                    modalContent.classList.remove('scale-100', 'opacity-100');
                    modalContent.classList.add('scale-95', 'opacity-0');
                }

                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.style.display = 'none';
                }, 300);
            }
        }

        function loadDetailData(mahasiswaId) {
            // Gunakan URL yang aman untuk HTTPS
            const baseUrl = window.location.protocol + '//' + window.location.host;
            const url = `${baseUrl}/dosen/nilai/{{ $tahunAjaranMatkul->id }}/detail?mahasiswa_id=${mahasiswaId}`;

            console.log('Loading detail for mahasiswa:', mahasiswaId, 'URL:', url);

            // Show loading state
            const contentDiv = document.getElementById('detail-content-placeholder');
            if (contentDiv) {
                contentDiv.innerHTML = `
                <div class="flex justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                </div>
            `;
            }

            fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        contentDiv.innerHTML = data.html;
                    } else {
                        contentDiv.innerHTML = '<p class="text-red-600">Gagal memuat detail nilai: ' + (data.message || 'Unknown error') + '</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading detail:', error);
                    contentDiv.innerHTML = '<p class="text-red-600">Terjadi kesalahan saat memuat data: ' + error.message + '</p>';
                });
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('detail-modal');
            if (e.target === modal) {
                hideDetailModal();
            }
        });

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('detail-modal');
            if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                hideDetailModal();
            }
        });

        // Close modal on close button click
        document.addEventListener('click', function(e) {
            if (e.target.closest('[data-modal-hide="detail-modal"]')) {
                hideDetailModal();
            }
        });

        // Make functions globally available
        window.showDetailModal = showDetailModal;
        window.hideDetailModal = hideDetailModal;

        // Add event listener for detail buttons
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-detail-nilai')) {
                e.preventDefault();
                const button = e.target.closest('.btn-detail-nilai');
                const mahasiswaId = button.getAttribute('data-mahasiswa-id');
                const mahasiswaNama = button.getAttribute('data-mahasiswa-nama');
                const nim = button.getAttribute('data-nim');

                console.log('Detail button clicked:', {
                    mahasiswaId,
                    mahasiswaNama,
                    nim
                });

                // Show detail modal
                showDetailModal(mahasiswaId, nim, mahasiswaNama);
            }
        });

        // Tab functionality
        document.addEventListener('click', function(e) {
            if (e.target.closest('.tab-button')) {
                e.preventDefault();
                const button = e.target.closest('.tab-button');
                const tabName = button.getAttribute('data-tab');

                console.log('Tab clicked:', tabName);

                // Update URL with tab parameter
                const url = new URL(window.location);
                if (tabName === 'all') {
                    url.searchParams.delete('tab');
                } else {
                    url.searchParams.set('tab', tabName);
                }

                // Navigate to new URL
                window.location.href = url.toString();
            }
        });

        // Import Modal Functions
        function showImportModal() {
            const modal = document.getElementById('import-modal');
            const modalContent = modal.querySelector('[data-modal-content]');

            if (modal && modalContent) {
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
        }

        function hideImportModal() {
            const modal = document.getElementById('import-modal');
            const modalContent = modal.querySelector('[data-modal-content]');

            if (modal && modalContent) {
                modalContent.classList.add('scale-95', 'opacity-0');
                modalContent.classList.remove('scale-100', 'opacity-100');
                modal.classList.remove('bg-opacity-10');
                modal.classList.add('bg-opacity-0');

                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }
        }

        // Toggle Edit Nilai Functionality
        const toggleEditBtn = document.getElementById('toggle-edit-nilai');
        const resetBtn = document.getElementById('reset-nilai');
        const nilaiInputs = document.querySelectorAll('.nilai-input');
        const nilaiPlains = document.querySelectorAll('.nilai-plain');
        const aksiColumns = document.querySelectorAll('.aksi-column');
        const gradeColumns = document.querySelectorAll('.grade-column');
        const bulkActions = document.getElementById('bulk-actions');
        const bulkSaveBtn = document.getElementById('bulk-save-btn');
        const confirmBulkSave = document.getElementById('confirm-bulk-save');

        // Function to enter edit mode
        function enterEditMode() {
            if (toggleEditBtn) {
                // Switch to edit mode
                toggleEditBtn.innerHTML = `
                <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Nonaktifkan Input Nilai
            `;
                toggleEditBtn.classList.remove('bg-amber-600', 'hover:bg-amber-700');
                toggleEditBtn.classList.add('bg-red-600', 'hover:bg-red-700');

                // Show input fields and hide plain text
                nilaiInputs.forEach(input => input.classList.remove('hidden'));
                nilaiPlains.forEach(plain => plain.classList.add('hidden'));
                // Show aksi columns
                aksiColumns.forEach(col => col.classList.remove('hidden'));
                gradeColumns.forEach(col => col.classList.add('hidden'));

                // Show bulk actions
                if (bulkActions) {
                    bulkActions.classList.remove('hidden');
                }

                // Show reset button
                if (resetBtn) {
                    resetBtn.classList.remove('hidden');
                }

                // Save edit mode state to localStorage
                localStorage.setItem('nilaiEditMode', 'true');

                // Update bulk save button text
                updateBulkSaveButtonText();
            }
        }

        // Function to restore edit mode state from localStorage
        function restoreEditModeState() {
            const isEditMode = localStorage.getItem('nilaiEditMode') === 'true';
            if (isEditMode) {
                enterEditMode();
                // Also restore changed values
                restoreChangedValues();
                // Update bulk save button text
                updateBulkSaveButtonText();
            }
        }

        // Function to save changed values to localStorage
        function saveChangedValuesToStorage(mahasiswaId, komponenId, value) {
            const key = `nilai_changes_${mahasiswaId}_${komponenId}`;
            localStorage.setItem(key, value);
        }

        // Function to remove changed value from localStorage
        function removeChangedValueFromStorage(mahasiswaId, komponenId) {
            const key = `nilai_changes_${mahasiswaId}_${komponenId}`;
            localStorage.removeItem(key);
        }

        // Function to restore changed values from localStorage
        function restoreChangedValues() {
            // Get all nilai inputs
            const allNilaiInputs = document.querySelectorAll('.nilai-input');

            allNilaiInputs.forEach(input => {
                const mahasiswaId = input.getAttribute('data-mahasiswa-id');
                const komponenId = input.getAttribute('data-komponen-id');
                const key = `nilai_changes_${mahasiswaId}_${komponenId}`;
                const savedValue = localStorage.getItem(key);

                if (savedValue !== null) {
                    input.value = savedValue;
                    // Trigger change event to update save button state
                    input.dispatchEvent(new Event('input'));
                }
            });

            // Update bulk save button text after restoring values
            updateBulkSaveButtonText();
        }

        // Function to clear changed values from localStorage for a specific mahasiswa
        function clearChangedValuesFromStorage(mahasiswaId) {
            // Get all keys for this mahasiswa
            const keys = Object.keys(localStorage);
            keys.forEach(key => {
                if (key.startsWith(`nilai_changes_${mahasiswaId}_`)) {
                    localStorage.removeItem(key);
                }
            });

            // Also clear student class ID for this mahasiswa
            localStorage.removeItem(`student_class_id_${mahasiswaId}`);
        }

        // Function to clear all edit mode data from localStorage
        function clearAllEditModeData() {
            const keys = Object.keys(localStorage);
            keys.forEach(key => {
                if (key.startsWith('nilai_changes_') ||
                    key.startsWith('student_class_id_') ||
                    key === 'nilaiEditMode') {
                    localStorage.removeItem(key);
                }
            });
        }

        // Function to collect all changed values from localStorage across all pages
        function collectAllChangedValuesFromStorage() {
            const allChangedValues = {};
            const keys = Object.keys(localStorage);

            keys.forEach(key => {
                if (key.startsWith('nilai_changes_')) {
                    // Extract mahasiswaId and komponenId from key
                    const parts = key.replace('nilai_changes_', '').split('_');
                    if (parts.length === 2) {
                        const mahasiswaId = parts[0];
                        const komponenId = parts[1];
                        const value = localStorage.getItem(key);

                        if (!allChangedValues[mahasiswaId]) {
                            allChangedValues[mahasiswaId] = {};
                        }
                        allChangedValues[mahasiswaId][komponenId] = value;
                    }
                }
            });

            console.log('All changed values from localStorage:', allChangedValues);
            return allChangedValues;
        }

        // Function to update bulk save button text with count
        function updateBulkSaveButtonText() {
            const allChangedValues = collectAllChangedValuesFromStorage();
            const totalChanges = Object.keys(allChangedValues).length;

            if (bulkSaveBtn) {
                if (totalChanges > 0) {
                    bulkSaveBtn.innerHTML = `
                    <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Semua Nilai (${totalChanges} perubahan)
                `;
                } else {
                    bulkSaveBtn.innerHTML = `
                    <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Semua Nilai
                `;
                }
            }
        }

        // Function to update nilai display (total nilai and grade)
        function updateNilaiDisplay(mahasiswaId) {
            // Get all nilai inputs for this mahasiswa
            const nilaiInputs = document.querySelectorAll(`input[data-mahasiswa-id="${mahasiswaId}"]`);
            let totalNilai = 0;
            let validNilaiCount = 0;

            // Calculate total nilai
            nilaiInputs.forEach(input => {
                const nilai = parseFloat(input.value);
                if (!isNaN(nilai) && nilai >= 0) {
                    totalNilai += nilai;
                    validNilaiCount++;
                }
            });

            // Calculate average if there are valid nilai
            const averageNilai = validNilaiCount > 0 ? totalNilai / validNilaiCount : 0;

            // Update total nilai display
            const totalNilaiCell = document.querySelector(`tr[data-mahasiswa-id="${mahasiswaId}"] td:nth-child(7)`); // Adjust index based on your table structure
            if (totalNilaiCell) {
                const totalNilaiSpan = totalNilaiCell.querySelector('span');
                if (totalNilaiSpan) {
                    totalNilaiSpan.textContent = averageNilai > 0 ? averageNilai.toFixed(2) : '-';
                }
            }

            // Calculate and update grade
            let grade = '-';
            if (averageNilai > 0) {
                if (averageNilai >= 80) grade = 'A';
                else if (averageNilai >= 75) grade = 'A-';
                else if (averageNilai >= 70) grade = 'B+';
                else if (averageNilai >= 65) grade = 'B';
                else if (averageNilai >= 60) grade = 'B-';
                else if (averageNilai >= 55) grade = 'C+';
                else if (averageNilai >= 50) grade = 'C';
                else if (averageNilai >= 45) grade = 'D';
                else grade = 'E';
            }

            // Update grade display
            const gradeCell = document.querySelector(`tr[data-mahasiswa-id="${mahasiswaId}"] td:nth-child(8)`); // Adjust index based on your table structure
            if (gradeCell) {
                const gradeSpan = gradeCell.querySelector('span');
                if (gradeSpan) {
                    // Update grade text
                    gradeSpan.textContent = grade;

                    // Update grade color classes
                    gradeSpan.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';
                    if (grade === 'A' || grade === 'A-') {
                        gradeSpan.classList.add('bg-green-100', 'text-green-800');
                    } else if (grade === 'B+' || grade === 'B' || grade === 'B-') {
                        gradeSpan.classList.add('bg-blue-100', 'text-blue-800');
                    } else if (grade === 'C+' || grade === 'C') {
                        gradeSpan.classList.add('bg-yellow-100', 'text-yellow-800');
                    } else if (grade === 'D') {
                        gradeSpan.classList.add('bg-orange-100', 'text-orange-800');
                    } else if (grade !== '-') {
                        gradeSpan.classList.add('bg-red-100', 'text-red-800');
                    }
                }
            }
        }

        // Function to exit edit mode
        function exitEditMode() {
            if (toggleEditBtn) {
                // Switch back to view mode
                toggleEditBtn.innerHTML = `
                <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6 6M3 17v2a2 2 0 002 2h2m14-6v6a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h6"></path>
                </svg>
                Aktifkan Input Nilai
            `;
                toggleEditBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
                toggleEditBtn.classList.add('bg-amber-600', 'hover:bg-amber-700');

                // Hide input fields and show plain text
                nilaiInputs.forEach(input => input.classList.add('hidden'));
                nilaiPlains.forEach(plain => plain.classList.remove('hidden'));
                aksiColumns.forEach(col => col.classList.add('hidden'));
                gradeColumns.forEach(col => col.classList.remove('hidden'));

                // Hide bulk actions
                if (bulkActions) {
                    bulkActions.classList.add('hidden');
                }

                // Hide reset button
                if (resetBtn) {
                    resetBtn.classList.add('hidden');
                }

                // Reset bulk save checkbox
                if (confirmBulkSave) {
                    confirmBulkSave.checked = false;
                }

                // Disable bulk save button
                if (bulkSaveBtn) {
                    bulkSaveBtn.disabled = true;
                    bulkSaveBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
                    bulkSaveBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                }

                // Save edit mode state to localStorage
                localStorage.setItem('nilaiEditMode', 'false');
            }
        }

        if (toggleEditBtn) {
            toggleEditBtn.addEventListener('click', function() {
                const isEditMode = this.textContent.includes('Aktifkan');

                if (isEditMode) {
                    // Switch to edit mode
                    enterEditMode();
                } else {
                    // Switch back to view mode using the function
                    exitEditMode();
                }
            });
        }

        // Individual nilai input change tracking
        nilaiInputs.forEach(input => {
            input.addEventListener('input', function() {
                const mahasiswaId = this.getAttribute('data-mahasiswa-id');
                const komponenId = this.getAttribute('data-komponen-id');
                const originalValue = this.getAttribute('data-original-value');
                const currentValue = this.value;
                const saveBtn = document.getElementById(`save-btn-${mahasiswaId}`);

                // Check if value has changed
                const hasChanged = currentValue !== originalValue;

                if (saveBtn) {
                    saveBtn.disabled = !hasChanged;
                    if (hasChanged) {
                        saveBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                        saveBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    } else {
                        saveBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
                        saveBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                    }
                }

                // Save changed values to localStorage for pagination persistence
                if (hasChanged) {
                    saveChangedValuesToStorage(mahasiswaId, komponenId, currentValue);

                    // Also save student class ID for this mahasiswa
                    const studentClassElement = document.querySelector(`input[name="student_class_id[${mahasiswaId}]"]`);
                    if (studentClassElement) {
                        localStorage.setItem(`student_class_id_${mahasiswaId}`, studentClassElement.value);
                    }
                } else {
                    removeChangedValueFromStorage(mahasiswaId, komponenId);
                }

                // Update bulk save button text if bulk actions are visible
                if (bulkActions && !bulkActions.classList.contains('hidden')) {
                    updateBulkSaveButtonText();
                }
            });
        });

        // Individual save button functionality
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-simpan-nilai')) {
                e.preventDefault();
                const button = e.target.closest('.btn-simpan-nilai');
                const mahasiswaId = button.getAttribute('data-mahasiswa-id');

                // Validate mahasiswaId
                if (!mahasiswaId || mahasiswaId === 'null' || mahasiswaId === 'undefined') {
                    console.error('Invalid mahasiswaId:', mahasiswaId);
                    if (typeof showToast === 'function') {
                        showToast('Error: ID mahasiswa tidak valid', 'error');
                    } else {
                        alert('Error: ID mahasiswa tidak valid');
                    }
                    return;
                }

                console.log('Processing save for mahasiswa ID:', mahasiswaId);

                // Get all nilai inputs for this mahasiswa
                const nilaiInputs = document.querySelectorAll(`input[data-mahasiswa-id="${mahasiswaId}"]`);
                const nilaiData = {};

                console.log(`Found ${nilaiInputs.length} nilai inputs for mahasiswa ${mahasiswaId}`);

                nilaiInputs.forEach((input, index) => {
                    const komponenId = input.getAttribute('data-komponen-id');
                    const nilai = input.value;
                    console.log(`Input ${index}: komponenId=${komponenId}, nilai=${nilai}`);

                    if (nilai !== '' && nilai !== null && nilai !== undefined) {
                        nilaiData[komponenId] = parseFloat(nilai);
                    }
                });

                console.log('Final nilaiData:', nilaiData);

                // Send AJAX request to save nilai
                const formData = new FormData();
                // Get CSRF token with null check
                const csrfElement = document.querySelector('meta[name="csrf-token"]');
                if (!csrfElement) {
                    console.error('CSRF token not found');
                    if (typeof showToast === 'function') {
                        showToast('Error: Token keamanan tidak ditemukan', 'error');
                    }
                    return;
                }
                formData.append('_token', csrfElement.getAttribute('content'));

                // Add nilai data in the expected format
                console.log(`Nilai data for mahasiswa ${mahasiswaId}:`, nilaiData);

                // Check if there are any valid nilai to save
                if (Object.keys(nilaiData).length === 0) {
                    console.warn('No valid nilai data to save');
                    if (typeof showToast === 'function') {
                        showToast('Tidak ada nilai yang valid untuk disimpan', 'warning');
                    } else {
                        alert('Tidak ada nilai yang valid untuk disimpan');
                    }
                    return;
                }

                Object.keys(nilaiData).forEach(komponenId => {
                    formData.append(`nilai[${mahasiswaId}][${komponenId}]`, nilaiData[komponenId]);
                });

                // Add student class ID with null check
                const studentClassElement = document.querySelector(`input[name="student_class_id[${mahasiswaId}]"]`);
                console.log(`Looking for student_class_id[${mahasiswaId}] element:`, studentClassElement);

                if (studentClassElement) {
                    const studentClassId = studentClassElement.value;
                    console.log(`Found student class ID for mahasiswa ${mahasiswaId}:`, studentClassId);

                    // Check if studentClassId is empty or null
                    if (!studentClassId || studentClassId.trim() === '') {
                        console.error(`Student class ID is empty for mahasiswa ${mahasiswaId}`);
                        if (typeof showToast === 'function') {
                            showToast('Error: Data mahasiswa tidak lengkap - Student class ID kosong', 'error');
                        } else {
                            alert('Error: Data mahasiswa tidak lengkap - Student class ID kosong');
                        }
                        return;
                    }

                    formData.append(`student_class_id[${mahasiswaId}]`, studentClassId);
                } else {
                    console.error(`Student class ID element not found for mahasiswa ${mahasiswaId}`);
                    console.error('Available student_class_id elements:', document.querySelectorAll('input[name^="student_class_id"]'));
                    console.error('All hidden inputs:', document.querySelectorAll('input[type="hidden"]'));

                    // Debug: Check if there are any elements with similar names
                    const similarElements = document.querySelectorAll(`input[name*="${mahasiswaId}"]`);
                    console.error('Elements with similar names:', similarElements);

                    if (typeof showToast === 'function') {
                        showToast('Error: Data mahasiswa tidak lengkap - Student class ID tidak ditemukan', 'error');
                    } else {
                        alert('Error: Data mahasiswa tidak lengkap - Student class ID tidak ditemukan');
                    }
                    return;
                }

                // Debug: Log formData contents
                console.log('FormData contents:');
                for (let [key, value] of formData.entries()) {
                    console.log(`${key}: ${value}`);
                }

                fetch(`/dosen/nilai/{{ $tahunAjaranMatkul->id }}/individual-store`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfElement.getAttribute('content')
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update original values and disable save button
                            nilaiInputs.forEach(input => {
                                input.setAttribute('data-original-value', input.value);
                            });
                            button.disabled = true;
                            button.classList.add('bg-gray-400', 'cursor-not-allowed');
                            button.classList.remove('bg-blue-600', 'hover:bg-blue-700');

                            // Update nilai plain text display to show new values
                            nilaiInputs.forEach(input => {
                                const mahasiswaId = input.getAttribute('data-mahasiswa-id');
                                const komponenId = input.getAttribute('data-komponen-id');
                                const nilaiValue = input.value;

                                // Find corresponding nilai plain text
                                const nilaiPlain = document.querySelector(`.nilai-plain[data-mahasiswa-id="${mahasiswaId}"][data-komponen-id="${komponenId}"]`);
                                if (nilaiPlain) {
                                    nilaiPlain.textContent = nilaiValue !== '' ? nilaiValue : '-';
                                }
                            });

                            // Update total nilai and grade display
                            updateNilaiDisplay(mahasiswaId);

                            // Clear changed values from localStorage after successful save
                            clearChangedValuesFromStorage(mahasiswaId);

                            // Show success message
                            if (typeof showToast === 'function') {
                                showToast('Nilai berhasil disimpan', 'success');
                            }

                            // Auto-exit from edit mode after successful save
                            setTimeout(() => {
                                exitEditMode();
                            }, 1000); // Delay 1 detik agar user bisa lihat pesan sukses
                        } else {
                            if (typeof showToast === 'function') {
                                showToast('Gagal menyimpan nilai: ' + data.message, 'error');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error saving nilai:', error);
                        if (typeof showToast === 'function') {
                            showToast('Terjadi kesalahan saat menyimpan nilai', 'error');
                        }
                    });
            }
        });

        // Bulk save functionality
        if (confirmBulkSave) {
            confirmBulkSave.addEventListener('change', function() {
                if (bulkSaveBtn) {
                    bulkSaveBtn.disabled = !this.checked;
                    if (this.checked) {
                        bulkSaveBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                        bulkSaveBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                    } else {
                        bulkSaveBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
                        bulkSaveBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                    }
                }
            });
        }

        if (bulkSaveBtn) {
            bulkSaveBtn.addEventListener('click', function() {
                if (confirmBulkSave && confirmBulkSave.checked) {
                    // Collect all changed values from localStorage across all pages
                    const allChangedValues = collectAllChangedValuesFromStorage();

                    if (Object.keys(allChangedValues).length === 0) {
                        if (typeof showToast === 'function') {
                            showToast('Tidak ada nilai yang diubah untuk disimpan', 'warning');
                        } else {
                            alert('Tidak ada nilai yang diubah untuk disimpan');
                        }
                        return;
                    }

                    // Proceed directly since checkbox validation is already in place
                    // Add all changed values to the existing form
                    const bulkForm = document.getElementById('bulk-nilai-form');
                    if (bulkForm) {
                        // Remove existing nilai inputs
                        const existingNilaiInputs = bulkForm.querySelectorAll('input[name^="nilai["]');
                        existingNilaiInputs.forEach(input => input.remove());

                        // Remove existing student_class_id inputs
                        const existingClassInputs = bulkForm.querySelectorAll('input[name^="student_class_id["]');
                        existingClassInputs.forEach(input => input.remove());

                        // Add all changed values to form
                        Object.keys(allChangedValues).forEach(mahasiswaId => {
                            Object.keys(allChangedValues[mahasiswaId]).forEach(komponenId => {
                                const value = allChangedValues[mahasiswaId][komponenId];
                                const nilaiInput = document.createElement('input');
                                nilaiInput.type = 'hidden';
                                nilaiInput.name = `nilai[${mahasiswaId}][${komponenId}]`;
                                nilaiInput.value = value;
                                bulkForm.appendChild(nilaiInput);
                            });

                            // Add student class ID
                            let studentClassId = localStorage.getItem(`student_class_id_${mahasiswaId}`);
                            if (!studentClassId) {
                                studentClassId = '{{ $tahunAjaranMatkul->id }}';
                            }
                            const classInput = document.createElement('input');
                            classInput.type = 'hidden';
                            classInput.name = `student_class_id[${mahasiswaId}]`;
                            classInput.value = studentClassId;
                            bulkForm.appendChild(classInput);
                        });

                        // Clear all edit mode data from localStorage before submitting
                        clearAllEditModeData();

                        // Submit the form
                        bulkForm.submit();
                    }
                }
            });
        }

        // Handle import form submission for new modal
        const newImportForm = document.querySelector('#import-modal form');
        const progressContainer = document.getElementById('import-progress-container');
        const progressBar = document.getElementById('import-progress-bar');
        const progressText = document.getElementById('import-progress-text');
        const progressDetail = document.getElementById('import-progress-detail');

        if (newImportForm) {
            newImportForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = this.querySelector('#import-submit-btn');
                const originalBtnText = submitBtn.innerHTML;

                // Show loading state on button
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Mengunggah...
            `;

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (response.redirected) {
                        console.error('[Import] Request was REDIRECTED to:', response.url);
                    }
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        return response.text().then(text => {
                            throw new Error('Server mengembalikan HTML, bukan JSON. Kemungkinan redirect ke login.');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success' && data.job_id) {
                        hideImportModal();
                        progressContainer.classList.remove('hidden');
                        // Scroll up to progress bar
                        progressContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        pollImportStatus(data.job_id);
                    } else {
                        alert(data.message || data.error || 'Terjadi kesalahan saat memulai import.');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }
                })
                .catch(error => {
                    alert('Error: ' + error.message);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                });
            });
        }

        function pollImportStatus(jobId) {
            let pollCount = 0;
            const statusUrl = `{{ route('dosen.nilai.import-status', $tahunAjaranMatkul->id) }}?job_id=${jobId}`;
            
            const interval = setInterval(() => {
                pollCount++;
                
                fetch(statusUrl, {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => {
                    const contentType = res.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        return res.text().then(text => {
                            throw new Error('Polling response bukan JSON');
                        });
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.error) {
                        clearInterval(interval);
                        progressDetail.innerText = 'Error: ' + data.error;
                        return;
                    }
                    
                    if (data.finished) {
                        clearInterval(interval);
                        progressBar.style.width = '100%';
                        progressText.innerText = '100%';
                        progressBar.classList.replace('bg-blue-600', 'bg-green-600');
                        progressText.classList.replace('text-blue-700', 'text-green-700');
                        progressDetail.classList.replace('text-gray-500', 'text-green-600');
                        progressDetail.innerText = "Selesai! Memuat ulang halaman...";
                        
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        const pct = data.percentage || 0;
                        progressBar.style.width = `${pct}%`;
                        progressText.innerText = `${pct}%`;
                        
                        if (data.total > 0) {
                            progressDetail.innerText = `Memproses baris ke-${data.processed} dari ${data.total}...`;
                        }
                    }
                    
                    // Safety: stop polling after 300 attempts (7.5 minutes)
                    if (pollCount >= 300) {
                        clearInterval(interval);
                        progressDetail.innerText = 'Polling dihentikan. Silakan reload halaman manual.';
                    }
                })
                .catch(err => {
                    // Don't stop polling on error, but show it
                    progressDetail.innerText = 'Error polling: ' + err.message;
                });
            }, 1500);
        }

        // Restore edit mode state from localStorage
        restoreEditModeState();

        // Update bulk save button text on page load
        updateBulkSaveButtonText();

        // Validation functions
        function checkImportValidation() {
            console.log('checkImportValidation called');
            const validation = @json($cpmkValidation);
            console.log('Validation data:', validation);
            const errors = [];

            if (!validation.hasCpmk) {
                errors.push('• Belum ada CPMK yang diatur untuk mata kuliah ini');
            }

            if (validation.hasCpmk && validation.totalBobotCpmk !== 100) {
                errors.push(`• Total bobot CPMK belum 100% (saat ini: ${validation.totalBobotCpmk}%)`);
            }

            if (!validation.isBobotValid) {
                errors.push('• Belum ada bobot komponen penilaian yang diatur');
            }

            console.log('Errors found:', errors);
            console.log('Can import:', validation.canImport);

            if (validation.canImport) {
                // Jika validasi berhasil, buka modal import
                console.log('Opening import modal');
                showImportModal();
            } else {
                // Jika validasi gagal, tampilkan modal validasi
                console.log('Opening validation modal');
                showValidationModal(errors);
            }
        }

        // Tampilkan modal validasi OTOMATIS saat halaman dibuka jika tidak valid
        (function autoShowValidationOnLoad() {
            const validation = @json($cpmkValidation);
            if (!validation) return;
            if (validation.canImport) return; // valid, tidak perlu tampilkan modal

            const errors = [];
            if (!validation.hasCpmk) {
                errors.push('• Belum ada CPMK yang diatur untuk mata kuliah ini');
            }
            if (validation.hasCpmk && validation.totalBobotCpmk !== 100) {
                errors.push(`• Total bobot CPMK belum 100% (saat ini: ${validation.totalBobotCpmk}%)`);
            }
            if (!validation.isBobotValid) {
                errors.push('• Belum ada bobot komponen penilaian yang diatur');
            }

            // Tampilkan modal konfirmasi validasi
            showValidationModal(errors);
        })();

        function showValidationModal(errors) {
            console.log('showValidationModal called with errors:', errors);
            const modal = document.getElementById('validation-modal');
            const modalMessage = modal.querySelector('p');
            const modalForm = modal.querySelector('form');
            const modalContent = modal.querySelector('[data-modal-content]');

            console.log('Modal elements found:', {
                modal,
                modalMessage,
                modalForm,
                modalContent
            });

            if (modal && modalMessage && modalForm) {
                // Create detailed message with errors and steps
                const errorList = errors.map(error => `• ${error}`).join('\n');
                const steps = [
                    '1. Atur CPMK untuk mata kuliah ini',
                    '2. Pastikan total bobot CPMK = 100%',
                    '3. Atur bobot komponen penilaian'
                ].join('\n');

                modalMessage.innerHTML = `
                <div class="mb-4">
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
                        <div class="flex">
                            <svg class="h-5 w-5 text-amber-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="text-sm text-amber-700">
                                <p class="font-semibold mb-2">Tidak dapat melakukan import nilai karena:</p>
                                <ul class="space-y-1 text-xs" style="white-space: pre-line;">${errorList}</ul>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <svg class="h-5 w-5 text-blue-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="text-sm text-blue-700">
                                <p class="font-semibold mb-1">Langkah yang harus dilakukan:</p>
                                <div class="text-xs" style="white-space: pre-line;">${steps}</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

                // Set form action to CPMK page
                modalForm.action = "{{ route('dosen.cpmk.index', $tahunAjaranMatkul->id) }}";

                // Show modal using the component's method (same as data-modal-toggle)
                modal.classList.remove('hidden');
                modal.classList.add('flex');

                // Trigger animation (same as component)
                setTimeout(() => {
                    modal.classList.remove('bg-opacity-0');
                    modal.classList.add('bg-opacity-10');
                    if (modalContent) {
                        modalContent.classList.remove('scale-95', 'opacity-0');
                        modalContent.classList.add('scale-100', 'opacity-100');
                    }
                }, 10);
            }
        }

        function closeValidationModal() {
            const modal = document.getElementById('validation-modal');
            const modalContent = modal.querySelector('[data-modal-content]');

            if (modal && modalContent) {
                // Hide modal with animation (same as component)
                modalContent.classList.add('scale-95', 'opacity-0');
                modalContent.classList.remove('scale-100', 'opacity-100');
                modal.classList.remove('bg-opacity-10');
                modal.classList.add('bg-opacity-0');

                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }, 300);
            }
        }

        // Make functions globally available
        window.showImportModal = showImportModal;
        window.hideImportModal = hideImportModal;
        window.checkImportValidation = checkImportValidation;
        window.closeValidationModal = closeValidationModal;
        window.hideCreatedStudentsModal = function() {
            const modal = document.getElementById('created-students-modal');
            if (modal) {
                modal.classList.add('hidden');
            }
        };
        window.hideImportErrorsModal = function() {
            const modal = document.getElementById('import-errors-modal');
            if (modal) {
                modal.classList.add('hidden');
            }
        };

        // Close import modal when clicking outside
        document.addEventListener('click', function(e) {
            const importModal = document.getElementById('import-modal');
            if (e.target === importModal) {
                hideImportModal();
            }
        });

        // Close import modal on escape key
        document.addEventListener('keydown', function(e) {
            const importModal = document.getElementById('import-modal');
            const validationModal = document.getElementById('validation-modal');

            if (e.key === 'Escape') {
                if (importModal && !importModal.classList.contains('hidden')) {
                    hideImportModal();
                }
                if (validationModal && !validationModal.classList.contains('hidden')) {
                    closeValidationModal();
                }
            }
        });

        // Tidak perlu handle submit manual: komponen confirm-modal sudah menangani redirect via data-modal-confirm-link/action
    });
</script>
@endpush

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

    /* Fix untuk tombol simpan disabled */
    .btn-simpan-nilai:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background-color: #9ca3af !important;
    }

    .btn-simpan-nilai:disabled:hover {
        background-color: #9ca3af !important;
    }

    /* Animasi loading untuk tombol import */
    .animate-spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    /* Disable tombol saat loading */
    button:disabled {
        cursor: not-allowed;
        opacity: 0.7;
    }

    /* Styling untuk kolom aksi */
    .aksi-column button {
        display: inline-block;
    }

    /* Memastikan tombol dalam satu baris */
    .flex.items-center.justify-center.space-x-2 {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    /* Fix untuk modal detail agar benar-benar center */
    #detail-modal {
        display: flex !important;
        align-items: center;
        justify-content: center;
    }

    #detail-modal .modal-content {
        margin: auto;
        max-height: 90vh;
        overflow-y: auto;
    }
</style>
@endpush