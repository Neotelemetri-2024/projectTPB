@extends('layouts.main')

@section('title', 'Data Mata Kuliah')

@section('content')
<div class="p-6">

    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <h2 class="text-lg font-semibold text-gray-900">Daftar Mata Kuliah</h2>
                <button data-modal-target="modal-tambah" data-modal-toggle="modal-tambah" 
                        class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg flex items-center w-fit">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Mata Kuliah
                </button>
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

        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mk->namaMatkul }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $mk->jenis == 'wajib' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
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
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
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
            <div class="mt-6">
                {{ $mataKuliah->links('pagination::tailwind') }}
            </div>
        </div>
        
    </div>
</div>

<!-- Modal Tambah Mata Kuliah -->
<div id="modal-tambah" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 rounded-t">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Tambah Mata Kuliah
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="modal-tambah">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <form action="{{ route('admin.mata-kuliah.store') }}" method="POST" class="p-4 md:p-5">
                @csrf
                <div class="grid gap-4 mb-4 grid-cols-2">
                    <div class="col-span-2">
                        <label for="kodeMatkul" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kode Mata Kuliah</label>
                        <input type="text" name="kodeMatkul" id="kodeMatkul" value="{{ old('kodeMatkul') }}" class="bg-gray-50 border {{ $errors->has('kodeMatkul') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="MK001" required>
                        @error('kodeMatkul')
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
                        <input type="number" name="sks" id="sks" value="{{ old('sks') }}" min="1" max="6" class="bg-gray-50 border {{ $errors->has('sks') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="3" required>
                        @error('sks')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="flex items-center justify-end space-x-2 rtl:space-x-reverse">
                    <button type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600" data-modal-hide="modal-tambah">
                        Batal
                    </button>
                    <button type="submit" class="text-white bg-amber-600 hover:bg-amber-700 focus:ring-4 focus:outline-none focus:ring-amber-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-amber-600 dark:hover:bg-amber-700 dark:focus:ring-amber-800">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Mata Kuliah -->
@foreach($mataKuliah as $mk)
<div id="modal-edit-{{ $mk->id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 rounded-t">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Edit Mata Kuliah
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="modal-edit-{{ $mk->id }}">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <form action="{{ route('admin.mata-kuliah.update', $mk->id) }}" method="POST" class="p-4 md:p-5">
                @csrf
                @method('PUT')
                <div class="grid gap-4 mb-4 grid-cols-2">
                    <div class="col-span-2">
                        <label for="kodeMatkul_{{ $mk->id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kode Mata Kuliah</label>
                        <input type="text" name="kodeMatkul" id="kodeMatkul_{{ $mk->id }}" value="{{ $mk->kodeMatkul }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="MK001" required>
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
                        <input type="number" name="sks" id="sks_{{ $mk->id }}" value="{{ $mk->sks }}" min="1" max="6" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="3" required>
                    </div>
                </div>
                <div class="flex items-center justify-end space-x-2 rtl:space-x-reverse">
                    <button type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600" data-modal-hide="modal-edit-{{ $mk->id }}">
                        Batal
                    </button>
                    <button type="submit" class="text-white bg-amber-600 hover:bg-amber-700 focus:ring-4 focus:outline-none focus:ring-amber-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-amber-600 dark:hover:bg-amber-700 dark:focus:ring-amber-800">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Modal Konfirmasi Hapus -->
@foreach($mataKuliah as $mk)
    <x-confirm-modal 
        :id="'modal-confirm-hapus-' . $mk->id"
        title="Konfirmasi Hapus Mata Kuliah"
        :message="'Apakah Anda yakin ingin menghapus mata kuliah ' . $mk->namaMatkul . ' (' . $mk->kodeMatkul . ')?'"
        :action="route('admin.mata-kuliah.destroy', $mk->id)"
    />
@endforeach

@endsection

 