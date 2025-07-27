@extends('layouts.main')
@section('title', 'Tambah Mata Kuliah ke Tahun Ajaran')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Tambah Mata Kuliah ke Tahun Ajaran</h2>
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

            <form action="{{ route('admin.tahun-ajaran-matkul.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tahunAjaranId" class="block text-sm font-medium text-gray-700 mb-2">
                            Tahun Ajaran <span class="text-red-500">*</span>
                        </label>
                        <select name="tahunAjaranId" id="tahunAjaranId"
                                class="bg-gray-50 border {{ $errors->has('tahunAjaranId') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5"
                                required>
                            <option value="">Pilih Tahun Ajaran</option>
                            @foreach($tahunAjarans as $tahunAjaran)
                                <option value="{{ $tahunAjaran->id }}" {{ old('tahunAjaranId') == $tahunAjaran->id ? 'selected' : '' }}>
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
                                <option value="{{ $mataKuliah->id }}" {{ old('mataKuliahId') == $mataKuliah->id ? 'selected' : '' }}>
                                    {{ $mataKuliah->namaMatkul }} ({{ $mataKuliah->kodeMatkul }})
                                </option>
                            @endforeach
                        </select>
                        @error('mataKuliahId')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Kelas <span class="text-red-500">*</span></label>
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

                <!-- Dosen Pengampu Section -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Dosen Pengampu <span class="text-red-500">*</span>
                    </label>
                    
                    <!-- Dosen Option -->
                    <div class="mb-4">
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="dosenOption" value="same" id="dosen-same" class="dosen-option rounded border-gray-300 text-amber-600 focus:ring-amber-500" checked>
                                <span class="ml-2 text-sm text-gray-700">Dosen sama untuk semua kelas</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="dosenOption" value="different" id="dosen-different" class="dosen-option rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                                <span class="ml-2 text-sm text-gray-700">Dosen berbeda per kelas</span>
                            </label>
                        </div>
                    </div>

                    <!-- Dosen Same for All Classes -->
                    <div id="dosen-same-section" class="dosen-section">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Dosen untuk Semua Kelas</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($dosens as $dosen)
                                <div class="flex items-center">
                                    <input type="checkbox"
                                           name="dosenIds[]"
                                           value="{{ $dosen->id }}"
                                           id="dosen_{{ $dosen->id }}"
                                           class="dosen-checkbox rounded border-gray-300 text-amber-600 shadow-sm focus:border-amber-300 focus:ring focus:ring-amber-200 focus:ring-opacity-50 {{ $errors->has('dosenIds') ? 'border-red-500' : '' }}"
                                           {{ in_array($dosen->id, old('dosenIds', [])) ? 'checked' : '' }}>
                                    <label for="dosen_{{ $dosen->id }}" class="ml-2 text-sm text-gray-700">
                                        {{ $dosen->nama }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Dosen Different per Class -->
                    <div id="dosen-different-section" class="dosen-section hidden">
                        <div id="kelas-dosen-container">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.tahun-ajaran-matkul.index') }}"
                       class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        Simpan
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
    const dosenOptionRadios = document.querySelectorAll('.dosen-option');
    const dosenSections = document.querySelectorAll('.dosen-section');
    const dosenSameSection = document.getElementById('dosen-same-section');
    const dosenDifferentSection = document.getElementById('dosen-different-section');
    const kelasDosenContainer = document.getElementById('kelas-dosen-container');
    const dosens = @json($dosens);

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
        if (document.getElementById('dosen-different').checked) {
            const kelasInputs = document.querySelectorAll('input[name="kelasNames[]"]');
            const selectedKelas = Array.from(kelasInputs)
                .map(input => input.value.trim())
                .filter(value => value !== '');

            kelasDosenContainer.innerHTML = '';

            selectedKelas.forEach(kelasName => {
                if (kelasName) {
                    const kelasSection = document.createElement('div');
                    kelasSection.className = 'mb-4 p-3 border border-gray-200 rounded-lg';
                    kelasSection.innerHTML = `
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Dosen untuk Kelas ${kelasName}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            ${dosens.map(dosen => `
                                <label class="flex items-center">
                                    <input type="checkbox"
                                           name="dosenPerKelas[${kelasName}][]"
                                           value="${dosen.id}"
                                           class="rounded border-gray-300 text-amber-600 shadow-sm focus:border-amber-300 focus:ring focus:ring-amber-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">${dosen.nama}</span>
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

    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const kelasInputs = document.querySelectorAll('input[name="kelasNames[]"]');
        const selectedKelas = Array.from(kelasInputs)
            .map(input => input.value.trim())
            .filter(value => value !== '');

        if (selectedKelas.length === 0) {
            e.preventDefault();
            alert('Pilih minimal satu kelas');
            return;
        }

        const dosenOption = document.querySelector('input[name="dosenOption"]:checked');
        if (!dosenOption) {
            e.preventDefault();
            alert('Pilih opsi dosen pengampu');
            return;
        }

        if (dosenOption.value === 'same') {
            const selectedDosen = document.querySelectorAll('input[name="dosenIds[]"]:checked');
            if (selectedDosen.length === 0) {
                e.preventDefault();
                alert('Pilih minimal satu dosen pengampu');
                return;
            }
        } else {
            const selectedKelas = Array.from(kelasInputs)
                .map(input => input.value.trim())
                .filter(value => value !== '');
            
            let hasDosen = false;
            selectedKelas.forEach(kelasName => {
                const dosenCheckboxes = document.querySelectorAll(`input[name="dosenPerKelas[${kelasName}][]"]:checked`);
                if (dosenCheckboxes.length > 0) {
                    hasDosen = true;
                }
            });

            if (!hasDosen) {
                e.preventDefault();
                alert('Pilih minimal satu dosen pengampu untuk setiap kelas');
                return;
            }
        }
    });
});
</script>
@endsection
