@extends('layouts.main')

@section('title', 'Ketercapaian CPL')

@section('content')
<div class="p-4 md:p-6 space-y-4">
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-gray-900 tracking-tight">Ketercapaian CPL per Mata Kuliah</h1>
                <p class="text-sm text-gray-500 mt-1">Nilai minimal & target dari master CPL. Filter hanya menyaring tampilan.</p>
            </div>
            <div class="flex flex-wrap items-end gap-2">
                <form method="GET" action="{{ route($rolePrefix . '.cpl-achievement.index') }}" class="flex flex-wrap items-end gap-2">
                    <div>
                        <label class="block text-[11px] font-medium text-gray-500 mb-1">Tahun Ajaran</label>
                        <select name="tahun_ajaran_id" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg px-3 py-2 min-w-[180px] shadow-sm">
                            <option value="">Semua</option>
                            @foreach($tahunAjaranList as $ta)
                                <option value="{{ $ta->id }}" @selected((string) $selectedTahunAjaranId === (string) $ta->id)>
                                    {{ $ta->tahun }} - {{ ucfirst($ta->periode) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-medium text-gray-500 mb-1">Kurikulum</label>
                        <select name="kurikulum" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg px-3 py-2 min-w-[160px] shadow-sm">
                            <option value="">Semua</option>
                            @foreach($kurikulumList as $kur)
                                <option value="{{ $kur->id }}" @selected((string) $selectedKurikulum === (string) $kur->id)>{{ $kur->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-3 py-2 rounded-md text-sm">Terapkan</button>
                    <a href="{{ route($rolePrefix . '.cpl-achievement.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-md text-sm">Reset</a>
                </form>
                <a href="{{ route($rolePrefix . '.cpl-achievement.export-pdf', request()->only(['tahun_ajaran_id', 'kurikulum'])) }}"
                   class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md text-sm">
                    Export PDF
                </a>
            </div>
        </div>
    </div>

    @if(!($hasAssessedMatkul ?? true))
    <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
        Belum ada matkul asesmen yang ditetapkan untuk filter ini.
        Tetapkan matkul asesmen di menu <strong>Kurikulum</strong> agar laporan menghitung nilai.
    </div>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
            <p class="text-[11px] uppercase tracking-wide text-gray-500">Total CPL</p>
            <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ $summary['total_cpl'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ $summary['cpl_tercapai'] }} mencapai target</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
            <p class="text-[11px] uppercase tracking-wide text-gray-500">Rata-rata Capaian</p>
            <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ $summary['avg_persen'] }}%</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
            <p class="text-[11px] uppercase tracking-wide text-gray-500">MK Pendukung</p>
            <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ $summary['total_mk'] }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
            <p class="text-[11px] uppercase tracking-wide text-gray-500">Mahasiswa Diases</p>
            <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ number_format($summary['total_diases']) }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ number_format($summary['total_mencapai']) }} mencapai</p>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <h2 class="text-base font-semibold text-gray-900">Target vs Capaian per CPL</h2>
        <p class="text-sm text-gray-500 mb-3">Persentase mahasiswa mencapai nilai minimal dibanding target CPL</p>
        <div class="relative h-72">
            <div id="cplAchievementChart" class="h-full w-full"></div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200">
            <h2 class="text-base font-semibold text-gray-900">Detail per CPL</h2>
            <p class="text-xs text-gray-500">Klik baris CPL untuk melihat rincian mata kuliah</p>
        </div>

        <div class="p-4 space-y-3">
            @forelse($grouped as $group)
                @php
                    $sectionId = 'cpl-section-' . $group['cpl_id'];
                    $sumPersen = $group['sum_persen'];
                    $targetPersen = (int) ($group['target_persen'] ?? 60);
                    $nilaiMinimal = (int) ($group['nilai_minimal'] ?? 60);
                    $reached = $sumPersen >= $targetPersen;
                @endphp
                <div class="border border-gray-200 rounded-lg overflow-hidden" data-cpl-section>
                    <button type="button" class="w-full px-4 py-3 bg-white hover:bg-gray-50 flex items-start justify-between gap-4 text-left" data-toggle-target="#{{ $sectionId }}">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
                                <span class="text-sm font-semibold text-gray-900">{{ $group['kode_cpl'] }}</span>
                                <span class="text-xs text-gray-500">{{ $group['cpmk_count'] }} CPMK · {{ $group['mk_count'] }} MK · Min {{ $nilaiMinimal }} · Target {{ $targetPersen }}%</span>
                            </div>
                            <p class="text-xs text-gray-600 mt-1 max-w-4xl">{{ $group['deskripsi'] }}</p>
                            @if($group['cpmk_kodes'])
                                <p class="text-xs text-gray-400 mt-1">CPMK: {{ $group['cpmk_kodes'] }}</p>
                            @endif
                        </div>
                        <div class="shrink-0 flex items-center gap-3 text-xs text-gray-600">
                            <span class="font-semibold {{ $reached ? 'text-emerald-700' : 'text-red-700' }}">{{ $sumPersen }}%</span>
                            <span class="text-gray-300">|</span>
                            <span>{{ $group['sum_mencapai'] }}/{{ $group['sum_total'] }}</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>

                    <div id="{{ $sectionId }}" class="overflow-x-auto hidden border-t border-gray-200">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-[11px] uppercase tracking-wide text-gray-500 border-b border-gray-200 bg-gray-50">
                                    <th class="px-4 py-2.5 font-semibold">Kode MK</th>
                                    <th class="px-4 py-2.5 font-semibold">Nama MK</th>
                                    <th class="px-4 py-2.5 font-semibold">Kurikulum</th>
                                    <th class="px-4 py-2.5 font-semibold">Tahun Ajaran</th>
                                    <th class="px-4 py-2.5 font-semibold text-center">Diases</th>
                                    <th class="px-4 py-2.5 font-semibold text-center">Mencapai</th>
                                    <th class="px-4 py-2.5 font-semibold text-center">Capaian</th>
                                    <th class="px-4 py-2.5 font-semibold text-center">Target</th>
                                    <th class="px-4 py-2.5 font-semibold text-center">Detail</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($group['items'] as $row)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2.5 font-medium text-gray-900">{{ $row['kode_mk'] }}</td>
                                    <td class="px-4 py-2.5 text-gray-700">{{ $row['nama_mk'] }}</td>
                                    <td class="px-4 py-2.5 text-gray-600">{{ $row['kurikulum'] ?: '-' }}</td>
                                    <td class="px-4 py-2.5 text-gray-600">{{ $row['tahun_ajaran_label'] }}</td>
                                    <td class="px-4 py-2.5 text-center">{{ $row['total'] }}</td>
                                    <td class="px-4 py-2.5 text-center">{{ $row['mencapai'] }}</td>
                                    <td class="px-4 py-2.5 text-center">
                                        <span class="font-semibold {{ $row['capai_persen'] >= $targetPersen ? 'text-emerald-700' : 'text-red-700' }}">
                                            {{ $row['capai_persen'] }}%
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5 text-center text-gray-500">{{ $targetPersen }}%</td>
                                    <td class="px-4 py-2.5 text-center">
                                        @if(!empty($row['tahun_ajaran_matkul_id']) && $rolePrefix === 'pimpinan')
                                            <a href="{{ route('pimpinan.cpmk-report.show', $row['tahun_ajaran_matkul_id']) }}"
                                               class="inline-flex items-center bg-amber-600 hover:bg-amber-700 text-white px-3 py-1.5 rounded-md text-sm font-medium">
                                                CPMK
                                            </a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                <tr class="bg-gray-50 font-semibold">
                                    <td class="px-4 py-2.5 text-right text-sm" colspan="4">Ringkasan {{ $group['kode_cpl'] }}</td>
                                    <td class="px-4 py-2.5 text-center">{{ $group['sum_total'] }}</td>
                                    <td class="px-4 py-2.5 text-center">{{ $group['sum_mencapai'] }}</td>
                                    <td class="px-4 py-2.5 text-center {{ $reached ? 'text-emerald-700' : 'text-red-700' }}">{{ $group['sum_persen'] }}%</td>
                                    <td class="px-4 py-2.5 text-center">{{ $targetPersen }}%</td>
                                    <td class="px-4 py-2.5"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="px-4 py-10 text-center text-sm text-gray-500">
                    Tidak ada data untuk filter yang dipilih.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('click', function(e) {
    const btn = e.target.closest('[data-toggle-target]');
    if (!btn) return;
    const target = document.querySelector(btn.getAttribute('data-toggle-target'));
    if (target) {
        target.classList.toggle('hidden');
    }
});

