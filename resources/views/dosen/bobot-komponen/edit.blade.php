@extends('layouts.main')

@section('title', 'Edit Bobot Komponen - ' . $mataKuliah->mataKuliah->namaMatkul)

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
            <li class="text-gray-900 font-medium">Edit Bobot</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-4 sm:p-6 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">Edit Bobot Komponen</h1>
            <p class="text-gray-600 mt-1">{{ $mataKuliah->mataKuliah->namaMatkul }} • {{ $mataKuliah->mataKuliah->kodeMatkul }}</p>
        </div>

        <div class="p-4 sm:p-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-blue-900">Bobot Saat Ini</h3>
                    <p class="text-lg font-bold text-blue-800">{{ number_format($bobot->bobot, 1) }}%</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-green-900">Total Bobot Lain</h3>
                    <p class="text-lg font-bold text-green-800">{{ number_format($totalBobotLain, 1) }}%</p>
                </div>
                <div class="bg-yellow-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-yellow-900">Maksimal Baru</h3>
                    <p class="text-lg font-bold text-yellow-800">{{ number_format(100 - $totalBobotLain, 1) }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-md">
        <form action="{{ route('dosen.bobot-komponen.update', [$mataKuliah->id, $bobot->id]) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

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
                <!-- Komponen (Read-only) -->
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Komponen Penilaian
                    </label>
                    <div class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-700">
                        {{ $bobot->komponen->namaKomponen }} - {{ $bobot->komponen->jenisKomponen }}
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Komponen tidak dapat diubah setelah dibuat</p>
                </div>

                <!-- Bobot -->
                <div class="sm:col-span-2">
                    <label for="bobot" class="block text-sm font-medium text-gray-700 mb-2">
                        Bobot (%) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number"
                               step="0.1"
                               min="0.1"
                               max="{{ 100 - $totalBobotLain }}"
                               name="bobot"
                               id="bobot"
                               value="{{ old('bobot', $bobot->bobot) }}"
                               placeholder="Masukkan bobot dalam persen"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('bobot') border-red-500 @enderror">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">%</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Rentang: 0.1% - {{ number_format(100 - $totalBobotLain, 1) }}%
                    </p>
                    @error('bobot')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if($bobot->nilai->count() > 0)
                <!-- Warning tentang nilai yang sudah ada -->
                <div class="sm:col-span-2">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-yellow-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <h3 class="text-sm font-medium text-yellow-800">Perhatian</h3>
                                <p class="text-sm text-yellow-700 mt-1">
                                    Komponen ini sudah memiliki {{ $bobot->nilai->count() }} nilai.
                                    Mengubah bobot akan mempengaruhi perhitungan nilai akhir.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('dosen.bobot-komponen.index', $mataKuliah->id) }}"
                   class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-center">
                    Batal
                </a>
                <button type="submit"
                        class="w-full sm:w-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Update Bobot
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bobotInput = document.getElementById('bobot');
    const maxAllowed = {{ 100 - $totalBobotLain }};

    // Update max value validation
    bobotInput.addEventListener('input', function() {
        const currentValue = parseFloat(this.value) || 0;

        if (currentValue > maxAllowed) {
            this.value = maxAllowed;
        }

        if (currentValue < 0.1) {
            this.value = 0.1;
        }
    });

    // Validate form before submit
    document.querySelector('form').addEventListener('submit', function(e) {
        const bobot = parseFloat(bobotInput.value) || 0;

        if (bobot < 0.1) {
            e.preventDefault();
            alert('Bobot harus minimal 0.1%');
            bobotInput.focus();
            return;
        }

        if (bobot > maxAllowed) {
            e.preventDefault();
            alert(`Bobot maksimal adalah ${maxAllowed}%`);
            bobotInput.focus();
            return;
        }

        @if($bobot->nilai->count() > 0)
        // Confirm if there are existing grades
        if (!confirm('Komponen ini sudah memiliki nilai. Apakah Anda yakin ingin mengubah bobotnya?')) {
            e.preventDefault();
            return;
        }
        @endif
    });
});
</script>
@endsection
