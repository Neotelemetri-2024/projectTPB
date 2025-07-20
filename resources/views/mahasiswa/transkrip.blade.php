@extends('layouts.main')

@section('content')
<div class="p-6" x-data="{ selectedDetailIdx: null }">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Transkrip Mahasiswa</h2>
                <p class="text-gray-600 mt-1 text-sm">Halaman ini menampilkan daftar nilai mata kuliah Anda per semester.</p>
            </div>
            <form method="GET" action="" class="flex items-center gap-2 w-full md:w-auto">
                <label for="periode_id" class="text-sm font-medium text-gray-700 mr-2">Periode Semester</label>
                <select name="periode_id" id="periode_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-48 p-2.5" onchange="this.form.submit()">
                    @foreach($periodes as $periode)
                        <option value="{{ $periode['id'] }}" {{ $periodeTerpilih == $periode['id'] ? 'selected' : '' }}>{{ $periode['label'] }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode Matkul</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Matkul</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">SKS</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Grade</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($matkulDiambil as $idx => $mk)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mk['no'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mk['kode'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mk['nama'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ $mk['sks'] }} SKS</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($mk['grade'] === 'A' || $mk['grade'] === 'A-') bg-purple-100 text-purple-700
                                @elseif(Str::startsWith($mk['grade'], 'B')) bg-blue-100 text-blue-700
                                @elseif(Str::startsWith($mk['grade'], 'C')) bg-yellow-100 text-yellow-800
                                @elseif($mk['grade'] === 'D') bg-orange-100 text-orange-800
                                @elseif($mk['grade'] === 'E') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $mk['grade'] ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <button type="button" title="Lihat Detail" class="text-blue-600 hover:text-blue-900 inline-flex items-center justify-center" data-modal-toggle="modal-detail-nilai" @click="selectedDetailIdx = {{ $idx }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada mata kuliah diambil.</td>
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
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Komponen</th>
                                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Nilai</th>
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
