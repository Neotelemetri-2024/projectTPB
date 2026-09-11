@extends('layouts.main')

@section('title', 'Asesmen CPL per Matkul')

@section('content')
@php
    $selectedCount = count($selectedPairs);
    $colW = 96;
    $leftKode = 130;
    $leftDesc = 300;
@endphp
<div class="p-4 md:p-6 space-y-4">
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <a href="{{ route('admin.kurikulum.index') }}" class="text-sm text-amber-700 hover:underline">&larr; Kembali ke daftar kurikulum</a>
            <h2 class="text-lg font-semibold text-gray-900 mt-2">Asesmen CPL — {{ $kurikulum->nama }}</h2>
            <p class="text-sm text-gray-500 mt-2">
                Centang sel pada perpotongan CPL dan mata kuliah yang nilainya dipakai untuk perhitungan laporan CPL.
                Satu mata kuliah bisa diases untuk sebagian CPL saja, dan satu CPL bisa diases oleh sebagian mata kuliah.
            </p>

            @if($cpl->isEmpty())
            <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                Belum ada CPL. Tambahkan CPL terlebih dahulu sebelum menetapkan asesmen.
            </div>
            @elseif($mataKuliah->isEmpty())
            <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                Belum ada mata kuliah pada kurikulum ini. Tambahkan mata kuliah terlebih dahulu.
            </div>
            @elseif($selectedCount === 0)
            <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                Belum ada pasangan CPL &times; matkul yang ditetapkan. Selama belum diisi,
                <strong>laporan CPL tidak akan menghitung matkul apa pun</strong> untuk kurikulum ini.
            </div>
            @else
            <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ $selectedCount }} pasangan CPL &times; matkul ditetapkan sebagai asesmen.
            </div>
            @endif
        </div>

        @if($cpl->isNotEmpty() && $mataKuliah->isNotEmpty())
        <form method="POST" action="{{ route('admin.kurikulum.matkul-asesmen.update', $kurikulum) }}">
            @csrf
            @method('PUT')

            <div class="px-6 py-4 border-b border-gray-200 flex flex-wrap items-center gap-3">
                <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg">
                    Simpan Asesmen
                </button>
                <button type="button" id="asesmen-check-all" class="px-4 py-2.5 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                    Centang semua
                </button>
                <button type="button" id="asesmen-clear" class="px-4 py-2.5 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                    Kosongkan semua
                </button>
            </div>

            <div class="overflow-auto max-h-[72vh]">
                <table class="border-collapse text-xs" style="min-width: {{ $leftDesc + ($mataKuliah->count() * $colW) }}px">
                    <thead>
                        <tr>
                            <th class="sticky top-0 left-0 z-30 border border-gray-300 bg-white px-2 py-2 font-semibold text-gray-600 text-left" style="width:{{ $leftKode }}px; min-width:{{ $leftKode }}px">CPL</th>
                            <th class="sticky top-0 z-30 border border-gray-300 bg-white px-2 py-2 font-semibold text-gray-600 text-left" style="left:{{ $leftKode }}px; width:{{ $leftDesc }}px; min-width:{{ $leftDesc }}px">Deskripsi</th>
                            @foreach($mataKuliah as $mk)
                                <th class="sticky top-0 z-20 border border-gray-300 bg-stone-50 px-1.5 py-2 font-semibold text-gray-800 align-bottom text-center" style="min-width:{{ $colW }}px; width:{{ $colW }}px" title="{{ $mk->kodeMatkul }} — {{ $mk->namaMatkul }}">
                                    <div class="leading-tight">{{ $mk->kodeMatkul }}</div>
                                    <div class="mt-1 font-normal text-[10px] text-gray-500 leading-snug line-clamp-3">{{ $mk->namaMatkul }}</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cpl as $item)
                        <tr>
                            <td class="sticky z-10 border border-gray-300 bg-white px-2 py-1.5 font-semibold text-gray-900 whitespace-nowrap" style="left:0">{{ $item->kodeCpl }}</td>
                            <td class="sticky z-10 border border-gray-300 bg-white px-2 py-1.5 text-gray-700" style="left:{{ $leftKode }}px">
                                <div class="leading-snug line-clamp-3" title="{{ $item->deskripsi }}">{{ $item->deskripsi }}</div>
                            </td>
                            @foreach($mataKuliah as $mk)
                                @php $key = $item->id . ':' . $mk->id; @endphp
                                <td class="border border-gray-300 px-1 py-1 text-center {{ isset($selectedPairs[$key]) ? 'bg-emerald-600' : 'bg-gray-50' }}">
                                    <input type="checkbox"
                                        name="asesmen[{{ $item->id }}][]"
                                        value="{{ $mk->id }}"
                                        class="asesmen-cb w-4 h-4 text-amber-600 border-gray-300 rounded focus:ring-amber-500"
                                        {{ isset($selectedPairs[$key]) ? 'checked' : '' }}>
                                </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
        @endif
    </div>
</div>

@if($cpl->isNotEmpty() && $mataKuliah->isNotEmpty())
<script>
(function () {
    function syncCellColors() {
        document.querySelectorAll('.asesmen-cb').forEach(function (cb) {
            cb.closest('td').classList.toggle('bg-emerald-600', cb.checked);
            cb.closest('td').classList.toggle('bg-gray-50', !cb.checked);
        });
    }

    document.querySelectorAll('.asesmen-cb').forEach(function (cb) {
        cb.addEventListener('change', syncCellColors);
    });

    document.getElementById('asesmen-check-all')?.addEventListener('click', function () {
        document.querySelectorAll('.asesmen-cb').forEach(function (cb) { cb.checked = true; });
        syncCellColors();
    });

    document.getElementById('asesmen-clear')?.addEventListener('click', function () {
        document.querySelectorAll('.asesmen-cb').forEach(function (cb) { cb.checked = false; });
        syncCellColors();
    });
})();
</script>
@endif
@endsection
