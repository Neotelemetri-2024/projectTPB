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
                    <a href="{{ route('dosen.cpmk.sub-cpmk.create', [$tahunAjaranMatkul->id, $cpmkList->first()->id]) }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Sub-CPMK
                    </a>
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
                <div class="flex space-x-2">
                    <button id="tab-main-cpmk" class="px-4 py-2 text-sm font-medium rounded-lg bg-amber-600 text-white">
                        CPMK Utama
                    </button>
                    <button id="tab-sub-cpmk" class="px-4 py-2 text-sm font-medium rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300">
                        Sub-CPMK
                    </button>
                </div>
                @php
                    $editBobot = false;
                @endphp
            </div>
        </div>
                <div class="p-6">

            <form method="POST" action="#" id="form-bobot-cpmk" style="display:none;">
                @csrf
                <!-- Form bobot CPMK dihapus -->
            </form>

            <!-- Tab Content: CPMK Utama -->
            <div id="tab-content-main-cpmk" class="tab-content">
                <div class="overflow-x-auto bg-white rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
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
                            @foreach($mainCpmkList as $cpmk)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800">
                                        {{ $cpmk->kodeCpmk }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        {{ $cpmk->deskripsi }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                    <div class="grid grid-cols-3 gap-1">
                                        @foreach($cpmk->cpl as $cpl)
                                            <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $cpl->kodeCpl }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm text-gray-900">
                                        @if($cpmk->children->count() > 0)
                                            <span class="text-gray-500">Ada di Sub-CPMK</span>
                                        @else
                                            {{ number_format($cpmk->bobot->sum('bobot'), 2) }} %
                                        @endif
                                    </div>
                                </td>
                                @if(!$editBobot)
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($cpmk->lastModified)
                                        <div class="text-xs text-gray-900 font-medium" title="Last updated: {{ \Carbon\Carbon::parse($cpmk->lastModified)->format('d M Y, H:i') }}">
                                            {{ \Carbon\Carbon::parse($cpmk->lastModified)->format('d/m/Y H:i') }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ \Carbon\Carbon::parse($cpmk->lastModified)->diffForHumans() }}
                                        </div>
                                    @else
                                        <div class="text-xs text-gray-500">-</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('dosen.cpmk.detail', [$tahunAjaranMatkul->id, $cpmk->id]) }}"
                                           class="text-blue-600 hover:text-blue-700 p-1" title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('dosen.cpmk.edit', [$tahunAjaranMatkul->id, $cpmk->id]) }}"
                                           class="text-amber-600 hover:text-amber-700 p-1" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <button type="button" class="delete-btn text-red-600 hover:text-red-700 p-1" title="Hapus"
                                                data-id="{{ $cpmk->id }}" data-kode="{{ $cpmk->kodeCpmk }}" data-modal-toggle="deleteModal">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab Content: Sub-CPMK -->
            <div id="tab-content-sub-cpmk" class="tab-content hidden">
                <div class="overflow-x-auto bg-white rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kode Sub-CPMK
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Deskripsi
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Parents
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    CPL Terkait
                                </th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Bobot
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
                            @foreach($subCpmkList as $subCpmk)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        {{ $subCpmk->kodeCpmk }}
                                        <span class="ml-1 text-xs">(Sub)</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        {{ $subCpmk->deskripsi }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $subCpmk->parents->pluck('kodeCpmk')->implode(', ') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                    <div class="grid grid-cols-3 gap-1">
                                        @foreach($subCpmk->cpl as $cpl)
                                            <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $cpl->kodeCpl }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="text-sm text-gray-900">
                                        {{ number_format($subCpmk->bobot->sum('bobot'), 2) }} %
                                    </div>
                                </td>
                                @if(!$editBobot)
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($subCpmk->lastModified)
                                        <div class="text-xs text-gray-900 font-medium" title="Last updated: {{ \Carbon\Carbon::parse($subCpmk->lastModified)->format('d M Y, H:i') }}">
                                            {{ \Carbon\Carbon::parse($subCpmk->lastModified)->format('d/m/Y H:i') }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ \Carbon\Carbon::parse($subCpmk->lastModified)->diffForHumans() }}
                                        </div>
                                    @else
                                        <div class="text-xs text-gray-500">-</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('dosen.cpmk.detail', [$tahunAjaranMatkul->id, $subCpmk->id]) }}"
                                           class="text-blue-600 hover:text-blue-700 p-1" title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('dosen.cpmk.sub-cpmk.edit', [$tahunAjaranMatkul->id, $subCpmk->id]) }}"
                                           class="text-amber-600 hover:text-amber-700 p-1" title="Edit Sub-CPMK">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <button type="button" class="delete-btn text-red-600 hover:text-red-700 p-1" title="Hapus"
                                                data-id="{{ $subCpmk->id }}" data-kode="{{ $subCpmk->kodeCpmk }}" data-modal-toggle="deleteModal">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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

        <!-- Total Bobot Section -->
        <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-blue-900">Total Bobot CPMK</h3>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-blue-700">
                        {{ number_format($mainCpmkList->sum(function($cpmk) {
                            return $cpmk->children->count() > 0 ? 0 : $cpmk->bobot->sum('bobot');
                        }) + $subCpmkList->sum(function($subCpmk) {
                            return $subCpmk->bobot->sum('bobot');
                        }), 2) }} %
                    </div>
                    <div class="text-sm text-blue-600">
                        Total dari {{ $mainCpmkList->filter(function($cpmk) {
                            return $cpmk->children->count() > 0 ? false : $cpmk->bobot->sum('bobot') > 0;
                        })->count() + $subCpmkList->filter(function($subCpmk) {
                            return $subCpmk->bobot->sum('bobot') > 0;
                        })->count() }} CPMK
                    </div>
                </div>
            </div>
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
    const tabMainCpmk = document.getElementById('tab-main-cpmk');
    const tabSubCpmk = document.getElementById('tab-sub-cpmk');
    const tabContentMainCpmk = document.getElementById('tab-content-main-cpmk');
    const tabContentSubCpmk = document.getElementById('tab-content-sub-cpmk');

    let currentDeleteId = null;
    let currentDeleteType = 'single'; // 'single' or 'bulk'

    // Tab functionality
    tabMainCpmk.addEventListener('click', function() {
        // Update tab buttons
        tabMainCpmk.classList.remove('bg-gray-200', 'text-gray-700');
        tabMainCpmk.classList.add('bg-amber-600', 'text-white');
        tabSubCpmk.classList.remove('bg-amber-600', 'text-white');
        tabSubCpmk.classList.add('bg-gray-200', 'text-gray-700');

        // Show main CPMK content
        tabContentMainCpmk.classList.remove('hidden');
        tabContentSubCpmk.classList.add('hidden');
    });

    tabSubCpmk.addEventListener('click', function() {
        // Update tab buttons
        tabSubCpmk.classList.remove('bg-gray-200', 'text-gray-700');
        tabSubCpmk.classList.add('bg-amber-600', 'text-white');
        tabMainCpmk.classList.remove('bg-amber-600', 'text-white');
        tabMainCpmk.classList.add('bg-gray-200', 'text-gray-700');

        // Show sub-CPMK content
        tabContentSubCpmk.classList.remove('hidden');
        tabContentMainCpmk.classList.add('hidden');
    });

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
