@extends('layouts.main')
@section('title', 'Detail Kelas')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Detail Kelas {{ $kelas->namaKelas }}</h2>
                <a href="{{ route('admin.tahun-ajaran-matkul.show', $kelas->tahunAjaranMatkul->id) }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <div class="p-6">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Informasi Kelas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-md font-semibold text-gray-900 mb-4">Informasi Kelas</h3>
                    <div class="space-y-3">
                        <div class="flex">
                            <span class="w-24 text-sm font-medium text-gray-600">Mata Kuliah:</span>
                            <span class="text-sm text-gray-900">{{ $kelas->tahunAjaranMatkul->mataKuliah->namaMatkul }}</span>
                        </div>
                        <div class="flex">
                            <span class="w-24 text-sm font-medium text-gray-600">Kode:</span>
                            <span class="text-sm text-gray-900">{{ $kelas->tahunAjaranMatkul->mataKuliah->kodeMatkul }}-{{ $kelas->tahunAjaranMatkul->mataKuliah->kurikulum }}</span>
                        </div>
                        <div class="flex">
                            <span class="w-24 text-sm font-medium text-gray-600">Tahun Ajaran:</span>
                            <span class="text-sm text-gray-900">{{ $kelas->tahunAjaranMatkul->tahunAjaran->tahun }}-{{ $kelas->tahunAjaranMatkul->tahunAjaran->periode }}</span>
                        </div>
                        <div class="flex">
                            <span class="w-24 text-sm font-medium text-gray-600">Kelas:</span>
                            <span class="text-sm text-gray-900">{{ $kelas->namaKelas }}</span>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-md font-semibold text-gray-900 mb-4">Statistik</h3>
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div class="border-r border-gray-300">
                            <div class="text-2xl font-bold text-blue-600">{{ $kelas->dosenPengampuKelas->count() }}</div>
                            <p class="text-sm text-gray-600">Dosen Pengampu</p>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-green-600">{{ $kelas->kelasMahasiswa->count() }}</div>
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
                        <a href="{{ route('admin.kelas.manage-dosen', $kelas->id) }}"
                           class="bg-amber-600 hover:bg-amber-700 text-white px-3 py-1 rounded text-sm flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Kelola Dosen
                        </a>
                    </div>
                    <div class="p-4">
                        @if($kelas->dosenPengampuKelas->count() > 0)
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
                                        @foreach($kelas->dosenPengampuKelas as $index => $dosenPengampuKelas)
                                            @php
                                                $dosen = $dosenPengampuKelas->dosen;
                                            @endphp
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $dosen->nip }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $dosen->nama }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $dosen->user->email ?? '-' }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <button type="button"
                                                            data-modal-target="modal-confirm-hapus-dosen-{{ $dosen->id }}"
                                                            data-modal-toggle="modal-confirm-hapus-dosen-{{ $dosen->id }}"
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
                    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-md font-semibold text-gray-900">Mahasiswa</h3>
                        <a href="{{ route('admin.kelas.manage-mahasiswa', $kelas->id) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Kelola Mahasiswa
                        </a>
                    </div>
                    <div class="p-4">
                        @if($kelas->kelasMahasiswa->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Mahasiswa</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun Masuk</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($kelas->kelasMahasiswa as $index => $kelasMahasiswaItem)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $kelasMahasiswaItem->mahasiswa->nim }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $kelasMahasiswaItem->mahasiswa->nama }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $kelasMahasiswaItem->mahasiswa->tahunMasuk }}</td>
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
                        @else
                            <p class="text-gray-500 text-center py-4">Belum ada mahasiswa</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Dosen -->
@foreach($kelas->dosenPengampuKelas as $dosenPengampuKelas)
    @php
        $dosen = $dosenPengampuKelas->dosen;
    @endphp
    <x-confirm-modal
        :id="'modal-confirm-hapus-dosen-' . $dosen->id"
        title="Konfirmasi Hapus Dosen Pengampu"
        :message="'Apakah Anda yakin ingin menghapus ' . $dosen->nama . ' dari Kelas ' . $kelas->namaKelas . '?'"
        :action="route('admin.kelas.remove-dosen', [$kelas->id, $dosen->id])"
        method="DELETE"
    />
@endforeach

<!-- Modal Konfirmasi Hapus Mahasiswa -->
@foreach($kelas->kelasMahasiswa as $kelasMahasiswaItem)
    <x-confirm-modal
        :id="'modal-confirm-hapus-mahasiswa-' . $kelasMahasiswaItem->mahasiswa->id"
        title="Konfirmasi Hapus Mahasiswa"
        :message="'Apakah Anda yakin ingin menghapus ' . $kelasMahasiswaItem->mahasiswa->nama . ' dari Kelas ' . $kelas->namaKelas . '?'"
        :action="route('admin.kelas.remove-mahasiswa', [$kelas->id, $kelasMahasiswaItem->mahasiswa->id])"
        method="DELETE"
    />
@endforeach

@endsection 