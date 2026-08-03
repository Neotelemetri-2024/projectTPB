@extends('layouts.main')
@section('title', 'Detail Mata Kuliah Tahun Ajaran')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Detail Mata Kuliah Tahun Ajaran</h2>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.tahun-ajaran-matkul.edit', $tahunAjaranMatkul->id) }}"
                       class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Mata Kuliah
                    </a>
                    <a href="{{ request('back_url', route('admin.tahun-ajaran-matkul.index')) }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Informasi Umum -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="mb-4">
                        <h3 class="text-md font-semibold text-gray-900">Informasi Mata Kuliah</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div class="flex">
                                <span class="w-24 text-sm font-medium text-gray-600">Tahun Ajaran:</span>
                                <span class="text-sm text-gray-900">{{ $tahunAjaranMatkul->tahunAjaran->tahun }}-{{ $tahunAjaranMatkul->tahunAjaran->periode }}</span>
                            </div>
                            <div class="flex">
                                <span class="w-24 text-sm font-medium text-gray-600">Mata Kuliah:</span>
                                <span class="text-sm text-gray-900">{{ $tahunAjaranMatkul->mataKuliah->namaMatkul }}</span>
                            </div>
                            <div class="flex">
                                <span class="w-24 text-sm font-medium text-gray-600">Kode:</span>
                                <span class="text-sm text-gray-900">{{ $tahunAjaranMatkul->mataKuliah->kodeMatkul }}-{{ $tahunAjaranMatkul->mataKuliah->kurikulum }}</span>
                            </div>
                            <div class="flex">
                                <span class="w-24 text-sm font-medium text-gray-600">Semester:</span>
                                <span class="text-sm text-gray-900">{{ $tahunAjaranMatkul->semester ?? 1 }}</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex">
                                <span class="w-16 text-sm font-medium text-gray-600">SKS:</span>
                                <span class="text-sm text-gray-900">{{ $tahunAjaranMatkul->mataKuliah->sks }}</span>
                            </div>
                            <div class="flex">
                                <span class="w-16 text-sm font-medium text-gray-600">Jenis:</span>
                                <span class="text-sm text-gray-900">{{ $tahunAjaranMatkul->mataKuliah->jenis }}</span>
                            </div>
                            <div class="flex">
                                <span class="w-16 text-sm font-medium text-gray-600">Total Kelas:</span>
                                <span class="text-sm text-gray-900">{{ $tahunAjaranMatkul->kelas->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-md font-semibold text-gray-900 mb-4">Statistik</h3>
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div class="border-r border-gray-300">
                            @php
                                $allDosen = collect();
                                foreach($tahunAjaranMatkul->kelas as $kelas) {
                                    $allDosen = $allDosen->merge($kelas->dosen);
                                }
                                $allDosen = $allDosen->unique('id');
                            @endphp
                            <div class="text-2xl font-bold text-blue-600">{{ $allDosen->count() }}</div>
                            <p class="text-sm text-gray-600">Dosen Pengampu</p>
                        </div>
                        <div>
                            @php
                                $totalMahasiswa = $tahunAjaranMatkul->kelas->flatMap->kelasMahasiswa->pluck('mahasiswaId')->unique()->count();
                            @endphp
                            <div class="text-2xl font-bold text-green-600">{{ $totalMahasiswa }}</div>
                            <p class="text-sm text-gray-600">Mahasiswa</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Kelas -->
            <div class="mb-6">
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-md font-semibold text-gray-900">Daftar Kelas</h3>
                    </div>
                    <div class="p-4">
                        @if($tahunAjaranMatkul->kelas->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($tahunAjaranMatkul->kelas as $kelas)
                                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:shadow-md transition-shadow">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="text-lg font-semibold text-gray-900">Kelas {{ $kelas->namaKelas }}</h4>
                                            <a href="{{ route('admin.kelas.show', $kelas->id) }}" 
                                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Detail →
                                            </a>
                                        </div>
                                        
                                        <!-- Dosen Pengampu -->
                                        <div class="mb-3">
                                            <h5 class="text-sm font-medium text-gray-700 mb-2">Dosen Pengampu:</h5>
                                            <div class="space-y-1">
                                                @forelse($kelas->dosenPengampuKelas as $dosenPengampuKelas)
                                                    <div class="text-sm text-gray-600">{{ $dosenPengampuKelas->dosen->nama }}</div>
                                                @empty
                                                    <div class="text-sm text-gray-500">Belum ada dosen</div>
                                                @endforelse
                                            </div>
                                        </div>
                                        
                                        <!-- Mahasiswa -->
                                        <div class="mb-3">
                                            <h5 class="text-sm font-medium text-gray-700 mb-2">Mahasiswa:</h5>
                                            <div class="text-sm text-gray-600">
                                                {{ $kelas->kelasMahasiswa->count() }} mahasiswa
                                            </div>
                                        </div>
                                        
                                        <!-- Quick Actions -->
                                        <div class="flex space-x-2 pt-2 border-t border-gray-200">
                                            <a href="{{ route('admin.kelas.manage-mahasiswa', $kelas->id) }}" 
                                               class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded hover:bg-blue-200">
                                                Kelola Mahasiswa
                                            </a>
                                            <a href="{{ route('admin.kelas.manage-dosen', $kelas->id) }}" 
                                               class="text-xs bg-amber-100 text-amber-800 px-2 py-1 rounded hover:bg-amber-200">
                                                Kelola Dosen
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="text-gray-500 mb-4">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-medium text-gray-900 mb-2">Belum ada kelas</h3>
                                <p class="text-sm text-gray-500 mb-4">Tambahkan kelas dengan cara menekan tombol Edit Mata Kuliah.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
