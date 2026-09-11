@extends('layouts.main')

@section('title', 'Matkul Asesmen Kurikulum')

@section('content')
<div class="p-4 md:p-6 space-y-4">
    <div class="bg-white border border-gray-200 rounded-xl">
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                <div>
                    <a href="{{ route('admin.kurikulum.index') }}" class="text-sm text-amber-700 hover:underline">&larr; Kembali ke daftar kurikulum</a>
                    <h2 class="text-lg font-semibold text-gray-900 mt-2">Matkul Asesmen — {{ $kurikulum->nama }}</h2>
                    <p class="text-sm text-gray-500 mt-2">
                        Centang mata kuliah pada kurikulum ini yang nilainya dipakai untuk perhitungan laporan CPL.
                        Matkul yang tidak dicentang tetap dapat dinilai, tetapi tidak dihitung sebagai asesmen CPL.
                    </p>
                </div>
            </div>

            @if($selectedIds === [])
            <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                Belum ada matkul asesmen yang ditetapkan untuk kurikulum ini. Selama belum diisi,
                <strong>laporan CPL tidak akan menghitung matkul apa pun</strong> untuk kurikulum ini.
            </div>
            @else
            <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ count($selectedIds) }} dari {{ $mataKuliah->count() }} matkul ditetapkan sebagai asesmen.
            </div>
            @endif
        </div>

        <form method="POST" action="{{ route('admin.kurikulum.matkul-asesmen.update', $kurikulum) }}">
            @csrf
            @method('PUT')

            @if($mataKuliah->isNotEmpty())
            <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="relative flex-1 max-w-md">
                    <input type="search" id="matkul-asesmen-search" autocomplete="off"
                           placeholder="Cari kode, nama, atau jenis..."
                           class="w-full pl-3 pr-3 py-2 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500">
                </div>
                <p id="matkul-asesmen-count" class="text-sm text-gray-500 whitespace-nowrap">
                    Menampilkan {{ $mataKuliah->count() }} dari {{ $mataKuliah->count() }} matkul
                </p>
            </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="matkul-asesmen-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">Diases</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Mata Kuliah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKS</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($mataKuliah as $mk)
                        <tr class="hover:bg-gray-50 matkul-asesmen-row"
                            data-search="{{ strtolower(trim(($mk->kodeMatkul ?? '') . ' ' . ($mk->namaMatkul ?? '') . ' ' . ($mk->jenis ?? ''))) }}">
                            <td class="px-6 py-4">
                                <input type="checkbox"
                                    name="mata_kuliah_ids[]"
                                    value="{{ $mk->id }}"
                                    class="matkul-asesmen-cb w-4 h-4 text-amber-600 border-gray-300 rounded focus:ring-amber-500"
                                    {{ in_array((int) $mk->id, $selectedIds, true) ? 'checked' : '' }}>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $mk->kodeMatkul }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $mk->namaMatkul }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ ucfirst($mk->jenis ?? '—') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $mk->sks ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                                Belum ada mata kuliah pada kurikulum ini. Tambahkan mata kuliah terlebih dahulu.
                            </td>
                        </tr>
                        @endforelse
                        <tr id="matkul-asesmen-empty" class="hidden">
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                                Tidak ada mata kuliah yang cocok dengan pencarian.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($mataKuliah->isNotEmpty())
            <div class="px-6 py-4 border-t border-gray-200 flex flex-wrap items-center gap-3">
                <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium rounded-lg">
                    Simpan Matkul Asesmen
                </button>
                <button type="button" id="matkul-asesmen-check-visible" class="px-4 py-2.5 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                    Centang yang tampil
                </button>
                <button type="button" id="matkul-asesmen-check-all" class="px-4 py-2.5 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                    Centang semua
                </button>
                <button type="button" id="matkul-asesmen-clear" class="px-4 py-2.5 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                    Kosongkan semua
                </button>
            </div>
            @endif
        </form>
    </div>
</div>

@if($mataKuliah->isNotEmpty())
<script>
(function () {
    const searchInput = document.getElementById('matkul-asesmen-search');
    const countEl = document.getElementById('matkul-asesmen-count');
    const emptyRow = document.getElementById('matkul-asesmen-empty');
    const rows = Array.from(document.querySelectorAll('.matkul-asesmen-row'));
    const total = rows.length;

    function visibleRows() {
        return rows.filter((row) => !row.classList.contains('hidden'));
    }

    function applyFilter() {
        const q = (searchInput.value || '').trim().toLowerCase();
        let shown = 0;

        rows.forEach((row) => {
            const hay = row.getAttribute('data-search') || '';
            const match = !q || hay.includes(q);
            row.classList.toggle('hidden', !match);
            if (match) shown++;
        });

        if (emptyRow) emptyRow.classList.toggle('hidden', shown > 0);
        if (countEl) {
            countEl.textContent = q
                ? `Menampilkan ${shown} dari ${total} matkul`
                : `Menampilkan ${total} dari ${total} matkul`;
        }
    }

    searchInput.addEventListener('input', applyFilter);

    document.getElementById('matkul-asesmen-check-visible')?.addEventListener('click', function () {
        visibleRows().forEach((row) => {
            const cb = row.querySelector('.matkul-asesmen-cb');
            if (cb) cb.checked = true;
        });
    });

    document.getElementById('matkul-asesmen-check-all')?.addEventListener('click', function () {
        document.querySelectorAll('.matkul-asesmen-cb').forEach((el) => { el.checked = true; });
    });

    document.getElementById('matkul-asesmen-clear')?.addEventListener('click', function () {
        document.querySelectorAll('.matkul-asesmen-cb').forEach((el) => { el.checked = false; });
    });
})();
</script>
@endif
@endsection
