@extends('layouts.main')

@section('content')
<div class="p-4 md:p-6 space-y-4">
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-gray-900 tracking-tight">Capaian Pembelajaran</h1>
                <p class="text-sm text-gray-500 mt-1">Ringkasan CPL mahasiswa dan unduhan surat keterangan.</p>
            </div>
            <a href="{{ route('mahasiswa.capaian.export-pdf', request()->only(['kurikulum_id'])) }}"
               class="inline-flex items-center justify-center px-3 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm rounded-md">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Unduh Surat Keterangan PDF
            </a>
        </div>

        {{-- Identitas singkat --}}
        <div class="px-5 py-4 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-[11px] uppercase tracking-wide text-gray-500 mb-1">Mahasiswa</p>
                    <p class="text-lg font-semibold text-gray-900">{{ strtoupper($mahasiswa->nama ?? '-') }}</p>
                    <p class="text-sm text-gray-700 mt-0.5">NIM {{ $mahasiswa->nim ?? '-' }}</p>
                    <p class="text-sm text-gray-600 mt-3">{{ $institution['prodi'] }}</p>
                    <p class="text-sm text-gray-600">{{ $institution['fakultas'] }}</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="border border-gray-200 rounded-xl px-4 py-3">
                        <p class="text-[11px] uppercase tracking-wide text-gray-500">IPK</p>
                        <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ $akademik['ipk'] !== null ? number_format($akademik['ipk'], 2) : '-' }}</p>
                    </div>
                    <div class="border border-gray-200 rounded-xl px-4 py-3">
                        <p class="text-[11px] uppercase tracking-wide text-gray-500">SKS Lulus</p>
                        <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ $akademik['total_sks'] }}</p>
                    </div>
                    <div class="border border-gray-200 rounded-xl px-4 py-3">
                        <p class="text-[11px] uppercase tracking-wide text-gray-500">Predikat</p>
                        <p class="text-base font-semibold text-gray-900 mt-1">{{ $akademik['predikat'] }}</p>
                    </div>
                    <div class="border border-gray-200 rounded-xl px-4 py-3">
                        <p class="text-[11px] uppercase tracking-wide text-gray-500">Gelar</p>
                        <p class="text-base font-semibold text-gray-900 mt-1">{{ $akademik['gelar'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel CPL utama (format surat) --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h2 class="text-base font-semibold text-gray-900">Rincian Capaian Pembelajaran Lulusan</h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    Status diambil dari nilai CPMK pendukung tertinggi.
                    Keterangan <span class="text-amber-700 font-medium">Nilai belum lengkap</span> berarti masih ada CPMK tanpa nilai.
                </p>
            </div>
            <form method="GET" action="{{ route('mahasiswa.capaian') }}" class="flex flex-wrap items-end gap-2">
                <div>
                    <label for="kurikulum_id" class="block text-[11px] font-medium text-gray-500 mb-1">Kurikulum</label>
                    <select name="kurikulum_id" id="kurikulum_id" onchange="this.form.submit()"
                        class="border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-amber-600 focus:border-amber-600 block w-44 p-2">
                        <option value="">Semua Kurikulum</option>
                        @foreach($kurikulumList as $kur)
                            <option value="{{ $kur->id }}" {{ (string) $kurikulumId === (string) $kur->id ? 'selected' : '' }}>
                                {{ $kur->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="cpl_id" class="block text-[11px] font-medium text-gray-500 mb-1">Filter</label>
                    <select name="cpl_id" id="cpl_id" onchange="this.form.submit()"
                        class="border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-amber-600 focus:border-amber-600 block w-44 p-2">
                        <option value="">Semua CPL</option>
                        @foreach($cplList as $cpl)
                            <option value="{{ $cpl->id }}" {{ (string)$cplIdTerpilih === (string)$cpl->id ? 'selected' : '' }}>
                                {{ $cpl->kodeCpl }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        @if(!($hasAssessedMatkul ?? true))
        <div class="mx-5 mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            Belum ada matkul asesmen yang ditetapkan untuk filter ini, sehingga capaian CPL belum dihitung.
        </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wide text-gray-500 w-14">No.</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-500 w-28">Kode CPL</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-wide text-gray-500">Deskripsi Capaian Pembelajaran</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wide text-gray-500 w-24">Nilai</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold uppercase tracking-wide text-gray-500 w-28">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($cplData as $i => $cpl)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-center text-gray-700">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-900">{{ $cpl['kode'] }}</td>
                            <td class="px-4 py-3 text-gray-700 leading-relaxed">{{ $cpl['deskripsi'] }}</td>
                            <td class="px-4 py-3 text-center text-xl font-semibold text-gray-900">
                                {{ $cpl['nilai_surat'] }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($cpl['status_cpl'] === 'Tercapai')
                                    <span class="text-sm font-medium text-emerald-700">Tercapai</span>
                                @else
                                    <span class="text-sm font-medium text-red-700">Belum</span>
                                @endif
                                @if(empty($cpl['nilai_lengkap']))
                                    <p class="text-[11px] text-amber-700 mt-0.5 leading-tight">Nilai belum lengkap</p>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-gray-500">Belum ada data CPL.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Detail CPMK (sekunder, collapsible) --}}
    @if(count($cplData) > 0)
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200">
            <h2 class="text-base font-semibold text-gray-900">Detail CPMK pendukung</h2>
            <p class="text-sm text-gray-500 mt-0.5">Buka tiap CPL untuk melihat mata kuliah dan nilai CPMK.</p>
        </div>

        <div class="divide-y divide-gray-100">
            @foreach($cplData as $index => $cpl)
            <div x-data="{ open: {{ $index === 0 ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open"
                    class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-gray-50 transition-colors">
                    <div class="pr-4">
                        <p class="font-semibold text-gray-900">{{ $cpl['kode'] }}</p>
                        <p class="text-sm text-gray-500 mt-0.5 line-clamp-2">{{ $cpl['deskripsi'] }}</p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <div class="text-right">
                            <span class="text-lg font-semibold text-gray-900">{{ $cpl['nilai_surat'] }}</span>
                            @if(empty($cpl['nilai_lengkap']))
                                <p class="text-[11px] text-amber-700 leading-tight">Nilai belum lengkap</p>
                            @endif
                        </div>
                        <svg class="w-5 h-5 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </button>

                <div x-show="open" x-cloak class="px-5 pb-5">
                    <div class="overflow-x-auto border border-gray-200 rounded-md">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-medium text-gray-500">Kode MK</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-500">Mata Kuliah</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-500">CPMK</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-500">Deskripsi</th>
                                    <th class="px-3 py-2 text-center font-medium text-gray-500">Nilai</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @php
                                    $grouped = collect($cpl['cpmk'])->groupBy(fn ($item) => $item['kode_mk'].'|'.$item['nama_mk']);
                                @endphp
                                @foreach($grouped as $mkKey => $cpmkList)
                                    @php
                                        [$kode_mk, $nama_mk] = explode('|', $mkKey);
                                        $rowspan = count($cpmkList);
                                        $printedMk = false;
                                    @endphp
                                    @foreach($cpmkList as $cpmk)
                                    <tr>
                                        @if(!$printedMk)
                                            <td class="px-3 py-2 font-medium text-gray-900 align-top" rowspan="{{ $rowspan }}">{{ $kode_mk }}</td>
                                            <td class="px-3 py-2 text-gray-700 align-top" rowspan="{{ $rowspan }}">{{ $nama_mk }}</td>
                                            @php $printedMk = true; @endphp
                                        @endif
                                        <td class="px-3 py-2 font-medium text-gray-900">{{ $cpmk['kode'] }}</td>
                                        <td class="px-3 py-2 text-gray-700">{{ $cpmk['deskripsi'] }}</td>
                                        <td class="px-3 py-2 text-center font-medium {{ is_numeric($cpmk['nilai']) && $cpmk['nilai'] >= ($cpl['nilai_minimal'] ?? 55) ? 'text-emerald-700' : 'text-red-700' }}">
                                            {{ $cpmk['nilai'] }}
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
        </div>
    </div>
    @endif
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
