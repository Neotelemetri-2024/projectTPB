@extends('layouts.main')

@section('title', 'Atur Bobot Bulk - ' . $mataKuliah->mataKuliah->namaMatkul)

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
            <li class="text-gray-900 font-medium">Atur Bobot Bulk</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-4 sm:p-6 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">Atur Bobot Komponen Secara Bulk</h1>
            <p class="text-gray-600 mt-1">{{ $mataKuliah->mataKuliah->namaMatkul }} • {{ $mataKuliah->mataKuliah->kodeMatkul }}</p>
        </div>

        <div class="p-4 sm:p-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-blue-900">Panduan Pengaturan Bulk</h3>
                        <div class="text-sm text-blue-800 mt-1">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Pilih komponen yang ingin diberi bobot</li>
                                <li>Total bobot harus tepat 100%</li>
                                <li>Bobot dapat diatur secara otomatis dengan distribusi merata</li>
                                <li>Komponen yang sudah memiliki nilai tidak akan diganti bobotnya</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-md">
        <form action="{{ route('dosen.bobot-komponen.bulk-store', $mataKuliah->id) }}" method="POST" class="p-6">
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

            <!-- Total Counter -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Total Bobot</h3>
                        <p class="text-sm text-gray-600">Harus tepat 100%</p>
                    </div>
                    <div class="text-right">
                        <div id="total-display" class="text-3xl font-bold text-blue-600">0.0%</div>
                        <div id="status-display" class="text-sm text-gray-500">Belum sesuai</div>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div id="progress-bar" class="h-3 rounded-full bg-blue-500 transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <h3 class="text-sm font-medium text-yellow-900 mb-3">Aksi Cepat</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <button type="button"
                            id="distribute-even"
                            class="px-3 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm rounded-md">
                        Distribusi Merata
                    </button>
                    <button type="button"
                            id="clear-all"
                            class="px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm rounded-md">
                        Kosongkan Semua
                    </button>
                    <button type="button"
                            id="select-all"
                            class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-md">
                        Pilih Semua
                    </button>
                </div>
            </div>

            <!-- Komponen List -->
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900">Komponen Penilaian</h3>

                @if($komponen->isEmpty())
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Komponen Tersedia</h3>
                        <p class="text-gray-500">Semua komponen sudah memiliki bobot atau belum ada komponen yang dibuat.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($komponen as $item)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <input type="checkbox"
                                               name="komponen_ids[]"
                                               value="{{ $item->id }}"
                                               id="komponen_{{ $item->id }}"
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded komponen-checkbox">
                                        <div>
                                            <label for="komponen_{{ $item->id }}" class="text-sm font-medium text-gray-900 cursor-pointer">
                                                {{ $item->namaKomponen }}
                                            </label>
                                            <p class="text-xs text-gray-500">{{ $item->jenisKomponen }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <div class="relative">
                                            <input type="number"
                                                   step="0.1"
                                                   min="0"
                                                   max="100"
                                                   name="bobot[{{ $item->id }}]"
                                                   id="bobot_{{ $item->id }}"
                                                   placeholder="0.0"
                                                   class="w-20 px-2 py-1 border border-gray-300 rounded-md text-sm text-center bobot-input"
                                                   disabled>
                                            <span class="absolute inset-y-0 right-0 flex items-center pr-2 text-xs text-gray-500">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Existing Bobot Info -->
            @if($existingBobot->count() > 0)
            <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <h3 class="text-sm font-medium text-green-900 mb-3">Bobot Yang Sudah Ada</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($existingBobot as $existing)
                        <div class="flex justify-between items-center py-2 px-3 bg-white rounded border">
                            <span class="text-sm text-gray-900">{{ $existing->komponen->namaKomponen }}</span>
                            <span class="text-sm font-medium text-green-600">{{ number_format($existing->bobot, 1) }}%</span>
                        </div>
                    @endforeach
                </div>
                <p class="text-xs text-green-700 mt-2">
                    Total yang sudah ada: {{ number_format($existingBobot->sum('bobot'), 1) }}%
                </p>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('dosen.bobot-komponen.index', $mataKuliah->id) }}"
                   class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-center">
                    Batal
                </a>
                <button type="submit"
                        id="submit-btn"
                        disabled
                        class="w-full sm:w-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    Simpan Semua Bobot
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.komponen-checkbox');
    const bobotInputs = document.querySelectorAll('.bobot-input');
    const totalDisplay = document.getElementById('total-display');
    const statusDisplay = document.getElementById('status-display');
    const progressBar = document.getElementById('progress-bar');
    const submitBtn = document.getElementById('submit-btn');
    const existingTotal = {{ $existingBobot->sum('bobot') }};

    // Quick action buttons
    document.getElementById('distribute-even').addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.komponen-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Pilih komponen terlebih dahulu');
            return;
        }

        const availableTotal = 100 - existingTotal;
        const perComponent = availableTotal / checkedBoxes.length;

        checkedBoxes.forEach(checkbox => {
            const komponenId = checkbox.value;
            const bobotInput = document.getElementById(`bobot_${komponenId}`);
            bobotInput.value = perComponent.toFixed(1);
        });

        updateTotal();
    });

    document.getElementById('clear-all').addEventListener('click', function() {
        bobotInputs.forEach(input => {
            input.value = '';
        });
        updateTotal();
    });

    document.getElementById('select-all').addEventListener('click', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
            const komponenId = checkbox.value;
            const bobotInput = document.getElementById(`bobot_${komponenId}`);
            bobotInput.disabled = false;
        });
    });

    // Handle checkbox changes
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const komponenId = this.value;
            const bobotInput = document.getElementById(`bobot_${komponenId}`);

            if (this.checked) {
                bobotInput.disabled = false;
                bobotInput.focus();
            } else {
                bobotInput.disabled = true;
                bobotInput.value = '';
            }

            updateTotal();
        });
    });

    // Handle bobot input changes
    bobotInputs.forEach(input => {
        input.addEventListener('input', function() {
            updateTotal();
        });
    });

    function updateTotal() {
        let total = existingTotal;

        bobotInputs.forEach(input => {
            if (!input.disabled && input.value) {
                total += parseFloat(input.value) || 0;
            }
        });

        // Update display
        totalDisplay.textContent = total.toFixed(1) + '%';

        // Update status
        if (total === 100) {
            statusDisplay.textContent = 'Sesuai ✓';
            statusDisplay.className = 'text-sm text-green-600';
            progressBar.className = 'h-3 rounded-full bg-green-500 transition-all duration-300';
            submitBtn.disabled = false;
        } else if (total > 100) {
            statusDisplay.textContent = 'Melebihi 100%';
            statusDisplay.className = 'text-sm text-red-600';
            progressBar.className = 'h-3 rounded-full bg-red-500 transition-all duration-300';
            submitBtn.disabled = true;
        } else {
            statusDisplay.textContent = 'Kurang dari 100%';
            statusDisplay.className = 'text-sm text-yellow-600';
            progressBar.className = 'h-3 rounded-full bg-yellow-500 transition-all duration-300';
            submitBtn.disabled = true;
        }

        // Update progress bar
        progressBar.style.width = Math.min(total, 100) + '%';
    }

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const checkedBoxes = document.querySelectorAll('.komponen-checkbox:checked');

        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('Pilih minimal satu komponen');
            return;
        }

        let total = existingTotal;
        let hasEmptyBobot = false;

        checkedBoxes.forEach(checkbox => {
            const komponenId = checkbox.value;
            const bobotInput = document.getElementById(`bobot_${komponenId}`);
            const bobotValue = parseFloat(bobotInput.value) || 0;

            if (bobotValue <= 0) {
                hasEmptyBobot = true;
            }

            total += bobotValue;
        });

        if (hasEmptyBobot) {
            e.preventDefault();
            alert('Semua komponen yang dipilih harus memiliki bobot lebih dari 0');
            return;
        }

        if (total !== 100) {
            e.preventDefault();
            alert(`Total bobot harus tepat 100%. Saat ini: ${total.toFixed(1)}%`);
            return;
        }
    });

    // Initial update
    updateTotal();
});
</script>
@endsection
