@extends('layouts.main')
@section('title', 'Detail Mata Kuliah Tahun Ajaran')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Detail Mata Kuliah Tahun Ajaran</h2>
                <a href="{{ request('back_url', route('admin.tahun-ajaran-matkul.index')) }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
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
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-md font-semibold text-gray-900">Informasi Mata Kuliah</h3>
                        <button type="button"
                                data-modal-target="addKelasModal"
                                data-modal-toggle="addKelasModal"
                                class="bg-amber-600 hover:bg-amber-700 text-white px-3 py-1 rounded text-sm flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah Kelas
                        </button>
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
                                <span class="text-sm text-gray-900">{{ $tahunAjaranMatkul->mataKuliah->kodeMatkul }}</span>
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
                                <p class="text-sm text-gray-500 mb-4">Tambahkan kelas pertama untuk mata kuliah ini</p>
                                <button type="button"
                                        data-modal-target="addKelasModal"
                                        data-modal-toggle="addKelasModal"
                                        class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm">
                                    Tambah Kelas Pertama
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Kelas -->
<x-form-modal
    id="addKelasModal"
    title="Tambah Kelas Baru"
    :action="route('admin.tahun-ajaran-matkul.add-kelas', $tahunAjaranMatkul->id)"
    submit-text="Tambah"
>
    <div class="grid gap-4 mb-4 grid-cols-1">
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Kelas</label>
            <div class="flex gap-2">
                <input type="text"
                       name="kelasNames[]"
                       class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block p-2.5"
                       placeholder="Contoh: A, B, C, atau 1, 2, 3"
                       required>
                <button type="button" 
                        id="add-kelas-input"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2.5 rounded-lg text-sm">
                    +
                </button>
            </div>
            <div id="additional-kelas-inputs" class="mt-2 space-y-2"></div>
            <p class="mt-1 text-xs text-gray-500">Masukkan nama kelas (huruf atau angka). Klik + untuk menambah kelas lain.</p>
        </div>

        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Opsi Dosen Pengampu</label>
            <div class="space-y-3">
                <label class="flex items-center">
                    <input type="radio" 
                           name="dosenOption" 
                           value="same" 
                           id="dosen-same-add" 
                           class="dosen-option-add rounded border-gray-300 text-amber-600 focus:ring-amber-500" 
                           checked>
                    <span class="ml-2 text-sm text-gray-700">Dosen sama untuk semua kelas baru</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" 
                           name="dosenOption" 
                           value="different" 
                           id="dosen-different-add" 
                           class="dosen-option-add rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                    <span class="ml-2 text-sm text-gray-700">Dosen berbeda per kelas</span>
                </label>
            </div>
        </div>

        <!-- Dosen Same Section -->
        <div id="dosen-same-section-add" class="dosen-section-add">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Dosen untuk Semua Kelas Baru</label>
            <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3">
                @forelse($availableDosens as $dosen)
                    <label class="flex items-center mb-2 last:mb-0">
                        <input type="checkbox"
                               name="dosenIds[]"
                               value="{{ $dosen->id }}"
                               class="rounded border-gray-300 text-amber-600 shadow-sm focus:border-amber-300 focus:ring focus:ring-amber-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            {{ $dosen->nama }} ({{ $dosen->nip }})
                        </span>
                    </label>
                @empty
                    <p class="text-sm text-gray-500">Tidak ada dosen tersedia</p>
                @endforelse
            </div>
        </div>

        <!-- Dosen Different Section -->
        <div id="dosen-different-section-add" class="dosen-section-add hidden">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Dosen per Kelas</label>
            <div id="kelas-dosen-container-add" class="space-y-3">
                <!-- Will be populated by JavaScript -->
            </div>
        </div>
    </div>
</x-form-modal>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addKelasBtn = document.getElementById('add-kelas-input');
    const additionalInputsContainer = document.getElementById('additional-kelas-inputs');
    const dosenOptionRadios = document.querySelectorAll('.dosen-option-add');
    const dosenSections = document.querySelectorAll('.dosen-section-add');
    const dosenSameSection = document.getElementById('dosen-same-section-add');
    const dosenDifferentSection = document.getElementById('dosen-different-section-add');
    const kelasDosenContainer = document.getElementById('kelas-dosen-container-add');
    const dosens = @json($availableDosens);

    // Add kelas input functionality
    addKelasBtn.addEventListener('click', function() {
        const newInput = document.createElement('div');
        newInput.className = 'flex gap-2';
        newInput.innerHTML = `
            <input type="text"
                   name="kelasNames[]"
                   class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block p-2.5"
                   placeholder="Contoh: A, B, C, atau 1, 2, 3"
                   required>
            <button type="button" 
                    class="remove-kelas-input bg-red-600 hover:bg-red-700 text-white px-3 py-2.5 rounded-lg text-sm">
                ×
            </button>
        `;
        
        additionalInputsContainer.appendChild(newInput);
        
        // Add remove functionality
        newInput.querySelector('.remove-kelas-input').addEventListener('click', function() {
            newInput.remove();
            updateDosenPerKelas();
        });

        // Add change event to update dosen per kelas
        newInput.querySelector('input').addEventListener('input', updateDosenPerKelas);
    });

    // Dosen option change handler
    dosenOptionRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            dosenSections.forEach(section => {
                section.classList.add('hidden');
            });

            if (this.value === 'same') {
                dosenSameSection.classList.remove('hidden');
            } else {
                dosenDifferentSection.classList.remove('hidden');
                updateDosenPerKelas();
            }
        });
    });

    // Update dosen per kelas when kelas inputs change
    function updateDosenPerKelas() {
        if (document.getElementById('dosen-different-add').checked) {
            const kelasInputs = document.querySelectorAll('input[name="kelasNames[]"]');
            const selectedKelas = Array.from(kelasInputs)
                .map(input => input.value.trim())
                .filter(value => value !== '');

            kelasDosenContainer.innerHTML = '';

            selectedKelas.forEach(kelasName => {
                if (kelasName) {
                    const kelasSection = document.createElement('div');
                    kelasSection.className = 'p-3 border border-gray-200 rounded-lg';
                    kelasSection.innerHTML = `
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Dosen untuk Kelas ${kelasName}</h4>
                        <div class="grid grid-cols-1 gap-2">
                            ${dosens.map(dosen => `
                                <label class="flex items-center">
                                    <input type="checkbox"
                                           name="dosenPerKelas[${kelasName}][]"
                                           value="${dosen.id}"
                                           class="rounded border-gray-300 text-amber-600 shadow-sm focus:border-amber-300 focus:ring focus:ring-amber-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">${dosen.nama} (${dosen.nip})</span>
                                </label>
                            `).join('')}
                        </div>
                    `;
                    kelasDosenContainer.appendChild(kelasSection);
                }
            });
        }
    }

    // Add change event to first kelas input
    document.querySelector('input[name="kelasNames[]"]').addEventListener('input', updateDosenPerKelas);
});
</script>

@endsection
