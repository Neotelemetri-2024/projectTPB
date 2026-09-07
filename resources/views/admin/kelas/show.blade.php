@extends('layouts.main')
@section('title', 'Detail Kelas')

@section('content')
<div class="p-4 md:p-6 space-y-4">
    <div class="bg-white border border-gray-200 rounded-xl">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Detail Kelas {{ $kelas->namaKelas }}</h2>
                <a href="{{ route('admin.tahun-ajaran-matkul.show', $kelas->tahunAjaranMatkul->id) }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <div class="p-6">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Informasi Kelas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Informasi Kelas</h3>
                    <div class="space-y-3">
                        <div class="flex">
                            <span class="w-24 text-sm font-medium text-gray-600">Mata Kuliah:</span>
                            <span class="text-sm text-gray-900">{{ $kelas->tahunAjaranMatkul->mataKuliah->namaMatkul }}</span>
                        </div>
                        <div class="flex">
                            <span class="w-24 text-sm font-medium text-gray-600">Kode:</span>
                            <span class="text-sm text-gray-900">{{ $kelas->tahunAjaranMatkul->mataKuliah->kodeMatkul }}-{{ $kelas->tahunAjaranMatkul->mataKuliah->kurikulum }}</span>
                        </div>
                        <div class="flex">
                            <span class="w-24 text-sm font-medium text-gray-600">Tahun Ajaran:</span>
                            <span class="text-sm text-gray-900">{{ $kelas->tahunAjaranMatkul->tahunAjaran->tahun }}-{{ $kelas->tahunAjaranMatkul->tahunAjaran->periode }}</span>
                        </div>
                        <div class="flex">
                            <span class="w-24 text-sm font-medium text-gray-600">Kelas:</span>
                            <span class="text-sm text-gray-900">{{ $kelas->namaKelas }}</span>
                        </div>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Statistik</h3>
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div class="border-r border-gray-200">
                            <div class="text-2xl font-semibold text-gray-900">{{ $kelas->dosenPengampuKelas->count() }}</div>
                            <p class="text-sm text-gray-500">Dosen Pengampu</p>
                        </div>
                        <div>
                            <div class="text-2xl font-semibold text-gray-900">{{ $kelas->kelasMahasiswa->count() }}</div>
                            <p class="text-sm text-gray-500">Mahasiswa</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dosen Pengampu -->
            <div class="mb-6">
                <div class="bg-white rounded-xl border border-gray-200">
                    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-base font-semibold text-gray-900">Dosen Pengampu</h3>
                        <a href="{{ route('admin.kelas.manage-dosen', $kelas->id) }}"
                           class="bg-amber-600 hover:bg-amber-700 text-white px-3 py-1 rounded text-sm flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Kelola Dosen
                        </a>
                    </div>
                    <div class="p-4">
                        @if($kelas->dosenPengampuKelas->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Dosen</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($kelas->dosenPengampuKelas as $index => $dosenPengampuKelas)
                                            @php
                                                $dosen = $dosenPengampuKelas->dosen;
                                            @endphp
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $dosen->nip }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $dosen->nama }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $dosen->user->email ?? '-' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <button type="button"
                                                            data-modal-target="modal-confirm-hapus-dosen-{{ $dosen->id }}"
                                                            data-modal-toggle="modal-confirm-hapus-dosen-{{ $dosen->id }}"
                                                            class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white p-1.5 rounded-md" title="Hapus">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">Belum ada dosen pengampu</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Mahasiswa -->
            <div class="mb-6">
                <div class="bg-white rounded-xl border border-gray-200">
                    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-base font-semibold text-gray-900">Mahasiswa</h3>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.kelas.manage-mahasiswa', $kelas->id) }}"
                               class="bg-amber-600 hover:bg-amber-700 text-white px-3 py-1 rounded text-sm flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Kelola Mahasiswa
                            </a>
                            @if($kelas->kelasMahasiswa->count() > 0)
                                <button type="button" id="toggle-bulk-delete"
                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Hapus Bulk
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="p-4">
                        @if($kelas->kelasMahasiswa->count() > 0)
                            <!-- Bulk Delete Controls (Hidden by default) -->
                            <div id="bulk-delete-controls" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg hidden">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <span class="text-sm text-red-700" id="selected-count">0 mahasiswa dipilih</span>
                                        <button type="button" id="select-all-mahasiswa" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            Pilih Semua
                                        </button>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button type="button" id="cancel-bulk-delete" class="text-gray-600 hover:text-gray-800 text-sm">
                                            Batal
                                        </button>
                                        <button type="button" id="confirm-bulk-delete"
                                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                                disabled>
                                            Hapus yang Dipilih
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <form id="bulk-delete-form" action="{{ route('admin.kelas.bulk-remove-mahasiswa', $kelas->id) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bulk-delete-column hidden">
                                                <input type="checkbox" id="select-all-checkbox" class="rounded border-gray-300 text-red-600 focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Mahasiswa</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun Masuk</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider individual-delete-column">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($kelas->kelasMahasiswa as $index => $kelasMahasiswaItem)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap bulk-delete-column hidden">
                                                    <input type="checkbox"
                                                           name="mahasiswa_ids[]"
                                                           value="{{ $kelasMahasiswaItem->mahasiswa->id }}"
                                                           class="mahasiswa-checkbox rounded border-gray-300 text-red-600 focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $kelasMahasiswaItem->mahasiswa->nim }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $kelasMahasiswaItem->mahasiswa->nama }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $kelasMahasiswaItem->mahasiswa->tahunMasuk }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $kelasMahasiswaItem->mahasiswa->user->email ?? '-' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium individual-delete-column">
                                                    <button type="button"
                                                            data-modal-target="modal-confirm-hapus-mahasiswa-{{ $kelasMahasiswaItem->mahasiswa->id }}"
                                                            data-modal-toggle="modal-confirm-hapus-mahasiswa-{{ $kelasMahasiswaItem->mahasiswa->id }}"
                                                            class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white p-1.5 rounded-md" title="Hapus">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">Belum ada mahasiswa</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Dosen -->
