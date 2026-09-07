@extends('layouts.main')

@section('content')
<div class="p-4 md:p-6 space-y-4" x-data="{ selectedDetailIdx: null }">
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200">
            <h1 class="text-xl font-semibold text-gray-900 tracking-tight">Transkrip Mahasiswa</h1>
            <p class="text-sm text-gray-500 mt-1">Daftar nilai mata kuliah per semester.</p>
        </div>

        <!-- Informasi Mahasiswa -->
        <div class="px-5 py-4 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 mb-3">Informasi Mahasiswa</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">No. BP</span>
                            <span class="text-sm text-gray-900">{{ $mahasiswa->nim ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Nama Mahasiswa</span>
                            <span class="text-sm text-gray-900">{{ $mahasiswa->nama ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Program Studi</span>
                            <span class="text-sm text-gray-900">Teknik Pertanian dan Biosistem</span>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-base font-semibold text-gray-900 mb-3">Prestasi Akademik</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">
                                @if($periodeTerpilih === 'all')
                                    Total SKS diambil
                                @else
                                    Jumlah SKS diambil
                                @endif
                            </span>
                            <span class="text-sm text-gray-900">{{ collect($matkulDiambil)->sum('sks') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">
                                @if($periodeTerpilih === 'all')
                                    Total Matakuliah diambil
                                @else
                                    Jumlah Matakuliah diambil
                                @endif
                            </span>
                            <span class="text-sm text-gray-900">{{ count($matkulDiambil) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">
                                @if($periodeTerpilih === 'all')
                                    IPK (Indeks Prestasi Kumulatif)
                                @else
                                    IP Semester
                                @endif
                            </span>
                            <span class="text-sm font-semibold text-gray-900">
                                @php
                                    $totalBobot = 0;
                                    $totalSks = 0;
                                    foreach($matkulDiambil as $mk) {
                                        if($mk['grade'] && $mk['grade'] !== '-') {
                                            $bobot = 0;
                                            switch($mk['grade']) {
                                                case 'A': $bobot = 4.0; break;
                                                case 'A-': $bobot = 3.7; break;
                                                case 'B+': $bobot = 3.3; break;
                                                case 'B': $bobot = 3.0; break;
                                                case 'B-': $bobot = 2.7; break;
                                                case 'C+': $bobot = 2.3; break;
                                                case 'C': $bobot = 2.0; break;
                                                case 'C-': $bobot = 1.7; break;
                                                case 'D': $bobot = 1.0; break;
                                                case 'E': $bobot = 0.0; break;
                                            }
                                            $totalBobot += $bobot * $mk['sks'];
                                            $totalSks += $mk['sks'];
                                        }
                                    }
                                    $ipk = $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0.00;
                                @endphp
                                {{ number_format($ipk, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Periode Semester -->
        <div class="px-5 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                <form method="GET" action="" class="flex flex-col sm:flex-row sm:items-end gap-2">
                    <div>
                        <label for="periode_id" class="block text-[11px] font-medium text-gray-500 mb-1">Periode Semester</label>
                        <select name="periode_id" id="periode_id" class="border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-amber-500 focus:border-amber-500 block w-full sm:w-64 p-2" onchange="this.form.submit()">
                            @foreach($periodes as $periode)
                                <option value="{{ $periode['id'] }}" {{ $periodeTerpilih == $periode['id'] ? 'selected' : '' }}>{{ $periode['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
                
                <a href="{{ route('mahasiswa.transkrip.export-pdf') }}" 
                   class="inline-flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export PDF
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-500">No</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-500">Semester</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-500">Kode Matkul</th>
                        <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-500">Nama Matkul</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold uppercase tracking-wide text-gray-500">SKS</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold uppercase tracking-wide text-gray-500">Grade</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold uppercase tracking-wide text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($matkulDiambil as $idx => $mk)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-900">{{ $mk['no'] }}</td>
                        <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-700">
                            {{ $mk['semester'] ?? '-' }}
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-900">{{ $mk['kode'] }}</td>
                        <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-900">{{ $mk['nama'] }}</td>
                        <td class="px-5 py-3 whitespace-nowrap text-center text-sm text-gray-700">
                            {{ $mk['sks'] }}
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap text-center text-sm font-semibold
                            @if($mk['grade'] === 'A' || $mk['grade'] === 'A-') text-emerald-700
                            @elseif(Str::startsWith($mk['grade'], 'B')) text-gray-900
                            @elseif(Str::startsWith($mk['grade'], 'C')) text-amber-700
                            @elseif($mk['grade'] === 'D') text-orange-700
                            @elseif($mk['grade'] === 'E') text-red-700
                            @else text-gray-500 @endif">
                            {{ $mk['grade'] ?? '-' }}
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap text-center text-sm font-medium">
                            <button type="button" title="Lihat Detail" class="inline-flex items-center justify-center bg-amber-600 hover:bg-amber-700 text-white px-2.5 py-1.5 rounded-md text-xs font-medium" data-modal-toggle="modal-detail-nilai" @click="selectedDetailIdx = {{ $idx }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-8 text-center text-gray-500">Belum ada mata kuliah diambil.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Modal Detail Nilai (pakai detail-modal, hanya komponen penilaian) -->
        <x-detail-modal id="modal-detail-nilai" title="Detail Nilai Mata Kuliah">
            <template x-if="selectedDetailIdx !== null && {{ json_encode($matkulDiambil) }}[selectedDetailIdx]">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Nama Mata Kuliah: <span x-text="{{ json_encode($matkulDiambil) }}[selectedDetailIdx]?.nama"></span></h3>
                    <div class="mb-6">
                        <h4 class="text-base font-semibold text-gray-800 mb-2">Komponen Penilaian</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 mb-4">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-500">No</th>
                                        <th class="px-4 py-2 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-500">Komponen</th>
                                        <th class="px-4 py-2 text-center text-[11px] font-semibold uppercase tracking-wide text-gray-500">Nilai</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-if="({{ json_encode($matkulDiambil) }}[selectedDetailIdx]?.komponen_nilai || []).length === 0">
                                        <tr>
                                            <td colspan="3" class="px-4 py-2 text-center text-gray-500">Tidak ada data komponen penilaian.</td>
                                        </tr>
                                    </template>
                                    <template x-for="kom in {{ json_encode($matkulDiambil) }}[selectedDetailIdx]?.komponen_nilai || []" :key="kom.no">
                                        <tr>
                                            <td class="px-4 py-2" x-text="kom.no"></td>
                                            <td class="px-4 py-2" x-text="kom.nama"></td>
                                            <td class="px-4 py-2 text-center" x-text="kom.nilai"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </template>
            <template x-if="selectedDetailIdx !== null && !{{ json_encode($matkulDiambil) }}[selectedDetailIdx]">
                <div class="text-center text-gray-500 py-8">Data tidak ditemukan.</div>
            </template>
        </x-detail-modal>
    </div>
</div>
@endsection
