@extends('layouts.main')
@section('title', 'Edit Mata Kuliah Tahun Ajaran')

@php
    // Determine if all classes have the same dosen
    $allDosenSets = [];
    foreach($tahunAjaranMatkul->kelas as $kelas) {
        $dosenIds = $kelas->dosenPengampuKelas->pluck('dosenId')->sort()->values()->toArray();
        $allDosenSets[] = $dosenIds;
    }
    $isSameDosen = count(array_unique(array_map('json_encode', $allDosenSets))) <= 1;
    
    // Get common dosen IDs (for "same" mode)
    $commonDosenIds = !empty($allDosenSets) ? $allDosenSets[0] : [];
    
    // Build per-kelas dosen map
    $dosenPerKelasMap = [];
    foreach($tahunAjaranMatkul->kelas as $kelas) {
        $dosenPerKelasMap[$kelas->namaKelas] = $kelas->dosenPengampuKelas->pluck('dosenId')->map(fn($id) => (string)$id)->toArray();
    }
@endphp

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

            <form action="{{ route('admin.tahun-ajaran-matkul.update', $tahunAjaranMatkul->id) }}" method="POST" class="space-y-6" id="editForm">
                @csrf
                @method('PUT')

                {{-- ============================== --}}
                {{-- STEP 1: Informasi Mata Kuliah --}}
                {{-- ============================== --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                        <span class="flex items-center justify-center w-6 h-6 bg-amber-600 text-white text-xs font-bold rounded-full mr-2">1</span>
                        Informasi Mata Kuliah
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div>
                            <label for="tahunAjaranId" class="block text-sm font-medium text-gray-700 mb-2">
                                Tahun Ajaran <span class="text-red-500">*</span>
                            </label>
                            <select name="tahunAjaranId" id="tahunAjaranId"
                                class="bg-white border {{ $errors->has('tahunAjaranId') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5"
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
                                class="hidden bg-white border {{ $errors->has('mataKuliahId') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5"
                                required>
                                <option value="">Pilih Mata Kuliah</option>
                                @foreach($mataKuliahs as $mataKuliah)
                                <option value="{{ $mataKuliah->id }}" {{ old('mataKuliahId', $tahunAjaranMatkul->mataKuliahId) == $mataKuliah->id ? 'selected' : '' }}>
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
                                class="bg-white border {{ $errors->has('semester') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5"
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
                </div>

                {{-- ============================== --}}
                {{-- STEP 2: Dosen Pengampu --}}
                {{-- ============================== --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                        <span class="flex items-center justify-center w-6 h-6 bg-amber-600 text-white text-xs font-bold rounded-full mr-2">2</span>
                        Dosen Pengampu
                    </h3>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        {{-- Radio options --}}
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-3">Bagaimana pengaturan dosen pengampu untuk kelas?</p>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <label class="flex items-center px-4 py-3 bg-white border border-gray-200 rounded-lg cursor-pointer hover:bg-amber-50 hover:border-amber-300 transition-colors has-[:checked]:bg-amber-50 has-[:checked]:border-amber-500 {{ $isSameDosen ? 'bg-amber-50 border-amber-500' : '' }}" id="label-dosen-same">
                                    <input type="radio"
                                        name="dosenType"
                                        value="same"
                                        id="dosen-same"
                                        class="mr-3 text-amber-600 focus:ring-amber-500"
                                        {{ $isSameDosen ? 'checked' : '' }}>
                                    <div>
                                        <span class="text-sm font-medium text-gray-700">Dosen sama untuk semua kelas</span>
                                        <p class="text-xs text-gray-500">Semua kelas diampu oleh dosen yang sama</p>
                                    </div>
                                </label>
                                <label class="flex items-center px-4 py-3 bg-white border border-gray-200 rounded-lg cursor-pointer hover:bg-amber-50 hover:border-amber-300 transition-colors has-[:checked]:bg-amber-50 has-[:checked]:border-amber-500 {{ !$isSameDosen ? 'bg-amber-50 border-amber-500' : '' }}" id="label-dosen-different">
                                    <input type="radio"
                                        name="dosenType"
                                        value="different"
                                        id="dosen-different"
                                        class="mr-3 text-amber-600 focus:ring-amber-500"
                                        {{ !$isSameDosen ? 'checked' : '' }}>
                                    <div>
                                        <span class="text-sm font-medium text-gray-700">Dosen berbeda setiap kelas</span>
                                        <p class="text-xs text-gray-500">Setiap kelas memiliki dosen pengampu tersendiri</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Dosen for all classes (shown when "same" selected) --}}
                        <div id="dosen-same-section" class="{{ $isSameDosen ? '' : 'hidden' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Dosen untuk Semua Kelas</label>
                            <div id="dosen-same-multiselect"></div>
                            <div id="dosen-same-hidden"></div>
                            {{-- Tags area for showing selected dosen --}}
                            <div id="dosen-same-tags" class="flex flex-wrap gap-2 mt-3"></div>
                        </div>

                        {{-- Info when "different" selected --}}
                        <div id="dosen-different-section" class="{{ !$isSameDosen ? '' : 'hidden' }}">
                            <div class="flex items-center p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <svg class="w-4 h-4 text-blue-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm text-blue-700">Pilih dosen untuk setiap kelas pada bagian input kelas di bawah.</p>
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
                <div id="step-kelas-wrapper">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                        <span class="flex items-center justify-center w-6 h-6 bg-amber-600 text-white text-xs font-bold rounded-full mr-2">3</span>
                        Input Kelas
                    </h3>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div id="kelas-container" class="space-y-3"></div>
                        <button type="button" id="add-kelas-btn" class="mt-3 inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah Kelas
                        </button>
                        <p class="mt-2 text-xs text-gray-500">Masukkan nama kelas (huruf atau angka). Klik + untuk menambah kelas lain.</p>
                    </div>
                </div>

                <div class="flex justify-between pt-6 border-t border-gray-200">
                    <a href="{{ request('back_url', route('admin.tahun-ajaran-matkul.index')) }}"
                        class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center">
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
        const dosens = @json($dosens);
        const mataKuliahSelect = document.getElementById('mataKuliahId');
        const kelasContainer = document.getElementById('kelas-container');
        const addKelasBtn = document.getElementById('add-kelas-btn');
        const stepKelasWrapper = document.getElementById('step-kelas-wrapper');

        // Existing data
        const existingKelas = @json($tahunAjaranMatkul->kelas);
        const isSameDosen = @json($isSameDosen);
        const commonDosenIds = @json(array_map('strval', $commonDosenIds));
        const dosenPerKelasMap = @json($dosenPerKelasMap);

        let currentDosenOption = isSameDosen ? 'same' : 'different';

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
                tag.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800 border border-amber-200';
                tag.innerHTML = `
                    <svg class="w-3.5 h-3.5 mr-1.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>${dosen.nama}</span>
                `;
                if (typeof removeCallback === 'function') {
                    const closeBtn = document.createElement('button');
                    closeBtn.type = 'button';
                    closeBtn.className = 'ml-1.5 inline-flex items-center justify-center w-4 h-4 rounded-full text-amber-600 hover:bg-amber-200 hover:text-amber-900 transition-colors';
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
        const dosenOptionRadios = document.querySelectorAll('input[name="dosenType"]');

        dosenOptionRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                currentDosenOption = this.value;

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

                // Rebuild kelas items to match layout
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
                initialSelected: isSameDosen ? commonDosenIds : [],
                onChange: (values) => {
                    hidden.innerHTML = '';
                    values.forEach(v => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'dosenIds[]';
                        input.value = v;
                        hidden.appendChild(input);
                    });
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

        function createKelasItem(kelasName = '', initialDosenIds = []) {
            kelasCount++;
            const idx = kelasCount;
            const item = document.createElement('div');
            item.className = 'kelas-item border border-gray-200 rounded-lg p-4 bg-white';
            item.dataset.index = idx;

            if (currentDosenOption === 'same') {
                item.innerHTML = `
                    <div class="flex gap-2 items-center">
                        <input type="text"
                               name="kelasNames[]"
                               value="${kelasName}"
                               class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block p-2.5"
                               placeholder="Nama kelas (A, B, C, dll)"
                               required>
                        <button type="button" class="remove-kelas-btn bg-red-600 hover:bg-red-700 text-white px-3 py-2.5 rounded-lg text-sm">×</button>
                    </div>
                `;
            } else {
                item.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Nama Kelas</label>
                            <div class="flex gap-2 items-center">
                                <input type="text"
                                       name="kelasNames[]"
                                       value="${kelasName}"
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

                const mountEl = item.querySelector('.dosen-per-kelas-mount');
                const hiddenEl = item.querySelector('.dosen-per-kelas-hidden');
                const tagsEl = item.querySelector('.dosen-per-kelas-tags');
                const nameInput = item.querySelector('.kelas-name-input');

                if (mountEl) {
                    const perKelasInstance = createSearchableMultiSelect({
                        mountEl: mountEl,
                        placeholder: 'Pilih dosen...',
                        options: dosens.map(d => ({ value: String(d.id), label: d.nama })),
                        initialSelected: initialDosenIds,
                        onChange: (values) => {
                            hiddenEl.innerHTML = '';
                            const kName = nameInput ? nameInput.value.trim() : '';
                            if (kName) {
                                values.forEach(v => {
                                    const input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = `dosenPerKelas[${kName}][]`;
                                    input.value = v;
                                    hiddenEl.appendChild(input);
                                });
                            }
                            renderDosenTags(tagsEl, values, dosens, (id) => {
                                perKelasInstance.removeItem(id);
                            });
                        }
                    });

                    if (nameInput) {
                        nameInput.addEventListener('input', function() {
                            const kName = this.value.trim();
                            const hiddenInputs = hiddenEl.querySelectorAll('input[type="hidden"]');
                            hiddenInputs.forEach(inp => {
                                inp.name = kName ? `dosenPerKelas[${kName}][]` : '';
                            });
                        });
                    }
                }
            }

            const removeBtn = item.querySelector('.remove-kelas-btn');
            if (removeBtn) {
                removeBtn.addEventListener('click', () => {
                    const nameInput = item.querySelector('.kelas-name-input');
                    const className = nameInput ? nameInput.value.trim() : '';
                    const isExisting = existingKelas.some(k => k.namaKelas === className);

                    if (isExisting && className !== '') {
                        Swal.fire({
                            title: 'Peringatan Hapus Kelas',
                            html: `Menghapus kelas <b>${className}</b> dari form ini akan menghapus <b>semua data mahasiswa dan nilai</b> yang ada di kelas tersebut setelah form disimpan.<br><br>Apakah Anda yakin?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Ya, Hapus Kelas',
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
            items.forEach((item) => {
                const removeBtn = item.querySelector('.remove-kelas-btn');
                if (removeBtn) {
                    removeBtn.classList.toggle('hidden', items.length <= 1);
                }
            });
        }

        function rebuildKelasItems() {
            kelasContainer.innerHTML = '';
            kelasCount = 0;

            if (existingKelas.length > 0) {
                existingKelas.forEach(kelas => {
                    const kelasDosenIds = dosenPerKelasMap[kelas.namaKelas] || [];
                    const item = createKelasItem(kelas.namaKelas, kelasDosenIds);
                    kelasContainer.appendChild(item);
                });
            } else {
                const item = createKelasItem();
                kelasContainer.appendChild(item);
            }
            updateRemoveButtons();
        }

        addKelasBtn.addEventListener('click', function() {
            const item = createKelasItem();
            kelasContainer.appendChild(item);
            updateRemoveButtons();
        });

        // =========================
        // Initialize on page load
        // =========================
        rebuildKelasItems();
    });
</script>
@endsection