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

        <!-- Parent CPMK Information -->
        <div class="p-6 border-b border-gray-200 bg-blue-50">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">CPMK</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode CPMK</label>
                    <div class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg p-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800">
                            {{ $parentCpmk->kodeCpmk }}
                        </span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi CPMK</label>
                    <div class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg p-3 min-h-[60px]">
                        {{ $parentCpmk->deskripsi }}
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">CPL Terkait CPMK</label>
                <div class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg p-3">
                    <div class="flex flex-wrap gap-2">
                        @foreach($parentCpmk->cpl as $cpl)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $cpl->kodeCpl }}
                            </span>
                        @endforeach
                        @if($parentCpmk->cpl->isEmpty())
                            <span class="text-gray-500 text-sm">Tidak ada CPL terkait</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>



        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Informasi Sub-CPMK</h2>
            <p class="text-sm text-gray-600 mt-1">Lengkapi informasi sub-CPMK yang akan ditambahkan</p>
        </div>

        <form action="{{ route('dosen.cpmk.sub-cpmk.store', [$tahunAjaranMatkul->id, $parentCpmk->id]) }}" method="POST" class="p-6">
            @csrf

            <!-- Hidden inputs for CPL IDs from parent -->
            @foreach($parentCpmk->cpl as $cpl)
                <input type="hidden" name="cpl_ids[]" value="{{ $cpl->id }}">
            @endforeach

            <div class="grid grid-cols-1 gap-6">

                <!-- Kode Sub-CPMK -->
                <div>
                    <label for="kodeCpmk" class="block text-sm font-medium text-gray-700 mb-2">
                        Kode Sub-CPMK <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="kodeCpmk" id="kodeCpmk" required maxlength="20"
                           value="{{ old('kodeCpmk') }}"
                           placeholder="Contoh: {{ $parentCpmk->kodeCpmk }}-1, {{ $parentCpmk->kodeCpmk }}-2, dst."
                           class="bg-gray-50 border text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-3 {{ $errors->has('kodeCpmk') ? 'border-red-500' : 'border-gray-300' }}">
                    @error('kodeCpmk')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Kode unik untuk identifikasi sub-CPMK (maksimal 20 karakter)</p>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi Sub-CPMK <span class="text-red-500">*</span>
                    </label>
                    <textarea name="deskripsi" id="deskripsi" rows="5" required maxlength="1000"
                              placeholder="Masukkan deskripsi sub capaian pembelajaran mata kuliah..."
                              class="bg-gray-50 border text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-3 {{ $errors->has('deskripsi') ? 'border-red-500' : 'border-gray-300' }}">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div class="flex justify-between mt-1">
                        <p class="text-sm text-gray-500">Deskripsikan kemampuan yang lebih spesifik dari CPMK</p>
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
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Sub-CPMK
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
    const parentKode = '{{ $parentCpmk->kodeCpmk }}';

    kodeCpmkInput.addEventListener('focus', function() {
        if (!this.value) {
            // Auto-suggestion based on parent CPMK
            this.placeholder = parentKode + '-1';
        }
    });

    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const kodeCpmk = kodeCpmkInput.value.trim();
        const deskripsi = deskripsiTextarea.value.trim();

        if (!kodeCpmk || !deskripsi) {
            e.preventDefault();
            alert('Harap lengkapi semua field yang wajib diisi');
            return false;
        }

        if (deskripsi.length < 10) {
            e.preventDefault();
            alert('Deskripsi sub-CPMK minimal 10 karakter');
            deskripsiTextarea.focus();
            return false;
        }
    });


});
</script>
@endsection
