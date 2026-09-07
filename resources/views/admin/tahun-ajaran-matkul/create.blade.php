@extends('layouts.main')
@section('title', 'Tambah Mata Kuliah ke Tahun Ajaran')

@section('content')
<div class="p-4 md:p-6 space-y-4">
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200">
            <h1 class="text-xl font-semibold text-gray-900 tracking-tight">Tambah Mata Kuliah ke Tahun Ajaran</h1>
            <p class="text-sm text-gray-500 mt-1">Isi informasi mata kuliah, dosen, dan kelas.</p>
        </div>

        <div class="p-5 space-y-4">
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md" role="alert">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('admin.tahun-ajaran-matkul.store') }}" method="POST" class="space-y-4" id="createForm">
                @csrf

                {{-- ============================== --}}
                {{-- STEP 1: Informasi Mata Kuliah --}}
                {{-- ============================== --}}
                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                        <span class="flex items-center justify-center w-6 h-6 bg-amber-600 text-white text-xs font-bold rounded-full mr-2">1</span>
                        Informasi Mata Kuliah
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="tahunAjaranId" class="block text-sm font-medium text-gray-700 mb-2">
                                Tahun Ajaran <span class="text-red-500">*</span>
                            </label>
                            <select name="tahunAjaranId" id="tahunAjaranId"
                                class="bg-white border {{ $errors->has('tahunAjaranId') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5"
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
                                class="hidden bg-white border {{ $errors->has('mataKuliahId') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5"
                                required>
                                <option value="">Pilih Mata Kuliah</option>
                                @foreach($mataKuliahs as $mataKuliah)
                                <option value="{{ $mataKuliah->id }}" {{ old('mataKuliahId') == $mataKuliah->id ? 'selected' : '' }}>
                                    {{ $mataKuliah->namaMatkul }} ({{ $mataKuliah->kodeMatkul }})
                                </option>
                                @endforeach
                            </select>
                            <div id="custom-mk-select" class="relative">
                                <button type="button" id="custom-mk-button" class="bg-white border {{ $errors->has('mataKuliahId') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full text-left p-2.5 flex items-center justify-between">
                                    <span id="custom-mk-label" class="truncate">Pilih Mata Kuliah</span>
                                    <svg class="w-4 h-4 text-gray-500 ml-2 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div id="custom-mk-panel" class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-md hidden">
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
                                class="bg-white border {{ $errors->has('semester') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5"
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
                </div>

                {{-- ============================== --}}
                {{-- STEP 2: Dosen Pengampu --}}
                {{-- ============================== --}}
                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                        <span class="flex items-center justify-center w-6 h-6 bg-amber-600 text-white text-xs font-bold rounded-full mr-2">2</span>
                        Dosen Pengampu
                    </h3>
                    <div>
                        {{-- Radio options --}}
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-3">Bagaimana pengaturan dosen pengampu untuk kelas?</p>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <label class="flex items-center px-4 py-3 bg-white border border-gray-200 rounded-md cursor-pointer hover:bg-amber-50 hover:border-amber-300 transition-colors has-[:checked]:bg-amber-50 has-[:checked]:border-amber-500" id="label-dosen-same">
                                    <input type="radio"
                                        name="dosenOption"
                                        value="same"
                                        id="dosen-same"
                                        class="mr-3 text-amber-600 focus:ring-amber-500">
                                    <div>
                                        <span class="text-sm font-medium text-gray-700">Dosen sama untuk semua kelas</span>
                                        <p class="text-xs text-gray-500">Semua kelas diampu oleh dosen yang sama</p>
                                    </div>
                                </label>
                                <label class="flex items-center px-4 py-3 bg-white border border-gray-200 rounded-md cursor-pointer hover:bg-amber-50 hover:border-amber-300 transition-colors has-[:checked]:bg-amber-50 has-[:checked]:border-amber-500" id="label-dosen-different">
                                    <input type="radio"
                                        name="dosenOption"
                                        value="different"
                                        id="dosen-different"
                                        class="mr-3 text-amber-600 focus:ring-amber-500">
                                    <div>
                                        <span class="text-sm font-medium text-gray-700">Dosen berbeda setiap kelas</span>
                                        <p class="text-xs text-gray-500">Setiap kelas memiliki dosen pengampu tersendiri</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Dosen for all classes (shown when "same" selected) --}}
                        <div id="dosen-same-section" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Dosen untuk Semua Kelas</label>
                            <div id="dosen-same-multiselect"></div>
                            <div id="dosen-same-hidden"></div>
                            {{-- Tags area for showing selected dosen --}}
                            <div id="dosen-same-tags" class="flex flex-wrap gap-2 mt-3"></div>
                        </div>

                        {{-- Info when "different" selected --}}
                        <div id="dosen-different-section" class="hidden">
                            <div class="flex items-center p-3 bg-gray-50 border border-gray-200 rounded-md">
                                <svg class="w-4 h-4 text-gray-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm text-gray-700">Pilih dosen untuk setiap kelas pada bagian input kelas di bawah.</p>
                            </div>
                        </div>

                        @error('dosenIds')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('dosenPerKelas')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- ============================== --}}
                {{-- STEP 3: Input Kelas --}}
                {{-- ============================== --}}
                <div id="step-kelas-wrapper" class="hidden bg-white border border-gray-200 rounded-xl p-4">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                        <span class="flex items-center justify-center w-6 h-6 bg-amber-600 text-white text-xs font-bold rounded-full mr-2">3</span>
                        Input Kelas
                    </h3>
                    <div>
                        <div id="kelas-container" class="space-y-3"></div>
                        <button type="button" id="add-kelas-btn" class="mt-3 inline-flex items-center px-3 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm rounded-md transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah Kelas
                        </button>
                        <p class="mt-2 text-xs text-gray-500">Masukkan nama kelas (huruf atau angka). Klik + untuk menambah kelas lain.</p>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <a href="{{ route('admin.tahun-ajaran-matkul.index') }}"
                        class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-md">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dosens = @json($dosens);
        const mataKuliahSelect = document.getElementById('mataKuliahId');
        const kelasContainer = document.getElementById('kelas-container');
        const addKelasBtn = document.getElementById('add-kelas-btn');
        const stepKelasWrapper = document.getElementById('step-kelas-wrapper');

        let currentDosenOption = null; // Track which option is selected

        // =========================
        // Custom searchable dropdown for Mata Kuliah
        // =========================
        (function initCustomMataKuliahSelect() {
            const button = document.getElementById('custom-mk-button');
            const label = document.getElementById('custom-mk-label');
            const panel = document.getElementById('custom-mk-panel');
            const search = document.getElementById('custom-mk-search');
            const list = document.getElementById('custom-mk-options');

            if (!button || !panel || !search || !list || !mataKuliahSelect) return;

            const closePanel = () => panel.classList.add('hidden');
            const openPanel = () => {
                panel.classList.remove('hidden');
                setTimeout(() => search.focus(), 0);
            };

            const setSelected = (value, text) => {
                mataKuliahSelect.value = value;
                label.textContent = text || 'Pilih Mata Kuliah';
            };

            const buildOptions = () => {
                list.innerHTML = '';
                const currentQuery = search.value.trim().toLowerCase();
                const options = Array.from(mataKuliahSelect.options).slice(1);
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

            const selectedOption = mataKuliahSelect.options[mataKuliahSelect.selectedIndex];
            label.textContent = selectedOption && selectedOption.value !== '' ? selectedOption.text : 'Pilih Mata Kuliah';

            button.addEventListener('click', () => {
                if (panel.classList.contains('hidden')) { openPanel(); buildOptions(); } else { closePanel(); }
            });
            search.addEventListener('input', () => buildOptions());
            document.addEventListener('click', (e) => {
                const wrapper = document.getElementById('custom-mk-select');
                if (!wrapper.contains(e.target)) closePanel();
            });
            mataKuliahSelect.addEventListener('change', () => {
                const sel = mataKuliahSelect.options[mataKuliahSelect.selectedIndex];
                label.textContent = sel && sel.value !== '' ? sel.text : 'Pilih Mata Kuliah';
            });
        })();

        // =========================
        // Multi-select component JS (reusable)
        // =========================
        function createSearchableMultiSelect({ mountEl, placeholder, options, initialSelected = [], onChange }) {
            const state = { isOpen: false, selected: new Set(initialSelected.map(String)), query: '' };

            const wrapper = document.createElement('div');
            wrapper.className = 'relative';

            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full text-left p-2.5 flex items-center justify-between';

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
            panel.className = 'absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-md hidden';

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
                if (state.selected.size === 0) { label.textContent = placeholder; return; }
                const selectedLabels = options.filter(o => state.selected.has(String(o.value))).map(o => o.label);
                label.textContent = selectedLabels.length === 1 ? selectedLabels[0] : `${selectedLabels.length} dosen dipilih`;
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
                    checkbox.className = 'inline-block w-4 h-4 mr-2 rounded border border-gray-300 flex-shrink-0 ' + (state.selected.has(String(o.value)) ? 'bg-amber-500 border-amber-500' : 'bg-white');
                    const text = document.createElement('span');
                    text.textContent = o.label;
                    li.appendChild(checkbox);
                    li.appendChild(text);
                    li.addEventListener('click', () => {
                        const key = String(o.value);
                        if (state.selected.has(key)) { state.selected.delete(key); } else { state.selected.add(key); }
                        renderLabel();
                        buildOptions();
                        if (typeof onChange === 'function') onChange(Array.from(state.selected));
                    });
                    list.appendChild(li);
                });
            };

            const open = () => { panel.classList.remove('hidden'); setTimeout(() => search.focus(), 0); };
            const close = () => panel.classList.add('hidden');

            button.addEventListener('click', () => {
                if (panel.classList.contains('hidden')) { open(); buildOptions(); } else { close(); }
            });
            search.addEventListener('input', () => { state.query = search.value; buildOptions(); });
            document.addEventListener('click', (e) => { if (!wrapper.contains(e.target)) close(); });

            renderLabel();
            if (typeof onChange === 'function') onChange(Array.from(state.selected));

            return {
                getSelected: () => Array.from(state.selected),
                removeItem: (id) => {
                    const key = String(id);
                    if (state.selected.has(key)) {
                        state.selected.delete(key);
                        renderLabel();
                        if (typeof onChange === 'function') onChange(Array.from(state.selected));
                    }
                },
                destroy: () => { mountEl.innerHTML = ''; }
            };
        }

        // =========================
        // Render dosen tags outside the select box
        // =========================
        function renderDosenTags(containerEl, selectedIds, dosenList, removeCallback) {
            containerEl.innerHTML = '';
            if (selectedIds.length === 0) return;
            selectedIds.forEach(id => {
                const dosen = dosenList.find(d => String(d.id) === String(id));
                if (!dosen) return;
                const tag = document.createElement('span');
                tag.className = 'inline-flex items-center px-2.5 py-1 rounded-md text-sm font-medium text-amber-800 border border-amber-200 bg-amber-50';
                tag.innerHTML = `
                    <svg class="w-3.5 h-3.5 mr-1.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>${dosen.nama}</span>
                `;
                if (typeof removeCallback === 'function') {
                    const closeBtn = document.createElement('button');
                    closeBtn.type = 'button';
                    closeBtn.className = 'ml-1.5 inline-flex items-center justify-center w-4 h-4 rounded text-amber-600 hover:bg-amber-100 hover:text-amber-900 transition-colors';
                    closeBtn.innerHTML = `<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>`;
                    closeBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        removeCallback(id);
                    });
                    tag.appendChild(closeBtn);
                }
                containerEl.appendChild(tag);
            });
        }

        // =========================
        // Dosen option change handler
        // =========================
        const dosenSameSection = document.getElementById('dosen-same-section');
        const dosenDifferentSection = document.getElementById('dosen-different-section');
        const dosenOptionRadios = document.querySelectorAll('input[name="dosenOption"]');

        dosenOptionRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                currentDosenOption = this.value;

                // Update label styling
                document.getElementById('label-dosen-same').classList.toggle('bg-amber-50', this.value === 'same');
                document.getElementById('label-dosen-same').classList.toggle('border-amber-500', this.value === 'same');
                document.getElementById('label-dosen-different').classList.toggle('bg-amber-50', this.value === 'different');
                document.getElementById('label-dosen-different').classList.toggle('border-amber-500', this.value === 'different');

                if (this.value === 'same') {
                    dosenSameSection.classList.remove('hidden');
                    dosenDifferentSection.classList.add('hidden');
                } else {
                    dosenSameSection.classList.add('hidden');
                    dosenDifferentSection.classList.remove('hidden');
                }

                // Show step 3
                stepKelasWrapper.classList.remove('hidden');

                // Rebuild kelas items
                rebuildKelasItems();
            });
        });

        // =========================
        // Init multi-select for "Dosen Same" option
        // =========================
        (function initDosenSameMultiSelect() {
            const mount = document.getElementById('dosen-same-multiselect');
            const hidden = document.getElementById('dosen-same-hidden');
            const tagsContainer = document.getElementById('dosen-same-tags');
            if (!mount || !hidden) return;

            const dosenSameInstance = createSearchableMultiSelect({
                mountEl: mount,
                placeholder: 'Pilih dosen...',
                options: dosens.map(d => ({ value: String(d.id), label: d.nama })),
                initialSelected: [],
                onChange: (values) => {
                    hidden.innerHTML = '';
                    values.forEach(v => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'dosenIds[]';
                        input.value = v;
                        hidden.appendChild(input);
                    });
                    // Render tags with remove callback
                    renderDosenTags(tagsContainer, values, dosens, (id) => {
                        dosenSameInstance.removeItem(id);
                    });
                }
            });
        })();

        // =========================
        // Kelas management
        // =========================
        let kelasCount = 0;

        function createKelasItem() {
            kelasCount++;
            const idx = kelasCount;
            const item = document.createElement('div');
            item.className = 'kelas-item border border-gray-200 rounded-md p-3 bg-white';
            item.dataset.index = idx;

            if (currentDosenOption === 'same') {
                // Only kelas name input
                item.innerHTML = `
                    <div class="flex gap-2 items-center">
                        <input type="text"
                               name="kelasNames[]"
                               class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block p-2.5"
                               placeholder="Nama kelas (A, B, C, dll)"
                               required>
                        <button type="button" class="remove-kelas-btn bg-red-600 hover:bg-red-700 text-white px-3 py-2.5 rounded-lg text-sm">×</button>
                    </div>
                `;
            } else {
                // Kelas name + dosen per kelas (2 column layout)
                item.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Nama Kelas</label>
                            <div class="flex gap-2 items-center">
                                <input type="text"
                                       name="kelasNames[]"
                                       class="kelas-name-input flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block p-2.5"
                                       placeholder="Nama kelas (A, B, C, dll)"
                                       required>
                                <button type="button" class="remove-kelas-btn bg-red-600 hover:bg-red-700 text-white px-3 py-2.5 rounded-lg text-sm">×</button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Dosen Pengampu Kelas</label>
                            <div class="dosen-per-kelas-mount"></div>
                            <div class="dosen-per-kelas-hidden"></div>
                            <div class="dosen-per-kelas-tags flex flex-wrap gap-1 mt-2"></div>
                        </div>
                    </div>
                `;

                // Init dosen multi-select for this kelas
                const mountEl = item.querySelector('.dosen-per-kelas-mount');
                const hiddenEl = item.querySelector('.dosen-per-kelas-hidden');
                const tagsEl = item.querySelector('.dosen-per-kelas-tags');
                const nameInput = item.querySelector('.kelas-name-input');

                if (mountEl) {
                    const perKelasInstance = createSearchableMultiSelect({
                        mountEl: mountEl,
                        placeholder: 'Pilih dosen...',
                        options: dosens.map(d => ({ value: String(d.id), label: d.nama })),
                        initialSelected: [],
                        onChange: (values) => {
                            hiddenEl.innerHTML = '';
                            const kelasName = nameInput ? nameInput.value.trim() : '';
                            if (kelasName) {
                                values.forEach(v => {
                                    const input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = `dosenPerKelas[${kelasName}][]`;
                                    input.value = v;
                                    hiddenEl.appendChild(input);
                                });
                            }
                            renderDosenTags(tagsEl, values, dosens, (id) => {
                                perKelasInstance.removeItem(id);
                            });
                        }
                    });

                    // Update hidden input names when kelas name changes
                    if (nameInput) {
                        nameInput.addEventListener('input', function() {
                            const kelasName = this.value.trim();
                            const hiddenInputs = hiddenEl.querySelectorAll('input[type="hidden"]');
                            hiddenInputs.forEach(inp => {
                                inp.name = kelasName ? `dosenPerKelas[${kelasName}][]` : '';
                            });
                        });
                    }
                }
            }

            // Remove button handler
            const removeBtn = item.querySelector('.remove-kelas-btn');
            if (removeBtn) {
                removeBtn.addEventListener('click', () => {
                    const nameInput = item.querySelector('.kelas-name-input');
                    const className = nameInput ? nameInput.value.trim() : '';
                    
                    if (className !== '') {
                        Swal.fire({
                            title: 'Hapus Kelas?',
                            html: `Apakah Anda yakin ingin menghapus kelas <b>${className}</b> dari form ini?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Ya, Hapus',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                item.remove();
                                updateRemoveButtons();
                            }
                        });
                    } else {
                        item.remove();
                        updateRemoveButtons();
                    }
                });
            }

            return item;
        }

        function updateRemoveButtons() {
            const items = kelasContainer.querySelectorAll('.kelas-item');
            items.forEach((item, i) => {
                const removeBtn = item.querySelector('.remove-kelas-btn');
                if (removeBtn) {
                    // Hide remove button if there's only one kelas
                    removeBtn.classList.toggle('hidden', items.length <= 1);
                }
            });
        }

        function rebuildKelasItems() {
            kelasContainer.innerHTML = '';
            kelasCount = 0;
            const item = createKelasItem();
            kelasContainer.appendChild(item);
            updateRemoveButtons();
        }

        addKelasBtn.addEventListener('click', function() {
            const item = createKelasItem();
            kelasContainer.appendChild(item);
            updateRemoveButtons();
        });
    });
</script>
@endsection