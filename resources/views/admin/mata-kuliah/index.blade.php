@extends('layouts.main')

@section('title', 'Data Mata Kuliah')

@section('content')
<div class="p-4 md:p-6 space-y-4">
    <div class="bg-white border border-gray-200 rounded-xl">
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <h2 class="text-lg font-semibold text-gray-900">Daftar Mata Kuliah</h2>
                <div class="flex flex-col sm:flex-row gap-2">
                    <!-- Import/Export Buttons -->
                    <button type="button" data-modal-target="modal-import" data-modal-toggle="modal-import" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg flex items-center text-sm">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        Import Excel
                    </button>
                    <a href="{{ route('admin.mata-kuliah.export-template') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg flex items-center text-sm">
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
                <button type="button" id="btn-bulk-delete" data-modal-target="modal-bulk-delete" data-modal-toggle="modal-bulk-delete" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center hidden">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Hapus Terpilih
                </button>
                </div>
            </div>
        </div>
        
        <!-- Form Pencarian -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center gap-3 w-full">
                <form method="GET" action="" class="flex flex-col md:flex-row md:items-center gap-3 w-full">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau kode mata kuliah..." class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 text-sm bg-gray-50 focus:bg-white transition-colors">
                    <select name="kurikulumId" class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 text-sm bg-gray-50 focus:bg-white transition-colors">
                        <option value="">Semua Kurikulum</option>
                        @foreach($kurikulumList as $kur)
                            <option value="{{ $kur->id }}" @selected((string) request('kurikulumId') === (string) $kur->id)>{{ $kur->nama }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-6 py-2.5 bg-amber-600 text-white rounded-lg hover:bg-amber-700 focus:ring-4 focus:ring-amber-300 text-sm font-medium transition-colors">
                        <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Cari
                    </button>
                </form>
                @if(request('q') || request('kurikulumId'))
                    <a href="{{ route('admin.mata-kuliah.index') }}" class="px-4 py-2.5 text-sm bg-gray-600 text-white rounded-lg hover:bg-gray-700 whitespace-nowrap font-medium transition-colors">
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
                        <th class="px-6 py-3 w-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAll" class="w-4 h-4 text-amber-600 bg-gray-100 border-gray-300 rounded focus:ring-amber-500">
                        </th>
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
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" value="{{ $mk->id }}" class="matkul-checkbox w-4 h-4 text-amber-600 bg-gray-100 border-gray-300 rounded focus:ring-amber-500">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $loop->iteration + ($mataKuliah->currentPage() - 1) * $mataKuliah->perPage() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mk->kodeMatkul }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mk->kurikulumRef->nama ?? $mk->kurikulum }}</td>
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
                                        class="inline-flex items-center justify-center bg-amber-600 hover:bg-amber-700 text-white p-1.5 rounded-md" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <!-- Tombol Hapus -->
                                <button type="button" data-modal-target="modal-confirm-hapus-{{ $mk->id }}" data-modal-toggle="modal-confirm-hapus-{{ $mk->id }}" class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white p-1.5 rounded-md" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-sm text-gray-500">
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
        @if($mataKuliah->total() > 0)
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
        <input type="file" name="excel_file" id="excel_file" accept=".xlsx,.xls" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100" required>
        <p class="mt-1 text-sm text-gray-500">Format yang didukung: .xlsx, .xls (Maksimal 2MB)</p>
    </div>
    
    <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
        <p class="text-sm text-gray-700">
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
            <label for="kodeMatkul" class="block mb-2 text-sm font-medium text-gray-900">Kode Mata Kuliah</label>
            <input type="text" name="kodeMatkul" id="kodeMatkul" value="{{ old('kodeMatkul') }}" class="bg-gray-50 border {{ $errors->has('kodeMatkul') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" placeholder="MK001" required>
            @error('kodeMatkul')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="kurikulumId" class="block mb-2 text-sm font-medium text-gray-900">Kurikulum</label>
            <select name="kurikulumId" id="kurikulumId" class="bg-gray-50 border {{ $errors->has('kurikulumId') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" required>
                <option value="">Pilih Kurikulum</option>
                @foreach($kurikulumList as $kur)
                    <option value="{{ $kur->id }}" {{ (string) old('kurikulumId') === (string) $kur->id ? 'selected' : '' }}>{{ $kur->nama }}</option>
                @endforeach
            </select>
            @error('kurikulumId')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="col-span-2">
            <label for="namaMatkul" class="block mb-2 text-sm font-medium text-gray-900">Nama Mata Kuliah</label>
            <input type="text" name="namaMatkul" id="namaMatkul" value="{{ old('namaMatkul') }}" class="bg-gray-50 border {{ $errors->has('namaMatkul') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" placeholder="Pemrograman Web" required>
            @error('namaMatkul')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="col-span-2">
            <label for="jenis" class="block mb-2 text-sm font-medium text-gray-900">Jenis</label>
            <select name="jenis" id="jenis" class="bg-gray-50 border {{ $errors->has('jenis') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" required>
                <option value="">Pilih Jenis</option>
                <option value="wajib" {{ old('jenis') == 'wajib' ? 'selected' : '' }}>Wajib</option>
                <option value="pilihan" {{ old('jenis') == 'pilihan' ? 'selected' : '' }}>Pilihan</option>
            </select>
            @error('jenis')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="col-span-2">
            <label for="sks" class="block mb-2 text-sm font-medium text-gray-900">SKS</label>
            <input type="number" name="sks" id="sks" value="{{ old('sks') }}" min="0" class="bg-gray-50 border {{ $errors->has('sks') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" placeholder="3" required>
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
                <label for="kurikulumId_{{ $mk->id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kurikulum</label>
                <select name="kurikulumId" id="kurikulumId_{{ $mk->id }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" required>
                    <option value="">Pilih Kurikulum</option>
                    @foreach($kurikulumList as $kur)
                        <option value="{{ $kur->id }}" {{ (string) old('kurikulumId', $mk->kurikulumId) === (string) $kur->id ? 'selected' : '' }}>{{ $kur->nama }}</option>
                    @endforeach
                </select>
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

<!-- Modal Bulk Delete -->
<div id="modal-bulk-delete" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center bg-gray-900/50">
    <div class="relative w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-xl border border-gray-200">
            <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center" data-modal-hide="modal-bulk-delete">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
                <span class="sr-only">Tutup modal</span>
            </button>
            <div class="p-6 text-center">
                <svg class="mx-auto mb-4 text-gray-400 w-12 h-12" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <h3 class="mb-5 text-lg font-normal text-gray-500">Apakah Anda yakin ingin menghapus data mata kuliah yang dipilih?</h3>
                <form id="form-bulk-delete" action="{{ route('admin.mata-kuliah.bulk-destroy') ?? '#' }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div id="bulk-delete-inputs"></div>
                    <button type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center justify-center px-5 py-2.5 text-center mr-2">
                        <svg data-spinner class="hidden w-4 h-4 mr-2 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span data-submit-text>Ya, hapus</span>
                    </button>
                    <button data-modal-hide="modal-bulk-delete" type="button" class="bg-gray-600 hover:bg-gray-700 text-white focus:ring-4 focus:outline-none focus:ring-gray-300 rounded-lg text-sm font-medium px-5 py-2.5 focus:z-10 transition-colors duration-200">Tidak, batal</button>
                </form>
            </div>
        </div>
    </div>
</div>

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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const matkulCheckboxes = document.querySelectorAll('.matkul-checkbox');
    const btnBulkDelete = document.getElementById('btn-bulk-delete');
    const bulkDeleteInputs = document.getElementById('bulk-delete-inputs');

    function updateBulkDeleteButton() {
        const checkedCount = document.querySelectorAll('.matkul-checkbox:checked').length;
        if (checkedCount > 0) {
            btnBulkDelete.classList.remove('hidden');
        } else {
            btnBulkDelete.classList.add('hidden');
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            matkulCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkDeleteButton();
        });
    }

    matkulCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = document.querySelectorAll('.matkul-checkbox:checked').length === matkulCheckboxes.length;
            selectAllCheckbox.checked = allChecked;
            updateBulkDeleteButton();
        });
    });

    const formBulkDelete = document.getElementById('form-bulk-delete');
    if (formBulkDelete) {
        formBulkDelete.addEventListener('submit', function(e) {
            bulkDeleteInputs.innerHTML = '';
            document.querySelectorAll('.matkul-checkbox:checked').forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = checkbox.value;
                bulkDeleteInputs.appendChild(input);
            });
            const submitBtn = formBulkDelete.querySelector('button[type="submit"]');
            if (submitBtn) {
                setTimeout(() => {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('cursor-not-allowed', 'opacity-75');
                    const spinner = submitBtn.querySelector('[data-spinner]');
                    const text = submitBtn.querySelector('[data-submit-text]');
                    if (spinner) spinner.classList.remove('hidden');
                }, 10);
            }
        });
    }
});
</script>