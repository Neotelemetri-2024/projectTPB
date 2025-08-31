@extends('layouts.main')

@section('title', 'Data Mata Kuliah')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <h2 class="text-lg font-semibold text-gray-900">Daftar Mata Kuliah</h2>
                <div class="flex flex-col sm:flex-row gap-2">
                    <!-- Import/Export Buttons -->
                    <button type="button" data-modal-target="modal-import" data-modal-toggle="modal-import" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg flex items-center text-sm">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        Import Excel
                    </button>
                    <a href="{{ route('admin.mata-kuliah.export-template') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg flex items-center text-sm">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download Template
                    </a>
                <button data-modal-target="modal-tambah" data-modal-toggle="modal-tambah" 
                            class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Mata Kuliah
                </button>
                </div>
            </div>
        </div>
        
        <!-- Form Pencarian -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center gap-3 w-full">
                <form method="GET" action="" class="flex flex-col md:flex-row md:items-center gap-3 w-full">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau kode mata kuliah..." class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 text-sm bg-gray-50 focus:bg-white transition-colors">
                    <button type="submit" class="px-6 py-2.5 bg-amber-600 text-white rounded-lg hover:bg-amber-700 focus:ring-4 focus:ring-amber-300 text-sm font-medium transition-colors">
                        <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Cari
                    </button>
                </form>
                @if(request('q'))
                    <a href="{{ route('admin.mata-kuliah.index') }}" class="px-4 py-2.5 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 border border-gray-300 whitespace-nowrap font-medium transition-colors">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kurikulum</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Mata Kuliah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKS</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($mataKuliah as $mk)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $loop->iteration + ($mataKuliah->currentPage() - 1) * $mataKuliah->perPage() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mk->kodeMatkul }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mk->kurikulum }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mk->namaMatkul }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                {{ ucfirst($mk->jenis) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mk->sks }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button data-modal-target="modal-edit-{{ $mk->id }}" data-modal-toggle="modal-edit-{{ $mk->id }}" 
                                        class="text-yellow-600 hover:text-yellow-900 p-1 rounded hover:bg-yellow-50 transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <!-- Tombol Hapus -->
                                <button type="button" data-modal-target="modal-confirm-hapus-{{ $mk->id }}" data-modal-toggle="modal-confirm-hapus-{{ $mk->id }}" class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50 transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p>Tidak ada data mata kuliah</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($mataKuliah->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $mataKuliah->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Import Error Messages -->
@if(session('import_errors'))
    <x-error-modal 
        id="modal-error"
        title="Error Import Mata Kuliah"
    >
        <div class="max-h-96 overflow-y-auto">
            <ul class="list-disc list-inside space-y-1 text-sm text-gray-700">
                @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </x-error-modal>
@endif

<!-- Modal Import Excel -->
<x-import-modal 
    id="modal-import"
    title="Import Data Mata Kuliah"
    :action="route('admin.mata-kuliah.import')"
    submit-text="Import Data"
>
    <div class="mb-4">
        <label for="excel_file" class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel</label>
        <input type="file" name="excel_file" id="excel_file" accept=".xlsx,.xls" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
        <p class="mt-1 text-sm text-gray-500">Format yang didukung: .xlsx, .xls (Maksimal 2MB)</p>
    </div>
    
    <div class="mb-4 p-3 bg-blue-50 rounded-lg">
        <p class="text-sm text-blue-800">
            <strong>Kolom wajib:</strong> NAMA_MATA_KULIAH, KODE, KURIKULUM, SKS, JENIS<br>
            <strong>SKS:</strong> angka positif | <strong>JENIS:</strong> wajib/pilihan
        </p>
    </div>
</x-import-modal>

<!-- Modal Tambah Mata Kuliah -->
<x-form-modal 
    id="modal-tambah"
    title="Tambah Mata Kuliah"
    :action="route('admin.mata-kuliah.store')"
    submit-text="Simpan"
>
    <div class="grid gap-4 mb-4 grid-cols-2">
        <div>
            <label for="kodeMatkul" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kode Mata Kuliah</label>
            <input type="text" name="kodeMatkul" id="kodeMatkul" value="{{ old('kodeMatkul') }}" class="bg-gray-50 border {{ $errors->has('kodeMatkul') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="MK001" required>
            @error('kodeMatkul')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="kurikulum" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kurikulum</label>
            <input type="text" name="kurikulum" id="kurikulum" value="{{ old('kurikulum') }}" class="bg-gray-50 border {{ $errors->has('kurikulum') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="2020" required>
            @error('kurikulum')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="col-span-2">
            <label for="namaMatkul" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Mata Kuliah</label>
            <input type="text" name="namaMatkul" id="namaMatkul" value="{{ old('namaMatkul') }}" class="bg-gray-50 border {{ $errors->has('namaMatkul') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="Pemrograman Web" required>
            @error('namaMatkul')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="col-span-2">
            <label for="jenis" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis</label>
            <select name="jenis" id="jenis" class="bg-gray-50 border {{ $errors->has('jenis') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
                <option value="">Pilih Jenis</option>
                <option value="wajib" {{ old('jenis') == 'wajib' ? 'selected' : '' }}>Wajib</option>
                <option value="pilihan" {{ old('jenis') == 'pilihan' ? 'selected' : '' }}>Pilihan</option>
            </select>
            @error('jenis')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="col-span-2">
            <label for="sks" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">SKS</label>
            <input type="number" name="sks" id="sks" value="{{ old('sks') }}" min="0" class="bg-gray-50 border {{ $errors->has('sks') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="3" required>
            @error('sks')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</x-form-modal>

<!-- Modal Edit Mata Kuliah -->
@foreach($mataKuliah as $mk)
    <x-form-modal 
        :id="'modal-edit-' . $mk->id"
        title="Edit Mata Kuliah"
        :action="route('admin.mata-kuliah.update', $mk->id)"
        method="PUT"
        submit-text="Update"
    >
        <div class="grid gap-4 mb-4 grid-cols-2">
            <div>
                <label for="kodeMatkul_{{ $mk->id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kode Mata Kuliah</label>
                <input type="text" name="kodeMatkul" id="kodeMatkul_{{ $mk->id }}" value="{{ $mk->kodeMatkul }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="MK001" required>
            </div>
            <div>
                <label for="kurikulum_{{ $mk->id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kurikulum</label>
                <input type="text" name="kurikulum" id="kurikulum_{{ $mk->id }}" value="{{ $mk->kurikulum }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="2020" required>
            </div>
            <div class="col-span-2">
                <label for="namaMatkul_{{ $mk->id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Mata Kuliah</label>
                <input type="text" name="namaMatkul" id="namaMatkul_{{ $mk->id }}" value="{{ $mk->namaMatkul }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="Pemrograman Web" required>
            </div>
            <div class="col-span-2">
                <label for="jenis_{{ $mk->id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis</label>
                <select name="jenis" id="jenis_{{ $mk->id }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
                    <option value="">Pilih Jenis</option>
                    <option value="wajib" {{ (old('jenis', $mk->jenis) == 'wajib') ? 'selected' : '' }}>Wajib</option>
                    <option value="pilihan" {{ (old('jenis', $mk->jenis) == 'pilihan') ? 'selected' : '' }}>Pilihan</option>
                </select>
            </div>
            <div class="col-span-2">
                <label for="sks_{{ $mk->id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">SKS</label>
                <input type="number" name="sks" id="sks_{{ $mk->id }}" value="{{ $mk->sks }}" min="0" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="3" required>
            </div>
        </div>
    </x-form-modal>
@endforeach

<!-- Modal Konfirmasi Hapus -->
@foreach($mataKuliah as $mk)
    <x-confirm-modal 
        :id="'modal-confirm-hapus-' . $mk->id"
        title="Konfirmasi Hapus Mata Kuliah"
        :message="'Apakah Anda yakin ingin menghapus mata kuliah ' . $mk->namaMatkul . ' (' . $mk->kodeMatkul . ')?'"
        :action="route('admin.mata-kuliah.destroy', $mk->id)"
        method="DELETE"
    />
@endforeach

@endsection

@if(session('import_errors'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto show error modal if there are import errors
    const errorModal = document.getElementById('modal-error');
    if (errorModal) {
        errorModal.classList.remove('hidden');
        errorModal.classList.add('flex');
        
        const modalContent = errorModal.querySelector('[data-modal-content]');
        setTimeout(() => {
            errorModal.classList.remove('bg-opacity-0');
            errorModal.classList.add('bg-opacity-10');
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }
});
</script>
@endif


 