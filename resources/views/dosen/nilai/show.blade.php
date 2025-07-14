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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Mahasiswa</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $mahasiswa->count() }}</p>
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
                    <button type="button"
                            id="toggle-bulk-input"
                            class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Aktifkan Input Sekaligus
                    </button>
                </div>
            </div>
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
                                    class="tab-button active whitespace-nowrap py-2 px-1 border-b-2 border-amber-500 font-medium text-sm text-amber-600"
                                    data-tab="all">
                                Semua Mahasiswa
                                <span class="ml-2 bg-amber-100 text-amber-600 py-0.5 px-2 rounded-full text-xs font-medium">{{ $mahasiswa->count() }}</span>
                            </button>                            @php
                                // Group mahasiswa by kelas
                                $mahasiswaByKelas = [];
                                foreach($mahasiswa as $mhs) {
                                    $kelasNumber = 'Tidak Ada Kelas';

                                    // Find the class this student belongs to
                                    foreach($mataKuliahClasses as $class) {
                                        $studentInClass = $class->kelasMahasiswa->where('mahasiswaId', $mhs->id)->first();
                                        if ($studentInClass) {
                                            $kelasNumber = $class->kelas;
                                            break;
                                        }
                                    }

                                    // Convert numeric class to letter
                                    $kelasHuruf = is_numeric($kelasNumber) ?
                                        App\Models\TahunAjaranMatkul::convertKelasToHuruf($kelasNumber) :
                                        $kelasNumber;

                                    if (!isset($mahasiswaByKelas[$kelasHuruf])) {
                                        $mahasiswaByKelas[$kelasHuruf] = [];
                                    }
                                    $mahasiswaByKelas[$kelasHuruf][] = $mhs;
                                }

                                // Sort by kelas (put letter classes in alphabetical order)
                                uksort($mahasiswaByKelas, function($a, $b) {
                                    if ($a === 'Tidak Ada Kelas') return 1;
                                    if ($b === 'Tidak Ada Kelas') return -1;
                                    return strcmp($a, $b);
                                });
                            @endphp
                            @foreach($mahasiswaByKelas as $kelasHuruf => $mahasiswaInKelas)
                                <button type="button"
                                        class="tab-button whitespace-nowrap py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300"
                                        data-tab="kelas-{{ Str::slug($kelasHuruf) }}">
                                    Kelas {{ $kelasHuruf }}
                                    <span class="ml-2 bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs font-medium">{{ count($mahasiswaInKelas) }}</span>
                                </button>
                            @endforeach
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
                        <!-- Bulk Input Form (Hidden by default) -->
                        <form id="bulk-nilai-form" action="{{ route('dosen.nilai.bulk-store', $mataKuliahDiampu->id) }}" method="POST" class="hidden">
                            @csrf
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50">
                                            Mahasiswa
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            NIM
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
                                    @foreach($mahasiswa as $mhs)
                                        @php
                                            $kelasNumber = 'Tidak Ada Kelas';

                                            // Find the class this student belongs to
                                            foreach($mataKuliahClasses as $class) {
                                                $studentInClass = $class->kelasMahasiswa->where('mahasiswaId', $mhs->id)->first();
                                                if ($studentInClass) {
                                                    $kelasNumber = $class->kelas;
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
                                            <td class="px-6 py-4 whitespace-nowrap sticky left-0 bg-white">
                                                <div class="flex items-center">
                                                    <div class="h-10 w-10 flex-shrink-0">
                                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                            <span class="text-sm font-medium text-gray-700">
                                                                {{ strtoupper(substr($mhs->nama, 0, 2)) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ $mhs->nama }}</div>
                                                        <div class="text-sm text-gray-500">{{ $mhs->user->email ?? 'Email tidak tersedia' }}</div>
                                                    </div>
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
                                                        $komponenBobot = $komponen->bobot ?? collect();
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
                                                           data-original-value="{{ $nilaiKomponen !== null ? number_format($nilaiKomponen, 2, '.', '') : '' }}">
                                                </td>
                                            @endforeach
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <button type="button"
                                                        class="reset-row-btn text-gray-400 hover:text-red-600 transition-colors duration-200"
                                                        data-mahasiswa-id="{{ $mhs->id }}"
                                                        title="Reset nilai mahasiswa ini">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </form>

                        <!-- Individual Input Mode (Default) -->
                        <table id="individual-table" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50">
                                        Mahasiswa
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        NIM
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
                                @foreach($mahasiswa as $mhs)
                                    @php
                                        $kelasNumber = 'Tidak Ada Kelas';

                                        // Find the class this student belongs to
                                        foreach($mataKuliahClasses as $class) {
                                            $studentInClass = $class->kelasMahasiswa->where('mahasiswaId', $mhs->id)->first();
                                            if ($studentInClass) {
                                                $kelasNumber = $class->kelas;
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
                                        <td class="px-6 py-4 whitespace-nowrap sticky left-0 bg-white">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 flex-shrink-0">
                                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-700">
                                                            {{ strtoupper(substr($mhs->nama, 0, 2)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $mhs->nama }}</div>
                                                    <div class="text-sm text-gray-500">{{ $mhs->user->email ?? 'Email tidak tersedia' }}</div>
                                                </div>
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

                                        <!-- Individual Forms for Each Student -->
                                        <form id="form-{{ $mhs->id }}" action="{{ route('dosen.nilai.bulk-store', $mataKuliahDiampu->id) }}" method="POST" class="contents">
                                            @csrf
                                            @foreach($allKomponen as $komponen)
                                                @php
                                                    // Calculate the existing component value from bobot values
                                                    $nilaiKomponen = null;
                                                    if ($studentNilai->isNotEmpty()) {
                                                        $komponenBobot = $komponen->bobot ?? collect();
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
                                                           class="individual-input w-16 px-2 py-1 border border-gray-300 rounded text-sm text-center focus:ring-amber-500 focus:border-amber-500 bg-gray-50"
                                                           placeholder="0"
                                                           readonly
                                                           data-original-value="{{ $nilaiKomponen !== null ? number_format($nilaiKomponen, 2, '.', '') : '' }}">
                                                </td>
                                            @endforeach
                                        </form>

                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <!-- Edit Button -->
                                                <button type="button"
                                                        class="edit-btn text-blue-600 hover:text-blue-800 transition-colors duration-200"
                                                        data-mahasiswa-id="{{ $mhs->id }}"
                                                        title="Edit nilai mahasiswa ini">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                                <!-- Save Button (Hidden by default) -->
                                                <button type="submit"
                                                        form="form-{{ $mhs->id }}"
                                                        class="save-btn hidden text-green-600 hover:text-green-800 transition-colors duration-200"
                                                        title="Simpan nilai mahasiswa ini">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </button>
                                                <!-- Cancel Button (Hidden by default) -->
                                                <button type="button"
                                                        class="cancel-btn hidden text-red-600 hover:text-red-800 transition-colors duration-200"
                                                        data-mahasiswa-id="{{ $mhs->id }}"
                                                        title="Batal edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                <!-- Bulk Save Controls (Bottom) -->
                <div id="bulk-actions" class="hidden mt-6 p-4 bg-gray-50 rounded-lg border-t border-gray-200">
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
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality for students
    const tabButtons = document.querySelectorAll('.tab-button');
    const mahasiswaRows = document.querySelectorAll('.mahasiswa-row');

    function switchTab(activeTab) {
        // Update tab buttons
        tabButtons.forEach(button => {
            const isActive = button.dataset.tab === activeTab;
            if (isActive) {
                button.classList.remove('border-transparent', 'text-gray-500');
                button.classList.add('border-amber-500', 'text-amber-600', 'active');
            } else {
                button.classList.remove('border-amber-500', 'text-amber-600', 'active');
                button.classList.add('border-transparent', 'text-gray-500');
            }
        });

        // Show/hide mahasiswa rows based on tab
        mahasiswaRows.forEach(row => {
            const rowKelas = row.dataset.kelas;
            const shouldShow = activeTab === 'all' || rowKelas === activeTab;

            if (shouldShow) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Add click event listeners to tab buttons
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.dataset.tab;
            switchTab(targetTab);
        });
    });

    // Initialize with "all" tab active
    switchTab('all');

    // Mode Toggle Functionality
    const toggleBulkBtn = document.getElementById('toggle-bulk-input');
    const bulkForm = document.getElementById('bulk-nilai-form');
    const individualTable = document.getElementById('individual-table');
    const bulkActions = document.getElementById('bulk-actions');
    const confirmCheckbox = document.getElementById('confirm-bulk-save');
    const bulkSaveBtn = document.getElementById('bulk-save-btn');

    let isBulkMode = false;

    toggleBulkBtn.addEventListener('click', function() {
        isBulkMode = !isBulkMode;

        if (isBulkMode) {
            // Switch to bulk mode
            individualTable.classList.add('hidden');
            bulkForm.classList.remove('hidden');
            bulkActions.classList.remove('hidden');
            toggleBulkBtn.innerHTML = `
                <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Lihat Mode Individual
            `;
            toggleBulkBtn.classList.remove('bg-amber-600', 'hover:bg-amber-700');
            toggleBulkBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
        } else {
            // Switch to individual mode
            individualTable.classList.remove('hidden');
            bulkForm.classList.add('hidden');
            bulkActions.classList.add('hidden');
            toggleBulkBtn.innerHTML = `
                <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Aktifkan Input Sekaligus
            `;
            toggleBulkBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            toggleBulkBtn.classList.add('bg-amber-600', 'hover:bg-amber-700');
        }
    });

    // Bulk mode functionality
    if (bulkForm) {
        const bulkInputs = bulkForm.querySelectorAll('input[type="number"]');
        let hasChanges = false;

        // Store original values
        const originalValues = new Map();
        bulkInputs.forEach(input => {
            originalValues.set(input.name, input.value);
        });

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
            hasChanges = false;
            bulkInputs.forEach(input => {
                if (input.value !== originalValues.get(input.name)) {
                    hasChanges = true;
                }
            });

            updateBulkSaveState();
        }

        function updateBulkSaveState() {
            const canSave = hasChanges && confirmCheckbox.checked;
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
        confirmCheckbox.addEventListener('change', updateBulkSaveState);

        // Bulk save button
        bulkSaveBtn.addEventListener('click', function() {
            if (hasChanges && confirmCheckbox.checked) {
                // Show loading state
                bulkSaveBtn.disabled = true;
                bulkSaveBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Menyimpan...
                `;

                // Submit the form
                bulkForm.submit();
            }
        });

        // Initialize
        checkForBulkChanges();
    }

    // Individual mode functionality
    const editButtons = document.querySelectorAll('.edit-btn');
    const saveButtons = document.querySelectorAll('.save-btn');
    const cancelButtons = document.querySelectorAll('.cancel-btn');

    console.log('Found edit buttons:', editButtons.length);
    console.log('Found save buttons:', saveButtons.length);
    console.log('Found cancel buttons:', cancelButtons.length);

    // Edit button functionality
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            console.log('Edit button clicked for mahasiswa:', this.dataset.mahasiswaId);
            const mahasiswaId = this.dataset.mahasiswaId;
            const row = document.querySelector(`tr[data-mahasiswa-id="${mahasiswaId}"]`);

            if (row) {
                // Enable inputs for editing
                row.querySelectorAll('.individual-input').forEach(input => {
                    input.readOnly = false;
                    input.classList.remove('bg-gray-50');
                    input.classList.add('bg-white');
                    console.log('Input enabled:', input);
                });

                // Toggle buttons
                this.classList.add('hidden');
                const saveBtn = row.querySelector('.save-btn');
                const cancelBtn = row.querySelector('.cancel-btn');

                if (saveBtn) saveBtn.classList.remove('hidden');
                if (cancelBtn) cancelBtn.classList.remove('hidden');

                console.log('Buttons toggled for row:', mahasiswaId);
            } else {
                console.error('Row not found for mahasiswa:', mahasiswaId);
            }
        });
    });

    // Cancel button functionality
    cancelButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            console.log('Cancel button clicked for mahasiswa:', this.dataset.mahasiswaId);
            const mahasiswaId = this.dataset.mahasiswaId;
            const row = document.querySelector(`tr[data-mahasiswa-id="${mahasiswaId}"]`);

            if (row) {
                // Disable inputs and reset values
                row.querySelectorAll('.individual-input').forEach(input => {
                    input.readOnly = true;
                    input.classList.remove('bg-white');
                    input.classList.add('bg-gray-50');
                    input.value = input.dataset.originalValue;
                });

                // Toggle buttons
                this.classList.add('hidden');
                const saveBtn = row.querySelector('.save-btn');
                const editBtn = row.querySelector('.edit-btn');

                if (saveBtn) saveBtn.classList.add('hidden');
                if (editBtn) editBtn.classList.remove('hidden');

                console.log('Edit cancelled for row:', mahasiswaId);
            }
        });
    });

    // Individual form submission
    document.querySelectorAll('form[id^="form-"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('.save-btn');

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            `;
        });
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
</style>
@endpush
