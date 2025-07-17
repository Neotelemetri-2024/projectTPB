@extends('layouts.main')

@section('title', 'Tambah CPMK - ' . $tahunAjaranMatkul->mataKuliah->namaMatkul)

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
            <li class="text-gray-900 font-medium">Tambah CPMK</li>
        </ol>
    </nav>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Tambah CPMK</h1>
                    <p class="text-gray-600 mt-1">{{ $tahunAjaranMatkul->mataKuliah->namaMatkul }} • {{ $tahunAjaranMatkul->mataKuliah->kodeMatkul }}</p>
                </div>
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

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Informasi CPMK</h2>
            <p class="text-sm text-gray-600 mt-1">Lengkapi informasi CPMK baru</p>
        </div>

        <form action="{{ route('dosen.cpmk.store', $tahunAjaranMatkul->id) }}" method="POST" class="p-6">
            @csrf

            <div class="grid grid-cols-1 gap-6">
                <!-- CPL Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Capaian Pembelajaran Lulusan (CPL) <span class="text-red-500">*</span>
                    </label>
                    <div class="bg-gray-50 border rounded-lg p-4 {{ $errors->has('cpl_ids') ? 'border-red-500' : 'border-gray-300' }}">
                        <div class="space-y-3 max-h-60 overflow-y-auto">
                            @foreach($cplList as $cpl)
                                <div class="flex items-start">
                                    <input type="checkbox"
                                           name="cpl_ids[]"
                                           value="{{ $cpl->id }}"
                                           id="cpl_{{ $cpl->id }}"
                                           class="mt-1 mr-3 rounded border-gray-300 text-amber-600 focus:ring-amber-500"
                                           {{ in_array($cpl->id, old('cpl_ids', [])) ? 'checked' : '' }}>
                                    <label for="cpl_{{ $cpl->id }}" class="text-sm text-gray-700 cursor-pointer">
                                        <span class="font-medium">{{ $cpl->kodeCpl }}</span>
                                        <span class="text-gray-600"> - {{ $cpl->deskripsi }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @if($cplList->isEmpty())
                            <p class="text-sm text-gray-500 text-center py-4">Belum ada CPL yang tersedia</p>
                        @endif
                    </div>
                    @error('cpl_ids')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Pilih satu atau lebih CPL yang akan dicapai melalui CPMK ini</p>
                </div>

                <!-- Kode CPMK -->
                <div>
                    <label for="kodeCpmk" class="block text-sm font-medium text-gray-700 mb-2">
                        Kode CPMK <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="kodeCpmk" id="kodeCpmk" required maxlength="20"
                           value="{{ old('kodeCpmk') }}"
                           placeholder="Contoh: CPMK-1, CPMK-2, dst."
                           class="bg-gray-50 border text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-3 {{ $errors->has('kodeCpmk') ? 'border-red-500' : 'border-gray-300' }}">
                    @error('kodeCpmk')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Kode unik untuk identifikasi CPMK (maksimal 20 karakter)</p>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi CPMK <span class="text-red-500">*</span>
                    </label>
                    <textarea name="deskripsi" id="deskripsi" rows="5" required maxlength="1000"
                              placeholder="Masukkan deskripsi capaian pembelajaran mata kuliah yang jelas dan terukur..."
                              class="bg-gray-50 border text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-3 {{ $errors->has('deskripsi') ? 'border-red-500' : 'border-gray-300' }}">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div class="flex justify-between mt-1">
                        <p class="text-sm text-gray-500">Deskripsikan kemampuan yang harus dicapai mahasiswa</p>
                        <span class="text-sm text-gray-500"><span id="charCount">0</span>/1000</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('dosen.cpmk.show', $tahunAjaranMatkul->id) }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2.5 rounded-lg font-medium transition-colors duration-200">
                    Batal
                </a>
                <button type="submit"
                        class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan CPMK
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deskripsiTextarea = document.getElementById('deskripsi');
    const charCount = document.getElementById('charCount');

    // Character counter
    function updateCharCount() {
        const count = deskripsiTextarea.value.length;
        charCount.textContent = count;

        if (count > 900) {
            charCount.parentElement.classList.add('text-red-500');
            charCount.parentElement.classList.remove('text-gray-500');
        } else {
            charCount.parentElement.classList.remove('text-red-500');
            charCount.parentElement.classList.add('text-gray-500');
        }
    }

    deskripsiTextarea.addEventListener('input', updateCharCount);
    updateCharCount(); // Initial count

    // Auto-generate kode CPMK suggestion
    const kodeCpmkInput = document.getElementById('kodeCpmk');

    kodeCpmkInput.addEventListener('focus', function() {
        if (!this.value) {
            // Simple auto-suggestion based on existing pattern
            this.placeholder = 'CPMK-' + (Math.floor(Math.random() * 10) + 1);
        }
    });

    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const kodeCpmk = kodeCpmkInput.value.trim();
        const deskripsi = deskripsiTextarea.value.trim();
        const selectedCpls = document.querySelectorAll('input[name="cpl_ids[]"]:checked');

        if (!kodeCpmk || !deskripsi || selectedCpls.length === 0) {
            e.preventDefault();
            if (selectedCpls.length === 0) {
                alert('Harap pilih minimal satu CPL');
            } else {
                alert('Harap lengkapi semua field yang wajib diisi');
            }
            return false;
        }

        if (deskripsi.length < 10) {
            e.preventDefault();
            alert('Deskripsi CPMK minimal 10 karakter');
            deskripsiTextarea.focus();
            return false;
        }
    });

    // CPL selection helper
    const cplCheckboxes = document.querySelectorAll('input[name="cpl_ids[]"]');
    const cplContainer = document.querySelector('.space-y-3');

    // Add select all / deselect all functionality
    if (cplCheckboxes.length > 0) {
        const selectAllBtn = document.createElement('button');
        selectAllBtn.type = 'button';
        selectAllBtn.className = 'text-sm text-amber-600 hover:text-amber-700 font-medium mb-2';
        selectAllBtn.textContent = 'Pilih Semua';

        selectAllBtn.addEventListener('click', function() {
            const allChecked = Array.from(cplCheckboxes).every(cb => cb.checked);
            cplCheckboxes.forEach(cb => cb.checked = !allChecked);
            this.textContent = allChecked ? 'Pilih Semua' : 'Batal Pilih Semua';
        });

        cplContainer.parentNode.insertBefore(selectAllBtn, cplContainer);
    }
});
</script>
@endsection
