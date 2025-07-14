@extends('layouts.main')

@section('title', 'Bobot Komponen Penilaian - ' . $tahunAjaranMatkul->mataKuliah->namaMatkul)

@section('content')
<div class="p-6">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li>
                <a href="{{ route('dosen.mata-kuliah.index') }}" class="hover:text-gray-700">Mata Kuliah</a>
            </li>
            <li>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </li>
            <li>
                <a href="{{ route('dosen.mata-kuliah.show', $tahunAjaranMatkul->id) }}" class="hover:text-gray-700">{{ $tahunAjaranMatkul->mataKuliah->namaMatkul }}</a>
            </li>
            <li>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </li>
            <li class="text-gray-900 font-medium">Bobot Komponen</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-4 sm:p-6 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl font-bold text-gray-900">Bobot Komponen Penilaian</h1>
                    <p class="text-gray-600 mt-1">{{ $tahunAjaranMatkul->mataKuliah->namaMatkul }} • {{ $tahunAjaranMatkul->mataKuliah->kodeMatkul }}</p>
                    <p class="text-gray-500 text-sm mt-1">Kelas {{ $tahunAjaranMatkul->kelasHuruf }} • {{ $tahunAjaranMatkul->tahunAjaran->tahunAjaran }}</p>
                </div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3">
                    <a href="{{ route('dosen.bobot-komponen.bulk-create', $tahunAjaranMatkul->id) }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center w-full sm:w-auto justify-center">
                        <svg class="w-4 h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <span class="hidden sm:inline">Atur Bobot Komponen</span><span class="sm:hidden">Atur Bobot</span>
                    </a>
                </div>
            </div>
        </div>

        @if($totalBobot > 0)
        <div class="p-4 sm:p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Total Bobot: {{ number_format($totalBobot, 1) }}%</h3>
                @if($totalBobot != 100)
                    <div class="flex items-center px-3 py-1 rounded-full text-sm {{ $totalBobot > 100 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $totalBobot > 100 ? 'Melebihi 100%' : 'Kurang dari 100%' }}
                    </div>
                @else
                    <div class="flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Sesuai
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    @if(session('success'))
        <div id="success-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div id="error-alert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if(session('info'))
        <div id="info-alert" class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
            {{ session('info') }}
        </div>
    @endif

    <!-- CPMK Info Section -->
    @if($cpmkList->isNotEmpty())
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="p-4 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">CPMK yang Tersedia</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kode CPMK
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Deskripsi
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Bobot (%)
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $totalAllCpmk = 0;
                            @endphp
                            @foreach($cpmkList as $cpmk)
                                @php
                                    $totalBobotCpmk = $bobotKomponen->where('cpmkId', $cpmk->id)->sum('bobot');
                                    $totalAllCpmk += $totalBobotCpmk;
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $cpmk->kodeCpmk }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $cpmk->deskripsi }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        <div class="flex justify-center items-center space-x-2">
                                            @if($totalBobotCpmk > 0)
                                                <!-- Show Detail Button -->
                                                <button onclick="showCpmkDetail({{ $cpmk->id }}, '{{ $cpmk->kodeCpmk }}')"
                                                        class="text-indigo-600 hover:text-indigo-900"
                                                        title="Lihat Detail Bobot CPMK">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </button>
                                            @else
                                                <span class="text-gray-400 text-sm">Belum ada bobot</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ number_format($totalBobotCpmk, 1) }}%
                                        </div>
                                        @if($totalBobotCpmk > 0)
                                            <div class="text-xs text-gray-500">
                                                {{ $bobotKomponen->where('cpmkId', $cpmk->id)->count() }} komponen
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100">
                            <tr>
                                <td colspan="3" class="px-6 py-3 text-sm font-medium text-gray-900">
                                    Total Keseluruhan CPMK
                                </td>
                                <td class="px-6 py-3 text-right text-sm font-bold text-gray-900">
                                    {{ number_format($totalAllCpmk, 1) }}%
                                    @if($totalAllCpmk != 100)
                                        <div class="text-xs {{ $totalAllCpmk > 100 ? 'text-red-600' : 'text-yellow-600' }}">
                                            {{ $totalAllCpmk > 100 ? 'Melebihi target' : 'Belum mencapai 100%' }}
                                        </div>
                                    @else
                                        <div class="text-xs text-green-600">
                                            Sesuai target
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Perhatian!</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>Belum ada CPMK yang ditetapkan untuk mata kuliah ini. Silakan tambahkan CPMK terlebih dahulu sebelum mengatur bobot komponen.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Content -->
    <div class="bg-white rounded-lg shadow-md">
        @if($bobotKomponen->isEmpty())
            <div class="p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Bobot Komponen</h3>
                <p class="text-gray-500 mb-4">Mulai dengan mengatur bobot untuk komponen penilaian berdasarkan CPMK.</p>
                @if($cpmkList->isNotEmpty())
                    <a href="{{ route('dosen.bobot-komponen.bulk-create', $tahunAjaranMatkul->id) }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Atur Bobot Komponen
                    </a>
                @else
                    <p class="text-red-500 text-sm">Tidak dapat mengatur bobot karena belum ada CPMK yang ditetapkan.</p>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                CPMK
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Komponen
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Bobot (%)
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($bobotKomponen as $bobot)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $bobot->cpmk->kodeCpmk }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ Str::limit($bobot->cpmk->deskripsi, 50) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $bobot->komponen->nama }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-medium">
                                        {{ number_format($bobot->bobot, 1) }}%
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($bobot->nilai->count() > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Sudah Ada Nilai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Belum Ada Nilai
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('dosen.bobot-komponen.show', [$tahunAjaranMatkul->id, $bobot->id]) }}"
                                           class="text-indigo-600 hover:text-indigo-900"
                                           title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        @if($bobot->nilai->count() == 0)
                                            <form action="{{ route('dosen.bobot-komponen.destroy', [$tahunAjaranMatkul->id, $bobot->id]) }}"
                                                  method="POST"
                                                  class="inline"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus bobot komponen ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-red-600 hover:text-red-900"
                                                        title="Hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400 cursor-not-allowed" title="Tidak dapat dihapus karena sudah ada nilai">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Modal Detail CPMK -->
