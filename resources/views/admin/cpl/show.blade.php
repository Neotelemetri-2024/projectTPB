@extends('layouts.main')

@section('title', 'Detail CPL')

@section('content')
<div class="p-4 md:p-6 space-y-4">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white border border-gray-200 rounded-xl">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Detail Capaian Pembelajaran Lulusan (CPL)</h2>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.cpl.edit', $cpl->id) }}" 
                           class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </a>
                        <a href="{{ route('admin.cpl.index') }}" 
                           class="text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Detail CPL -->
                    <div class="lg:col-span-2">
                        <div class="space-y-6">
                            <!-- Kode CPL -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kode CPL</label>
                                <div class="px-4 py-3 bg-gray-50 rounded-lg border">
                                    <span class="text-lg font-semibold text-amber-600">{{ $cpl->kodeCpl }}</span>
                                </div>
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi CPL</label>
                                <div class="px-4 py-3 bg-gray-50 rounded-lg border">
                                    <p class="text-gray-800 leading-relaxed">{{ $cpl->deskripsi }}</p>
                                </div>
                            </div>

                            <!-- Informasi Tanggal -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Dibuat</label>
                                    <div class="px-4 py-3 bg-gray-50 rounded-lg border">
                                        <p class="text-sm text-gray-600">{{ $cpl->created_at->format('d M Y, H:i') }}</p>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Terakhir Diubah</label>
                                    <div class="px-4 py-3 bg-gray-50 rounded-lg border">
                                        <p class="text-sm text-gray-600">{{ $cpl->updated_at->format('d M Y, H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistik dan Relasi -->
                    <div class="space-y-6">
                        <!-- Statistik -->
                        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
                            <p class="text-[11px] uppercase tracking-wide text-gray-500">CPMK Terkait</p>
                            <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ $cpl->cpmk->count() }}</p>
                        </div>

                        <!-- Aksi -->
                        <div class="bg-white border border-gray-200 rounded-xl p-4">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">Aksi Cepat</h3>
                            <div class="space-y-2">
                                <a href="{{ route('admin.cpl.edit', $cpl->id) }}" 
                                   class="w-full px-4 py-2 text-sm font-medium text-amber-700 bg-white border border-amber-300 rounded-lg hover:bg-amber-50 focus:ring-4 focus:ring-amber-300 transition-colors flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit CPL
                                </a>
                                
                                @if($cpl->cpmk->count() === 0)
                                <button type="button" data-modal-target="modal-confirm-hapus-cpl" data-modal-toggle="modal-confirm-hapus-cpl"
                                        class="w-full px-4 py-2 text-sm font-medium text-red-700 bg-white border border-red-300 rounded-lg hover:bg-red-50 focus:ring-4 focus:ring-red-300 transition-colors flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Hapus CPL
                                </button>
                                @else
                                <div class="w-full px-4 py-2 text-sm text-gray-500 bg-gray-100 border border-gray-200 rounded-lg">
                                    <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.734-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    Tidak dapat dihapus (memiliki CPMK)
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CPMK Terkait -->
                @if($cpl->cpmk->count() > 0)
                <div class="mt-8 border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">CPMK Terkait ({{ $cpl->cpmk->count() }})</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode CPMK</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($cpl->cpmk as $cpmk)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $cpmk->kodeCpmk }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ Str::limit($cpmk->deskripsi, 150) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus CPL -->
@if($cpl->cpmk->count() === 0)
    <x-confirm-modal 
        id="modal-confirm-hapus-cpl"
        title="Konfirmasi Hapus CPL"
        :message="'Apakah Anda yakin ingin menghapus CPL ' . $cpl->kodeCpl . '?'"
        :action="route('admin.cpl.destroy', $cpl->id)"
    />
@endif

@endsection
