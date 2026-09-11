@extends('layouts.main')

@section('title', 'Data CPL')

@section('content')
<div class="p-4 md:p-6 space-y-4">
    <div class="bg-white border border-gray-200 rounded-xl">
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <h2 class="text-lg font-semibold text-gray-900">Daftar Capaian Pembelajaran Lulusan (CPL)</h2>
                <a href="{{ route('admin.cpl.create') }}" 
                   class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg flex items-center w-fit transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah CPL
                </a>
            </div>
        </div>
        
        <!-- Form Pencarian -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center gap-3 w-full">
                <form method="GET" action="" class="flex flex-col md:flex-row md:items-center gap-3 w-full">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode atau deskripsi CPL..." class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 text-sm bg-gray-50 focus:bg-white transition-colors">
                    <button type="submit" class="px-6 py-2.5 bg-amber-600 text-white rounded-lg hover:bg-amber-700 focus:ring-4 focus:ring-amber-300 text-sm font-medium transition-colors">
                        <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Cari
                    </button>
                </form>
                @if(request('q'))
                    <a href="{{ route('admin.cpl.index') }}" class="px-4 py-2.5 text-sm bg-gray-600 text-white rounded-lg hover:bg-gray-700 whitespace-nowrap font-medium transition-colors">
                        <svg class="w-4 h-4 mr-1 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Reset
                    </a>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode CPL</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Min</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Target %</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah CPMK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($cpl as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $loop->iteration + ($cpl->currentPage() - 1) * $cpl->perPage() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->kodeCpl }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="max-w-xs truncate" title="{{ $item->deskripsi }}">
                                {{ Str::limit($item->deskripsi, 100) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">{{ $item->nilaiMinimal ?? 60 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">{{ $item->targetPersen ?? 60 }}%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                {{ $item->cpmk->count() }} CPMK
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.cpl.edit', $item->id) }}" 
                                   class="inline-flex items-center justify-center bg-amber-600 hover:bg-amber-700 text-white p-1.5 rounded-md" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <button type="button" 
                                        data-modal-target="modal-confirm-hapus-{{ $item->id }}" 
                                        data-modal-toggle="modal-confirm-hapus-{{ $item->id }}"
                                        class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white p-1.5 rounded-md" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p>Tidak ada data CPL</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($cpl->total() > 0)
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $cpl->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
@foreach($cpl as $item)
    <x-confirm-modal 
        :id="'modal-confirm-hapus-' . $item->id"
        title="Konfirmasi Hapus CPL"
        :message="'Apakah Anda yakin ingin menghapus CPL ' . $item->kodeCpl . '? Aksi ini tidak dapat dibatalkan.'"
        :action="route('admin.cpl.destroy', $item->id)"
        method="DELETE"
    />
@endforeach

@endsection
