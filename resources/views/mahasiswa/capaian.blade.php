@extends('layouts.main')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Capaian CPL & CPMK</h2>
                <p class="text-gray-600 mt-1 text-sm">Halaman ini menampilkan capaian pembelajaran Anda berdasarkan CPL dan CPMK yang mendukungnya.</p>
            </div>
            <div class="flex items-center gap-2">
                <form method="GET" action="" class="flex items-center gap-2 w-full md:w-auto">
                    <label for="cpl_id" class="text-sm font-medium text-gray-700 mr-2">Pilih CPL</label>
                    <select name="cpl_id" id="cpl_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-48 p-2.5" onchange="this.form.submit()">
                        <option value="">Semua CPL</option>
                        @foreach($cplList as $cpl)
                            <option value="{{ $cpl->id }}" {{ $cplIdTerpilih == $cpl->id ? 'selected' : '' }}>{{ $cpl->kodeCpl }}</option>
                        @endforeach
                    </select>
                </form>

                <!-- Tombol Export PDF -->
                <form method="GET" action="{{ route('mahasiswa.capaian.export-pdf') }}" class="ml-2">
                    @if($cplIdTerpilih)
                        <input type="hidden" name="cpl_id" value="{{ $cplIdTerpilih }}">
                    @endif
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export PDF
                    </button>
                </form>
            </div>
        </div>

        @if(count($cplData) > 0)
            @foreach($cplData as $index => $cpl)
            <div class="mb-6 border border-gray-200 rounded-lg overflow-hidden">
                <!-- CPL Header with Toggle Button -->
                <div class="bg-gray-50 border-b border-gray-200 p-4 cursor-pointer hover:bg-gray-100 transition-colors duration-200"
                     onclick="toggleCplTable({{ $index }})">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0">
                                <svg id="icon-{{ $index }}" class="w-5 h-5 text-gray-600 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">{{ $cpl['kode'] }}</h3>
                                <p class="text-gray-600 mt-1 text-sm">{{ $cpl['deskripsi'] }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-600 font-medium w-44">Total Capaian:</div>
                            @if($cpl['status_cpl'] === 'Tercapai')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    {{ $cpl['total_cpl'] }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    {{ $cpl['missing_cpmk_count'] ?? 0 }} CPMK belum bernilai
                                </span>
                            @endif
                            <div class="mt-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $cpl['status_cpl'] === 'Tercapai' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $cpl['status_cpl'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Collapsible Table Content -->
                <div id="table-{{ $index }}" class="hidden bg-white">
                    <div class="overflow-x-auto">
                        <table class="w-full table-fixed divide-y divide-gray-200">
                            <colgroup>
                                <col class="w-28"> <!-- Kode Mata Kuliah - Reduced width -->
                                <col class="w-48"> <!-- Nama Mata Kuliah - Reduced width -->
                                <col class="w-32"> <!-- Kode CPMK - Fixed width -->
                                <col class="w-auto"> <!-- Deskripsi CPMK - Auto width for more space -->
                                <col class="w-24"> <!-- Nilai - Fixed width -->
                            </colgroup>
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode Mata Kuliah</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Mata Kuliah</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode CPMK</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Deskripsi CPMK</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Nilai</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php
                                    $grouped = collect($cpl['cpmk'])->groupBy(function($item) {
                                        return $item['kode_mk'].'|'.$item['nama_mk'];
                                    });
                                @endphp
                                @foreach($grouped as $mkKey => $cpmkList)
                                    @php
                                        [$kode_mk, $nama_mk] = explode('|', $mkKey);
                                        $rowspan = count($cpmkList);
                                        $printedMk = false;
                                    @endphp
                                    @foreach($cpmkList as $idx => $cpmk)
                                    <tr class="hover:bg-gray-50">
                                        @if(!$printedMk)
                                            <td class="px-4 py-3 font-medium text-gray-900" rowspan="{{ $rowspan }}">{{ $kode_mk }}</td>
                                            <td class="px-4 py-3 text-gray-700" rowspan="{{ $rowspan }}">{{ $nama_mk }}</td>
                                            @php $printedMk = true; @endphp
                                        @endif
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $cpmk['kode'] }}</td>
                                        <td class="px-4 py-3 text-gray-700">{{ $cpmk['deskripsi'] }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ is_numeric($cpmk['nilai']) && $cpmk['nilai'] >= 55 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $cpmk['nilai'] }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
        @else
        <div class="p-6 text-center text-gray-500">Tidak ada data CPL yang dipilih.</div>
        @endif
    </div>
</div>

<script>
function toggleCplTable(index) {
    const table = document.getElementById(`table-${index}`);
    const icon = document.getElementById(`icon-${index}`);

    if (table.classList.contains('hidden')) {
        // Show table
        table.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        // Hide table
        table.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}

// Auto-expand first CPL table on page load
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('table-0')) {
        toggleCplTable(0);
    }
});
</script>
@endsection
