@extends('layouts.main')

@section('title', 'Detail Bobot Komponen - ' . $mataKuliah->mataKuliah->namaMatkul)

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
            <li class="text-gray-900 font-medium">Detail</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-4 sm:p-6 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl font-bold text-gray-900">Detail Bobot Komponen</h1>
                    <p class="text-gray-600 mt-1">{{ $mataKuliah->mataKuliah->namaMatkul }} • {{ $mataKuliah->mataKuliah->kodeMatkul }}</p>
                </div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3">
                    <a href="{{ route('dosen.bobot-komponen.edit', [$mataKuliah->id, $bobot->id]) }}"
                       class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg flex items-center w-full sm:w-auto justify-center">
                        <svg class="w-4 h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Bobot
                    </a>
                    @if($bobot->nilai->count() == 0)
                        <form action="{{ route('dosen.bobot-komponen.destroy', [$mataKuliah->id, $bobot->id]) }}"
                              method="POST"
                              class="inline w-full sm:w-auto"
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus bobot komponen ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Hapus
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Detail Bobot -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Informasi Bobot</h3>

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Komponen Penilaian</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $bobot->komponen->namaKomponen }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Jenis Komponen</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $bobot->komponen->jenisKomponen }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Bobot</dt>
                            <dd class="mt-1">
                                <span class="text-2xl font-bold text-blue-600">{{ number_format($bobot->bobot, 1) }}%</span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                @if($bobot->nilai->count() > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Sudah Ada Nilai ({{ $bobot->nilai->count() }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        Belum Ada Nilai
                                    </span>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tanggal Dibuat</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $bobot->created_at->format('d/m/Y H:i') }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Terakhir Diupdate</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $bobot->updated_at->format('d/m/Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Statistik -->
        <div class="space-y-6">
            <!-- Total Bobot -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Ringkasan Bobot</h3>

                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Semua Bobot</span>
                        <span class="text-sm font-medium {{ $totalBobot == 100 ? 'text-green-600' : ($totalBobot > 100 ? 'text-red-600' : 'text-yellow-600') }}">
                            {{ number_format($totalBobot, 1) }}%
                        </span>
                    </div>

                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full {{ $totalBobot == 100 ? 'bg-green-500' : ($totalBobot > 100 ? 'bg-red-500' : 'bg-yellow-500') }}"
                             style="width: {{ min($totalBobot, 100) }}%"></div>
                    </div>

                    @if($totalBobot != 100)
                        <p class="text-xs {{ $totalBobot > 100 ? 'text-red-600' : 'text-yellow-600' }}">
                            @if($totalBobot > 100)
                                Melebihi 100% sebesar {{ number_format($totalBobot - 100, 1) }}%
                            @else
                                Kurang {{ number_format(100 - $totalBobot, 1) }}% dari 100%
                            @endif
                        </p>
                    @endif
                </div>
            </div>

            <!-- Komponen Lainnya -->
            @if($komponenLain->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Komponen Lainnya</h3>

                <div class="space-y-3">
                    @foreach($komponenLain as $komponen)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $komponen->komponen->namaKomponen }}</p>
                                <p class="text-xs text-gray-500">{{ $komponen->komponen->jenisKomponen }}</p>
                            </div>
                            <span class="text-sm font-medium text-gray-700">{{ number_format($komponen->bobot, 1) }}%</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Nilai Terkait -->
            @if($bobot->nilai->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Nilai Terkait</h3>

                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ $bobot->nilai->count() }}</div>
                    <p class="text-sm text-gray-500">Mahasiswa memiliki nilai</p>

                    @if($bobot->nilai->count() > 0)
                        <div class="mt-4 text-xs text-gray-600">
                            <p>Rata-rata: {{ number_format($bobot->nilai->avg('nilai'), 2) }}</p>
                            <p>Tertinggi: {{ number_format($bobot->nilai->max('nilai'), 2) }}</p>
                            <p>Terendah: {{ number_format($bobot->nilai->min('nilai'), 2) }}</p>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-6">
        <a href="{{ route('dosen.bobot-komponen.index', $mataKuliah->id) }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar Bobot
        </a>
    </div>
</div>
@endsection
