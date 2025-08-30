@extends('layouts.main')

@section('title', 'Kelola Nilai Mahasiswa')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Kelola Nilai Mahasiswa</h2>
                    <p class="text-gray-600 mt-1">Kelola dan input nilai mahasiswa untuk mata kuliah yang diampu</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-sm text-gray-500">
                        Total: <span id="total-count">{{ $mataKuliahDiampu->count() }}</span> mata kuliah
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter dan Search Form -->
        <div class="p-6 border-b border-gray-200">
            <form method="GET" action="{{ route('dosen.nilai.index') }}">
                <div class="flex flex-col lg:flex-row gap-4 lg:items-center lg:justify-between">
                    <!-- Filter dropdowns -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="w-full sm:w-48">
                            <select name="tahun_ajaran_id" id="filter-tahun-ajaran" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" onchange="this.form.submit()">
                                <option value="">Semua Tahun Ajaran</option>
                                @foreach($tahunAjaranList as $ta)
                                    <option value="{{ $ta->id }}" {{ $selectedTahunAjaranId == $ta->id ? 'selected' : '' }}>
                                        {{ $ta->tahun }} - {{ $ta->periode }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full sm:w-32">
                            <select name="jenis" id="filter-jenis" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" onchange="this.form.submit()">
                                <option value="">Semua Jenis</option>
                                <option value="wajib" {{ request('jenis') == 'wajib' ? 'selected' : '' }}>Wajib</option>
                                <option value="pilihan" {{ request('jenis') == 'pilihan' ? 'selected' : '' }}>Pilihan</option>
                            </select>
                        </div>
                    </div>

                    <!-- Search dan buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 lg:flex-1">
                        <div class="flex-1">
                            <input type="text" name="search" id="search-input" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" placeholder="Cari nama atau kode mata kuliah..." value="{{ request('search') }}">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg flex items-center justify-center whitespace-nowrap">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Cari
                            </button>
                            <a href="{{ route('dosen.nilai.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2.5 rounded-lg flex items-center justify-center whitespace-nowrap">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="p-6">
            <!-- No Results Message (hidden by default) -->
            <div id="no-results" class="text-center py-12 hidden">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Hasil</h3>
                <p class="text-gray-500">Tidak ada mata kuliah yang sesuai dengan pencarian atau filter yang dipilih.</p>
            </div>

            @if ($mataKuliahDiampu->isEmpty())
                <div id="empty-state" class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Mata Kuliah</h3>
                    <p class="text-gray-500">Anda belum ditugaskan untuk mengampu mata kuliah apapun.</p>
                </div>
            @else
                <!-- Table View -->
                <div id="mata-kuliah-table" class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                    Kode
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                    Mata Kuliah
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                    SKS
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                    Jenis
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                    Kelas
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                    Tahun Ajaran
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                    Mahasiswa
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                    Dosen Pengampu
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                    Last Modified
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($mataKuliahDiampu as $mataKuliah)
                                <tr class="data-item hover:bg-gray-50 transition-colors duration-150"
                                    data-nama="{{ strtolower($mataKuliah->mataKuliah->namaMatkul ?? '') }}"
                                    data-kode="{{ strtolower($mataKuliah->mataKuliah->kodeMatkul ?? '') }}"
                                    data-tahun-ajaran="{{ $mataKuliah->tahunAjaran->id ?? '' }}"
                                    data-tahun-ajaran-display="{{ $mataKuliah->tahunAjaran->tahun ?? '' }}"
                                    data-tahun-ajaran-periode="{{ $mataKuliah->tahunAjaran->periode ?? '' }}"
                                    data-jenis="{{ $mataKuliah->mataKuliah->jenis ?? '' }}"
                                    data-sks="{{ $mataKuliah->mataKuliah->sks ?? '' }}"
                                    data-created="{{ $mataKuliah->created_at ?? '' }}">

                                    <!-- Kode -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            <div class="font-mono font-medium">{{ $mataKuliah->mataKuliah->kodeMatkul ?? '-' }}</div>
                                        </div>
                                    </td>

                                    <!-- Nama Mata Kuliah -->
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <h3 class="text-sm font-semibold text-gray-900 leading-tight">
                                                {{ $mataKuliah->mataKuliah->namaMatkul ?? 'Nama tidak tersedia' }}
                                            </h3>
                                        </div>
                                    </td>

                                    <!-- Kode -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            <div class="text-gray-600">{{ $mataKuliah->mataKuliah->sks ?? '-' }} SKS</div>
                                        </div>
                                    </td>

                                    <!-- Jenis -->
                                    <td class="px-6 py-4">
                                        @if($mataKuliah->mataKuliah->jenis)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $mataKuliah->mataKuliah->jenis == 'wajib' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                                {{ ucfirst($mataKuliah->mataKuliah->jenis) }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>

                                    <!-- Kelas -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            @if(isset($mataKuliah->kelasNames) && $mataKuliah->kelasNames->count() > 0)
                                                {{ $mataKuliah->kelasNames->implode(', ') }}
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Tahun Ajaran -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            <div class="font-medium">{{ $mataKuliah->tahunAjaran->tahun ?? '-' }} - {{ $mataKuliah->tahunAjaran->periode ?? '-' }}</div>
                                        </div>
                                    </td>

                                    <!-- Jumlah Mahasiswa -->
                                    <td class="px-6 py-4 text-center">
                                        <div class="inline-flex items-center justify-center w-8 h-8 bg-orange-100 rounded-full">
                                            <span class="text-sm font-semibold text-orange-800">{{ $mataKuliah->kelasMahasiswa->count() }}</span>
                                        </div>
                                    </td>

                                    <!-- Dosen Pengampu -->
                                    <td class="px-6 py-4 text-center">
                                        <div class="text-sm text-gray-900">
                                            @if(isset($mataKuliah->dosenPengampuNames) && $mataKuliah->dosenPengampuNames->count() > 0)
                                                @if($mataKuliah->dosenPengampuNames->count() <= 2)
                                                    @foreach($mataKuliah->dosenPengampuNames as $dosenName)
                                                        <span class="inline-block {{ $dosenName === $dosen->nama ? 'bg-green-100 text-green-800 font-medium' : 'bg-blue-100 text-blue-800' }} text-xs px-2 py-1 rounded-full mr-1"
                                                              title="{{ $dosenName === $dosen->nama ? 'Anda' : $dosenName }}">
                                                            {{ $dosenName === $dosen->nama ? 'Anda' : Str::limit($dosenName, 12) }}
                                                        </span>
                                                    @endforeach
                                                @else
                                                    @php
                                                        $currentUserFirst = $mataKuliah->dosenPengampuNames->contains($dosen->nama);
                                                        $firstDosen = $currentUserFirst ? $dosen->nama : $mataKuliah->dosenPengampuNames->first();
                                                    @endphp
                                                    <span class="inline-block {{ $firstDosen === $dosen->nama ? 'bg-green-100 text-green-800 font-medium' : 'bg-blue-100 text-blue-800' }} text-xs px-2 py-1 rounded-full mr-1"
                                                          title="{{ $firstDosen === $dosen->nama ? 'Anda' : $firstDosen }}">
                                                        {{ $firstDosen === $dosen->nama ? 'Anda' : Str::limit($firstDosen, 8) }}
                                                    </span>
                                                    <span class="text-xs text-gray-500" title="{{ $mataKuliah->dosenPengampuNames->reject(fn($name) => $name === $firstDosen)->implode(', ') }}">
                                                        +{{ $mataKuliah->dosenPengampuNames->count() - 1 }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-gray-500">-</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Last Modified -->
                                    <td class="px-6 py-4 text-center">
                                        <div class="text-xs text-gray-600">
                                            @php
                                                $lastNilaiUpdate = \App\Models\Nilai::where('tahunAjaranMatkulId', $mataKuliah->id)->max('updated_at');
                                            @endphp
                                            @if($lastNilaiUpdate)
                                                <span class="font-medium" title="{{ \Carbon\Carbon::parse($lastNilaiUpdate)->format('d/m/Y H:i:s') }}">
                                                    {{ \Carbon\Carbon::parse($lastNilaiUpdate)->format('d/m/Y H:i') }}
                                                </span>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ \Carbon\Carbon::parse($lastNilaiUpdate)->diffForHumans() }}
                                                </div>
                                            @else
                                                <div class="text-xs text-gray-500">
                                                    -
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Aksi -->
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('dosen.nilai.show', $mataKuliah->id) }}"
                                           class="inline-flex items-center px-3 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-medium rounded-md transition-colors duration-150">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
                                            </svg>
                                            Kelola Nilai
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
    </div>
</div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form if tahun ajaran is auto-selected but not in URL
    const tahunAjaranSelect = document.querySelector('select[name="tahun_ajaran_id"]');
    const urlParams = new URLSearchParams(window.location.search);
    const hasTahunAjaranInUrl = urlParams.has('tahun_ajaran_id');

    if (tahunAjaranSelect && tahunAjaranSelect.value && !hasTahunAjaranInUrl) {
        // Preserve any existing search parameter
        const searchInput = document.querySelector('input[name="search"]');
        const jenisSelect = document.querySelector('select[name="jenis"]');

        // Auto-submit the form to update URL with the selected tahun ajaran
        tahunAjaranSelect.form.submit();
    }
});
</script>
@endpush
