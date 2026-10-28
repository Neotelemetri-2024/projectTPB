@extends('layouts.main')
@section('title', 'Kelola Dosen Kelas')

@section('content')
<div class="p-4 md:p-6 space-y-4">
    <div class="bg-white border border-gray-200 rounded-xl">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Kelola Dosen Kelas {{ $kelas->namaKelas }}</h2>
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

            <form method="GET" action="{{ route('admin.kelas.manage-dosen', $kelas->id) }}" class="mb-6" data-live-search data-live-search-target="#available-dosen-results">
                <div class="flex flex-col sm:flex-row gap-3 items-end">
                    <div class="flex-1 w-full">
                        <label for="dosen-search" class="block text-sm font-medium text-gray-700 mb-1">Cari Dosen</label>
                        <input id="dosen-search" type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIP dosen..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2.5 rounded-lg flex items-center text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Cari
                        </button>
                        <a href="{{ route('admin.kelas.manage-dosen', $kelas->id) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2.5 rounded-lg flex items-center text-sm">
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            <div id="available-dosen-results" data-live-search-results>
            <!-- Dosen Tersedia -->
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="p-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-base font-semibold text-gray-900">Dosen Tersedia</h3>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600" id="selected-count">0 dosen dipilih</span>
                            <button type="button" id="select-all-btn" class="inline-flex items-center bg-amber-600 hover:bg-amber-700 text-white px-3 py-1.5 rounded-md text-sm font-medium">
                                Pilih Semua
                            </button>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    @if($availableDosens->count() > 0)
                        <form id="dosen-form" action="{{ route('admin.kelas.add-dosen', $kelas->id) }}" method="POST">
                            @csrf
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-amber-600 focus:border-amber-300 focus:ring focus:ring-amber-200 focus:ring-opacity-50">
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Dosen</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($availableDosens as $dosen)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <input type="checkbox" 
                                                           name="dosenIds[]" 
                                                           value="{{ $dosen->id }}"
                                                           class="dosen-checkbox rounded border-gray-300 text-amber-600 focus:border-amber-300 focus:ring focus:ring-amber-200 focus:ring-opacity-50">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $dosen->nip }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $dosen->nama }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $dosen->user->email ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4 flex justify-between items-center">
                                <div class="text-sm text-gray-700">
                                    Menampilkan {{ $availableDosens->firstItem() }} - {{ $availableDosens->lastItem() }}
                                    dari {{ $availableDosens->total() }} dosen tersedia
                                </div>
                                <button type="submit" 
                                        id="add-selected-btn"
                                        class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                        disabled>
                                    Tambahkan yang Dipilih
                                </button>
                            </div>
                            @if($availableDosens->total() > 0)
                            <div class="mt-3">{{ $availableDosens->links() }}</div>
                            @endif
                        </form>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-500 mb-4">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            @if(request('search'))
                                <h3 class="text-sm font-medium text-gray-900 mb-2">Tidak ada dosen yang cocok</h3>
                                <p class="text-sm text-gray-500">Coba kata kunci nama atau NIP yang lain.</p>
                            @else
                                <h3 class="text-sm font-medium text-gray-900 mb-2">Semua dosen sudah ditambahkan ke kelas ini</h3>
                                <p class="text-sm text-gray-500">Tidak ada dosen tersedia untuk ditambahkan</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

<script>
function initDosenPicker() {
    const selectAllCheckbox = document.getElementById('select-all');
    const selectedCountSpan = document.getElementById('selected-count');
    const addSelectedBtn = document.getElementById('add-selected-btn');
    const selectAllBtn = document.getElementById('select-all-btn');
    const dosenCheckboxes = document.querySelectorAll('.dosen-checkbox');

    if (!selectAllCheckbox || !selectedCountSpan || !addSelectedBtn || !selectAllBtn) {
        return;
    }

    function updateSelectedCount() {
        const selectedCount = document.querySelectorAll('.dosen-checkbox:checked').length;
        selectedCountSpan.textContent = `${selectedCount} dosen dipilih`;
        addSelectedBtn.disabled = selectedCount === 0;
    }

    function updateSelectAllState() {
        const boxes = document.querySelectorAll('.dosen-checkbox');
        const checkedCheckboxes = document.querySelectorAll('.dosen-checkbox:checked').length;

        if (checkedCheckboxes === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (checkedCheckboxes === boxes.length) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }

    selectAllCheckbox.addEventListener('change', function() {
        document.querySelectorAll('.dosen-checkbox').forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedCount();
        updateSelectAllState();
    });

    dosenCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectedCount();
            updateSelectAllState();
        });
    });

    selectAllBtn.addEventListener('click', function() {
        const boxes = Array.from(document.querySelectorAll('.dosen-checkbox'));
        const allChecked = boxes.length > 0 && boxes.every(cb => cb.checked);
        boxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });
        selectAllCheckbox.checked = !allChecked;
        selectAllCheckbox.indeterminate = false;
        updateSelectedCount();
        updateSelectAllState();
        this.textContent = allChecked ? 'Pilih Semua' : 'Hapus Semua';
    });

    updateSelectedCount();
    updateSelectAllState();
}

document.addEventListener('DOMContentLoaded', initDosenPicker);
document.addEventListener('live-search:updated', initDosenPicker);
</script>

@endsection 