@extends('layouts.main')

@section('title', 'Data Mahasiswa')

@section('content')
<div class="p-4 md:p-6 space-y-4">

    <div class="bg-white border border-gray-200 rounded-xl">
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <h2 class="text-lg font-semibold text-gray-900">Daftar Mahasiswa</h2>
                <div class="flex flex-col sm:flex-row gap-2">
                    <a href="{{ route('admin.mahasiswa.create') }}" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg flex items-center w-fit">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Mahasiswa
                    </a>
                    <button type="button" id="btn-bulk-delete" data-modal-target="modal-bulk-delete" data-modal-toggle="modal-bulk-delete" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center hidden w-fit">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus Terpilih
                    </button>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <form method="GET" action="{{ route('admin.mahasiswa.index') }}" class="mt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div class="lg:col-span-2">
                        <div class="flex gap-2">
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Cari nama, NIM, atau email..."
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                            @if(request('search') || request('tahun_masuk') || request('status'))
                                <a href="{{ route('admin.mahasiswa.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Filter Tahun Masuk -->
                    <div>
                        <select name="tahun_masuk" onchange="this.form.submit()"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <option value="" {{ (request()->has('tahun_masuk') && request('tahun_masuk') === '') ? 'selected' : '' }}>Semua Tahun Masuk</option>
                            @foreach($tahunMasukList as $tahun)
                                <option value="{{ $tahun }}" {{ (request()->has('tahun_masuk') ? request('tahun_masuk') : null) == $tahun ? 'selected' : '' }}>
                                    {{ $tahun }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Status -->
                    <div>
                        <select name="status" onchange="this.form.submit()"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                </div>
            </form>

            <!-- Results Info -->
            @if(request('search') || request('tahun_masuk') || request('status'))
                <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                    <p class="text-sm text-gray-700">
                        Menampilkan {{ $mahasiswa->total() }} hasil
                        @if(request('search'))
                            untuk pencarian "{{ request('search') }}"
                        @endif
                        @if(request('tahun_masuk'))
                            dengan tahun masuk {{ request('tahun_masuk') }}
                        @endif
                        @if(request('status'))
                            dengan status {{ request('status') }}
                        @endif
                    </p>
                </div>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 w-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAll" class="w-4 h-4 text-amber-600 bg-gray-100 border-gray-300 rounded focus:ring-amber-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun Masuk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($mahasiswa as $mhs)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" value="{{ $mhs->id }}" class="mahasiswa-checkbox w-4 h-4 text-amber-600 bg-gray-100 border-gray-300 rounded focus:ring-amber-500">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $loop->iteration + ($mahasiswa->currentPage() - 1) * $mahasiswa->perPage() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mhs->nim }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mhs->nama }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mhs->user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mhs->tahunMasuk }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($mhs->user->isAktif)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-700">Aktif</span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-700">Tidak Aktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.mahasiswa.show', $mhs->id) }}"
                                   class="inline-flex items-center justify-center bg-gray-600 hover:bg-gray-700 text-white p-1.5 rounded-md" title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.mahasiswa.edit', $mhs->id) }}"
                                   class="inline-flex items-center justify-center bg-amber-600 hover:bg-amber-700 text-white p-1.5 rounded-md" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <button type="button" data-modal-target="modal-confirm-hapus-{{ $mhs->id }}" data-modal-toggle="modal-confirm-hapus-{{ $mhs->id }}"
                                        class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white p-1.5 rounded-md" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data mahasiswa</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($mahasiswa->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Menampilkan {{ $mahasiswa->firstItem() ?? 0 }} sampai {{ $mahasiswa->lastItem() ?? 0 }} dari {{ $mahasiswa->total() }} data
                    </div>
                    <div class="flex space-x-2">
                        {{ $mahasiswa->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
@foreach($mahasiswa as $mhs)
    <x-confirm-modal
        :id="'modal-confirm-hapus-' . $mhs->id"
        title="Konfirmasi Hapus Mahasiswa"
        :message="'Apakah Anda yakin ingin menghapus mahasiswa ' . $mhs->nama . ' (' . $mhs->nim . ')?'"
        :action="route('admin.mahasiswa.destroy', $mhs->id)"
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
                <h3 class="mb-5 text-lg font-normal text-gray-500">Apakah Anda yakin ingin menghapus data mahasiswa yang dipilih?</h3>
                <form id="form-bulk-delete" action="{{ route('admin.mahasiswa.bulk-destroy') ?? '#' }}" method="POST">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const mahasiswaCheckboxes = document.querySelectorAll('.mahasiswa-checkbox');
    const btnBulkDelete = document.getElementById('btn-bulk-delete');
    const bulkDeleteInputs = document.getElementById('bulk-delete-inputs');

    function updateBulkDeleteButton() {
        const checkedCount = document.querySelectorAll('.mahasiswa-checkbox:checked').length;
        if (checkedCount > 0) {
            btnBulkDelete.classList.remove('hidden');
        } else {
            btnBulkDelete.classList.add('hidden');
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            mahasiswaCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkDeleteButton();
        });
    }

    mahasiswaCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = document.querySelectorAll('.mahasiswa-checkbox:checked').length === mahasiswaCheckboxes.length;
            selectAllCheckbox.checked = allChecked;
            updateBulkDeleteButton();
        });
    });

    const formBulkDelete = document.getElementById('form-bulk-delete');
    if (formBulkDelete) {
        formBulkDelete.addEventListener('submit', function(e) {
            bulkDeleteInputs.innerHTML = '';
            document.querySelectorAll('.mahasiswa-checkbox:checked').forEach(checkbox => {
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
