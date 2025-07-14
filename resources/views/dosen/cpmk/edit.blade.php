@extends('layouts.main')

@section('title', 'Edit CPMK - ' . $cpmk->kodeCpmk)

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit CPMK</h1>
                    <p class="text-gray-600 mt-1">{{ $tahunAjaranMatkul->mataKuliah->namaMatkul }} • {{ $tahunAjaranMatkul->mataKuliah->kodeMatkul }}</p>
                </div>
                <a href="{{ route('dosen.cpmk.index', $tahunAjaranMatkul->id) }}"
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
            <h2 class="text-xl font-semibold text-gray-900">Edit Informasi CPMK</h2>
            <p class="text-sm text-gray-600 mt-1">Perbarui informasi CPMK {{ $cpmk->kodeCpmk }}</p>
        </div>

        <form action="{{ route('dosen.cpmk.update', [$tahunAjaranMatkul->id, $cpmk->id]) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6">
                <!-- CPL Selection -->
                <div>
                    <label for="idCpl" class="block text-sm font-medium text-gray-700 mb-2">
                        Capaian Pembelajaran Lulusan (CPL) <span class="text-red-500">*</span>
                    </label>
                    <select name="idCpl" id="idCpl" required
                            class="bg-gray-50 border text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-3 {{ $errors->has('idCpl') ? 'border-red-500' : 'border-gray-300' }}">
                        <option value="">Pilih CPL</option>
                        @foreach($cplList as $cpl)
                            <option value="{{ $cpl->id }}" {{ (old('idCpl', $cpmk->idCpl) == $cpl->id) ? 'selected' : '' }}>
                                {{ $cpl->kodeCpl }} - {{ $cpl->deskripsi }}
                            </option>
                        @endforeach
                    </select>
                    @error('idCpl')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Pilih CPL yang akan dicapai melalui CPMK ini</p>
                </div>

                <!-- Kode CPMK -->
                <div>
                    <label for="kodeCpmk" class="block text-sm font-medium text-gray-700 mb-2">
                        Kode CPMK <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="kodeCpmk" id="kodeCpmk" required maxlength="20"
                           value="{{ old('kodeCpmk', $cpmk->kodeCpmk) }}"
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
                              class="bg-gray-50 border text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-3 {{ $errors->has('deskripsi') ? 'border-red-500' : 'border-gray-300' }}">{{ old('deskripsi', $cpmk->deskripsi) }}</textarea>
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
                <a href="{{ route('dosen.cpmk.index', $tahunAjaranMatkul->id) }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2.5 rounded-lg font-medium transition-colors duration-200">
                    Batal
                </a>
                <button type="submit"
                        class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Perbarui CPMK
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

    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const kodeCpmk = document.getElementById('kodeCpmk').value.trim();
        const deskripsi = deskripsiTextarea.value.trim();
        const idCpl = document.getElementById('idCpl').value;

        if (!kodeCpmk || !deskripsi || !idCpl) {
            e.preventDefault();
            alert('Harap lengkapi semua field yang wajib diisi');
            return false;
        }

        if (deskripsi.length < 10) {
            e.preventDefault();
            alert('Deskripsi CPMK minimal 10 karakter');
            deskripsiTextarea.focus();
            return false;
        }
    });
});
</script>
@endsection
