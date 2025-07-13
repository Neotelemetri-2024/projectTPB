@extends('layouts.main')
@section('title', 'Detail Mata Kuliah Tahun Ajaran')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Detail Mata Kuliah Tahun Ajaran</h2>
                <a href="{{ request('back_url', route('admin.tahun-ajaran-matkul.index')) }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <div class="p-6">
            {{-- @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif --}}

            <!-- Informasi Umum -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-md font-semibold text-gray-900 mb-4">Informasi Mata Kuliah</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div class="flex">
                                <span class="w-24 text-sm font-medium text-gray-600">Tahun Ajaran:</span>
                                <span class="text-sm text-gray-900">{{ $tahunAjaranMatkul->tahunAjaran->tahun }}-{{ $tahunAjaranMatkul->tahunAjaran->periode }}</span>
                            </div>
                            <div class="flex">
                                <span class="w-24 text-sm font-medium text-gray-600">Mata Kuliah:</span>
                                <span class="text-sm text-gray-900">{{ $tahunAjaranMatkul->mataKuliah->namaMatkul }}</span>
                            </div>
                            <div class="flex">
                                <span class="w-24 text-sm font-medium text-gray-600">Kode:</span>
                                <span class="text-sm text-gray-900">{{ $tahunAjaranMatkul->mataKuliah->kodeMatkul }}</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex">
                                <span class="w-16 text-sm font-medium text-gray-600">Kelas:</span>
                                <span class="text-sm text-gray-900">{{ $tahunAjaranMatkul->kelasHuruf }}</span>
                            </div>
                            <div class="flex">
                                <span class="w-16 text-sm font-medium text-gray-600">SKS:</span>
                                <span class="text-sm text-gray-900">{{ $tahunAjaranMatkul->mataKuliah->sks }}</span>
                            </div>
                            <div class="flex">
                                <span class="w-16 text-sm font-medium text-gray-600">Jenis:</span>
                                <span class="text-sm text-gray-900">{{ $tahunAjaranMatkul->mataKuliah->jenis }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-md font-semibold text-gray-900 mb-4">Statistik</h3>
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div class="border-r border-gray-300">
                            <div class="text-2xl font-bold text-blue-600">{{ $tahunAjaranMatkul->dosenPengampu->count() }}</div>
                            <p class="text-sm text-gray-600">Dosen Pengampu</p>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-green-600">{{ $kelasMahasiswa->total() }}</div>
                            <p class="text-sm text-gray-600">Mahasiswa</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dosen Pengampu -->
            <div class="mb-6">
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-md font-semibold text-gray-900">Dosen Pengampu</h3>
                        <button type="button"
                                data-modal-target="addDosenModal"
                                data-modal-toggle="addDosenModal"
                                class="bg-amber-600 hover:bg-amber-700 text-white px-3 py-1 rounded text-sm flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah Dosen
                        </button>
                    </div>
                    <div class="p-4">
                        @if($tahunAjaranMatkul->dosenPengampu->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Dosen</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($tahunAjaranMatkul->dosenPengampu as $index => $dosenPengampu)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $dosenPengampu->dosen->nip }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $dosenPengampu->dosen->nama }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $dosenPengampu->dosen->user->email ?? '-' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <button type="button"
                                                            data-modal-target="modal-confirm-hapus-dosen-{{ $dosenPengampu->dosen->id }}"
                                                            data-modal-toggle="modal-confirm-hapus-dosen-{{ $dosenPengampu->dosen->id }}"
                                                            class="text-red-600 hover:text-red-900" title="Hapus">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">Belum ada dosen pengampu</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Mahasiswa -->
            <div class="mb-6">
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-md font-semibold text-gray-900">Mahasiswa</h3>
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.tahun-ajaran-matkul.manage-mahasiswa', $tahunAjaranMatkul->id) }}?back_url={{ urlencode(request()->fullUrl()) }}"
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Kelola Mahasiswa
                                </a>
                                <button type="button"
                                        data-modal-target="addMahasiswaModal"
                                        data-modal-toggle="addMahasiswaModal"
                                        class="bg-amber-600 hover:bg-amber-700 text-white px-3 py-1 rounded text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Tambah Cepat
                                </button>
                            </div>
                        </div>

                        <!-- Search Form -->
                        <form method="GET" action="{{ route('admin.tahun-ajaran-matkul.show', $tahunAjaranMatkul->id) }}">
                            <div class="flex gap-2 items-end">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari Mahasiswa</label>
                                    <input type="text"
                                           name="search"
                                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5"
                                           placeholder="Cari nama atau NIM mahasiswa..."
                                           value="{{ request('search') }}">
                                </div>
                                <div class="flex-shrink-0 flex space-x-2">
                                    <button type="submit"
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2.5 rounded-lg flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                        Cari
                                    </button>
                                    <a href="{{ route('admin.tahun-ajaran-matkul.show', $tahunAjaranMatkul->id) }}"
                                       class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2.5 rounded-lg flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="p-4">
                        @if($kelasMahasiswa->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Mahasiswa</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($kelasMahasiswa as $index => $kelasMahasiswaItem)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $kelasMahasiswa->firstItem() + $index }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $kelasMahasiswaItem->mahasiswa->nim }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $kelasMahasiswaItem->mahasiswa->nama }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $kelasMahasiswaItem->mahasiswa->user->email ?? '-' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <button type="button"
                                                            data-modal-target="modal-confirm-hapus-mahasiswa-{{ $kelasMahasiswaItem->mahasiswa->id }}"
                                                            data-modal-toggle="modal-confirm-hapus-mahasiswa-{{ $kelasMahasiswaItem->mahasiswa->id }}"
                                                            class="text-red-600 hover:text-red-900" title="Hapus">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                                <div class="text-sm text-gray-700">
                                    Menampilkan {{ $kelasMahasiswa->firstItem() ?? 0 }} sampai {{ $kelasMahasiswa->lastItem() ?? 0 }}
                                    dari {{ $kelasMahasiswa->total() }} mahasiswa
                                    @if(request('search'))
                                        <span class="text-amber-600">(hasil pencarian: "{{ request('search') }}")</span>
                                    @endif
                                </div>
                                <div>
                                    {{ $kelasMahasiswa->links() }}
                                </div>
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">
                                @if(request('search'))
                                    Tidak ada mahasiswa yang sesuai dengan pencarian "{{ request('search') }}"
                                @else
                                    Belum ada mahasiswa
                                @endif
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Dosen -->
<x-form-modal
    id="addDosenModal"
    title="Tambah Dosen Pengampu"
    :action="route('admin.tahun-ajaran-matkul.add-dosen', $tahunAjaranMatkul->id)"
    submit-text="Tambah"
