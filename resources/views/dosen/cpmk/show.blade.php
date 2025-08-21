@extends('layouts.main')

@section('title', 'CPMK - ' . $tahunAjaranMatkul->mataKuliah->namaMatkul)

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
            <li class="text-gray-900 font-medium">{{ $tahunAjaranMatkul->mataKuliah->namaMatkul }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">CPMK - {{ $tahunAjaranMatkul->mataKuliah->namaMatkul }}</h1>
                    <p class="text-gray-600 mt-1">{{ $tahunAjaranMatkul->mataKuliah->kodeMatkul }} • {{ $tahunAjaranMatkul->tahunAjaran->tahun }} - {{ $tahunAjaranMatkul->tahunAjaran->periode }}</p>
                </div>
                <div class="flex items-center">
                    <a href="{{ route('dosen.cpmk.index') }}"
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Action Buttons Row -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('dosen.cpmk.create', $tahunAjaranMatkul->id) }}"
                   class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah CPMK Utama
                </a>
                @if($cpmkList->isNotEmpty())
                    <a href="{{ route('dosen.bobot-komponen.bulk-create', $tahunAjaranMatkul->id) }}"
                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Kelola Bobot Penilaian
                    </a>
                @else
                    <button disabled
                            class="bg-gray-400 cursor-not-allowed text-white px-4 py-2 rounded-lg flex items-center justify-center"
                            title="Tambahkan CPMK terlebih dahulu untuk mengatur bobot">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Kelola Bobot Penilaian
                    </button>
                @endif
            </div>
        </div>

        <!-- Search dan Filter Form -->
        <div class="p-6">
            <form method="GET" action="{{ route('dosen.cpmk.show', $tahunAjaranMatkul->id) }}">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" name="search" id="search-input" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" placeholder="Cari kode atau deskripsi CPMK..." value="{{ request('search') }}">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg flex items-center justify-center whitespace-nowrap">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Cari
                        </button>
                        @if(request('search'))
                        <a href="{{ route('dosen.cpmk.show', $tahunAjaranMatkul->id) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2.5 rounded-lg flex items-center justify-center whitespace-nowrap">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h
                                -.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Reset
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Content -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">Daftar CPMK</h2>
                @php
                    $editBobot = false;
                @endphp
                <div class="mb-4">
                    <!-- Tombol kembali di tabel daftar CPMK dihapus -->
                </div>
            </div>
        </div>
        <div class="p-6">

            <form method="POST" action="#" id="form-bobot-cpmk" style="display:none;">
                @csrf
                <!-- Form bobot CPMK dihapus -->
            </form>
            <div class="overflow-x-auto bg-white rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            @if(!$editBobot)
                            <th scope="col" class="px-6 py-3 text-left">
                                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            @endif
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kode CPMK
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Deskripsi
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                CPL Terkait
                            </th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Bobot CPMK
                            </th>
                            @if(!$editBobot)
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Last Modified
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($cpmkHierarchy as $hierarchy)
                            @php
                                $parent = $hierarchy['parent'];
                                $children = $hierarchy['children'];
                            @endphp

                            <!-- Parent CPMK Row -->
                            <tr class="hover:bg-gray-50 transition-colors {{ $parent->parent_id ? 'bg-gray-50' : 'bg-white' }}">
                                @if(!$editBobot)
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" class="cpmk-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                           value="{{ $parent->id }}" data-kode="{{ $parent->kodeCpmk }}">
                                </td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($parent->parent_id)
                                            <div class="w-4 h-4 mr-2 text-gray-400">
                                                <svg fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $parent->parent_id ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $parent->kodeCpmk }}
                                            @if($parent->parent_id)
                                                <span class="ml-1 text-xs">(Sub)</span>
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        {{ $parent->deskripsi }}
                                        @if($parent->parent_id && $parent->parent)
                                            <div class="text-xs text-gray-500 mt-1">
                                                Parent: {{ $parent->parent->kodeCpmk }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                <div class="grid grid-cols-3 gap-1">
                                    @foreach($parent->cpl as $cpl)
                                        <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $cpl->kodeCpl }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            @if($editBobot)
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center">
                                    <input type="number" name="bobotCpmk[{{ $parent->id }}]" step="0.01" min="0" max="100" class="bobot-cpmk-input w-24 px-2 py-1 rounded text-sm text-center @if(!$editBobot) border-0 bg-transparent focus:outline-none @else border border-gray-300 @endif" value="{{ $parent->cpmkMatKul[0]->bobotCpmk ?? '' }}" required>
                                </div>
                            </td>
                            @else
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="text-sm text-gray-900">
                                    {{ number_format($parent->bobot->sum('bobot'), 2) }} %
                                </div>
                            </td>
                            @endif
                            @if(!$editBobot)
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($parent->lastModified)
                                    <div class="text-xs text-gray-900 font-medium" title="Last updated: {{ \Carbon\Carbon::parse($parent->lastModified)->format('d M Y, H:i') }}">
                                        {{ \Carbon\Carbon::parse($parent->lastModified)->format('d/m/Y H:i') }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ \Carbon\Carbon::parse($parent->lastModified)->diffForHumans() }}
                                    </div>
                                @else
                                    <div class="text-xs text-gray-500">-</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('dosen.cpmk.detail', [$tahunAjaranMatkul->id, $parent->id]) }}"
                                       class="text-blue-600 hover:text-blue-700 p-1" title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('dosen.cpmk.edit', [$tahunAjaranMatkul->id, $parent->id]) }}"
                                       class="text-amber-600 hover:text-amber-700 p-1" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('dosen.cpmk.sub-cpmk.create', [$tahunAjaranMatkul->id, $parent->id]) }}"
                                       class="text-green-600 hover:text-green-700 p-1" title="Tambah Sub-CPMK">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </a>
                                    <button type="button" class="delete-btn text-red-600 hover:text-red-700 p-1" title="Hapus"
                                            data-id="{{ $parent->id }}" data-kode="{{ $parent->kodeCpmk }}" data-modal-toggle="deleteModal">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                            @endif
                        </tr>

                        <!-- Child CPMK Rows -->
                        @foreach($children as $child)
                        <tr class="hover:bg-gray-50 transition-colors bg-gray-50">
                            @if(!$editBobot)
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" class="cpmk-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                       value="{{ $child->id }}" data-kode="{{ $child->kodeCpmk }}">
                            </td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center pl-6">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        {{ $child->kodeCpmk }}
                                        <span class="ml-1 text-xs">(Sub)</span>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ $child->deskripsi }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                <div class="grid grid-cols-3 gap-1">
                                    @foreach($child->cpl as $cpl)
                                        <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $cpl->kodeCpl }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            @if($editBobot)
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center">
                                    <input type="number" name="bobotCpmk[{{ $child->id }}]" step="0.01" min="0" max="100" class="bobot-cpmk-input w-24 px-2 py-1 rounded text-sm text-center @if(!$editBobot) border-0 bg-transparent focus:outline-none @else border border-gray-300 @endif" value="{{ $child->cpmkMatKul[0]->bobotCpmk ?? '' }}" required>
                                </div>
                            </td>
                            @else
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="text-sm text-gray-900">
                                    {{ number_format($child->bobot->sum('bobot'), 2) }} %
                                </div>
                            </td>
                            @endif
                            @if(!$editBobot)
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($child->lastModified)
                                    <div class="text-xs text-gray-900 font-medium" title="Last updated: {{ \Carbon\Carbon::parse($child->lastModified)->format('d M Y, H:i') }}">
                                        {{ \Carbon\Carbon::parse($child->lastModified)->format('d/m/Y H:i') }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ \Carbon\Carbon::parse($child->lastModified)->diffForHumans() }}
                                    </div>
                                @else
                                    <div class="text-xs text-gray-500">-</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('dosen.cpmk.detail', [$tahunAjaranMatkul->id, $child->id]) }}"
                                       class="text-blue-600 hover:text-blue-700 p-1" title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('dosen.cpmk.sub-cpmk.edit', [$tahunAjaranMatkul->id, $child->id]) }}"
                                       class="text-amber-600 hover:text-amber-700 p-1" title="Edit Sub-CPMK">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <button type="button" class="delete-btn text-red-600 hover:text-red-700 p-1" title="Hapus"
                                            data-id="{{ $child->id }}" data-kode="{{ $child->kodeCpmk }}" data-modal-toggle="deleteModal">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                        @endforeach
                        <tr class="bg-blue-50 font-bold">
                            <td class="px-6 py-3 text-blue-900 text-sm" colspan="{{ $editBobot ? 3 : 6 }}">Total Bobot CPMK</td>
                            @if($editBobot)
                            <td class="px-6 py-3 text-center text-blue-900 text-base" id="total-bobot-cpmk-cell">0.00%</td>
                            @else
                            <td class="px-6 py-3 text-center text-blue-900 text-base">
                                {{ number_format($cpmkList->sum(fn($cpmk) => $cpmk->bobot->sum('bobot')), 2) }} %
                            </td>
                            @endif
                        </tr>

                    </tbody>
                </table>
            </div>
            <div class="flex flex-col sm:flex-row justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <div class="flex-1 flex items-center">
                    <span id="bobot-cpmk-warning" class="text-sm font-semibold"></span>
                </div>
                @if($editBobot)
                <button type="submit" id="submit-bobot-cpmk" class="w-full sm:w-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-400 cursor-not-allowed" disabled>
                    Simpan Semua Bobot CPMK
                </button>
                @endif
            </div>
        </form>
        </div>
        <!-- Pagination -->
        @if($cpmkList->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $cpmkList->links() }}
        </div>
        @endif
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
    const checkboxes = document.querySelectorAll('.cpmk-checkbox');
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCount = document.getElementById('selected-count');
    const bulkDeleteBtn = document.getElementById('bulk-delete');
    const clearSelectionBtn = document.getElementById('clear-selection');
    const selectAllCheckbox = document.getElementById('select-all');

    let currentDeleteId = null;
    let currentDeleteType = 'single'; // 'single' or 'bulk'
    let selectedIds = [];
    // Single delete handlers
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentDeleteId = this.dataset.id;
            currentDeleteType = 'single';
            const kodeCpmk = this.dataset.kode;

            // Update modal content
            const modalForm = deleteModal.querySelector('form');
            const modalMessage = deleteModal.querySelector('p');
            if (modalForm && modalMessage) {
                modalForm.action = `{{ route('dosen.cpmk.destroy', [$tahunAjaranMatkul->id, '']) }}/${currentDeleteId}`;
                modalMessage.textContent = `Apakah Anda yakin ingin menghapus CPMK "${kodeCpmk}"? Tindakan ini tidak dapat dibatalkan.`;
            }
        });
    });

    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelection();
        });
    }

    // Checkbox selection handlers
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelection();
        });
    });

    function updateSelection() {
        selectedIds = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);

        // Update select all checkbox state
        if (selectAllCheckbox) {
            if (selectedIds.length === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            } else if (selectedIds.length === checkboxes.length) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = true;
            } else {
                selectAllCheckbox.indeterminate = true;
            }
        }

        if (selectedIds.length > 0) {
            bulkActions.classList.remove('hidden');
            selectedCount.textContent = `${selectedIds.length} item dipilih`;
        } else {
            bulkActions.classList.add('hidden');
        }
    }
    // Bulk delete handler - add data-modal-toggle for bulk delete button too
    bulkDeleteBtn.setAttribute('data-modal-toggle', 'deleteModal');
    bulkDeleteBtn.addEventListener('click', function() {
        if (selectedIds.length === 0) return;

        currentDeleteType = 'bulk';

        // Update modal content for bulk delete
        const modalForm = deleteModal.querySelector('form');
        const modalMessage = deleteModal.querySelector('p');
        if (modalForm && modalMessage) {
            modalForm.action = '#'; // We'll handle this with fetch
            modalMessage.textContent = `Apakah Anda yakin ingin menghapus ${selectedIds.length} CPMK yang dipilih? Tindakan ini tidak dapat dibatalkan.`;
        }
    });

    // Clear selection
    clearSelectionBtn.addEventListener('click', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        }
        updateSelection();
    });
    // Override form submission for bulk delete
    const modalForm = deleteModal.querySelector('form');
    if (modalForm) {
        modalForm.addEventListener('submit', function(e) {
            if (currentDeleteType === 'bulk' && selectedIds.length > 0) {
                e.preventDefault();

                // Bulk delete via fetch
                fetch(`{{ route('dosen.cpmk.bulk-action', $tahunAjaranMatkul->id) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        action: 'delete',
                        cpmk_ids: selectedIds
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.error || 'Terjadi kesalahan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan');
                });

                // Hide modal using the component's method
                const modalContent = deleteModal.querySelector('[data-modal-content]');
                if (modalContent) {
                    modalContent.classList.add('scale-95', 'opacity-0');
                    modalContent.classList.remove('scale-100', 'opacity-100');
                    setTimeout(() => {
                        deleteModal.classList.add('hidden');
                        deleteModal.classList.remove('flex');
                    }, 300);
                }
            }
            // For single delete, let the form submit normally
        });
    }
});
</script>
@endsection
