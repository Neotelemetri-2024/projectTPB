@extends('layouts.main')

@section('title', 'Laporan CPMK')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Laporan Pengukuran CPMK</h1>
                <p class="mt-2 text-gray-600">Analisis detail ketercapaian CPMK per mata kuliah</p>
            </div>
            <form method="GET" action="" class="flex items-center gap-2">
                <label for="tahun_ajaran_id" class="text-sm font-medium text-gray-700">Tahun Ajaran:</label>
                <select name="tahun_ajaran_id" id="tahun_ajaran_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 p-2.5" onchange="this.form.submit()">
                    @foreach($tahunAjaranList as $ta)
                        <option value="{{ $ta->id }}" {{ $selectedTahunAjaranId == $ta->id ? 'selected' : '' }}>
                            {{ $ta->tahun }} - {{ ucfirst($ta->periode) }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    @if($mataKuliahList->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($mataKuliahList as $matkul)
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                    {{ $matkul->mataKuliah->kodeMatkul }}
                                </h3>
                                <p class="text-sm text-gray-600 mb-2">
                                    {{ $matkul->mataKuliah->namaMatkul }}
                                </p>
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    <span>{{ $matkul->kelas->count() }} Kelas</span>
                                    <span>{{ $matkul->kelas->flatMap->kelasMahasiswa->count() }} Mahasiswa</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-4">
                            <a href="{{ route('pimpinan.cpmk-report.show', $matkul->id) }}" 
                               class="w-full bg-amber-600 hover:bg-amber-700 text-white font-medium py-2 px-4 rounded-lg flex items-center justify-center transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Lihat Laporan Detail
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Mata Kuliah</h3>
            <p class="text-gray-600 mb-4">
                Belum ada mata kuliah yang tersedia untuk tahun ajaran yang dipilih.
            </p>
            <a href="{{ route('pimpinan.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700">
                Kembali ke Dashboard
            </a>
        </div>
    @endif
</div>
@endsection
