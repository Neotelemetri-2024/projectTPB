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

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Nilai Mahasiswa - {{ $tahunAjaranMatkul->mataKuliah->namaMatkul }}</h1>
                    <p class="text-gray-600 mt-1">{{ $tahunAjaranMatkul->mataKuliah->kodeMatkul }} • {{ $tahunAjaranMatkul->tahunAjaran->tahun }} - {{ $tahunAjaranMatkul->tahunAjaran->periode }}</p>
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
                    <button type="button" onclick="showImportModal()"
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
                                        <div class="text-xs text-gray-400 mt-1">{{ number_format($bobotKomponen, 1) }}%</div>
                                    </th>
                                @endforeach
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Nilai</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider grade-column">Grade</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Detail</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider aksi-column hidden">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($mahasiswa as $mhs)
                                                                    @php
                                        $kelasNama = 'Tidak Ada Kelas';
                                        $studentClassId = null;
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
                                            <input type="hidden" name="student_class_id[{{ $mhs->id }}]" value="{{ $studentClassId }}">
                                        </td>
                                    @endforeach
                                    <!-- Kolom Grade -->
                                    <td class="px-4 py-4 whitespace-nowrap text-center grade-column">
                                        @php
                                            // Ambil grade dan total nilai yang sudah dihitung dari kelas_mahasiswa
                                            $kelasMahasiswa = $mhs->kelasMahasiswa->where('tahunAjaranMatkulId', $tahunAjaranMatkul->id)->first();
                                            $totalNilai = $kelasMahasiswa ? $kelasMahasiswa->totalNilai : null;
                                            $grade = $kelasMahasiswa ? $kelasMahasiswa->grade : null;

                                            // Jika belum ada nilai yang tersimpan, hitung dari bobot dan nilai yang ada
                                            if ($totalNilai === null || $grade === null) {
                                                // Group nilai by CPMK
                                                $allNilaiMahasiswa = $nilaiData->where('mahasiswaId', $mhs->id);
                                                $nilaiPerCpmk = $allNilaiMahasiswa->groupBy('cpmkId');
                                                $totalNilaiKeseluruhan = 0;
                                                $totalBobotKeseluruhan = 0;

                                                // Calculate nilai per CPMK
                                                foreach ($nilaiPerCpmk as $cpmkId => $nilaiCpmk) {
                                                    $nilaiCpmkTotal = 0;
                                                    $bobotCpmkTotal = 0;

                                                    foreach ($nilaiCpmk as $nilai) {
                                                        if ($nilai->bobot && $nilai->bobot->bobot > 0) {
                                                            $nilaiCpmkTotal += ($nilai->nilai * $nilai->bobot->bobot);
                                                            $bobotCpmkTotal += $nilai->bobot->bobot;
                                                        }
                                                    }

                                                    // Jika bobot CPMK > 0, hitung rata-rata terbobot
                                                    if ($bobotCpmkTotal > 0) {
                                                        $nilaiRataRataCpmk = $nilaiCpmkTotal / $bobotCpmkTotal;
                                                        $totalNilaiKeseluruhan += $nilaiRataRataCpmk;
                                                        $totalBobotKeseluruhan += 1; // Setiap CPMK dihitung sebagai 1 unit
                                                    }
                                                }

                                                // Hitung nilai akhir (rata-rata dari semua CPMK)
                                                $totalNilai = $totalBobotKeseluruhan > 0 ? $totalNilaiKeseluruhan / $totalBobotKeseluruhan : 0;

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
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $grade == 'A' || $grade == 'A-' ? 'bg-green-100 text-green-800' :
                                               ($grade == 'B+' || $grade == 'B' || $grade == 'B-' ? 'bg-blue-100 text-blue-800' :
                                               ($grade == 'C+' || $grade == 'C' ? 'bg-yellow-100 text-yellow-800' :
                                               ($grade == 'D' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800'))) }}">
                                            {{ $grade ?: '-' }}
                                        </span>
                                    </td>
                                    <!-- Kolom Total Nilai -->
                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ $totalNilai !== null ? number_format($totalNilai, 1) : '-' }}
                                        </span>
                                    </td>
                                    <!-- Kolom Detail -->
                                    <td class="px-4 py-4 whitespace-nowrap text-center">
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
                                    </td>
                                    <!-- Kolom Aksi (hidden by default) -->
                                    <td class="px-4 py-4 whitespace-nowrap text-center aksi-column hidden">
                                        <button type="button"
                                                id="save-btn-{{ $mhs->id }}"
                                                class="btn-simpan-nilai px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed"
                                                disabled>
                                            <svg class="w-3 h-3 mr-1 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Simpan
                                        </button>
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
    cancelText="Tetap di Halaman"
/>

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
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
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
                    <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors duration-200">
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
<div id="detail-modal" class="fixed inset-0 overflow-y-auto overflow-x-hidden flex justify-center items-center min-h-screen w-full z-50 hidden" style="background: rgba(0,0,0,0.6);">
    <div class="relative p-4 w-full max-w-6xl max-h-full transform transition-all duration-300 ease-out modal-content scale-95 opacity-0">
        <div class="relative bg-white rounded-lg shadow-xl">
            <div class="flex items-center justify-between p-4 md:p-5 border-b border-gray-200 rounded-t">
                <h3 class="text-lg font-semibold text-gray-900" id="detail-modal-title">Detail Nilai Mahasiswa</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition-colors duration-200" data-modal-hide="detail-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <div class="p-4 md:p-5 overflow-y-auto max-h-[70vh]">
                <div class="mb-4 bg-blue-50 rounded-lg p-3 border border-blue-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <p class="text-sm text-gray-700"><strong>NIM:</strong> <span id="detail-modal-nim"></span></p>
                        <p class="text-sm text-gray-700"><strong>Nama:</strong> <span id="detail-modal-nama"></span></p>
                    </div>
                </div>

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
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
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
                                <td class="px-4 py-2 text-sm text-gray-900 text-xs">{{ $student['email'] }}</td>
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
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <div class="p-4 md:p-5">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 max-h-64 overflow-y-auto">
                    <ul class="text-sm text-red-700 space-y-1">
                        @foreach(session('import_errors') as $error)
                            <li class="flex items-start">
                                <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
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
        const modalNim = document.getElementById('detail-modal-nim');
        const modalNama = document.getElementById('detail-modal-nama');

        if (modal && modalTitle && modalNim && modalNama) {
            modalTitle.textContent = `Detail Nilai Mahasiswa`;
            modalNim.textContent = nim;
            modalNama.textContent = nama;

            // Load detail data
            loadDetailData(mahasiswaId);

            // Show modal
            modal.classList.remove('hidden');
            modal.classList.add('flex');

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
                modal.classList.remove('flex');
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

            console.log('Detail button clicked:', { mahasiswaId, mahasiswaNama, nim });

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
                modal.classList.remove('flex');
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

    if (toggleEditBtn) {
        toggleEditBtn.addEventListener('click', function() {
            const isEditMode = this.textContent.includes('Aktifkan');

            if (isEditMode) {
                // Switch to edit mode
                this.innerHTML = `
                    <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Nonaktifkan Input Nilai
                `;
                this.classList.remove('bg-amber-600', 'hover:bg-amber-700');
                this.classList.add('bg-red-600', 'hover:bg-red-700');

                // Show input fields and hide plain text
                nilaiInputs.forEach(input => input.classList.remove('hidden'));
                nilaiPlains.forEach(plain => plain.classList.add('hidden'));
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
            } else {
                // Switch back to view mode
                this.innerHTML = `
                    <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6 6M3 17v2a2 2 0 002 2h2m14-6v6a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h6"></path>
                    </svg>
                    Aktifkan Input Nilai
                `;
                this.classList.remove('bg-red-600', 'hover:bg-red-700');
                this.classList.add('bg-amber-600', 'hover:bg-amber-700');

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
        });
    });

    // Individual save button functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-simpan-nilai')) {
            e.preventDefault();
            const button = e.target.closest('.btn-simpan-nilai');
            const mahasiswaId = button.getAttribute('data-mahasiswa-id');

            // Get all nilai inputs for this mahasiswa
            const nilaiInputs = document.querySelectorAll(`input[data-mahasiswa-id="${mahasiswaId}"]`);
            const nilaiData = {};

            nilaiInputs.forEach(input => {
                const komponenId = input.getAttribute('data-komponen-id');
                const nilai = input.value;
                if (nilai !== '') {
                    nilaiData[komponenId] = parseFloat(nilai);
                }
            });

            // Send AJAX request to save nilai
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            // Add nilai data in the expected format
            Object.keys(nilaiData).forEach(komponenId => {
                formData.append(`nilai[${mahasiswaId}][${komponenId}]`, nilaiData[komponenId]);
            });

            // Add student class ID
            const studentClassId = document.querySelector(`input[name="student_class_id[${mahasiswaId}]"]`).value;
            formData.append(`student_class_id[${mahasiswaId}]`, studentClassId);

            fetch(`/dosen/nilai/{{ $tahunAjaranMatkul->id }}/individual-store`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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

                    // Show success message
                    if (typeof showToast === 'function') {
                        showToast('Nilai berhasil disimpan', 'success');
                    }
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
                document.getElementById('bulk-nilai-form').submit();
            }
        });
    }

    // Make functions globally available
    window.showImportModal = showImportModal;
    window.hideImportModal = hideImportModal;
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
        if (e.key === 'Escape' && importModal && !importModal.classList.contains('hidden')) {
            hideImportModal();
        }
    });
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
</style>
@endpush
