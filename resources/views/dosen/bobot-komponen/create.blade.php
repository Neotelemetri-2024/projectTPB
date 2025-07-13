@extends('layouts.main')

@section('title', 'Tambah Bobot Komponen - ' . $mataKuliah->mataKuliah->namaMatkul)

@section('content')
<div class="p-6">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li>
                <a href="{{ route('dosen.mata-kuliah.index') }}" class="hover:text-gray-700">Mata Kuliah</a>
            </li>
            <li>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </li>
            <li>
                <a href="{{ route('dosen.mata-kuliah.show', $mataKuliah->id) }}" class="hover:text-gray-700">{{ $mataKuliah->mataKuliah->namaMatkul }}</a>
            </li>
            <li>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </li>
            <li>
                <a href="{{ route('dosen.bobot-komponen.index', $mataKuliah->id) }}" class="hover:text-gray-700">Bobot Komponen</a>
            </li>
            <li>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </li>
            <li class="text-gray-900 font-medium">Tambah Bobot</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-4 sm:p-6 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">Tambah Bobot Komponen</h1>
            <p class="text-gray-600 mt-1">{{ $mataKuliah->mataKuliah->namaMatkul }} • {{ $mataKuliah->mataKuliah->kodeMatkul }}</p>
        </div>

        @if($totalBobot > 0)
        <div class="p-4 sm:p-6">
            <div class="flex items-center justify-between bg-blue-50 rounded-lg p-4">
                <div>
                    <h3 class="text-sm font-medium text-blue-900">Total Bobot Saat Ini</h3>
                    <p class="text-lg font-bold text-blue-800">{{ number_format($totalBobot, 1) }}%</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-blue-900">Sisa Bobot</h3>
                    <p class="text-lg font-bold text-blue-800">{{ number_format(100 - $totalBobot, 1) }}%</p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-md">
        <form action="{{ route('dosen.bobot-komponen.store', $mataKuliah->id) }}" method="POST" class="p-6">
            @csrf

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <h3 class="text-sm font-medium text-red-800 mb-2">Terdapat kesalahan:</h3>
                    <ul class="text-sm text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- Komponen -->
                <div class="sm:col-span-2">
                    <label for="komponenId" class="block text-sm font-medium text-gray-700 mb-2">
                        Komponen Penilaian <span class="text-red-500">*</span>
                    </label>
                    <select name="komponenId" id="komponenId"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('komponenId') border-red-500 @enderror">
                        <option value="">Pilih Komponen Penilaian</option>
                        @foreach($komponen as $item)
                            <option value="{{ $item->id }}" {{ old('komponenId') == $item->id ? 'selected' : '' }}>
                                {{ $item->namaKomponen }} - {{ $item->jenisKomponen }}
                            </option>
                        @endforeach
                    </select>
                    @error('komponenId')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bobot -->
                <div class="sm:col-span-2">
                    <label for="bobot" class="block text-sm font-medium text-gray-700 mb-2">
                        Bobot (%) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number"
                               step="0.1"
                               min="0"
                               max="{{ 100 - $totalBobot }}"
                               name="bobot"
                               id="bobot"
                               value="{{ old('bobot') }}"
                               placeholder="Masukkan bobot dalam persen"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('bobot') border-red-500 @enderror">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">%</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Maksimal: {{ number_format(100 - $totalBobot, 1) }}% (sisa dari total 100%)
                    </p>
                    @error('bobot')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('dosen.bobot-komponen.index', $mataKuliah->id) }}"
                   class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-center">
                    Batal
                </a>
                <button type="submit"
                        class="w-full sm:w-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Simpan Bobot
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bobotInput = document.getElementById('bobot');
    const komponenSelect = document.getElementById('komponenId');

    // Update max value when total bobot changes
    bobotInput.addEventListener('input', function() {
        const currentValue = parseFloat(this.value) || 0;
        const maxAllowed = {{ 100 - $totalBobot }};

        if (currentValue > maxAllowed) {
            this.value = maxAllowed;
        }
    });

    // Validate form before submit
    document.querySelector('form').addEventListener('submit', function(e) {
        const komponenId = komponenSelect.value;
        const bobot = parseFloat(bobotInput.value) || 0;

        if (!komponenId) {
            e.preventDefault();
            alert('Silakan pilih komponen penilaian');
            komponenSelect.focus();
            return;
        }

        if (bobot <= 0) {
            e.preventDefault();
            alert('Bobot harus lebih besar dari 0');
            bobotInput.focus();
            return;
        }

        if (bobot > {{ 100 - $totalBobot }}) {
            e.preventDefault();
            alert('Bobot melebihi batas maksimal yang tersisa');
            bobotInput.focus();
            return;
        }
    });
});
</script>
@endsection
