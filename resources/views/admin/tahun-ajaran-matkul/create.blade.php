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
                                class="hidden bg-gray-50 border {{ $errors->has('mataKuliahId') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5"
                                required>
                            <option value="">Pilih Mata Kuliah</option>
                                @foreach($mataKuliahs as $mataKuliah)
                                    <option value="{{ $mataKuliah->id }}" {{ old('mataKuliahId') == $mataKuliah->id ? 'selected' : '' }}>
                                        {{ $mataKuliah->namaMatkul }} ({{ $mataKuliah->kodeMatkul }}-{{ $mataKuliah->kurikulum }})
                                    </option>
                                @endforeach
                        </select>
                        <div id="custom-mk-select" class="relative">
                            <button type="button" id="custom-mk-button" class="bg-gray-50 border {{ $errors->has('mataKuliahId') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full text-left p-2.5 flex items-center justify-between">
                                <span id="custom-mk-label" class="truncate">Pilih Mata Kuliah</span>
                                <svg class="w-4 h-4 text-gray-500 ml-2 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <div id="custom-mk-panel" class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg hidden">
                                <div class="p-2 border-b border-gray-100">
                                    <input id="custom-mk-search" type="text" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-amber-500 focus:border-amber-500 p-2" placeholder="Cari mata kuliah...">
                                </div>
                                <ul id="custom-mk-options" class="max-h-56 overflow-auto py-1"></ul>
                            </div>
                        </div>
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
                                <option value="{{ $i }}" {{ old('semester') == $i ? 'selected' : '' }}>
                                    Semester {{ $i }}
                                </option>
                            @endfor
                        </select>
                        @error('semester')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Kelas Section -->
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
                        
                        <!-- Search input -->
                        <div class="mb-3">
                            <input id="dosen-search-same" type="text" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 p-2.5" placeholder="Cari dosen...">
                        </div>
                        
                        <!-- Dosen checkboxes -->
                        <div id="dosen-checkboxes-same" class="max-h-48 overflow-auto space-y-2 border border-gray-200 rounded-lg p-3 bg-white">
                            @foreach($dosens as $dosen)
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                                    <input type="checkbox"
                                           name="dosenIds[]"
                                           value="{{ $dosen->id }}"
                                           class="dosen-checkbox rounded border-gray-300 text-amber-600 shadow-sm focus:border-amber-300 focus:ring focus:ring-amber-200 focus:ring-opacity-50"
                                           {{ in_array($dosen->id, old('dosenIds', [])) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">{{ $dosen->nama }}</span>
                                </label>
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
    const mataKuliahSelect = document.getElementById('mataKuliahId');

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

    // Custom searchable dropdown for Mata Kuliah (tanpa field baru di form)
    (function initCustomMataKuliahSelect() {
        const button = document.getElementById('custom-mk-button');
        const label = document.getElementById('custom-mk-label');
        const panel = document.getElementById('custom-mk-panel');
        const search = document.getElementById('custom-mk-search');
        const list = document.getElementById('custom-mk-options');

        if (!button || !panel || !search || !list || !mataKuliahSelect) return;

        const closePanel = () => {
            panel.classList.add('hidden');
        };

        const openPanel = () => {
            panel.classList.remove('hidden');
            // focus search on open
            setTimeout(() => search.focus(), 0);
        };

        const setSelected = (value, text) => {
            mataKuliahSelect.value = value;
            label.textContent = text || 'Pilih Mata Kuliah';
        };

        const buildOptions = () => {
            list.innerHTML = '';
            const currentQuery = search.value.trim().toLowerCase();
            const options = Array.from(mataKuliahSelect.options).slice(1); // skip placeholder

            const fragment = document.createDocumentFragment();
            const filtered = options.filter(opt => opt.text.toLowerCase().includes(currentQuery));

            if (filtered.length === 0) {
                const empty = document.createElement('li');
                empty.className = 'px-3 py-2 text-sm text-gray-500';
                empty.textContent = 'Tidak ada hasil';
                fragment.appendChild(empty);
            } else {
                filtered.forEach(opt => {
                    const li = document.createElement('li');
                    li.className = 'px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer';
                    li.textContent = opt.text;
                    li.dataset.value = opt.value;
                    li.addEventListener('click', () => {
                        setSelected(opt.value, opt.text);
                        closePanel();
                    });
                    fragment.appendChild(li);
                });
            }

            list.appendChild(fragment);
        };

        // Initialize label with current selected or placeholder
        const selectedOption = mataKuliahSelect.options[mataKuliahSelect.selectedIndex];
        label.textContent = selectedOption && selectedOption.value !== '' ? selectedOption.text : 'Pilih Mata Kuliah';

        // Events
        button.addEventListener('click', () => {
            if (panel.classList.contains('hidden')) {
                openPanel();
                buildOptions();
            } else {
                closePanel();
            }
        });

        search.addEventListener('input', () => buildOptions());

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            const wrapper = document.getElementById('custom-mk-select');
            if (!wrapper.contains(e.target)) {
                closePanel();
            }
        });

        // Sync label when value changed programmatically
        mataKuliahSelect.addEventListener('change', () => {
            const sel = mataKuliahSelect.options[mataKuliahSelect.selectedIndex];
            label.textContent = sel && sel.value !== '' ? sel.text : 'Pilih Mata Kuliah';
        });
    })();

    // Initialize search functionality for dosen same for all classes
    (function initDosenSearchSame() {
        const searchInput = document.getElementById('dosen-search-same');
        const checkboxesContainer = document.getElementById('dosen-checkboxes-same');

        if (!searchInput || !checkboxesContainer) return;

        // Search functionality - hide/show instead of replacing HTML
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.trim().toLowerCase();
            const labels = checkboxesContainer.querySelectorAll('label');
            
            if (searchTerm === '') {
                // Show all labels
                labels.forEach(label => {
                    label.style.display = 'flex';
                });
            } else {
                // Filter labels based on search term
                labels.forEach(label => {
                    const dosenName = label.querySelector('span').textContent.toLowerCase();
                    if (dosenName.includes(searchTerm)) {
                        label.style.display = 'flex';
                    } else {
                        label.style.display = 'none';
                    }
                });
            }
        });
    })();

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
                    kelasSection.className = 'mb-4 p-4 border border-gray-200 rounded-lg bg-gray-50';
                    kelasSection.innerHTML = `
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Dosen untuk Kelas ${kelasName}</h4>
                        
                        <!-- Search input -->
                        <div class="mb-3">
                            <input id="dosen-search-${kelasName}" type="text" class="w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 p-2.5" placeholder="Cari dosen...">
                        </div>
                        
                        <!-- Dosen checkboxes -->
                        <div id="dosen-checkboxes-${kelasName}" class="max-h-48 overflow-auto space-y-2 border border-gray-200 rounded-lg p-3 bg-white">
                            ${dosens.map(dosen => `
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                                    <input type="checkbox"
                                           name="dosenPerKelas[${kelasName}][]"
                                           value="${dosen.id}"
                                           class="dosen-checkbox rounded border-gray-300 text-amber-600 shadow-sm focus:border-amber-300 focus:ring focus:ring-amber-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">${dosen.nama}</span>
                                </label>
                            `).join('')}
                        </div>
                    `;
                    kelasDosenContainer.appendChild(kelasSection);
                    
                    // Initialize search functionality for this class
                    initDosenSearchForKelas(kelasName);
                }
            });
        }
    }

    // Initialize search functionality for specific kelas
    function initDosenSearchForKelas(kelasName) {
        const searchInput = document.getElementById(`dosen-search-${kelasName}`);
        const checkboxesContainer = document.getElementById(`dosen-checkboxes-${kelasName}`);

        if (!searchInput || !checkboxesContainer) return;

        // Search functionality - hide/show instead of replacing HTML
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.trim().toLowerCase();
            const labels = checkboxesContainer.querySelectorAll('label');
            
            if (searchTerm === '') {
                // Show all labels
                labels.forEach(label => {
                    label.style.display = 'flex';
                });
            } else {
                // Filter labels based on search term
                labels.forEach(label => {
                    const dosenName = label.querySelector('span').textContent.toLowerCase();
                    if (dosenName.includes(searchTerm)) {
                        label.style.display = 'flex';
                    } else {
                        label.style.display = 'none';
                    }
                });
            }
        });
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
