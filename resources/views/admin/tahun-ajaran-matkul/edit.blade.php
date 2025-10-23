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
                    <div id="kelas-container" class="space-y-3">
                        @foreach($tahunAjaranMatkul->kelas as $index => $kelas)
                        <div class="kelas-item border border-gray-200 rounded-lg p-4">
                            <div class="flex gap-2 items-center">
                                <input type="text"
                                    name="kelasNames[]"
                                    class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block p-2.5"
                                    value="{{ old('kelasNames.' . $index, $kelas->namaKelas) }}"
                                    placeholder="Nama kelas (A, B, C, dll)"
                                    required>
                                @if($index > 0)
                                <button type="button"
                                    class="remove-kelas-input bg-red-600 hover:bg-red-700 text-white px-3 py-2.5 rounded-lg text-sm">
                                    ×
                                </button>
                                @endif
                            </div>
                            <div class="dosen-per-kelas-section mt-3 hidden">
                                <!-- Dosen per kelas akan ditampilkan di sini -->
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button"
                        id="add-kelas-input"
                        class="mt-3 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Kelas
                    </button>
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
        const dosens = @json($dosens);
        const tahunAjaranMatkul = @json($tahunAjaranMatkul);

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
                updateDosenPerKelasForNewKelas();
            });

            // Add remove functionality
            newKelasItem.querySelector('.remove-kelas-input').addEventListener('click', function() {
                newKelasItem.remove();
                updateDosenPerKelasForNewKelas();
            });
        });

        // Add remove functionality to existing remove buttons
        document.querySelectorAll('.remove-kelas-input').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.kelas-item').remove();
                updateDosenPerKelasForNewKelas();
            });
        });

        // Add event listeners to existing kelas inputs
        document.querySelectorAll('input[name="kelasNames[]"]').forEach(input => {
            input.addEventListener('input', function() {
                updateDosenPerKelasForNewKelas();
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
        dosenSameRadio.addEventListener('change', function() {
            toggleDosenSections();
            updateDosenPerKelasForNewKelas();
        });
        dosenDifferentRadio.addEventListener('change', function() {
            toggleDosenSections();
            updateDosenPerKelasForNewKelas();
        });

        // Initialize on page load
        toggleDosenSections();
        updateDosenPerKelasForNewKelas();

        // Function to update dosen per kelas for all kelas (existing and new)
        function updateDosenPerKelasForNewKelas() {
            const dosenOptionDifferent = document.getElementById('dosen_different').checked;
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

            // Get existing dosen IDs from all classes
            const existingDosenIds = tahunAjaranMatkul.kelas
                .flatMap(kelas => kelas.dosen_pengampu_kelas)
                .map(dosen => String(dosen.dosen_id))
                .filter((value, index, self) => self.indexOf(value) === index);

            const initial = existingDosenIds;
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

        // Init multi-select for Dosen per Kelas
        (function initDosenPerKelasMultiSelect() {
            tahunAjaranMatkul.kelas.forEach(kelas => {
                const mount = document.getElementById(`dosen-per-kelas-${kelas.nama_kelas}-multiselect`);
                const hidden = document.getElementById(`dosen-per-kelas-${kelas.nama_kelas}-hidden`);
                if (!mount || !hidden) return;

                const existingDosenIds = kelas.dosen_pengampu_kelas.map(dosen => String(dosen.dosen_id));
                const initial = existingDosenIds;

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
                            input.name = `dosenPerKelas[${kelas.nama_kelas}][]`;
                            input.value = v;
                            hidden.appendChild(input);
                        });
                    }
                });
            });
        })();
    });
</script>

@endsection