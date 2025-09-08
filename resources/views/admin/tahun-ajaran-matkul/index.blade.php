@extends('layouts.main')

@section('title', 'Tahun Ajaran Mata Kuliah')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Tahun Ajaran Mata Kuliah</h2>
                <div class="flex space-x-2">
                    <!-- Import/Export Buttons -->
                    <button type="button" onclick="openImportModal()" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg flex items-center text-sm">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        Import Excel
                    </button>
                    <a href="{{ route('admin.tahun-ajaran-matkul.export-template', ['tahun_ajaran_id' => $selectedTahunAjaranId]) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg flex items-center text-sm">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download Template
                    </a>
                    <button type="button" onclick="openDuplicateModal()" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1.5 rounded-lg flex items-center text-sm">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Duplicate Tahun Sebelumnya
                    </button>
                    <a href="{{ route('admin.tahun-ajaran-matkul.create') }}"
                       class="bg-amber-600 hover:bg-amber-700 text-white px-3 py-1.5 rounded-lg flex items-center text-sm">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Mata Kuliah
                    </a>
                </div>
            </div>
        </div>

        <!-- Error Import -->
        @if(session('import_errors'))
            <div class="p-6 border-b border-red-200 bg-red-50">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <h3 class="text-sm font-medium text-red-800">Error Import:</h3>
                </div>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach(session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Filter dan Search -->
        <div class="p-6 border-b border-gray-200">
            <form method="GET" action="{{ route('admin.tahun-ajaran-matkul.index') }}">
                <!-- Preserve current page when filtering -->
                @if(request('page'))
                    <input type="hidden" name="page" value="{{ request('page') }}">
                @endif
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
                            Reset Semua
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
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
                                {{ $item->tahunAjaran->tahun }}-{{ $item->tahunAjaran->periode }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $item->mataKuliah->namaMatkul }}</div>
                                    <div class="text-sm text-gray-500">{{ $item->mataKuliah->kodeMatkul }}-{{ $item->mataKuliah->kurikulum }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Semester {{ $item->semester ?? 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($item->kelas as $kelas)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700">
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
                                {{ $totalMahasiswa }} mahasiswa
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
                {{ $tahunAjaranMatkuls->appends(request()->query())->links() }}
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

<!-- Modal Import Excel -->
<div id="import-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Import Data Mata Kuliah</h3>
                <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.tahun-ajaran-matkul.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload File Excel</label>
                    <input type="file" name="file" accept=".xlsx,.xls" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5">
                    <p class="text-xs text-gray-500 mt-1">Format: .xlsx atau .xls (Maksimal 2MB)</p>
                    <p class="text-xs text-gray-500 mt-1">Pastikan file Excel berisi kolom: TAHUN_AJARAN, KODE_MATKUL, MATA_KULIAH, SEMESTER, NAMA_KELAS, NAMA_DOSEN, NIP_DOSEN, EMAIL_DOSEN</p>
                    <p class="text-xs text-gray-500 mt-1"><strong>Format:</strong> Satu baris = satu kelas. Jika mata kuliah sama, semester sama, buat baris terpisah per kelas.</p>
                    <p class="text-xs text-gray-500 mt-1"><strong>Multiple Dosen:</strong> Gunakan titik koma (;) untuk memisahkan multiple dosen dalam satu baris.</p>
                    <p class="text-xs text-gray-500 mt-1">Contoh: Dr. John Doe; Dr. Jane Smith | NIP1; NIP2 | email1; email2</p>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeImportModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                        Batal
                    </button>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Duplicate Tahun Sebelumnya -->
<x-duplicate-modal id="duplicate-modal" title="Duplicate dari Tahun Sebelumnya" action="{{ route('admin.tahun-ajaran-matkul.duplicate') }}" submit-text="Duplicate">
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran Sumber</label>
        <select name="source_tahun_ajaran_id" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5">
            <option value="">Pilih Tahun Ajaran Sumber</option>
            @foreach($tahunAjarans as $tahunAjaran)
                <option value="{{ $tahunAjaran->id }}">{{ $tahunAjaran->tahun }}-{{ $tahunAjaran->periode }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran Target</label>
        <select name="target_tahun_ajaran_id" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5">
            <option value="">Pilih Tahun Ajaran Target</option>
            @foreach($tahunAjarans as $tahunAjaran)
                <option value="{{ $tahunAjaran->id }}">{{ $tahunAjaran->tahun }}-{{ $tahunAjaran->periode }}</option>
            @endforeach
        </select>
    </div>
</x-duplicate-modal>

<script>
function openImportModal() {
    document.getElementById('import-modal').classList.remove('hidden');
}

function closeImportModal() {
    document.getElementById('import-modal').classList.add('hidden');
}

function openDuplicateModal() {
    const modal = document.getElementById('duplicate-modal');
    const modalContent = modal.querySelector('[data-modal-content]');

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    // Trigger animation
    setTimeout(() => {
        modal.classList.remove('bg-opacity-0');
        modal.classList.add('bg-opacity-10');
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeDuplicateModal() {
    const modal = document.getElementById('duplicate-modal');
    const modalContent = modal.querySelector('[data-modal-content]');

    modalContent.classList.add('scale-95', 'opacity-0');
    modalContent.classList.remove('scale-100', 'opacity-100');
    modal.classList.remove('bg-opacity-10');
    modal.classList.add('bg-opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 300);
}

// Close modal when clicking outside
window.onclick = function(event) {
    const importModal = document.getElementById('import-modal');
    const duplicateModal = document.getElementById('duplicate-modal');

    if (event.target === importModal) {
        closeImportModal();
    }
    if (event.target === duplicateModal) {
        closeDuplicateModal();
    }
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form if tahun ajaran is auto-selected but not in URL
    // Only do this if we're on the first page and no filters are applied
    const tahunAjaranSelect = document.querySelector('select[name="tahun_ajaran_id"]');
    const urlParams = new URLSearchParams(window.location.search);
    const hasTahunAjaranInUrl = urlParams.has('tahun_ajaran_id');
    const hasPageParam = urlParams.has('page');
    const hasSearchParam = urlParams.has('search');

    // Only auto-submit if:
    // 1. We have a tahun ajaran selected
    // 2. It's not in the URL
    // 3. We're on the first page (no page parameter)
    // 4. No search is active
    if (tahunAjaranSelect && tahunAjaranSelect.value && !hasTahunAjaranInUrl && !hasPageParam && !hasSearchParam) {
        // Auto-submit the form to update URL with the selected tahun ajaran
        tahunAjaranSelect.form.submit();
    }
});
</script>

@endsection