>
    <div class="grid gap-4 mb-4 grid-cols-1">
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Dosen</label>
            <div class="max-h-48 overflow-y-auto">
                @foreach($dosens as $dosen)
                    <label class="flex items-center mb-2">
                        <input type="checkbox"
                               name="dosenIds[]"
                               value="{{ $dosen->id }}"
                               class="rounded border-gray-300 text-amber-600 shadow-sm focus:border-amber-300 focus:ring focus:ring-amber-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            {{ $dosen->nama }}
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>
</x-form-modal>

<!-- Modal Tambah Mahasiswa -->
<x-form-modal
    id="addMahasiswaModal"
    title="Tambah Mahasiswa ke Kelas"
    :action="route('admin.tahun-ajaran-matkul.add-mahasiswa', $tahunAjaranMatkul->id)"
    submit-text="Tambah"
>
    <div class="grid gap-4 mb-4 grid-cols-1">
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Mahasiswa</label>
            <div class="max-h-48 overflow-y-auto">
                @foreach($mahasiswas as $mahasiswa)
                    <label class="flex items-center mb-2">
                        <input type="checkbox"
                               name="mahasiswaIds[]"
                               value="{{ $mahasiswa->id }}"
                               class="rounded border-gray-300 text-amber-600 shadow-sm focus:border-amber-300 focus:ring focus:ring-amber-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            {{ $mahasiswa->nama }} ({{ $mahasiswa->nim }})
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>
</x-form-modal>

<!-- Modal Konfirmasi Hapus Dosen -->
@foreach($tahunAjaranMatkul->dosenPengampu as $dosenPengampu)
    <x-confirm-modal
        :id="'modal-confirm-hapus-dosen-' . $dosenPengampu->dosen->id"
        title="Konfirmasi Hapus Dosen Pengampu"
        :message="'Apakah Anda yakin ingin menghapus ' . $dosenPengampu->dosen->nama . ' dari dosen pengampu?'"
        :action="route('admin.tahun-ajaran-matkul.remove-dosen', [$tahunAjaranMatkul->id, $dosenPengampu->dosen->id])"
        method="DELETE"
    />
@endforeach

<!-- Modal Konfirmasi Hapus Mahasiswa -->
@foreach($kelasMahasiswa as $kelasMahasiswaItem)
    <x-confirm-modal
        :id="'modal-confirm-hapus-mahasiswa-' . $kelasMahasiswaItem->mahasiswa->id"
        title="Konfirmasi Hapus Mahasiswa"
        :message="'Apakah Anda yakin ingin menghapus ' . $kelasMahasiswaItem->mahasiswa->nama . ' dari kelas?'"
        :action="route('admin.tahun-ajaran-matkul.remove-mahasiswa', [$tahunAjaranMatkul->id, $kelasMahasiswaItem->mahasiswa->id])"
        method="DELETE"
    />
@endforeach

@endsection
