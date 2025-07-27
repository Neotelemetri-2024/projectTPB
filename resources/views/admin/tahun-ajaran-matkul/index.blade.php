@extends('layouts.main')

@section('title', 'Tahun Ajaran Mata Kuliah')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Tahun Ajaran Mata Kuliah</h2>
                <a href="{{ route('admin.tahun-ajaran-matkul.create') }}"
                   class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Mata Kuliah
                </a>
            </div>
        </div>

        <!-- Filter dan Search -->
        <div class="p-6 border-b border-gray-200">
            <form method="GET" action="{{ route('admin.tahun-ajaran-matkul.index') }}">
                <div class="flex gap-4 items-end">
                    <div class="flex-shrink-0 w-48">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Filter Tahun Ajaran</label>
                        <select name="tahun_ajaran_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" onchange="this.form.submit()">
                            <option value="">Semua Tahun Ajaran</option>
                            @foreach($tahunAjarans as $tahunAjaran)
                                <option value="{{ $tahunAjaran->id }}" {{ $selectedTahunAjaranId == $tahunAjaran->id ? 'selected' : '' }}>
                                    {{ $tahunAjaran->tahun }}-{{ $tahunAjaran->periode }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cari Mata Kuliah</label>
                        <input type="text" name="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" placeholder="Cari nama atau kode mata kuliah..." value="{{ request('search') }}">
                    </div>
                    <div class="flex-shrink-0 flex space-x-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2.5 rounded-lg flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Cari
                        </button>
                        <a href="{{ route('admin.tahun-ajaran-matkul.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2.5 rounded-lg flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun Ajaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Kuliah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dosen Pengampu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mahasiswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tahunAjaranMatkuls as $index => $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $tahunAjaranMatkuls->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $item->tahunAjaran->tahun }}-{{ $item->tahunAjaran->periode }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $item->mataKuliah->namaMatkul }}</div>
                                    <div class="text-sm text-gray-500">{{ $item->mataKuliah->kodeMatkul }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($item->kelas as $kelas)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $kelas->namaKelas }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="max-w-xs">
                                    @php
                                        $allDosen = $item->kelas->flatMap->dosenPengampuKelas->map(function($dosenPengampuKelas) {
                                            return $dosenPengampuKelas->dosen;
                                        })->unique('id');
                                    @endphp
                                    @foreach($allDosen->take(3) as $dosen)
                                        <div class="text-sm">{{ $dosen->nama }}</div>
                                    @endforeach
                                    @if($allDosen->count() > 3)
                                        <div class="text-xs text-gray-500">+{{ $allDosen->count() - 3 }} dosen lainnya</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @php
                                    $totalMahasiswa = $item->kelas->flatMap->kelasMahasiswa->pluck('mahasiswaId')->unique()->count();
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ $totalMahasiswa }} mahasiswa
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.tahun-ajaran-matkul.show', $item->id) }}"
                                       class="text-blue-600 hover:text-blue-900" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.tahun-ajaran-matkul.edit', $item->id) }}"
                                       class="text-amber-600 hover:text-amber-900" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <button type="button"
                                            data-modal-target="modal-confirm-hapus-{{ $item->id }}"
                                            data-modal-toggle="modal-confirm-hapus-{{ $item->id }}"
                                            class="text-red-600 hover:text-red-900" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                Tidak ada data tahun ajaran mata kuliah
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Menampilkan {{ $tahunAjaranMatkuls->firstItem() ?? 0 }} sampai {{ $tahunAjaranMatkuls->lastItem() ?? 0 }}
                dari {{ $tahunAjaranMatkuls->total() }} data
            </div>
            <div>
                {{ $tahunAjaranMatkuls->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
@foreach($tahunAjaranMatkuls as $item)
    <x-confirm-modal
        :id="'modal-confirm-hapus-' . $item->id"
        title="Konfirmasi Hapus"
        :message="'Apakah Anda yakin ingin menghapus mata kuliah ' . $item->mataKuliah->namaMatkul . ' dari tahun ajaran ' . $item->tahunAjaran->tahun . '-' . $item->tahunAjaran->periode . '?'"
        :action="route('admin.tahun-ajaran-matkul.destroy', $item->id)"
        method="DELETE"
    />
@endforeach

@endsection


