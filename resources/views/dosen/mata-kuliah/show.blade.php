@extends('layouts.main')

@section('title', 'Detail Mata Kuliah')

@section('content')
<div class="p-6">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li>
                <a href="{{ route('dosen.mata-kuliah.index') }}" class="hover:text-gray-700">Mata Kuliah</a>
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
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $mataKuliahDiampu->mataKuliah->namaMatkul }}</h1>
                    <p class="text-gray-600 mt-1">{{ $mataKuliahDiampu->mataKuliah->kodeMatkul }} • {{ $mataKuliahDiampu->tahunAjaran->tahun }} - {{ $mataKuliahDiampu->tahunAjaran->periode }}</p>
                </div>
                <a href="{{ route('dosen.mata-kuliah.index') }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span class="hidden sm:inline">Kembali</span>
                </a>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('dosen.cpmk.index', $mataKuliahDiampu->id) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h2M9 5a2 2 0 012 2v10a2 2 0 01-2 2M9 5a2 2 0 012-2h2a2 2 0 012 2M15 5a2 2 0 012 2v10a2 2 0 01-2 2h-2"></path>
                    </svg>
                    Kelola CPMK
                </a>
                <a href="{{ route('dosen.bobot-komponen.index', $mataKuliahDiampu->id) }}"
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Kelola Bobot Komponen
                </a>
            </div>
        </div>
    </div>

    <!-- Overview Content -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Informasi Mata Kuliah</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-blue-900 mb-1">SKS</h3>
                    <p class="text-2xl font-bold text-blue-600">{{ $mataKuliahDiampu->mataKuliah->sks ?? '-' }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-green-900 mb-1">Jenis</h3>
                    <p class="text-lg font-semibold text-green-600">{{ ucfirst($mataKuliahDiampu->mataKuliah->jenis ?? '-') }}</p>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-yellow-900 mb-1">Kelas</h3>
                    <p class="text-lg font-semibold text-yellow-600">
                        @if(isset($kelasNumbers) && $kelasNumbers->count() > 0)
                            {{ App\Models\TahunAjaranMatkul::convertKelasToHuruf($kelasNumbers)->implode(', ') }}
                        @else
                            {{ $mataKuliahDiampu->kelasHuruf ?: '-' }}
                        @endif
                    </p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-purple-900 mb-1">Dosen Pengampu</h3>
                    <p class="text-2xl font-bold text-purple-600">{{ $mataKuliahDiampu->dosenPengampu->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Dosen Pengampu -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Dosen Pengampu</h2>
            <p class="text-sm text-gray-600 mt-1">Total: {{ $mataKuliahDiampu->dosenPengampu->count() }} dosen</p>
        </div>

        <div class="p-6">
            @if($mataKuliahDiampu->dosenPengampu->isEmpty())
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Dosen Pengampu</h3>
                        <p class="text-gray-500">Belum ada dosen yang ditugaskan untuk mengampu mata kuliah ini.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($mataKuliahDiampu->dosenPengampu as $dosenPengampu)
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ substr($dosenPengampu->dosen->nama ?? 'D', 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $dosenPengampu->dosen->nama ?? 'Nama tidak tersedia' }}
                                        </p>
                                        <p class="text-sm text-gray-500 truncate">
                                            {{ $dosenPengampu->dosen->user->email ?? 'Email tidak tersedia' }}
                                        </p>
                                        @if($dosenPengampu->dosen->nidn)
                                            <p class="text-xs text-gray-400">
                                                NIDN: {{ $dosenPengampu->dosen->nidn }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                @if($dosenPengampu->dosenId == auth()->user()->dosen->id)
                                    <div class="mt-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Anda
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Daftar Mahasiswa -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Daftar Mahasiswa</h2>
                <p class="text-sm text-gray-600 mt-1">Total: {{ $mahasiswa->count() }} mahasiswa</p>
            </div>

            @if($mahasiswa->isEmpty())
                <div class="p-6">
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Mahasiswa</h3>
                        <p class="text-gray-500">Belum ada mahasiswa yang terdaftar di mata kuliah ini.</p>
                    </div>
                </div>
            @else
                @if(isset($kelasNumbers) && $kelasNumbers->count() > 1)
                    <!-- Tab Navigation untuk Multiple Kelas -->
                    <div class="border-b border-gray-200">
                        <nav class="flex overflow-x-auto">
                            <button class="tab-button active flex-shrink-0 py-4 px-6 text-sm font-medium text-blue-600 border-b-2 border-blue-500 whitespace-nowrap"
                                    onclick="showTab('all')">
                                Semua Kelas ({{ $mahasiswa->count() }})
                            </button>
                            @foreach($kelasNumbers as $kelasNum)
                                @php
                                    $kelasHuruf = App\Models\TahunAjaranMatkul::convertKelasToHuruf(collect([$kelasNum]))->first();
                                    $mahasiswaKelas = collect();
                                    foreach($mataKuliahClasses as $class) {
                                        if($class->kelas == $kelasNum) {
                                            $mahasiswaKelas = $mahasiswaKelas->merge($class->kelasMahasiswa->pluck('mahasiswa'));
                                        }
                                    }
                                    $mahasiswaKelas = $mahasiswaKelas->unique('id');
                                @endphp
                                <button class="tab-button flex-shrink-0 py-4 px-6 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 whitespace-nowrap"
                                        onclick="showTab('kelas-{{ $kelasNum }}')">
                                    Kelas {{ $kelasHuruf }} ({{ $mahasiswaKelas->count() }})
                                </button>
                            @endforeach
                        </nav>
                    </div>
                @endif

                <!-- Tab Content untuk Semua Kelas -->
                <div id="tab-all" class="tab-content p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    @if(isset($kelasNumbers) && $kelasNumbers->count() > 1)
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                                    @endif
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($mahasiswa as $index => $mhs)
                                    @php
                                        $kelasLabel = '-';
                                        foreach($mataKuliahClasses as $class) {
                                            foreach($class->kelasMahasiswa as $kelasMhs) {
                                                if($kelasMhs->mahasiswa && $kelasMhs->mahasiswa->id == $mhs->id) {
                                                    $kelasLabel = App\Models\TahunAjaranMatkul::convertKelasToHuruf(collect([$class->kelas]))->first();
                                                    break 2;
                                                }
                                            }
                                        }
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $mhs->nim ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-700">{{ substr($mhs->nama ?? 'N', 0, 1) }}</span>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">{{ $mhs->nama ?? 'Nama tidak tersedia' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $mhs->user->email ?? '-' }}</td>
                                        @if(isset($kelasNumbers) && $kelasNumbers->count() > 1)
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $kelasLabel }}
                                                </span>
                                            </td>
                                        @endif
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Aktif
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if(isset($kelasNumbers) && $kelasNumbers->count() > 1)
                    <!-- Tab Content untuk Setiap Kelas -->
                    @foreach($kelasNumbers as $kelasNum)
                        @php
                            $kelasHuruf = App\Models\TahunAjaranMatkul::convertKelasToHuruf(collect([$kelasNum]))->first();
                            $mahasiswaKelas = collect();
                            foreach($mataKuliahClasses as $class) {
                                if($class->kelas == $kelasNum) {
                                    $mahasiswaKelas = $mahasiswaKelas->merge($class->kelasMahasiswa->pluck('mahasiswa'));
                                }
                            }
                            $mahasiswaKelas = $mahasiswaKelas->unique('id')->values();
                        @endphp
                        <div id="tab-kelas-{{ $kelasNum }}" class="tab-content p-6 hidden">
                            <div class="mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Kelas {{ $kelasHuruf }}</h3>
                                <p class="text-sm text-gray-600">{{ $mahasiswaKelas->count() }} mahasiswa</p>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($mahasiswaKelas as $index => $mhs)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $mhs->nim ?? '-' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-8 w-8">
                                                            <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                                                <span class="text-sm font-medium text-gray-700">{{ substr($mhs->nama ?? 'N', 0, 1) }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="ml-3">
                                                            <div class="text-sm font-medium text-gray-900">{{ $mhs->nama ?? 'Nama tidak tersedia' }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $mhs->user->email ?? '-' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Aktif
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                @endif
            @endif
        </div>
    </div>
</div>

<script>
function showTab(tabId) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active class from all buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'text-blue-600', 'border-blue-500');
        button.classList.add('text-gray-500', 'border-transparent');
    });

    // Show selected tab content
    document.getElementById('tab-' + tabId).classList.remove('hidden');

    // Add active class to clicked button
    event.target.classList.add('active', 'text-blue-600', 'border-blue-500');
    event.target.classList.remove('text-gray-500', 'border-transparent');
}
</script>

<style>
.tab-button {
    transition: all 0.2s ease-in-out;
}

.tab-button:hover {
    color: #374151 !important;
    border-color: #D1D5DB !important;
}

.tab-button.active {
    color: #2563EB !important;
    border-color: #2563EB !important;
}

.tab-content {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endsection
