@extends('layouts.main')

@section('title', 'Data Komponen')

@section('content')
<div class="p-4 md:p-6 space-y-4">
    <div class="bg-white border border-gray-200 rounded-xl">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Daftar Komponen</h2>
            <button data-modal-target="modal-tambah-komponen" data-modal-toggle="modal-tambah-komponen" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah Komponen
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Komponen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($komponen as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ method_exists($komponen, 'firstItem') ? $komponen->firstItem() + $loop->index : $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->nama }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button type="button" data-modal-target="modal-edit-komponen-{{ $item->id }}" data-modal-toggle="modal-edit-komponen-{{ $item->id }}" class="inline-flex items-center justify-center bg-amber-600 hover:bg-amber-700 text-white p-1.5 rounded-md" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button type="button" data-modal-target="modal-confirm-hapus-komponen-{{ $item->id }}" data-modal-toggle="modal-confirm-hapus-komponen-{{ $item->id }}" class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white p-1.5 rounded-md" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p>Belum ada data komponen</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($komponen, 'hasPages') && $komponen->hasPages())
        <div class="p-4 border-t border-gray-200">
            {{ $komponen->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Komponen -->
<x-form-modal id="modal-tambah-komponen" title="Tambah Komponen" :action="route('admin.komponen.store')" submit-text="Simpan">
    <div class="mb-4">
        <label for="nama" class="block mb-2 text-sm font-medium text-gray-900">Nama Komponen</label>
        <input type="text" name="nama" id="nama" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" required>
    </div>
</x-form-modal>

<!-- Modal Edit Komponen -->
@foreach($komponen as $item)
<x-form-modal :id="'modal-edit-komponen-' . $item->id" title="Edit Komponen" :action="route('admin.komponen.update', $item->id)" method="PUT" submit-text="Update">
    <div class="mb-4">
        <label for="nama_edit_{{ $item->id }}" class="block mb-2 text-sm font-medium text-gray-900">Nama Komponen</label>
        <input type="text" name="nama" id="nama_edit_{{ $item->id }}" value="{{ $item->nama }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" required>
    </div>
</x-form-modal>
@endforeach

<!-- Modal Konfirmasi Hapus Komponen -->
@foreach($komponen as $item)
<x-confirm-modal :id="'modal-confirm-hapus-komponen-' . $item->id" title="Konfirmasi Hapus Komponen" :message="'Apakah Anda yakin ingin menghapus komponen ' . $item->nama . '?'" :action="route('admin.komponen.destroy', $item->id)" method="DELETE" />
@endforeach
@endsection 