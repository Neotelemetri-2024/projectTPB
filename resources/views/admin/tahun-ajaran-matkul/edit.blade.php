@extends('layouts.main')
@section('title', 'Edit Mata Kuliah Tahun Ajaran')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Edit Mata Kuliah Tahun Ajaran</h2>
            </div>
        </div>

        <div class="p-6">
            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.tahun-ajaran-matkul.update', $tahunAjaranMatkul->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="tahunAjaranId" class="block text-sm font-medium text-gray-700 mb-2">
                            Tahun Ajaran <span class="text-red-500">*</span>
                        </label>
                        <select name="tahunAjaranId" id="tahunAjaranId"
                                class="bg-gray-50 border {{ $errors->has('tahunAjaranId') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5"
                                required>
                            <option value="">Pilih Tahun Ajaran</option>
                            @foreach($tahunAjarans as $tahunAjaran)
                                <option value="{{ $tahunAjaran->id }}" {{ old('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId) == $tahunAjaran->id ? 'selected' : '' }}>
                                    {{ $tahunAjaran->tahun }}-{{ $tahunAjaran->periode }}
                                </option>
                            @endforeach
                        </select>
                        @error('tahunAjaranId')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mataKuliahId" class="block text-sm font-medium text-gray-700 mb-2">
                            Mata Kuliah <span class="text-red-500">*</span>
                        </label>
                        <select name="mataKuliahId" id="mataKuliahId"
                                class="bg-gray-50 border {{ $errors->has('mataKuliahId') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5"
                                required>
                            <option value="">Pilih Mata Kuliah</option>
                            @foreach($mataKuliahs as $mataKuliah)
                                <option value="{{ $mataKuliah->id }}" {{ old('mataKuliahId', $tahunAjaranMatkul->mataKuliahId) == $mataKuliah->id ? 'selected' : '' }}>
                                    {{ $mataKuliah->namaMatkul }} ({{ $mataKuliah->kodeMatkul }}-{{ $mataKuliah->kurikulum }})
                                </option>
                            @endforeach
                        </select>
                        @error('mataKuliahId')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="semester" class="block text-sm font-medium text-gray-700 mb-2">
                            Semester <span class="text-red-500">*</span>
                        </label>
                        <select name="semester" id="semester"
                                class="bg-gray-50 border {{ $errors->has('semester') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5"
                                required>
                            <option value="">Pilih Semester</option>
                            @for($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}" {{ old('semester', $tahunAjaranMatkul->semester) == $i ? 'selected' : '' }}>
                                    Semester {{ $i }}
                                </option>
                            @endfor
                        </select>
                        @error('semester')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Kelas <span class="text-red-500">*</span></label>
                    <div class="space-y-2">
                        @foreach($tahunAjaranMatkul->kelas as $index => $kelas)
                            <div class="flex gap-2">
                                <input type="text"
                                       name="kelasNames[]"
                                       class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block p-2.5"
                                       value="{{ old('kelasNames.' . $index, $kelas->namaKelas) }}"
                                       required>
                                @if($index > 0)
                                    <button type="button" 
                                            class="remove-kelas-input bg-red-600 hover:bg-red-700 text-white px-3 py-2.5 rounded-lg text-sm">
                                        ×
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <button type="button" 
                            id="add-kelas-input"
                            class="mt-2 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm">
                        + Tambah Kelas
                    </button>
                    <div id="additional-kelas-inputs" class="mt-2 space-y-2"></div>
                    <p class="mt-1 text-xs text-gray-500">Masukkan nama kelas (huruf atau angka). Klik + untuk menambah kelas lain.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Dosen Pengampu <span class="text-red-500">*</span>
                    </label>
                    
                    <!-- Opsi Dosen Pengampu -->
                    <div class="mb-4">
                        <label class="flex items-center mb-2">
                            <input type="radio" 
                                   name="dosenType" 
                                   value="same" 
                                   id="dosen_same"
                                   class="mr-2 text-amber-600"
                                   {{ old('dosenType', 'same') == 'same' ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700">Dosen pengampu sama untuk semua kelas</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="dosenType" 
                                   value="different" 
                                   id="dosen_different"
                                   class="mr-2 text-amber-600"
                                   {{ old('dosenType', 'same') == 'different' ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700">Dosen pengampu berbeda per kelas</span>
                        </label>
                    </div>

                    <!-- Dosen untuk semua kelas (default) -->
                    <div id="dosen-same-section" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($dosens as $dosen)
                            <div class="flex items-center">
                                <input type="checkbox"
                                       name="dosenIds[]"
                                       value="{{ $dosen->id }}"
                                       id="dosen_{{ $dosen->id }}"
                                       class="rounded border-gray-300 text-amber-600 shadow-sm focus:border-amber-300 focus:ring focus:ring-amber-200 focus:ring-opacity-50 {{ $errors->has('dosenIds') ? 'border-red-500' : '' }}"
                                       {{ in_array($dosen->id, old('dosenIds', $tahunAjaranMatkul->kelas->flatMap->dosenPengampuKelas->pluck('dosenId')->unique()->toArray())) ? 'checked' : '' }}>
                                <label for="dosen_{{ $dosen->id }}" class="ml-2 text-sm text-gray-700">
                                    {{ $dosen->nama }}
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <!-- Dosen per kelas (hidden by default) -->
                    <div id="dosen-different-section" class="hidden space-y-4">
                        @foreach($tahunAjaranMatkul->kelas as $index => $kelas)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-2">Kelas {{ $kelas->namaKelas }}</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach($dosens as $dosen)
                                        <div class="flex items-center">
                                            <input type="checkbox"
                                                   name="dosenPerKelas[{{ $kelas->id }}][]"
                                                   value="{{ $dosen->id }}"
                                                   id="dosen_{{ $kelas->id }}_{{ $dosen->id }}"
                                                   class="rounded border-gray-300 text-amber-600 shadow-sm focus:border-amber-300 focus:ring focus:ring-amber-200 focus:ring-opacity-50"
                                                   {{ in_array($dosen->id, old('dosenPerKelas.' . $kelas->id, $kelas->dosenPengampuKelas->pluck('dosenId')->toArray())) ? 'checked' : '' }}>
                                            <label for="dosen_{{ $kelas->id }}_{{ $dosen->id }}" class="ml-2 text-sm text-gray-700">
                                                {{ $dosen->nama }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @error('dosenIds')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('dosenPerKelas')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-between pt-6 border-t border-gray-200">
                    <a href="{{ request('back_url', route('admin.tahun-ajaran-matkul.index')) }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                    <button type="submit"
                            class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addKelasBtn = document.getElementById('add-kelas-input');
    const additionalInputsContainer = document.getElementById('additional-kelas-inputs');

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
        });
    });

    // Add remove functionality to existing remove buttons
    document.querySelectorAll('.remove-kelas-input').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.flex').remove();
        });
    });

    // Dosen pengampu toggle functionality
    const dosenSameRadio = document.getElementById('dosen_same');
    const dosenDifferentRadio = document.getElementById('dosen_different');
    const dosenSameSection = document.getElementById('dosen-same-section');
    const dosenDifferentSection = document.getElementById('dosen-different-section');

    function toggleDosenSections() {
        if (dosenSameRadio.checked) {
            dosenSameSection.classList.remove('hidden');
            dosenDifferentSection.classList.add('hidden');
        } else {
            dosenSameSection.classList.add('hidden');
            dosenDifferentSection.classList.remove('hidden');
        }
    }

    // Add event listeners
    dosenSameRadio.addEventListener('change', toggleDosenSections);
    dosenDifferentRadio.addEventListener('change', toggleDosenSections);

    // Initialize on page load
    toggleDosenSections();
});
</script>

@endsection