@foreach($kelas->dosenPengampuKelas as $dosenPengampuKelas)
    @php
        $dosen = $dosenPengampuKelas->dosen;
    @endphp
    <x-confirm-modal
        :id="'modal-confirm-hapus-dosen-' . $dosen->id"
        title="Konfirmasi Hapus Dosen Pengampu"
        :message="'Apakah Anda yakin ingin menghapus ' . $dosen->nama . ' dari Kelas ' . $kelas->namaKelas . '?'"
        :action="route('admin.kelas.remove-dosen', [$kelas->id, $dosen->id])"
        method="DELETE"
    />
@endforeach

<!-- Modal Konfirmasi Hapus Mahasiswa -->
@foreach($kelas->kelasMahasiswa as $kelasMahasiswaItem)
    <x-confirm-modal
        :id="'modal-confirm-hapus-mahasiswa-' . $kelasMahasiswaItem->mahasiswa->id"
        title="Konfirmasi Hapus Mahasiswa"
        :message="'Apakah Anda yakin ingin menghapus ' . $kelasMahasiswaItem->mahasiswa->nama . ' dari Kelas ' . $kelas->namaKelas . '?'"
        :action="route('admin.kelas.remove-mahasiswa', [$kelas->id, $kelasMahasiswaItem->mahasiswa->id])"
        method="DELETE"
    />
@endforeach

