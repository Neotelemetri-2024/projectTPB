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
                                {{ $tahunAjaran->tahun }} - {{ $tahunAjaran->periode }}
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
                                {{ $mataKuliah->namaMatkul }} ({{ $mataKuliah->kodeMatkul }})
                            </option>
                            @endforeach
                        </select>
                        <div id="custom-mk-select" class="relative">
                            <button type="button" id="custom-mk-button" class="bg-gray-50 border {{ $errors->has('mataKuliahId') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full text-left p-2.5 flex items-center justify-between">
                                <span id="custom-mk-label" class="truncate">Pilih Mata Kuliah</span>
                                <svg class="w-4 h-4 text-gray-500 ml-2 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
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

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Kelas <span class="text-red-500">*</span></label>
                    <div id="kelas-container" class="space-y-3">
                        <div class="kelas-item border border-gray-200 rounded-lg p-4">
                            <div class="flex gap-2 items-center">
                                <input type="text"
                                    name="kelasNames[]"
                                    class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block p-2.5"
                                    placeholder="Nama kelas (A, B, C, dll)"
                                    required>
                                <button type="button"
                                    id="add-kelas-input"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2.5 rounded-lg text-sm">+
                                </button>
                            </div>
                            <div class="dosen-per-kelas-section mt-3 hidden">
                                <!-- Dosen per kelas akan ditampilkan di sini -->
                            </div>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Masukkan nama kelas (huruf atau angka). Klik + untuk menambah kelas lain.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Dosen Pengampu <span class="text-red-500">*</span>
                    </label>

                    <!-- Opsi Dosen Pengampu -->
                    <div class="mb-4">
                        <label class="flex items-center mb-2">
                            <input type="radio"
                                name="dosenOption"
                                value="same"
                                id="dosen-same"
                                class="mr-2 text-amber-600"
                                checked>
                            <span class="text-sm text-gray-700">Dosen pengampu sama untuk semua kelas</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio"
                                name="dosenOption"
                                value="different"
                                id="dosen-different"
                                class="mr-2 text-amber-600">
                            <span class="text-sm text-gray-700">Dosen pengampu berbeda per kelas</span>
                        </label>
                    </div>

                    <!-- Dosen untuk semua kelas (default) -->
                    <div id="dosen-same-section">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Dosen untuk Semua Kelas</label>
                        <div id="dosen-same-multiselect"></div>
                        <div id="dosen-same-hidden"></div>
                    </div>

                    <!-- Dosen per kelas (hidden by default) -->
                    <div id="dosen-different-section" class="hidden">
                        <p class="text-sm text-gray-600 mb-4">Pilih dosen untuk setiap kelas di atas.</p>
                    </div>

                    @error('dosenIds')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('dosenPerKelas')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
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
        const dosens = @json($dosens);
        const mataKuliahSelect = document.getElementById('mataKuliahId');

        // Add kelas input functionality
        addKelasBtn.addEventListener('click', function() {
            const kelasContainer = document.getElementById('kelas-container');
            const newKelasItem = document.createElement('div');
            newKelasItem.className = 'kelas-item border border-gray-200 rounded-lg p-4';
            newKelasItem.innerHTML = `
                <div class="flex gap-2 items-center">
                    <input type="text"
                           name="kelasNames[]"
                           class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block p-2.5"
                           placeholder="Nama kelas (A, B, C, dll)"
                           required>
                    <button type="button" 
                            class="remove-kelas-input bg-red-600 hover:bg-red-700 text-white px-3 py-2.5 rounded-lg text-sm">
                        ×
                    </button>
                </div>
                <div class="dosen-per-kelas-section mt-3 hidden">
                    <!-- Dosen per kelas akan ditampilkan di sini -->
                </div>
            `;

            kelasContainer.appendChild(newKelasItem);

            // Add event listener for input change
            const input = newKelasItem.querySelector('input[name="kelasNames[]"]');
            input.addEventListener('input', function() {
                updateDosenPerKelas();
            });

            // Add remove functionality
            newKelasItem.querySelector('.remove-kelas-input').addEventListener('click', function() {
                newKelasItem.remove();
                updateDosenPerKelas();
            });
        });

        // Dosen option change handler
        const dosenOptionRadios = document.querySelectorAll('input[name="dosenOption"]');
        const dosenSameSection = document.getElementById('dosen-same-section');
        const dosenDifferentSection = document.getElementById('dosen-different-section');

        dosenOptionRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'same') {
                    dosenSameSection.classList.remove('hidden');
                    dosenDifferentSection.classList.add('hidden');
                } else {
                    dosenSameSection.classList.add('hidden');
                    dosenDifferentSection.classList.remove('hidden');
                    updateDosenPerKelas();
                }
            });
        });

        // Function to update dosen per kelas
        function updateDosenPerKelas() {
            const dosenOptionDifferent = document.getElementById('dosen-different').checked;
            if (!dosenOptionDifferent) return;

            // Handle all kelas items
            const kelasItems = document.querySelectorAll('.kelas-item');
            kelasItems.forEach(kelasItem => {
                const kelasInput = kelasItem.querySelector('input[name="kelasNames[]"]');
                if (!kelasInput) return;

                const kelasValue = kelasInput.value.trim();
                const dosenSection = kelasItem.querySelector('.dosen-per-kelas-section');

                // Clear existing content
                if (dosenSection) {
                    dosenSection.innerHTML = '';
                }

                if (kelasValue !== '') {
                    // Show dosen section
                    dosenSection.classList.remove('hidden');

                    // Create dosen section content
                    dosenSection.innerHTML = `
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Dosen untuk Kelas ${kelasValue}</label>
                        <div id="dosen-per-kelas-${kelasValue}-multiselect"></div>
                        <div id="dosen-per-kelas-${kelasValue}-hidden"></div>
                    `;

                    // Initialize multi-select for this kelas
                    const mount = dosenSection.querySelector(`#dosen-per-kelas-${kelasValue}-multiselect`);
                    const hidden = dosenSection.querySelector(`#dosen-per-kelas-${kelasValue}-hidden`);

                    if (mount && hidden) {
                        createSearchableMultiSelect({
                            mountEl: mount,
                            placeholder: 'Pilih dosen...',
                            options: dosens.map(d => ({
                                value: String(d.id),
                                label: d.nama
                            })),
                            initialSelected: [],
                            onChange: (values) => {
                                hidden.innerHTML = '';
                                values.forEach(v => {
                                    const input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = `dosenPerKelas[${kelasValue}][]`;
                                    input.value = v;
                                    hidden.appendChild(input);
                                });
                            }
                        });
                    }
                } else {
                    // Hide dosen section if kelas value is empty
                    dosenSection.classList.add('hidden');
                }
            });
        }

        // Add event listeners to existing kelas inputs
        document.querySelectorAll('input[name="kelasNames[]"]').forEach(input => {
            input.addEventListener('input', function() {
                updateDosenPerKelas();
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

        // =========================
        // Multi-select component JS
        // =========================
        function createSearchableMultiSelect({
            mountEl,
            placeholder,
            options,
            initialSelected = [],
            onChange
        }) {
            const state = {
                isOpen: false,
                selected: new Set(initialSelected.map(String)),
                query: ''
            };

            const wrapper = document.createElement('div');
            wrapper.className = 'relative';

            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full text-left p-2.5 flex items-center justify-between';

            const label = document.createElement('span');
            label.className = 'truncate';
            label.textContent = placeholder;

            const chevron = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            chevron.setAttribute('class', 'w-4 h-4 text-gray-500 ml-2 flex-shrink-0');
            chevron.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
            chevron.setAttribute('fill', 'none');
            chevron.setAttribute('viewBox', '0 0 24 24');
            chevron.setAttribute('stroke', 'currentColor');
            chevron.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />';

            button.appendChild(label);
            button.appendChild(chevron);

            const panel = document.createElement('div');
            panel.className = 'absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg hidden';

            const searchWrap = document.createElement('div');
            searchWrap.className = 'p-2 border-b border-gray-100';
            const search = document.createElement('input');
            search.type = 'text';
            search.placeholder = 'Cari...';
            search.className = 'w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-amber-500 focus:border-amber-500 p-2';
            searchWrap.appendChild(search);

            const list = document.createElement('ul');
            list.className = 'max-h-56 overflow-auto py-1';

            panel.appendChild(searchWrap);
            panel.appendChild(list);

            wrapper.appendChild(button);
            wrapper.appendChild(panel);
            mountEl.appendChild(wrapper);

            const renderLabel = () => {
                if (state.selected.size === 0) {
                    label.textContent = placeholder;
                    return;
                }
                const selectedLabels = options
                    .filter(o => state.selected.has(String(o.value)))
                    .map(o => o.label);
                label.textContent = selectedLabels.length === 1 ?
                    selectedLabels[0] :
                    `${selectedLabels.length} dipilih`;
            };

            const buildOptions = () => {
                list.innerHTML = '';
                const q = state.query.toLowerCase();
                const filtered = options.filter(o => o.label.toLowerCase().includes(q));
                if (filtered.length === 0) {
                    const li = document.createElement('li');
                    li.className = 'px-3 py-2 text-sm text-gray-500';
                    li.textContent = 'Tidak ada hasil';
                    list.appendChild(li);
                    return;
                }
                filtered.forEach(o => {
                    const li = document.createElement('li');
                    li.className = 'px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer flex items-center';
                    const checkbox = document.createElement('span');
                    checkbox.className = 'inline-block w-4 h-4 mr-2 rounded border border-gray-300 ' + (state.selected.has(String(o.value)) ? 'bg-amber-500 border-amber-500' : 'bg-white');
                    const text = document.createElement('span');
                    text.textContent = o.label;
                    li.appendChild(checkbox);
                    li.appendChild(text);
                    li.addEventListener('click', () => {
                        const key = String(o.value);
                        if (state.selected.has(key)) {
                            state.selected.delete(key);
                        } else {
                            state.selected.add(key);
                        }
                        renderLabel();
                        buildOptions();
                        if (typeof onChange === 'function') {
                            onChange(Array.from(state.selected));
                        }
                    });
                    list.appendChild(li);
                });
            };

            const open = () => {
                panel.classList.remove('hidden');
                setTimeout(() => search.focus(), 0);
            };
            const close = () => panel.classList.add('hidden');

            button.addEventListener('click', () => {
                if (panel.classList.contains('hidden')) {
                    open();
                    buildOptions();
                } else {
                    close();
                }
            });

            search.addEventListener('input', () => {
                state.query = search.value;
                buildOptions();
            });

            document.addEventListener('click', (e) => {
                if (!wrapper.contains(e.target)) close();
            });

            // Initial render
            renderLabel();
            if (typeof onChange === 'function') onChange(Array.from(state.selected));

            return {
                getSelected: () => Array.from(state.selected)
            };
        }

        // Init multi-select for Dosen (Same for all classes)
        (function initDosenSameMultiSelect() {
            const mount = document.getElementById('dosen-same-multiselect');
            const hidden = document.getElementById('dosen-same-hidden');
            if (!mount || !hidden) return;

            const initial = [];
            createSearchableMultiSelect({
                mountEl: mount,
                placeholder: 'Pilih dosen...',
                options: dosens.map(d => ({
                    value: String(d.id),
                    label: d.nama
                })),
                initialSelected: initial,
                onChange: (values) => {
                    hidden.innerHTML = '';
                    values.forEach(v => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'dosenIds[]';
                        input.value = v;
                        hidden.appendChild(input);
                    });
                }
            });
        })();
    });
</script>
@endsection