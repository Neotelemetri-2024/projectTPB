@extends('layouts.main')

@section('title', 'Kelola Mahasiswa - ' . $tahunAjaranMatkul->mataKuliah->namaMatkul)

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Kelola Mahasiswa</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ $tahunAjaranMatkul->mataKuliah->kodeMatkul }} - {{ $tahunAjaranMatkul->mataKuliah->namaMatkul }}
                        <span class="mx-2">•</span>
                        Kelas {{ chr(64 + $tahunAjaranMatkul->kelas) }}
                        <span class="mx-2">•</span>
                        {{ $tahunAjaranMatkul->tahunAjaran->tahun }} - {{ $tahunAjaranMatkul->tahunAjaran->periode }}
                    </p>
                </div>
                <a href="{{ request('back_url', route('admin.tahun-ajaran-matkul.show', $tahunAjaranMatkul->id)) }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mx-6 mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mx-6 mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Filter dan Search Form -->
        <div class="p-6 border-b border-gray-200">
            <form method="GET" action="{{ route('admin.tahun-ajaran-matkul.manage-mahasiswa', $tahunAjaranMatkul->id) }}">
                <div class="flex gap-4 items-end">
                    <div class="flex-shrink-0 w-48">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Filter Tahun Masuk</label>
                        <select name="tahun_masuk" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" onchange="this.form.submit()">
                            <option value="">Semua Tahun</option>
                            @foreach($tahunMasukOptions as $tahun)
                                <option value="{{ $tahun }}" {{ request('tahun_masuk') == $tahun ? 'selected' : '' }}>
                                    {{ $tahun }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cari Mahasiswa (Nama/NIM)</label>
                        <input type="text" name="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" placeholder="Cari nama atau NIM mahasiswa..." value="{{ request('search') }}">
                    </div>
                    <div class="flex-shrink-0 flex space-x-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2.5 rounded-lg flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Cari
                        </button>
                        <a href="{{ route('admin.tahun-ajaran-matkul.manage-mahasiswa', $tahunAjaranMatkul->id) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2.5 rounded-lg flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Bulk Add Form -->
        <form method="POST" action="{{ route('admin.tahun-ajaran-matkul.bulk-add-mahasiswa', $tahunAjaranMatkul->id) }}" id="bulkAddForm">
            @csrf
            
            <!-- Action Bar -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                            <span class="ml-2 text-sm text-gray-700">Pilih Semua</span>
                        </label>
                        <span class="text-sm text-gray-600" id="selectedCount">0 mahasiswa dipilih</span>
                    </div>
                    <button type="button" 
                            data-modal-toggle="confirmAddModal"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center" id="addSelectedBtn" disabled>
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambahkan yang Dipilih
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="selectAllHeader" class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun Masuk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($availableMahasiswas as $mahasiswa)
                            <tr class="hover:bg-gray-50 mahasiswa-row">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="mahasiswa_ids[]" value="{{ $mahasiswa->id }}" class="mahasiswa-checkbox rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $mahasiswa->nim }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mahasiswa->nama }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $mahasiswa->tahunMasuk }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mahasiswa->user->email ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    @if(request('search') || request('tahun_masuk'))
                                        Tidak ada mahasiswa yang sesuai dengan pencarian/filter
                                    @else
                                        Semua mahasiswa sudah ditambahkan ke kelas ini
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Menampilkan {{ $availableMahasiswas->firstItem() ?? 0 }} sampai {{ $availableMahasiswas->lastItem() ?? 0 }} 
                dari {{ $availableMahasiswas->total() }} mahasiswa tersedia
            </div>
            <div>
                {{ $availableMahasiswas->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Confirm Add Modal -->
<x-confirm-modal 
    id="confirmAddModal"
    title="Konfirmasi Tambah Mahasiswa"
    message="Apakah Anda yakin ingin menambahkan mahasiswa yang dipilih ke kelas ini?"
    :action="route('admin.tahun-ajaran-matkul.bulk-add-mahasiswa', $tahunAjaranMatkul->id)"
    type="success"
    confirmText="Tambahkan"
    cancelText="Batal"
/>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const selectAllHeader = document.getElementById('selectAllHeader');
    const checkboxes = document.querySelectorAll('.mahasiswa-checkbox');
    const selectedCount = document.getElementById('selectedCount');
    const addSelectedBtn = document.getElementById('addSelectedBtn');

    function updateUI() {
        const checked = document.querySelectorAll('.mahasiswa-checkbox:checked');
        const count = checked.length;
        
        selectedCount.textContent = `${count} mahasiswa dipilih`;
        addSelectedBtn.disabled = count === 0;
        
        // Update select all checkboxes
        const allChecked = count === checkboxes.length && checkboxes.length > 0;
        const someChecked = count > 0;
        
        selectAll.checked = allChecked;
        selectAllHeader.checked = allChecked;
        selectAll.indeterminate = someChecked && !allChecked;
        selectAllHeader.indeterminate = someChecked && !allChecked;
    }

    // Select all functionality
    [selectAll, selectAllHeader].forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateUI();
        });
    });

    // Individual checkbox change
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateUI);
    });

    // Initial UI update
    updateUI();

    // Handle modal confirmation
    document.getElementById('addSelectedBtn').addEventListener('click', function() {
        const checked = document.querySelectorAll('.mahasiswa-checkbox:checked');
        if (checked.length === 0) {
            alert('Pilih minimal satu mahasiswa untuk ditambahkan.');
            return;
        }
        
        // Update modal message with count
        const modal = document.getElementById('confirmAddModal');
        const messageElement = modal.querySelector('p');
        messageElement.textContent = `Apakah Anda yakin ingin menambahkan ${checked.length} mahasiswa ke kelas ini?`;
        
        // Update form to include selected mahasiswa
        const form = modal.querySelector('form');
        
        // Clear existing hidden inputs
        const existingInputs = form.querySelectorAll('input[name="mahasiswa_ids[]"]');
        existingInputs.forEach(input => input.remove());
        
        // Add selected mahasiswa IDs to modal form
        checked.forEach(checkbox => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'mahasiswa_ids[]';
            hiddenInput.value = checkbox.value;
            form.appendChild(hiddenInput);
        });
    });
});
</script>
@endpush

@endsection 