@extends('layouts.main')

@section('title', 'Detail CPMK - ' . $cpmk->kodeCpmk)

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
            <li class="text-gray-900 font-medium">{{ $cpmk->kodeCpmk }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Detail CPMK - {{ $cpmk->kodeCpmk }}</h1>
                    <p class="text-gray-600 mt-1">{{ $tahunAjaranMatkul->mataKuliah->namaMatkul }} • {{ $tahunAjaranMatkul->mataKuliah->kodeMatkul }}</p>
                </div>
                <div class="flex items-center gap-3">
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
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- CPMK Information -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Informasi CPMK</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">CPL Terkait</label>
                            <div class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-3">
                                <div class="space-y-3">
                                    @foreach($cpmk->cpl as $cpl)
                                        <div class="flex items-start gap-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $cpl->kodeCpl }}
                                            </span>
                                            <div class="flex-1">
                                                <p class="text-sm text-gray-900">{{ $cpl->deskripsi }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kode CPMK</label>
                            <div class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800">
                                    {{ $cpmk->kodeCpmk }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi CPMK</label>
                            <div class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-3 min-h-[100px]">
                                {{ $cpmk->deskripsi }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bobot Komponen -->
            @if($bobotKomponen->isNotEmpty())
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Bobot Komponen Penilaian</h2>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Komponen
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Bobot (%)
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kelas
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($bobotKomponen as $bobot)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $bobot->komponen->namaKomponen }}</div>
                                        @if($bobot->komponen->deskripsi)
                                        <div class="text-sm text-gray-500">{{ $bobot->komponen->deskripsi }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $bobot->bobot }}%
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $bobot->tahunAjaranMatkul->kelas->namaKelas }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">


            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Aksi Cepat</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <a href="{{ route('dosen.cpmk.edit', [$tahunAjaranMatkul->id, $cpmk->id]) }}"
                           class="w-full bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit CPMK
                        </a>
                        <button type="button"
                                class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center justify-center"
                                data-id="{{ $cpmk->id }}" data-kode="{{ $cpmk->kodeCpmk }}" data-modal-toggle="deleteModal">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Hapus CPMK
                        </button>
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
    action="{{ route('dosen.cpmk.destroy', [$tahunAjaranMatkul->id, $cpmk->id]) }}"
    method="DELETE"
    confirmText="Hapus"
    cancelText="Batal"
/>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('deleteModal');
    const deleteBtn = document.querySelector('[data-modal-toggle="deleteModal"]');

    if (deleteBtn) {
        deleteBtn.addEventListener('click', function() {
            const kodeCpmk = this.dataset.kode;
            const modalMessage = deleteModal.querySelector('p');
            if (modalMessage) {
                modalMessage.textContent = `Apakah Anda yakin ingin menghapus CPMK "${kodeCpmk}"? Tindakan ini tidak dapat dibatalkan.`;
            }
        });
    }
});
</script>
@endsection
