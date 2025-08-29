@extends('layouts.main')

@section('title', 'Tambah Sub-CPMK - ' . $tahunAjaranMatkul->mataKuliah->namaMatkul)

@section('content')
<div class="p-6">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li>
                <a href="{{ route('dosen.cpmk.index') }}" class="hover:text-gray-700">Kelola CPMK</a>
            </li>
            <li>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </li>
            <li>
                <a href="{{ route('dosen.cpmk.show', $tahunAjaranMatkul->id) }}">{{ $tahunAjaranMatkul->mataKuliah->namaMatkul }}</a>
            </li>
            <li>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </li>
            <li class="text-gray-900 font-medium">Tambah Sub-CPMK</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Tambah Sub-CPMK</h1>
                    <p class="text-gray-600 mt-1">{{ $tahunAjaranMatkul->mataKuliah->namaMatkul }} • {{ $tahunAjaranMatkul->tahunAjaran->tahun }} - {{ $tahunAjaranMatkul->tahunAjaran->periode }}</p>
                </div>
                <div class="flex items-center">
                    <a href="{{ route('dosen.cpmk.show', $tahunAjaranMatkul->id) }}"
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>



        <!-- Form Header -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Tambah Multiple Sub-CPMK</h2>
                    <p class="text-sm text-gray-600 mt-1">Tambah beberapa sub-CPMK sekaligus dalam satu halaman</p>
                </div>
                <button type="button" id="addSubCpmkBtn"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Sub-CPMK
                </button>
            </div>
        </div>

        <form action="{{ route('dosen.cpmk.sub-cpmk.store', [$tahunAjaranMatkul->id, $parentCpmk->id]) }}" method="POST" class="p-6" id="subCpmkForm">
            @csrf
            <div id="subCpmkContainer">
                <!-- Sub-CPMK fields will be added here dynamically -->
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('dosen.cpmk.show', $tahunAjaranMatkul->id) }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2.5 rounded-lg font-medium transition-colors duration-200">
                    Batal
                </a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Semua Sub-CPMK
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Template for Sub-CPMK field -->
<template id="subCpmkTemplate">
    <div class="sub-cpmk-field bg-gray-50 rounded-lg p-6 mb-6 border-2 border-gray-200">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 sub-cpmk-title">Sub-CPMK #<span class="sub-cpmk-number">1</span></h3>
            <button type="button" class="remove-sub-cpmk-btn text-red-600 hover:text-red-700 p-1" title="Hapus Sub-CPMK ini">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <!-- Parent CPMK Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Parent CPMK <span class="text-red-500">*</span>
                </label>
                <div class="bg-white border rounded-lg p-4 border-gray-300">
                    <div class="space-y-3">
                        @foreach($allCpmkForParent as $cpmk)
                            <div class="flex items-start">
                                <input type="checkbox"
                                       name="sub_cpmk[sub_cpmk_index][parent_ids][]"
                                       value="{{ $cpmk->id }}"
                                       class="mt-1 mr-3 rounded border-gray-300 text-amber-600 focus:ring-amber-500 parent-checkbox">
                                <label class="text-sm text-gray-700 cursor-pointer">
                                    <span class="font-medium">{{ $cpmk->kodeCpmk }}</span>
                                    <span class="text-gray-600"> - {{ Str::limit($cpmk->deskripsi, 50) }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @if($allCpmkForParent->isEmpty())
                        <p class="text-sm text-gray-500 text-center py-4">Belum ada CPMK yang tersedia</p>
                    @endif
                </div>
                <p class="mt-1 text-sm text-gray-500">Pilih satu atau lebih parent CPMK untuk sub-CPMK ini</p>
            </div>

            <!-- Kode Sub-CPMK -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Kode Sub-CPMK <span class="text-red-500">*</span>
                </label>
                <input type="text" name="sub_cpmk[sub_cpmk_index][kodeCpmk]" required maxlength="20"
                       placeholder="Contoh: {{ $parentCpmk->kodeCpmk }}-1, {{ $parentCpmk->kodeCpmk }}-2, dst."
                       class="bg-white border text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-3 border-gray-300 kode-sub-cpmk-input">
                <p class="mt-1 text-sm text-gray-500">Kode unik untuk identifikasi sub-CPMK (maksimal 20 karakter)</p>
            </div>

            <!-- Deskripsi -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi Sub-CPMK <span class="text-red-500">*</span>
                </label>
                <textarea name="sub_cpmk[sub_cpmk_index][deskripsi]" rows="4" required maxlength="1000"
                          placeholder="Masukkan deskripsi sub capaian pembelajaran mata kuliah..."
                          class="bg-white border text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-3 border-gray-300 deskripsi-textarea"></textarea>
                <div class="flex justify-between mt-1">
                    <p class="text-sm text-gray-500">Deskripsikan kemampuan yang lebih spesifik dari parent CPMK</p>
                    <span class="text-sm text-gray-500 char-count">0/1000</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('subCpmkContainer');
    const addBtn = document.getElementById('addSubCpmkBtn');
    const template = document.getElementById('subCpmkTemplate');
    const form = document.getElementById('subCpmkForm');
    let subCpmkCount = 0;

    // Add first Sub-CPMK field by default
    addSubCpmkField();

    // Add Sub-CPMK field
    function addSubCpmkField() {
        subCpmkCount++;
        const clone = template.content.cloneNode(true);

        // Update all placeholders and names
        clone.querySelectorAll('[name*="sub_cpmk_index"]').forEach(input => {
            input.name = input.name.replace('sub_cpmk_index', subCpmkCount - 1);
        });

        // Update title
        clone.querySelector('.sub-cpmk-number').textContent = subCpmkCount;

        // Add event listeners
        const subCpmkField = clone.querySelector('.sub-cpmk-field');

        // Character counter
        const textarea = subCpmkField.querySelector('.deskripsi-textarea');
        const charCount = subCpmkField.querySelector('.char-count');

        textarea.addEventListener('input', function() {
            const count = this.value.length;
            charCount.textContent = count + '/1000';

            if (count > 900) {
                charCount.classList.add('text-red-500');
                charCount.classList.remove('text-gray-500');
            } else {
                charCount.classList.remove('text-red-500');
                charCount.classList.add('text-gray-500');
            }
        });

        // Remove button
        const removeBtn = subCpmkField.querySelector('.remove-sub-cpmk-btn');
        removeBtn.addEventListener('click', function() {
            if (container.children.length > 1) {
                subCpmkField.remove();
                updateSubCpmkNumbers();
            } else {
                alert('Minimal harus ada satu Sub-CPMK');
            }
        });

        // Auto-generate kode Sub-CPMK
        const kodeInput = subCpmkField.querySelector('.kode-sub-cpmk-input');
        kodeInput.addEventListener('focus', function() {
            if (!this.value) {
                const parentKode = '{{ $parentCpmk->kodeCpmk }}';
                this.value = parentKode + '-' + subCpmkCount;
            }
        });

        container.appendChild(clone);
    }

    // Update Sub-CPMK numbers after removal
    function updateSubCpmkNumbers() {
        const fields = container.querySelectorAll('.sub-cpmk-field');
        fields.forEach((field, index) => {
            field.querySelector('.sub-cpmk-number').textContent = index + 1;
            field.querySelector('.sub-cpmk-title').textContent = 'Sub-CPMK #' + (index + 1);

            // Update input names
            const inputs = field.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                if (input.name.includes('sub_cpmk[')) {
                    input.name = input.name.replace(/sub_cpmk\[\d+\]/, `sub_cpmk[${index}]`);
                }
            });
        });
    }

    // Add button event
    addBtn.addEventListener('click', addSubCpmkField);

    // Form validation
    form.addEventListener('submit', function(e) {
        const subCpmkFields = container.querySelectorAll('.sub-cpmk-field');
        let isValid = true;
        let errorMessage = '';

        subCpmkFields.forEach((field, index) => {
            const kodeInput = field.querySelector('.kode-sub-cpmk-input');
            const deskripsiTextarea = field.querySelector('.deskripsi-textarea');
            const parentCheckboxes = field.querySelectorAll('.parent-checkbox:checked');

            // Check required fields
            if (!kodeInput.value.trim()) {
                isValid = false;
                errorMessage = `Kode Sub-CPMK #${index + 1} harus diisi`;
                kodeInput.focus();
                return;
            }

            if (!deskripsiTextarea.value.trim()) {
                isValid = false;
                errorMessage = `Deskripsi Sub-CPMK #${index + 1} harus diisi`;
                deskripsiTextarea.focus();
                return;
            }

            if (parentCheckboxes.length === 0) {
                isValid = false;
                errorMessage = `Sub-CPMK #${index + 1} harus memilih minimal satu parent CPMK`;
                return;
            }

            if (deskripsiTextarea.value.trim().length < 10) {
                isValid = false;
                errorMessage = `Deskripsi Sub-CPMK #${index + 1} minimal 10 karakter`;
                deskripsiTextarea.focus();
                return;
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert(errorMessage);
            return false;
        }

        // Check for duplicate kode Sub-CPMK
        const kodeInputs = container.querySelectorAll('.kode-sub-cpmk-input');
        const kodeValues = Array.from(kodeInputs).map(input => input.value.trim());
        const uniqueKodes = [...new Set(kodeValues)];

        if (kodeValues.length !== uniqueKodes.length) {
            e.preventDefault();
            alert('Kode Sub-CPMK tidak boleh duplikat');
            return false;
        }
    });
});
</script>
@endsection
