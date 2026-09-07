@extends('layouts.main')

@section('title', 'Mapping CPL × CPMK')

@section('content')
@php
    $colW = [
        'num' => 40,
        'kode' => 88,
        'nama' => 220,
        'sks' => 48,
        'cpl' => 112,
    ];
    $leftKode = $colW['num'];
    $leftNama = $colW['num'] + $colW['kode'];
    $leftSks = $colW['num'] + $colW['kode'] + $colW['nama'];
@endphp
<div class="p-4 md:p-6 space-y-4">
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-gray-900 tracking-tight">Mapping CPL × CPMK</h1>
                <p class="text-sm text-gray-500 mt-1">Matriks mata kuliah ke CPL melalui CPMK (tanpa SCP).</p>
            </div>
            <form method="GET" action="{{ route($rolePrefix . '.cpl-mapping.index') }}" class="flex flex-wrap items-end gap-2">
                <div>
                    <label for="kurikulum" class="block text-[11px] font-medium text-gray-500 mb-1">Kurikulum</label>
                    <select name="kurikulum" id="kurikulum" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg px-3 py-2 min-w-[200px] shadow-sm">
                        <option value="">Semua</option>
                        @foreach($kurikulumList as $kur)
                            <option value="{{ $kur }}" @selected($selectedKurikulum === $kur)>{{ $kur }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-3 py-2 rounded-md text-sm">Terapkan</button>
                <a href="{{ route($rolePrefix . '.cpl-mapping.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-md text-sm">Reset</a>
            </form>
        </div>

        <div class="px-5 py-2.5 bg-gray-50 border-b border-gray-200 flex flex-wrap items-center justify-between gap-2 text-xs text-gray-600">
            <span>{{ $rows->count() }} mata kuliah · {{ $cpls->count() }} CPL</span>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5"><span class="inline-block w-3 h-3 rounded-sm bg-emerald-600"></span> Terpetakan</span>
                <span class="inline-flex items-center gap-1.5"><span class="inline-block w-3 h-3 rounded-sm bg-gray-100 border border-gray-300"></span> Kosong</span>
            </div>
        </div>

        <div class="overflow-auto max-h-[78vh]">
            <table class="border-collapse text-xs" style="min-width: {{ $leftSks + $colW['sks'] + ($cpls->count() * $colW['cpl']) }}px">
                <thead>
                    <tr class="text-center">
                        <th class="sticky top-0 z-30 border border-gray-300 bg-white px-2 py-2 font-semibold text-gray-600" style="left:0; width:{{ $colW['num'] }}px; min-width:{{ $colW['num'] }}px">#</th>
                        <th class="sticky top-0 z-30 border border-gray-300 bg-white px-2 py-2 font-semibold text-gray-600 text-left" style="left:{{ $leftKode }}px; width:{{ $colW['kode'] }}px; min-width:{{ $colW['kode'] }}px">Kode</th>
                        <th class="sticky top-0 z-30 border border-gray-300 bg-white px-2 py-2 font-semibold text-gray-600 text-left" style="left:{{ $leftNama }}px; width:{{ $colW['nama'] }}px; min-width:{{ $colW['nama'] }}px">Mata Kuliah</th>
                        <th class="sticky top-0 z-20 border border-gray-300 bg-white px-2 py-2 font-semibold text-gray-600" style="width:{{ $colW['sks'] }}px; min-width:{{ $colW['sks'] }}px">SKS</th>
                        @foreach($cpls as $cpl)
                            <th class="sticky top-0 z-20 border border-gray-300 bg-stone-50 px-1.5 py-2 font-semibold text-gray-800 align-bottom" style="min-width:{{ $colW['cpl'] }}px; width:{{ $colW['cpl'] }}px" title="{{ $cpl->deskripsi }}">
                                <div class="leading-tight">{{ $cpl->kodeCpl }}</div>
                                <div class="mt-1 font-normal text-[10px] text-gray-500 leading-snug line-clamp-3">{{ $cpl->deskripsi }}</div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $i => $row)
                        <tr>
                            <td class="sticky z-10 border border-gray-300 bg-white px-2 py-1.5 text-center text-gray-500" style="left:0">{{ $i + 1 }}</td>
                            <td class="sticky z-10 border border-gray-300 bg-white px-2 py-1.5 font-semibold text-gray-900 whitespace-nowrap" style="left:{{ $leftKode }}px">{{ $row['kode'] }}</td>
                            <td class="sticky z-10 border border-gray-300 bg-white px-2 py-1.5 text-gray-800" style="left:{{ $leftNama }}px">
                                <div class="leading-snug">{{ $row['nama'] }}</div>
                                @if($row['kurikulum'])
                                    <div class="text-[10px] text-gray-400 mt-0.5">{{ $row['kurikulum'] }}</div>
                                @endif
                            </td>
                            <td class="border border-gray-300 bg-white px-2 py-1.5 text-center text-gray-700">{{ $row['sks'] ?? '—' }}</td>
                            @foreach($cpls as $cpl)
                                @php $labels = $row['mapped'][$cpl->id] ?? []; @endphp
                                <td class="border border-gray-300 px-1 py-1 align-top {{ count($labels) ? 'bg-emerald-600 text-white' : 'bg-gray-50' }}">
                                    @foreach($labels as $label)
                                        <div class="leading-tight whitespace-nowrap">{{ $label }}</div>
                                    @endforeach
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 4 + $cpls->count() }}" class="border border-gray-300 px-4 py-12 text-center text-gray-500 text-sm">
                                Belum ada data mapping.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
