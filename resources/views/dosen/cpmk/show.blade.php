@extends('layouts.main')

@section('title', 'Detail CPMK - ' . $cpmk->kodeCpmk)

@section('content')
<div class="p-4 sm:p-6">
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
                <a href="{{ route('dosen.cpmk.index', $mataKuliah->id) }}" class="hover:text-gray-700">CPMK</a>
            </li>
            <li>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </li>
            <li class="text-gray-900 font-medium">{{ $cpmk->kodeCpmk }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-4 sm:p-6 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl font-bold text-gray-900">Detail CPMK</h1>
                    <p class="text-gray-600 mt-1">{{ $mataKuliah->mataKuliah->namaMatkul }} • {{ $mataKuliah->mataKuliah->kodeMatkul }}</p>
                </div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3">
                    <a href="{{ route('dosen.cpmk.index', $mataKuliah->id) }}"
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center w-full sm:w-auto justify-center">
                        <svg class="w-4 h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span class="hidden sm:inline">Kembali</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 sm:gap-6">
        <!-- Main Information -->
        <div class="xl:col-span-2">
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-4 sm:p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Informasi CPMK</h2>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="space-y-4 sm:space-y-6">
                        <!-- Kode CPMK -->
                        <div class="flex flex-col sm:flex-row sm:items-start">
                            <div class="w-full sm:w-32 text-sm font-medium text-gray-600 mb-2 sm:mb-0">Kode CPMK:</div>
                            <div class="flex-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800">
                                    {{ $cpmk->kodeCpmk }}
                                </span>
                            </div>
                        </div>

                        <!-- CPL Terkait -->
                        <div class="flex flex-col sm:flex-row sm:items-start">
                            <div class="w-full sm:w-32 text-sm font-medium text-gray-600 mb-2 sm:mb-0">CPL Terkait:</div>
                            <div class="flex-1">
                                <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="font-medium text-blue-900 break-words">{{ $cpmk->cpl->kodeCpl }}</div>
                                    <div class="text-sm text-blue-700 mt-1 break-words">{{ $cpmk->cpl->deskripsi }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="flex flex-col sm:flex-row sm:items-start">
                            <div class="w-full sm:w-32 text-sm font-medium text-gray-600 mb-2 sm:mb-0">Deskripsi:</div>
                            <div class="flex-1">
                                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                    <p class="text-gray-900 leading-relaxed break-words">{{ $cpmk->deskripsi }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Created/Updated Info -->
                        <div class="pt-4 border-t border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                                <div>
                                    <span class="font-medium">Dibuat:</span>
                                    {{ $cpmk->created_at->format('d M Y, H:i') }}
                                </div>
                                <div>
                                    <span class="font-medium">Terakhir diperbarui:</span>
                                    {{ $cpmk->updated_at->format('d M Y, H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Side Information -->
        <div class="space-y-4 sm:space-y-6">

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-4 sm:p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Aksi Cepat</h3>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="space-y-3">
                        <a href="{{ route('dosen.cpmk.edit', [$mataKuliah->id, $cpmk->id]) }}"
                           class="w-full bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg flex items-center justify-center text-sm font-medium transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit CPMK
                        </a>

                        <button type="button" class="delete-btn w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center justify-center text-sm font-medium transition-colors duration-200"
                                data-id="{{ $cpmk->id }}" data-kode="{{ $cpmk->kodeCpmk }}" data-modal-toggle="deleteModal">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Hapus CPMK
                        </button>

                        <a href="{{ route('dosen.cpmk.create', $mataKuliah->id) }}"
                           class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center text-sm font-medium transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah CPMK Baru
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-4 sm:p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Statistik</h3>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total CPMK di Mata Kuliah</span>
                            <span class="font-semibold text-gray-900">{{ $totalCpmk }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<x-confirm-modal
    id="deleteModal"
    title="Konfirmasi Hapus CPMK"
    message="Apakah Anda yakin ingin menghapus CPMK ini? Tindakan ini tidak dapat dibatalkan."
    action=""
    method="DELETE"
    confirmText="Hapus"
    cancelText="Batal"
/>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('deleteModal');
    let currentDeleteId = null;

    // Single delete handler
    document.querySelector('.delete-btn').addEventListener('click', function() {
        currentDeleteId = this.dataset.id;
        const kodeCpmk = this.dataset.kode;

        // Update modal content
        const modalForm = deleteModal.querySelector('form');
        const modalMessage = deleteModal.querySelector('p');
        if (modalForm && modalMessage) {
            modalForm.action = `{{ url('/dosen/mata-kuliah/' . $mataKuliah->id . '/cpmk') }}/${currentDeleteId}`;
            modalMessage.textContent = `Apakah Anda yakin ingin menghapus CPMK "${kodeCpmk}"? Tindakan ini tidak dapat dibatalkan.`;
        }
    });
});
</script>
@endsection
