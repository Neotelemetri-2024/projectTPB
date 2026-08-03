@extends('layouts.main')

@section('title', 'Tahun Ajaran Mata Kuliah')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Tahun Ajaran Mata Kuliah</h2>
                <div class="flex space-x-2">
                    <!-- Import/Export Buttons -->
                    <button type="button" data-modal-target="import-modal" data-modal-toggle="import-modal" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg flex items-center text-sm">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        Import Excel
                    </button>
                    <button type="button" data-modal-target="download-modal" data-modal-toggle="download-modal" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg flex items-center text-sm">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download Template
                    </button>
                    <button type="button" data-modal-target="duplicate-modal" data-modal-toggle="duplicate-modal" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1.5 rounded-lg flex items-center text-sm">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Duplicate Tahun Sebelumnya
                    </button>
                    <button type="button" id="btn-bulk-delete" data-modal-target="modal-bulk-delete" data-modal-toggle="modal-bulk-delete" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg flex items-center text-sm hidden">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus Terpilih
                    </button>
                    <a href="{{ route('admin.tahun-ajaran-matkul.create') }}"
                        class="bg-amber-600 hover:bg-amber-700 text-white px-3 py-1.5 rounded-lg flex items-center text-sm">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Mata Kuliah
                    </a>
                </div>
            </div>
        </div>

        <!-- Error Import -->
        @if(session('import_errors'))
        <div class="p-6 border-b border-red-200 bg-red-50">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <h3 class="text-sm font-medium text-red-800">Error Import:</h3>
            </div>
            <div class="mt-2 text-sm text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <!-- Filter dan Search -->
        <div class="p-6 border-b border-gray-200">
            <form method="GET" action="{{ route('admin.tahun-ajaran-matkul.index') }}">
                <!-- Reset page to 1 when filtering -->
                <div class="flex gap-4 items-end">
                    <div class="flex-shrink-0 w-48">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Filter Tahun Ajaran</label>
                        <select name="tahun_ajaran_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" onchange="this.form.submit()">
                            <option value="">Semua Tahun Ajaran</option>
                            @foreach($tahunAjarans as $tahunAjaran)
                            <option value="{{ $tahunAjaran->id }}" {{ $selectedTahunAjaranId == $tahunAjaran->id ? 'selected' : '' }}>
                                {{ $tahunAjaran->tahun }} - {{ $tahunAjaran->periode }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cari Mata Kuliah</label>
                        <input type="text" name="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" placeholder="Cari nama atau kode mata kuliah..." value="{{ request('search') }}">
                    </div>
                    <div class="flex-shrink-0 flex space-x-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2.5 rounded-lg flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Cari
                        </button>
                        <a href="{{ route('admin.tahun-ajaran-matkul.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2.5 rounded-lg flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Reset Semua
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Progress Bar (Hidden by default) -->
        <div id="import-progress-container" class="hidden px-6 py-4 border-b border-gray-200 bg-blue-50">
            <div class="flex justify-between mb-1">
                <span class="text-sm font-medium text-blue-700">Memproses Data Excel...</span>
                <span id="import-progress-text" class="text-sm font-medium text-blue-700">0%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div id="import-progress-bar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-500 ease-out" style="width: 0%"></div>
            </div>
            <p id="import-progress-detail" class="text-xs text-blue-600 mt-2">Menginisialisasi import...</p>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 w-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAll" class="w-4 h-4 text-amber-600 bg-gray-100 border-gray-300 rounded focus:ring-amber-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun Ajaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Kuliah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dosen Pengampu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mahasiswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tahunAjaranMatkuls as $index => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" value="{{ $item->id }}" class="item-checkbox w-4 h-4 text-amber-600 bg-gray-100 border-gray-300 rounded focus:ring-amber-500">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $tahunAjaranMatkuls->firstItem() + $index }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->tahunAjaran->tahun }}-{{ $item->tahunAjaran->periode }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $item->mataKuliah->namaMatkul }}</div>
                                <div class="text-sm text-gray-500">{{ $item->mataKuliah->kodeMatkul }}-{{ $item->mataKuliah->kurikulum }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Semester {{ $item->semester ?? 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="flex flex-wrap gap-1">
                                @foreach($item->kelas as $kelas)
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                    {{ $kelas->namaKelas }}
                                </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="max-w-xs">
                                @php
                                $allDosen = $item->kelas->flatMap->dosenPengampuKelas->map(function($dosenPengampuKelas) {
                                return $dosenPengampuKelas->dosen;
                                })->unique('id');
                                @endphp
                                @foreach($allDosen->take(3) as $dosen)
                                <div class="text-sm">{{ $dosen->nama }}</div>
                                @endforeach
                                @if($allDosen->count() > 3)
                                <div class="text-xs text-gray-500">+{{ $allDosen->count() - 3 }} dosen lainnya</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @php
                            $totalMahasiswa = $item->kelas->flatMap->kelasMahasiswa->pluck('mahasiswaId')->unique()->count();
                            @endphp
                            {{ $totalMahasiswa }} mahasiswa
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.tahun-ajaran-matkul.show', $item->id) }}"
                                    class="text-blue-600 hover:text-blue-900" title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.tahun-ajaran-matkul.edit', $item->id) }}"
                                    class="text-amber-600 hover:text-amber-900" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <button type="button"
                                    data-modal-target="modal-confirm-hapus-{{ $item->id }}"
                                    data-modal-toggle="modal-confirm-hapus-{{ $item->id }}"
                                    class="text-red-600 hover:text-red-900" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">
                            Tidak ada data tahun ajaran mata kuliah
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Menampilkan {{ $tahunAjaranMatkuls->firstItem() ?? 0 }} sampai {{ $tahunAjaranMatkuls->lastItem() ?? 0 }}
                dari {{ $tahunAjaranMatkuls->total() }} data
            </div>
            <div>
                {{ $tahunAjaranMatkuls->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
@foreach($tahunAjaranMatkuls as $item)
<x-confirm-modal
    :id="'modal-confirm-hapus-' . $item->id"
    title="Konfirmasi Hapus"
    :message="'Apakah Anda yakin ingin menghapus mata kuliah ' . $item->mataKuliah->namaMatkul . ' dari tahun ajaran ' . $item->tahunAjaran->tahun . '-' . $item->tahunAjaran->periode . '?'"
    :action="route('admin.tahun-ajaran-matkul.destroy', $item->id)"
    method="DELETE" />
@endforeach

<!-- Modal Import Excel -->
<x-form-modal id="import-modal" title="Import Data Mata Kuliah" :action="route('admin.tahun-ajaran-matkul.import')" submit-text="Import" enctype="multipart/form-data">
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Upload File Excel</label>
        <input type="file" name="file" accept=".xlsx,.xls" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5">
        <div class="mt-4 p-3 bg-amber-50 rounded-lg border border-amber-200">
            <h4 class="text-xs font-semibold text-amber-800 mb-1">Panduan Format File:</h4>
            <ul class="text-[10px] text-amber-700 space-y-1 list-disc list-inside">
                <li>Format: .xlsx atau .xls (Maksimal 2MB)</li>
                <li>Gunakan header: TAHUN_AJARAN, KODE_MATKUL, MATA_KULIAH, SEMESTER, NAMA_KELAS, NAMA_DOSEN, NIP_DOSEN, EMAIL_DOSEN</li>
                <li>Format Tahun: <b>2025/2026 - ganjil</b></li>
                <li>Multiple Dosen: Gunakan titik koma (;) untuk memisahkan.</li>
            </ul>
        </div>
    </div>
</x-form-modal>

<!-- Modal Duplicate Tahun Sebelumnya -->
<x-duplicate-modal id="duplicate-modal" title="Duplicate dari Tahun Sebelumnya" action="{{ route('admin.tahun-ajaran-matkul.duplicate') }}" submit-text="Duplicate">
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran Sumber</label>
        <select name="source_tahun_ajaran_id" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5">
            <option value="">Pilih Tahun Ajaran Sumber</option>
            @foreach($tahunAjarans as $tahunAjaran)
            <option value="{{ $tahunAjaran->id }}">{{ $tahunAjaran->tahun }}-{{ $tahunAjaran->periode }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran Target</label>
        <select name="target_tahun_ajaran_id" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5">
            <option value="">Pilih Tahun Ajaran Target</option>
            @foreach($tahunAjarans as $tahunAjaran)
            <option value="{{ $tahunAjaran->id }}">{{ $tahunAjaran->tahun }}-{{ $tahunAjaran->periode }}</option>
            @endforeach
        </select>
    </div>
</x-duplicate-modal>
 
<!-- Modal Download Template -->
<x-form-modal id="download-modal" title="Download Template" :action="route('admin.tahun-ajaran-matkul.export-template')" method="GET" submit-text="Download">
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Tahun Ajaran</label>
        <select name="tahun_ajaran_id" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5">
            <option value="">Pilih Tahun Ajaran</option>
            @foreach($tahunAjarans as $tahunAjaran)
            <option value="{{ $tahunAjaran->id }}" {{ $selectedTahunAjaranId == $tahunAjaran->id ? 'selected' : '' }}>
                {{ $tahunAjaran->tahun }} - {{ $tahunAjaran->periode }}
            </option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 mt-2 italic">Kolom TAHUN_AJARAN di template akan terisi otomatis sesuai pilihan Anda.</p>
    </div>
</x-form-modal>

<!-- Modal Bulk Delete -->
<div id="modal-bulk-delete" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full" style="background: rgba(0,0,0,0.6);">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow">
            <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="modal-bulk-delete">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
                <span class="sr-only">Tutup modal</span>
            </button>
            <div class="p-4 md:p-5 text-center">
                <svg class="mx-auto mb-4 text-gray-400 w-12 h-12" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <h3 class="mb-5 text-lg font-normal text-gray-500">Apakah Anda yakin ingin menghapus data mata kuliah terpilih?</h3>
                <form id="form-bulk-delete" action="{{ route('admin.tahun-ajaran-matkul.bulk-destroy') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div id="bulk-delete-inputs"></div>
                    <button type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center justify-center px-5 py-2.5 text-center">
                        <svg data-spinner class="hidden w-4 h-4 mr-2 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span data-submit-text>Ya, hapus</span>
                    </button>
                    <button data-modal-hide="modal-bulk-delete" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100">Batal</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const btnBulkDelete = document.getElementById('btn-bulk-delete');
    const bulkDeleteInputs = document.getElementById('bulk-delete-inputs');

    function updateBulkDeleteButton() {
        const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
        if (checkedCount > 0) {
            btnBulkDelete.classList.remove('hidden');
        } else {
            btnBulkDelete.classList.add('hidden');
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkDeleteButton();
        });
    }

    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = document.querySelectorAll('.item-checkbox:checked').length === itemCheckboxes.length;
            if(selectAllCheckbox) selectAllCheckbox.checked = allChecked;
            updateBulkDeleteButton();
        });
    });

    const formBulkDelete = document.getElementById('form-bulk-delete');
    if (formBulkDelete) {
        formBulkDelete.addEventListener('submit', function(e) {
            bulkDeleteInputs.innerHTML = '';
            document.querySelectorAll('.item-checkbox:checked').forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = checkbox.value;
                bulkDeleteInputs.appendChild(input);
            });
            const submitBtn = formBulkDelete.querySelector('button[type="submit"]');
            if (submitBtn) {
                setTimeout(() => {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('cursor-not-allowed', 'opacity-75');
                    const spinner = submitBtn.querySelector('[data-spinner]');
                    const text = submitBtn.querySelector('[data-submit-text]');
                    if (spinner) spinner.classList.remove('hidden');
                }, 10);
            }
        });
    }
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit form if tahun ajaran is auto-selected but not in URL
        // Only do this if we're on the first page and no filters are applied
        const tahunAjaranSelect = document.querySelector('select[name="tahun_ajaran_id"]');
        const urlParams = new URLSearchParams(window.location.search);
        const hasTahunAjaranInUrl = urlParams.has('tahun_ajaran_id');
        const hasPageParam = urlParams.has('page');
        const hasSearchParam = urlParams.has('search');

        // Only auto-submit if:
        // 1. We have a tahun ajaran selected
        // 2. It's not in the URL
        // 3. We're on the first page (no page parameter)
        // 4. No search is active
        // 5. No flash messages are present (to avoid cutting off notifications)
        const hasFlashMessages = {{ session('success') || session('error') || $errors->any() ? 'true' : 'false' }};

        if (tahunAjaranSelect && tahunAjaranSelect.value && !hasTahunAjaranInUrl && !hasPageParam && !hasSearchParam && !hasFlashMessages) {
            // Auto-submit the form to update URL with the selected tahun ajaran
            tahunAjaranSelect.form.submit();
        }
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const importModal = document.getElementById('import-modal');
    if (!importModal) return;
    
    const importForm = importModal.querySelector('form');
    const progressContainer = document.getElementById('import-progress-container');
    const progressBar = document.getElementById('import-progress-bar');
    const progressText = document.getElementById('import-progress-text');
    const progressDetail = document.getElementById('import-progress-detail');

    
    if (importForm) {
        importForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');

            
            if(submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Mengunggah...';
            }
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                
                if (response.redirected) {
                    console.error('[Import] Request was REDIRECTED to:', response.url);
                }

                const contentType = response.headers.get('content-type');
                
                if (!contentType || !contentType.includes('application/json')) {
                    return response.text().then(text => {
                        throw new Error('Server mengembalikan HTML, bukan JSON. Kemungkinan redirect ke login.');
                    });
                }
                
                return response.json();
            })
            .then(data => {
                
                // Close modal
                const closeBtn = importModal.querySelector('[data-modal-hide="import-modal"]');
                if(closeBtn) closeBtn.click();
                
                if (data.status === 'success' && data.job_id) {
                    progressContainer.classList.remove('hidden');
                    pollImportStatus(data.job_id);
                } else {
                    alert(data.message || 'Terjadi kesalahan saat memulai import.');
                    if(submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Import';
                    }
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
                if(submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Import';
                }
            });
        });
    }
    
    function pollImportStatus(jobId) {
        let pollCount = 0;
        const statusUrl = `{{ route('admin.tahun-ajaran-matkul.import-status') }}?job_id=${jobId}`;
        
        const interval = setInterval(() => {
            pollCount++;
            
            fetch(statusUrl, {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => {
                
                const contentType = res.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    return res.text().then(text => {
                        throw new Error('Polling response bukan JSON');
                    });
                }
                
                return res.json();
            })
            .then(data => {
                
                if (data.error) {
                    clearInterval(interval);
                    progressDetail.innerText = 'Error: ' + data.error;
                    return;
                }
                
                const pct = data.percentage || 0;
                progressBar.style.width = `${pct}%`;
                progressText.innerText = `${pct}%`;
                
                if (data.total > 0) {
                    progressDetail.innerText = `Memproses ${data.processed} dari ${data.total} baris...`;
                }
                
                if (data.finished) {
                    clearInterval(interval);
                    progressBar.classList.replace('bg-blue-600', 'bg-green-600');
                    progressText.classList.replace('text-blue-700', 'text-green-700');
                    progressDetail.classList.replace('text-blue-600', 'text-green-600');
                    progressDetail.innerText = "Selesai! Memuat ulang halaman...";
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
                
                // Safety: stop polling after 200 attempts (5 minutes)
                if (pollCount >= 200) {
                    clearInterval(interval);
                    progressDetail.innerText = 'Polling dihentikan. Silakan reload halaman manual.';
                }
            })
            .catch(err => {
                // Don't stop polling on error, but show it
                progressDetail.innerText = 'Error polling: ' + err.message;
            });
        }, 1500);
    }
});
</script>

@endsection