<!-- Modal Konfirmasi Bulk Delete -->
<div id="modal-confirm-bulk-delete" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center min-h-screen w-full transition-opacity duration-300 ease-out" style="background: rgba(0,0,0,0.6);">
    <div class="relative p-4 w-full max-w-md max-h-full transform transition-all duration-300 ease-out scale-95 opacity-0" data-modal-content>
        <div class="relative bg-white rounded-xl border border-gray-200 animate-in fade-in slide-in-from-top-4 duration-300">
            <div class="flex items-center justify-between p-4 md:p-5 border-b border-gray-200 rounded-t">
                <h3 class="text-lg font-semibold text-gray-900">
                    Konfirmasi Hapus Bulk Mahasiswa
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition-colors duration-200" data-modal-hide="modal-confirm-bulk-delete">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <div class="p-4 md:p-5">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.734-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-700">Apakah Anda yakin ingin menghapus mahasiswa yang dipilih dari Kelas {{ $kelas->namaKelas }}? Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                </div>
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" class="bg-gray-600 hover:bg-gray-700 text-white focus:ring-4 focus:outline-none focus:ring-gray-300 rounded-lg text-sm font-medium px-5 py-2.5 focus:z-10 transition-colors duration-200" data-modal-hide="modal-confirm-bulk-delete">
                        Batal
                    </button>
                    <button type="button" id="confirm-bulk-delete-submit" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Ya, Hapus Semua
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBulkDeleteBtn = document.getElementById('toggle-bulk-delete');
    const bulkDeleteControls = document.getElementById('bulk-delete-controls');
    const bulkDeleteForm = document.getElementById('bulk-delete-form');
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    const mahasiswaCheckboxes = document.querySelectorAll('.mahasiswa-checkbox');
    const selectedCountSpan = document.getElementById('selected-count');
    const confirmBulkDeleteBtn = document.getElementById('confirm-bulk-delete');
    const cancelBulkDeleteBtn = document.getElementById('cancel-bulk-delete');
    const selectAllMahasiswaBtn = document.getElementById('select-all-mahasiswa');
    const bulkDeleteColumns = document.querySelectorAll('.bulk-delete-column');
    const individualDeleteColumns = document.querySelectorAll('.individual-delete-column');

    let isBulkDeleteMode = false;

    function updateSelectedCount() {
        const selectedCount = document.querySelectorAll('.mahasiswa-checkbox:checked').length;
        selectedCountSpan.textContent = `${selectedCount} mahasiswa dipilih`;
        confirmBulkDeleteBtn.disabled = selectedCount === 0;
    }

    function updateSelectAllState() {
        const totalCheckboxes = mahasiswaCheckboxes.length;
        const checkedCheckboxes = document.querySelectorAll('.mahasiswa-checkbox:checked').length;

        if (checkedCheckboxes === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (checkedCheckboxes === totalCheckboxes) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }

    function enterBulkDeleteMode() {
        isBulkDeleteMode = true;
        bulkDeleteControls.classList.remove('hidden');
        bulkDeleteColumns.forEach(col => col.classList.remove('hidden'));
        individualDeleteColumns.forEach(col => col.classList.add('hidden'));
        toggleBulkDeleteBtn.textContent = 'Keluar Mode Hapus';
        toggleBulkDeleteBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
        toggleBulkDeleteBtn.classList.add('bg-gray-600', 'hover:bg-gray-700');
    }

    function exitBulkDeleteMode() {
        isBulkDeleteMode = false;
        bulkDeleteControls.classList.add('hidden');
        bulkDeleteColumns.forEach(col => col.classList.add('hidden'));
        individualDeleteColumns.forEach(col => col.classList.remove('hidden'));
        toggleBulkDeleteBtn.textContent = 'Hapus Bulk';
        toggleBulkDeleteBtn.classList.remove('bg-gray-600', 'hover:bg-gray-700');
        toggleBulkDeleteBtn.classList.add('bg-red-600', 'hover:bg-red-700');

        // Reset checkboxes
        mahasiswaCheckboxes.forEach(checkbox => checkbox.checked = false);
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
        updateSelectedCount();
    }

    // Toggle bulk delete mode
    if (toggleBulkDeleteBtn) {
        toggleBulkDeleteBtn.addEventListener('click', function() {
            if (isBulkDeleteMode) {
                exitBulkDeleteMode();
            } else {
                enterBulkDeleteMode();
            }
        });
    }

    // Cancel bulk delete
    if (cancelBulkDeleteBtn) {
        cancelBulkDeleteBtn.addEventListener('click', function() {
            exitBulkDeleteMode();
        });
    }

    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            mahasiswaCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
            updateSelectAllState();
        });
    }

    // Individual checkbox change
    mahasiswaCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectedCount();
            updateSelectAllState();
        });
    });

    // Select all button
    if (selectAllMahasiswaBtn) {
        selectAllMahasiswaBtn.addEventListener('click', function() {
            const allChecked = Array.from(mahasiswaCheckboxes).every(cb => cb.checked);
            mahasiswaCheckboxes.forEach(checkbox => {
                checkbox.checked = !allChecked;
            });
            selectAllCheckbox.checked = !allChecked;
            selectAllCheckbox.indeterminate = false;
            updateSelectedCount();
            updateSelectAllState();

            // Update button text
            this.textContent = allChecked ? 'Pilih Semua' : 'Hapus Semua';
        });
    }

    // Confirm bulk delete
    if (confirmBulkDeleteBtn) {
        confirmBulkDeleteBtn.addEventListener('click', function() {
            const selectedCheckboxes = document.querySelectorAll('.mahasiswa-checkbox:checked');
            if (selectedCheckboxes.length === 0) {
                alert('Pilih minimal satu mahasiswa untuk dihapus');
                return;
            }

            // Update modal message with count
            const modalMessage = document.querySelector('#modal-confirm-bulk-delete .text-gray-700');
            if (modalMessage) {
                modalMessage.textContent = `Apakah Anda yakin ingin menghapus ${selectedCheckboxes.length} mahasiswa dari Kelas {{ $kelas->namaKelas }}? Tindakan ini tidak dapat dibatalkan.`;
            }

            // Update form action
            const form = document.getElementById('bulk-delete-form');
            if (form) {
                // Clear existing inputs
                const existingInputs = form.querySelectorAll('input[name="mahasiswa_ids[]"]');
                existingInputs.forEach(input => input.remove());

                // Add selected mahasiswa IDs
                selectedCheckboxes.forEach(checkbox => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'mahasiswa_ids[]';
                    input.value = checkbox.value;
                    form.appendChild(input);
                });
            }

            // Show modal
            showBulkDeleteModal();
        });
    }

    // Show bulk delete modal
    function showBulkDeleteModal() {
        const modal = document.getElementById('modal-confirm-bulk-delete');
        const modalContent = modal.querySelector('[data-modal-content]');

        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Trigger animation
            setTimeout(() => {
                modal.classList.remove('bg-opacity-0');
                modal.classList.add('bg-opacity-10');
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }
    }

    // Hide bulk delete modal
    function hideBulkDeleteModal() {
        const modal = document.getElementById('modal-confirm-bulk-delete');
        const modalContent = modal.querySelector('[data-modal-content]');

        modalContent.classList.add('scale-95', 'opacity-0');
        modalContent.classList.remove('scale-100', 'opacity-100');
        modal.classList.remove('bg-opacity-10');
        modal.classList.add('bg-opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    // Handle modal close buttons
    document.querySelectorAll('[data-modal-hide="modal-confirm-bulk-delete"]').forEach(btn => {
        btn.addEventListener('click', hideBulkDeleteModal);
    });

    // Handle modal background click
    const bulkDeleteModal = document.getElementById('modal-confirm-bulk-delete');
    if (bulkDeleteModal) {
        bulkDeleteModal.addEventListener('click', function(e) {
            if (e.target === bulkDeleteModal) {
                hideBulkDeleteModal();
            }
        });
    }

    // Handle escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !bulkDeleteModal.classList.contains('hidden')) {
            hideBulkDeleteModal();
        }
    });

    // Handle confirm submit button
    const confirmBulkDeleteSubmitBtn = document.getElementById('confirm-bulk-delete-submit');
    if (confirmBulkDeleteSubmitBtn) {
        confirmBulkDeleteSubmitBtn.addEventListener('click', function() {
            const form = document.getElementById('bulk-delete-form');
            if (form) {
                form.submit();
            }
        });
    }

    // Initial state
    updateSelectedCount();
    updateSelectAllState();
});
</script>

@endsection
