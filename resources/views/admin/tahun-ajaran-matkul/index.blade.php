@extends('layouts.main')

@section('title', 'Tahun Ajaran Mata Kuliah')

@section('content')
<div class="p-4 md:p-6 space-y-4">
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900 tracking-tight">Tahun Ajaran Mata Kuliah</h1>
                    <p class="text-sm text-gray-500 mt-1">Kelola penawaran mata kuliah per tahun ajaran.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" data-modal-target="import-modal" data-modal-toggle="import-modal" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md flex items-center text-sm">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        Import Excel
                    </button>
                    <button type="button" data-modal-target="download-modal" data-modal-toggle="download-modal" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-md flex items-center text-sm">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download Template
                    </button>
                    <button type="button" data-modal-target="duplicate-modal" data-modal-toggle="duplicate-modal" class="bg-amber-700 hover:bg-amber-800 text-white px-3 py-2 rounded-md flex items-center text-sm">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Duplicate Tahun Sebelumnya
                    </button>
                    <button type="button" id="btn-bulk-delete" data-modal-target="modal-bulk-delete" data-modal-toggle="modal-bulk-delete" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md flex items-center text-sm hidden">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus Terpilih
                    </button>
                    <a href="{{ route('admin.tahun-ajaran-matkul.create') }}"
                        class="bg-amber-600 hover:bg-amber-700 text-white px-3 py-2 rounded-md flex items-center text-sm">
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
        <div class="px-5 py-4 border-b border-red-200 bg-red-50">
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
        <div class="px-5 py-4 border-b border-gray-200">
            <form method="GET" action="{{ route('admin.tahun-ajaran-matkul.index') }}">
                <div class="flex flex-col md:flex-row gap-3 items-end">
                    <div class="flex-shrink-0 w-full md:w-48">
                        <label class="block text-[11px] font-medium text-gray-500 mb-1">Filter Tahun Ajaran</label>
                        <select name="tahun_ajaran_id" class="border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-amber-500 focus:border-amber-500 block w-full p-2" onchange="this.form.submit()">
                            <option value="">Semua Tahun Ajaran</option>
                            @foreach($tahunAjarans as $tahunAjaran)
                            <option value="{{ $tahunAjaran->id }}" {{ $selectedTahunAjaranId == $tahunAjaran->id ? 'selected' : '' }}>
                                {{ $tahunAjaran->tahun }} - {{ $tahunAjaran->periode }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1 w-full">
                        <label class="block text-[11px] font-medium text-gray-500 mb-1">Cari Mata Kuliah</label>
                        <div class="relative">
                            <input type="text" id="search-input" name="search" autocomplete="off" class="border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-amber-500 focus:border-amber-500 block w-full p-2" placeholder="Cari nama atau kode mata kuliah..." value="{{ request('search') }}">
                            <svg id="search-loading" class="hidden absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 animate-spin text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-shrink-0 flex gap-2">
                        <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-3 py-2 rounded-md flex items-center text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Cari
                        </button>
                        <a href="{{ route('admin.tahun-ajaran-matkul.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-md flex items-center text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Reset Semua
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Progress Bar (Hidden by default) -->
        <div id="import-progress-container" class="hidden px-5 py-4 border-b border-gray-200 bg-amber-50">
            <div class="flex justify-between mb-1">
                <span class="text-sm font-medium text-amber-800">Memproses Data Excel...</span>
                <span id="import-progress-text" class="text-sm font-medium text-amber-800">0%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div id="import-progress-bar" class="bg-amber-600 h-2.5 rounded-full transition-all duration-500 ease-out" style="width: 0%"></div>
            </div>
            <p id="import-progress-detail" class="text-xs text-amber-700 mt-2">Menginisialisasi import...</p>
        </div>

        <div id="table-container">
            @include('admin.tahun-ajaran-matkul._table')
        </div>
    </div>
</div>

<!-- Modal Import Excel -->
<x-form-modal id="import-modal" title="Import Data Mata Kuliah" :action="route('admin.tahun-ajaran-matkul.import')" submit-text="Import" enctype="multipart/form-data">
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Upload File Excel</label>
        <input type="file" name="file" accept=".xlsx,.xls" required class="border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-amber-500 focus:border-amber-500 block w-full p-2">
        <div class="mt-4 p-3 bg-amber-50 rounded-md border border-amber-200">
            <h4 class="text-xs font-semibold text-amber-800 mb-1">Panduan Format File:</h4>
            <ul class="text-[10px] text-amber-700 space-y-1 list-disc list-inside">
                <li>Format: .xlsx atau .xls (Maksimal 2MB)</li>
                <li>Gunakan header: TAHUN_AJARAN, KODE_MATKUL, MATA_KULIAH, SEMESTER, NAMA_KELAS, NAMA_DOSEN, NIP_DOSEN, EMAIL_DOSEN</li>
                <li>Format Tahun: <b>2025/2026 - ganjil</b></li>
                <li>Multiple Dosen: Gunakan titik koma (;) untuk memisahkan.</li>
            </ul>
        </div>
    </div>
</x-form-modal>

<!-- Modal Duplicate Tahun Sebelumnya -->
<x-duplicate-modal id="duplicate-modal" title="Duplicate dari Tahun Sebelumnya" action="{{ route('admin.tahun-ajaran-matkul.duplicate') }}" submit-text="Duplicate">
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran Sumber</label>
        <select name="source_tahun_ajaran_id" required class="border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-amber-500 focus:border-amber-500 block w-full p-2">
            <option value="">Pilih Tahun Ajaran Sumber</option>
            @foreach($tahunAjarans as $tahunAjaran)
            <option value="{{ $tahunAjaran->id }}">{{ $tahunAjaran->tahun }}-{{ $tahunAjaran->periode }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran Target</label>
        <select name="target_tahun_ajaran_id" required class="border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-amber-500 focus:border-amber-500 block w-full p-2">
            <option value="">Pilih Tahun Ajaran Target</option>
            @foreach($tahunAjarans as $tahunAjaran)
            <option value="{{ $tahunAjaran->id }}">{{ $tahunAjaran->tahun }}-{{ $tahunAjaran->periode }}</option>
            @endforeach
        </select>
    </div>
</x-duplicate-modal>
 
<!-- Modal Download Template -->
<x-form-modal id="download-modal" title="Download Template" :action="route('admin.tahun-ajaran-matkul.export-template')" method="GET" submit-text="Download">
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Tahun Ajaran</label>
        <select name="tahun_ajaran_id" required class="border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-amber-500 focus:border-amber-500 block w-full p-2">
            <option value="">Pilih Tahun Ajaran</option>
            @foreach($tahunAjarans as $tahunAjaran)
            <option value="{{ $tahunAjaran->id }}" {{ $selectedTahunAjaranId == $tahunAjaran->id ? 'selected' : '' }}>
                {{ $tahunAjaran->tahun }} - {{ $tahunAjaran->periode }}
            </option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 mt-2 italic">Kolom TAHUN_AJARAN di template akan terisi otomatis sesuai pilihan Anda.</p>
    </div>
</x-form-modal>

<!-- Modal Bulk Delete -->
<div id="modal-bulk-delete" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full" style="background: rgba(0,0,0,0.6);">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-xl border border-gray-200">
            <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="modal-bulk-delete">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
                <span class="sr-only">Tutup modal</span>
            </button>
            <div class="p-4 md:p-5 text-center">
                <svg class="mx-auto mb-4 text-gray-400 w-12 h-12" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <h3 class="mb-5 text-lg font-normal text-gray-500">Apakah Anda yakin ingin menghapus data mata kuliah terpilih?</h3>
                <form id="form-bulk-delete" action="{{ route('admin.tahun-ajaran-matkul.bulk-destroy') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div id="bulk-delete-inputs"></div>
                    <button type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:outline-none font-medium rounded-md text-sm inline-flex items-center justify-center px-5 py-2.5 text-center">
                        <svg data-spinner class="hidden w-4 h-4 mr-2 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span data-submit-text>Ya, hapus</span>
                    </button>
                    <button data-modal-hide="modal-bulk-delete" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-white focus:outline-none bg-gray-600 rounded-md hover:bg-gray-700">Batal</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function initTableInteractions() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const btnBulkDelete = document.getElementById('btn-bulk-delete');

    function updateBulkDeleteButton() {
        const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
        if (checkedCount > 0) {
            btnBulkDelete.classList.remove('hidden');
        } else {
            btnBulkDelete.classList.add('hidden');
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkDeleteButton();
        });
    }

    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = document.querySelectorAll('.item-checkbox:checked').length === itemCheckboxes.length;
            if(selectAllCheckbox) selectAllCheckbox.checked = allChecked;
            updateBulkDeleteButton();
        });
    });

    updateBulkDeleteButton();

    if (window.initFlowbite) {
        window.initFlowbite();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    initTableInteractions();

    const bulkDeleteInputs = document.getElementById('bulk-delete-inputs');
    const formBulkDelete = document.getElementById('form-bulk-delete');
    if (formBulkDelete) {
        formBulkDelete.addEventListener('submit', function(e) {
            bulkDeleteInputs.innerHTML = '';
            document.querySelectorAll('.item-checkbox:checked').forEach(checkbox => {
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const searchLoading = document.getElementById('search-loading');
    const tableContainer = document.getElementById('table-container');
    const tahunAjaranSelect = document.querySelector('select[name="tahun_ajaran_id"]');
    if (!searchInput || !tableContainer) return;

    let debounceTimer = null;
    let currentController = null;

    function buildUrl(page) {
        const url = new URL(window.location.href);
        url.searchParams.set('search', searchInput.value.trim());
        if (tahunAjaranSelect && tahunAjaranSelect.value) {
            url.searchParams.set('tahun_ajaran_id', tahunAjaranSelect.value);
        }
        if (page) {
            url.searchParams.set('page', page);
        } else {
            url.searchParams.delete('page');
        }
        return url;
    }

    function fetchResults(url, pushState) {
        if (currentController) currentController.abort();
        currentController = new AbortController();

        if (searchLoading) searchLoading.classList.remove('hidden');

        fetch(url.toString(), {
            credentials: 'same-origin',
            signal: currentController.signal,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(res => res.text())
        .then(html => {
            tableContainer.innerHTML = html;
            initTableInteractions();
            if (pushState) {
                window.history.replaceState({}, '', url.toString());
            }
        })
        .catch(err => {
            if (err.name !== 'AbortError') {
                console.error('Search error:', err);
            }
        })
        .finally(() => {
            if (searchLoading) searchLoading.classList.add('hidden');
        });
    }

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            fetchResults(buildUrl(null), true);
        }, 400);
    });

    // Intercept pagination link clicks inside the table container for live search too
    tableContainer.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (!link) return;
        const href = link.getAttribute('href');
        if (!href) return;
        // Only intercept pagination links (they point to the same route)
        if (link.closest('nav') || link.closest('[aria-label="Pagination"]') || link.classList.contains('page-link')) {
            e.preventDefault();
            fetchResults(new URL(href, window.location.href), true);
        }
    });
});
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
        // 5. No flash messages are present (to avoid cutting off notifications)
        const hasFlashMessages = {{ session('success') || session('error') || $errors->any() ? 'true' : 'false' }};

        if (tahunAjaranSelect && tahunAjaranSelect.value && !hasTahunAjaranInUrl && !hasPageParam && !hasSearchParam && !hasFlashMessages) {
            // Auto-submit the form to update URL with the selected tahun ajaran
            tahunAjaranSelect.form.submit();
        }
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const importModal = document.getElementById('import-modal');
    if (!importModal) return;
    
    const importForm = importModal.querySelector('form');
    const progressContainer = document.getElementById('import-progress-container');
    const progressBar = document.getElementById('import-progress-bar');
    const progressText = document.getElementById('import-progress-text');
    const progressDetail = document.getElementById('import-progress-detail');

    
    if (importForm) {
        importForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');

            
            if(submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Mengunggah...';
            }
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                
                if (response.redirected) {
                    console.error('[Import] Request was REDIRECTED to:', response.url);
                }

                const contentType = response.headers.get('content-type');
                
                if (!contentType || !contentType.includes('application/json')) {
                    return response.text().then(text => {
                        throw new Error('Server mengembalikan HTML, bukan JSON. Kemungkinan redirect ke login.');
                    });
                }
                
                return response.json();
            })
            .then(data => {
                
                // Close modal
                const closeBtn = importModal.querySelector('[data-modal-hide="import-modal"]');
                if(closeBtn) closeBtn.click();
                
                if (data.status === 'success' && data.job_id) {
                    progressContainer.classList.remove('hidden');
                    pollImportStatus(data.job_id);
                } else {
                    alert(data.message || 'Terjadi kesalahan saat memulai import.');
                    if(submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Import';
                    }
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
                if(submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Import';
                }
            });
        });
    }
    
    function pollImportStatus(jobId) {
        let pollCount = 0;
        const statusUrl = `{{ route('admin.tahun-ajaran-matkul.import-status') }}?job_id=${jobId}`;
        
        const interval = setInterval(() => {
            pollCount++;
            
            fetch(statusUrl, {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => {
                
                const contentType = res.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    return res.text().then(text => {
                        throw new Error('Polling response bukan JSON');
                    });
                }
                
                return res.json();
            })
            .then(data => {
                
                if (data.error) {
                    clearInterval(interval);
                    progressDetail.innerText = 'Error: ' + data.error;
                    return;
                }
                
                const pct = data.percentage || 0;
                progressBar.style.width = `${pct}%`;
                progressText.innerText = `${pct}%`;
                
                if (data.total > 0) {
                    progressDetail.innerText = `Memproses ${data.processed} dari ${data.total} baris...`;
                }
                
                if (data.finished) {
                    clearInterval(interval);
                    progressBar.classList.replace('bg-amber-600', 'bg-green-600');
                    progressText.classList.replace('text-amber-800', 'text-green-700');
                    progressDetail.classList.replace('text-amber-700', 'text-green-600');
                    progressDetail.innerText = "Selesai! Memuat ulang halaman...";
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
                
                // Safety: stop polling after 200 attempts (5 minutes)
                if (pollCount >= 200) {
                    clearInterval(interval);
                    progressDetail.innerText = 'Polling dihentikan. Silakan reload halaman manual.';
                }
            })
            .catch(err => {
                // Don't stop polling on error, but show it
                progressDetail.innerText = 'Error polling: ' + err.message;
            });
        }, 1500);
    }
});
</script>

@endsection
