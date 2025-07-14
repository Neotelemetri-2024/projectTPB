@extends('layouts.main')

@section('title', 'Atur Bobot Bulk - ' . $tahunAjaranMatkul->mataKuliah->namaMatkul)

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
                <a href="{{ route('dosen.cpmk.show', $tahunAjaranMatkul->id) }}" class="hover:text-gray-700">{{ $tahunAjaranMatkul->mataKuliah->namaMatkul }}</a>
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
            <p class="text-gray-600 mt-1">{{ $tahunAjaranMatkul->mataKuliah->namaMatkul }} • {{ $tahunAjaranMatkul->mataKuliah->kodeMatkul }}</p>
        </div>

        <div class="p-4 sm:p-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-blue-800">Pengaturan Bobot Komponen</h3>
                        <div class="text-sm text-blue-700 mt-1">
                            <p>• Pilih komponen penilaian yang akan digunakan terlebih dahulu</p>
                            <p>• Atur bobot untuk setiap kombinasi CPMK dan Komponen penilaian</p>
                            <p>• Total bobot maksimal 100% (tidak harus tepat 100%)</p>
                            <p>• Bobot yang sudah memiliki nilai mahasiswa akan terkunci (tidak dapat diubah)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('dosen.bobot-komponen.bulk-store', $tahunAjaranMatkul->id) }}" method="POST" class="p-6">
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

            <!-- Komponen Management -->
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <h3 class="text-sm font-medium text-green-900 mb-3">Kelola Komponen Penilaian</h3>
                <div class="space-y-4">
                    <!-- Available Komponen -->
                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-2 block">Pilih Komponen Penilaian:</label>
                        <div id="available-komponen" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-w-2xl">
                            @foreach($komponen as $komponenItem)
                                <div class="komponen-card border border-gray-300 rounded-lg p-3 cursor-pointer hover:border-green-500 hover:bg-green-50 transition-all duration-200"
                                     data-id="{{ $komponenItem->id }}"
                                     data-nama="{{ $komponenItem->nama }}">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <h4 class="text-sm font-medium text-gray-900">{{ $komponenItem->nama}}</h4>
                                        </div>
                                        <div class="ml-2">
                                            <!-- Toggle button with + and x icons -->
                                            <button type="button" class="toggle-komponen-btn w-6 h-6 bg-green-600 hover:bg-green-700 text-white rounded-full flex items-center justify-center text-xs font-bold transition-all duration-200">
                                                <span class="add-icon">+</span>
                                                <span class="remove-icon hidden">×</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Counter -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Total Bobot</h3>
                        <p class="text-sm text-gray-600">Maksimal 100% (tidak harus tepat 100%)</p>
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
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <button type="button"
                            id="clear-all"
                            class="px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm rounded-md">
                        Kosongkan Semua Input
                    </button>
                    <button type="button"
                            id="clear-komponen"
                            class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-md">
                        Hapus Semua Komponen
                    </button>
                </div>
            </div>

            <!-- CPMK-Komponen Matrix -->
            <div class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900">Matriks Bobot CPMK - Komponen</h3>

                @if($cpmkList->isEmpty())
                    <div class="text-center py-8 w-full">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada CPMK Tersedia</h3>
                        <p class="text-gray-500">Belum ada CPMK yang ditetapkan untuk mata kuliah ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table id="bobot-table" class="min-w-full bg-white border border-gray-200 rounded-lg">
                            <thead class="bg-gray-50">
                                <tr id="table-header-row">
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-r border-gray-200">
                                        CPMK
                                    </th>
                                    <!-- Komponen headers will be added dynamically here -->
                                </tr>
                            </thead>
                            <tbody id="table-body" class="divide-y divide-gray-200">
                                @foreach($cpmkList as $cpmk)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 border-r border-gray-200 bg-gray-50">
                                        <div class="flex flex-col">
                                            <span class="font-semibold">{{ $cpmk->cpmk->kodeCpmk }}</span>
                                            <span class="text-xs text-gray-600 mt-1">{{ Str::limit($cpmk->cpmk->deskripsi, 60) }}</span>
                                        </div>
                                    </td>
                                    <!-- Komponen columns will be added dynamically -->
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Message when no komponen selected -->
                        <div id="no-komponen-message" class="text-center py-4 border-t border-gray-200 bg-yellow-50">
                            <div class="flex items-center justify-center">
                                <svg class="w-5 h-5 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.98-.833-2.75 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <span class="text-sm text-yellow-700 font-medium">Pilih komponen penilaian di atas untuk mulai mengatur bobot</span>
                            </div>
                        </div>
                    </div>

                    <!-- Existing Locked Bobot Info -->
                    @if(count($bobotWithNilai) > 0)
                    <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 text-red-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 616 0z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <h3 class="text-sm font-medium text-red-800">Bobot Terkunci</h3>
                                <p class="text-sm text-red-700 mt-1">
                                    Beberapa kombinasi CPMK-Komponen sudah memiliki nilai mahasiswa dan tidak dapat diubah.
                                    Bobot yang terkunci ditandai dengan ikon gembok.
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('dosen.cpmk.show', $tahunAjaranMatkul->id) }}"
                   class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 text-center">
                    Batal
                </a>
                <button type="submit"
                        id="submit-btn"
                        class="w-full sm:w-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-400 cursor-not-allowed"
                        disabled>
                    Simpan Semua Bobot
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Set data for external JavaScript
window.cpmkListData = {!! json_encode($cpmkList) !!};
window.komponenListData = {!! json_encode($komponen) !!};
window.existingCombinationsData = {!! json_encode($existingCombinations) !!};
window.bobotWithNilaiData = {!! json_encode($bobotWithNilai) !!};
window.usedKomponenIdsData = {!! json_encode($usedKomponenIds) !!};
</script>
<script src="{{ asset('assets/js/bobot-bulk-create.js') }}"></script>
@endsection
