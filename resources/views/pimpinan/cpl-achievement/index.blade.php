@extends('layouts.main')

@section('title', 'Ketercapaian CPL')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-6 border-b border-gray-200">
            <h1 class="text-xl font-semibold text-gray-900">Ketercapaian CPL per Mata Kuliah (Akumulasi Semua Tahun)</h1>
            <p class="text-sm text-gray-600 mt-1">Laporan ini merupakan akumulasi lintas seluruh tahun ajaran.</p>
        </div>

        <div class="p-4">
            @php
                $grouped = collect($rows)->groupBy(function($r) { return $r['cpl']->id; });
            @endphp

            @forelse($grouped as $cplId => $items)
                @php
                    $cpl = $items->first()['cpl'];
                    $sumTotal = $items->sum('total');
                    $sumMencapai = $items->sum('mencapai');
                    $sumPersen = $sumTotal > 0 ? round(($sumMencapai / $sumTotal) * 100) : 0;
                    $sectionId = 'cpl-section-' . $cplId;
                @endphp
                <div class="border border-gray-200 rounded-lg overflow-hidden mb-6 bg-white">
                    <button type="button" class="w-full px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-start justify-between hover:bg-gray-100" data-toggle-target="#{{ $sectionId }}">
                        <div class="text-left">
                            <div class="text-sm font-semibold text-gray-900">{{ $cpl->kodeCpl }}</div>
                            <div class="text-xs text-gray-600 max-w-4xl">{{ $cpl->deskripsi }}</div>
                        </div>
                        <div class="text-xs text-gray-700 flex items-center gap-3">
                            <div class="hidden md:block">Capaian CPL:</div>
                            <span class="inline-flex items-center px-2 py-1 rounded bg-{{ $sumPersen >= 60 ? 'green' : 'red' }}-100 text-{{ $sumPersen >= 60 ? 'green' : 'red' }}-800 font-semibold">{{ $sumPersen }}%</span>
                            <span class="text-gray-400">|</span>
                            <div class="hidden md:block">Mhs diases:</div>
                            <span class="font-semibold">{{ $sumTotal }}</span>
                            <span class="text-gray-400">|</span>
                            <div class="hidden md:block">Mencapai:</div>
                            <span class="font-semibold">{{ $sumMencapai }}</span>
                            <svg class="w-4 h-4 text-gray-500 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>

                    <div id="{{ $sectionId }}" class="overflow-x-auto hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kode MK</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama MK</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Jumlah Mhs diases</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Jumlah Mhs mencapai Target</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Capaian MK</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Capaian CPL</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Target</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($items as $row)
                                <tr>
                                    <td class="px-4 py-3">{{ $row['kode_mk'] }}</td>
                                    <td class="px-4 py-3">{{ $row['nama_mk'] }}</td>
                                    <td class="px-4 py-3 text-center">{{ $row['total'] }}</td>
                                    <td class="px-4 py-3 text-center">{{ $row['mencapai'] }}</td>
                                    <td class="px-4 py-3 text-center">{{ $row['capai_persen'] }}%</td>
                                    <td class="px-4 py-3 text-center">{{ $row['capai_persen'] }}%</td>
                                    <td class="px-4 py-3 text-center">60%</td>
                                </tr>
                                @endforeach
                                <tr class="bg-gray-50 font-semibold">
                                    <td class="px-4 py-3 text-right" colspan="2">Capaian {{ $cpl->kodeCpl }}</td>
                                    <td class="px-4 py-3 text-center">{{ $sumTotal }}</td>
                                    <td class="px-4 py-3 text-center">{{ $sumMencapai }}</td>
                                    <td class="px-4 py-3 text-center" colspan="2">{{ $sumPersen }}%</td>
                                    <td class="px-4 py-3 text-center">60%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="px-4 py-6 text-center text-gray-500">Tidak ada data</div>
            @endforelse
        </div>
        <script>
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('[data-toggle-target]');
            if (!btn) return;
            const target = document.querySelector(btn.getAttribute('data-toggle-target'));
            if (target) {
                target.classList.toggle('hidden');
            }
        });
        </script>
    </div>
</div>
@endsection