window.__chartReadyQueue = window.__chartReadyQueue || [];
window.__chartReadyQueue.push(function () {
    const chartData = @json($chartData);
    const el = document.querySelector('#cplAchievementChart');
    if (!el || typeof ApexCharts === 'undefined') return;

    new ApexCharts(el, {
        chart: {
            type: 'bar',
            height: '100%',
            toolbar: { show: false },
            fontFamily: 'inherit'
        },
        series: [
            { name: 'Capaian (%)', data: chartData.capaian },
            { name: 'Target (%)', data: chartData.target }
        ],
        xaxis: {
            categories: chartData.labels,
            labels: { style: { fontSize: '12px' } }
        },
        yaxis: {
            max: 100,
            min: 0,
            title: { text: 'Persentase (%)' },
            labels: {
                formatter: function (val) { return Math.round(val) + '%'; }
            }
        },
        colors: ['#D97706', '#9CA3AF'],
        plotOptions: {
            bar: {
                columnWidth: '55%',
                borderRadius: 3
            }
        },
        dataLabels: { enabled: false },
        legend: { position: 'top' },
        tooltip: {
            y: {
                formatter: function (val) { return val + '%'; }
            }
        },
        grid: {
            borderColor: '#E5E7EB',
            strokeDashArray: 4
        }
    }).render();
});
</script>
@endpush
