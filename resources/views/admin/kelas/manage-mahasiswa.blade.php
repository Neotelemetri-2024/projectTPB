@extends('layouts.main')
@section('title', 'Kelola Mahasiswa Kelas')

@section('content')
<div class="p-4 md:p-6 space-y-4">
    <div class="bg-white border border-gray-200 rounded-xl">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Kelola Mahasiswa Kelas {{ $kelas->namaKelas }}</h2>
                <a href="{{ route('admin.kelas.show', $kelas->id) }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
            <div class="mt-2 text-sm text-gray-600">
                {{ $kelas->tahunAjaranMatkul->mataKuliah->namaMatkul }} â€¢ {{ $kelas->tahunAjaranMatkul->tahunAjaran->tahun }}-{{ $kelas->tahunAjaranMatkul->tahunAjaran->periode }}
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

            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.kelas.manage-mahasiswa', $kelas->id) }}" class="mb-6">
                <div class="flex gap-4 items-end">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cari Mahasiswa</label>
                        <input type="text"
                               name="search"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5"
                               placeholder="Cari nama atau NIM mahasiswa..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="flex-shrink-0">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Masuk</label>
                        <select name="tahun_masuk" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block p-2.5">
                            <option value="">Semua Tahun</option>
                            @foreach($tahunMasukOptions as $tahun)
                                <option value="{{ $tahun }}" {{ request('tahun_masuk') == $tahun ? 'selected' : '' }}>
                                    {{ $tahun }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-shrink-0 flex space-x-2">
                        <button type="submit"
                                class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2.5 rounded-lg flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Cari
                        </button>
                        <a href="{{ route('admin.kelas.manage-mahasiswa', $kelas->id) }}"
                           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2.5 rounded-lg  flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Mahasiswa List -->
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="p-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-base font-semibold text-gray-900">Mahasiswa Tersedia</h3>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600" id="selected-count">0 mahasiswa dipilih</span>
                            <button type="button" id="select-all-btn" class="inline-flex items-center bg-amber-600 hover:bg-amber-700 text-white px-3 py-1.5 rounded-md text-sm font-medium">
                                Pilih Semua
                            </button>
                            @if($kelas->kelasMahasiswa->count() > 0)
                                <a href="{{ route('admin.kelas.show', $kelas->id) }}"
                                   class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Hapus Bulk Mahasiswa
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    @if($availableMahasiswas->count() > 0)
                        <form id="mahasiswa-form" action="{{ route('admin.kelas.bulk-add-mahasiswa', $kelas->id) }}" method="POST">
                            @csrf
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-amber-600 focus:border-amber-300 focus:ring focus:ring-amber-200 focus:ring-opacity-50">
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Mahasiswa</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun Masuk</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($availableMahasiswas as $mahasiswa)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <input type="checkbox"
                                                           name="mahasiswa_ids[]"
                                                           value="{{ $mahasiswa->id }}"
                                                           class="mahasiswa-checkbox rounded border-gray-300 text-amber-600 focus:border-amber-300 focus:ring focus:ring-amber-200 focus:ring-opacity-50">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mahasiswa->nim }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mahasiswa->nama }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mahasiswa->tahunMasuk }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mahasiswa->user->email ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4 flex justify-between items-center">
                                <div class="text-sm text-gray-700">
                                    Menampilkan {{ $availableMahasiswas->firstItem() ?? 0 }} sampai {{ $availableMahasiswas->lastItem() ?? 0 }}
                                    dari {{ $availableMahasiswas->total() }} mahasiswa tersedia
                                    @if(request('search') || request('tahun_masuk'))
                                        <span class="text-amber-600">
                                            (hasil filter)
                                        </span>
                                    @endif
                                </div>
                                <button type="submit"
                                        id="add-selected-btn"
                                        class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                        disabled>
                                    Tambahkan yang Dipilih
                                </button>
                            </div>
                        </form>

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $availableMahasiswas->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-500 mb-4">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-medium text-gray-900 mb-2">Semua mahasiswa sudah ditambahkan ke kelas ini</h3>
                            <p class="text-sm text-gray-500">Tidak ada mahasiswa tersedia untuk ditambahkan</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const mahasiswaCheckboxes = document.querySelectorAll('.mahasiswa-checkbox');
    const selectedCountSpan = document.getElementById('selected-count');
    const addSelectedBtn = document.getElementById('add-selected-btn');
    const selectAllBtn = document.getElementById('select-all-btn');

    function updateSelectedCount() {
        const selectedCount = document.querySelectorAll('.mahasiswa-checkbox:checked').length;
        selectedCountSpan.textContent = `${selectedCount} mahasiswa dipilih`;
        addSelectedBtn.disabled = selectedCount === 0;
    }

    function updateSelectAllState() {
        const totalCheckboxes = mahasiswaCheckboxes.length;
        const checkedCheckboxes = document.querySelectorAll('.mahasiswa-checkbox:checked').length;

        if (checkedCheckboxes === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (checkedCheckboxes === totalCheckboxes) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        mahasiswaCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedCount();
        updateSelectAllState();
    });

    // Individual checkbox change
    mahasiswaCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectedCount();
            updateSelectAllState();
        });
    });

    // Select all button
    selectAllBtn.addEventListener('click', function() {
        const allChecked = Array.from(mahasiswaCheckboxes).every(cb => cb.checked);
        mahasiswaCheckboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });
        selectAllCheckbox.checked = !allChecked;
        selectAllCheckbox.indeterminate = false;
        updateSelectedCount();
        updateSelectAllState();

        // Update button text
        this.textContent = allChecked ? 'Pilih Semua' : 'Hapus Semua';
    });

    // Initial state
    updateSelectedCount();
    updateSelectAllState();
});
</script>

@endsection