<div id="cpmkDetailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Detail Bobot CPMK</h3>
                <button onclick="closeCpmkModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="modalContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto hide alerts after 5 seconds
    const alerts = document.querySelectorAll('#success-alert, #error-alert, #info-alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});

// Function to show CPMK detail
function showCpmkDetail(cpmkId, kodeCpmk) {
    const modal = document.getElementById('cpmkDetailModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalContent = document.getElementById('modalContent');

    modalTitle.textContent = `Detail Bobot - ${kodeCpmk}`;
    modalContent.innerHTML = '<div class="text-center py-4">Loading...</div>';

    // Get bobot data for this CPMK
    const bobotData = @json($bobotKomponen);
    const cpmkBobot = bobotData.filter(bobot => bobot.cpmk_id == cpmkId);

    if (cpmkBobot.length > 0) {
        let totalBobot = 0;
        let tableRows = '';

        cpmkBobot.forEach(bobot => {
            totalBobot += parseFloat(bobot.bobot);
            tableRows += `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 text-sm text-gray-900">${bobot.komponen.nama}</td>
                    <td class="px-4 py-2 text-sm text-gray-900 text-right">${parseFloat(bobot.bobot).toFixed(1)}%</td>
                    <td class="px-4 py-2 text-sm text-center">
                        ${bobot.nilai.length > 0 ?
                            '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">Ada Nilai</span>' :
                            '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800">Belum Ada</span>'
                        }
                    </td>
                </tr>
            `;
        });

        modalContent.innerHTML = `
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Komponen</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Bobot</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        ${tableRows}
                    </tbody>
                    <tfoot class="bg-gray-100">
                        <tr>
                            <td class="px-4 py-2 text-sm font-medium text-gray-900">Total</td>
                            <td class="px-4 py-2 text-sm font-bold text-gray-900 text-right">${totalBobot.toFixed(1)}%</td>
                            <td class="px-4 py-2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        `;
    } else {
        modalContent.innerHTML = '<div class="text-center py-8 text-gray-500">Belum ada bobot yang ditetapkan untuk CPMK ini.</div>';
    }

    modal.classList.remove('hidden');
}

// Function to close modal
function closeCpmkModal() {
    const modal = document.getElementById('cpmkDetailModal');
    modal.classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('cpmkDetailModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCpmkModal();
    }
});
</script>
@endsection
