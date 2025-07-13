@extends('layouts.main')

@section('title', 'Data Tahun Ajaran')

@section('content')
<div class="p-6">


    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <h2 class="text-lg font-semibold text-gray-900">Daftar Tahun Ajaran</h2>
                <button data-modal-target="modal-tambah" data-modal-toggle="modal-tambah" 
                        class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg flex items-center w-fit">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Tahun Ajaran
                </button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tahunAjaran as $ta)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $loop->iteration + ($tahunAjaran->currentPage() - 1) * $tahunAjaran->perPage() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $ta->tahun }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $ta->periode }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button data-modal-target="modal-edit-{{ $ta->id }}" data-modal-toggle="modal-edit-{{ $ta->id }}" 
                                        class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <!-- Tombol Hapus -->
                                <button type="button" data-modal-target="modal-confirm-hapus-{{ $ta->id }}" data-modal-toggle="modal-confirm-hapus-{{ $ta->id }}" class="text-red-600 hover:text-red-900" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data tahun ajaran</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $tahunAjaran->links('pagination::tailwind') }}
            </div>
        </div>
        
    </div>
</div>

<!-- Modal Tambah Tahun Ajaran -->
<x-form-modal 
    id="modal-tambah"
    title="Tambah Tahun Ajaran"
    :action="route('admin.tahun-ajaran.store')"
    submit-text="Simpan"
>
    <div class="grid gap-4 mb-4 grid-cols-2">
        <div class="col-span-2">
            <label for="tahun" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tahun</label>
            <input type="number" name="tahun" id="tahun" value="{{ old('tahun') }}" class="bg-gray-50 border {{ $errors->has('tahun') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="2024" required>
            @error('tahun')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="col-span-2">
            <label for="periode" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Periode</label>
            <input type="text" name="periode" id="periode" value="{{ old('periode') }}" class="bg-gray-50 border {{ $errors->has('periode') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="Ganjil" required>
            @error('periode')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</x-form-modal>

<!-- Modal Edit Tahun Ajaran -->
@foreach($tahunAjaran as $ta)
    <x-form-modal 
        :id="'modal-edit-' . $ta->id"
        title="Edit Tahun Ajaran"
        :action="route('admin.tahun-ajaran.update', $ta->id)"
        method="PUT"
        submit-text="Update"
    >
        <div class="grid gap-4 mb-4 grid-cols-2">
            <div class="col-span-2">
                <label for="tahun_{{ $ta->id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tahun</label>
                <input type="number" name="tahun" id="tahun_{{ $ta->id }}" value="{{ old('tahun', $ta->tahun) }}" class="bg-gray-50 border {{ $errors->has('tahun') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="2024" required>
                @error('tahun')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="col-span-2">
                <label for="periode_{{ $ta->id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Periode</label>
                <input type="text" name="periode" id="periode_{{ $ta->id }}" value="{{ old('periode', $ta->periode) }}" class="bg-gray-50 border {{ $errors->has('periode') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="Ganjil" required>
                @error('periode')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </x-form-modal>
@endforeach

<!-- Modal Konfirmasi Hapus -->
@foreach($tahunAjaran as $ta)
    <x-confirm-modal 
        :id="'modal-confirm-hapus-' . $ta->id"
        title="Konfirmasi Hapus Tahun Ajaran"
        :message="'Apakah Anda yakin ingin menghapus tahun ajaran ' . $ta->tahun . ' (' . $ta->periode . ')?'"
        :action="route('admin.tahun-ajaran.destroy', $ta->id)"
        method="DELETE"
    />
@endforeach

@endsection

 