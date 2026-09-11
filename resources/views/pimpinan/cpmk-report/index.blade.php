@extends('layouts.main')

@section('title', 'Laporan CPMK')

@section('content')
@php
    $pageMk = $mataKuliahList->count();
    $pageKelas = $mataKuliahList->sum(fn ($m) => $m->kelas->count());
    $pageMhs = $mataKuliahList->sum(fn ($m) => $m->kelas->sum('kelas_mahasiswa_count'));
    $pageCpmk = $mataKuliahList->sum(fn ($m) => $m->cpmkMatKul->count());
@endphp
<div class="p-4 md:p-6 space-y-4">
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-gray-900 tracking-tight">Laporan Pengukuran CPMK</h1>
                <p class="text-sm text-gray-500 mt-1">Ketercapaian CPMK per mata kuliah</p>
            </div>
            <form method="GET" action="" class="flex flex-wrap items-end gap-2">
                <div>
                    <label for="tahun_ajaran_id" class="block text-[11px] font-medium text-gray-500 mb-1">Tahun Ajaran</label>
                    <select name="tahun_ajaran_id" id="tahun_ajaran_id" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg px-3 py-2 min-w-[200px] shadow-sm" onchange="this.form.submit()">
                        @foreach($tahunAjaranList as $ta)
                            <option value="{{ $ta->id }}" @selected($selectedTahunAjaranId == $ta->id)>
                                {{ $ta->tahun }} - {{ ucfirst($ta->periode) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    @if($mataKuliahList->count() > 0)
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-gray-500">Mata Kuliah</p>
                <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ $pageMk }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-gray-500">Kelas</p>
                <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ $pageKelas }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-gray-500">Mahasiswa</p>
                <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ number_format($pageMhs) }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
                <p class="text-[11px] uppercase tracking-wide text-gray-500">CPMK</p>
                <p class="text-2xl font-semibold text-gray-900 mt-0.5">{{ $pageCpmk }}</p>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-[11px] uppercase tracking-wide text-gray-500 border-b border-gray-200">
                            <th class="px-4 py-3 font-semibold">Kode</th>
                            <th class="px-4 py-3 font-semibold">Mata Kuliah</th>
                            <th class="px-4 py-3 font-semibold text-center">Kelas</th>
                            <th class="px-4 py-3 font-semibold text-center">Mahasiswa</th>
                            <th class="px-4 py-3 font-semibold text-center">CPMK</th>
                            <th class="px-4 py-3 font-semibold">Status</th>
                            <th class="px-4 py-3 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($mataKuliahList as $matkul)
                            @php
                                $cpmkCount = $matkul->cpmkMatKul->count();
                                $mhsCount = $matkul->kelas->sum('kelas_mahasiswa_count');
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $matkul->mataKuliah->kodeMatkul }}-{{ $matkul->mataKuliah->kurikulum }}
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    <div>{{ $matkul->mataKuliah->namaMatkul }}</div>
                                    @if($matkul->mataKuliah->jenis)
                                        <div class="text-xs text-gray-400 mt-0.5">{{ ucfirst($matkul->mataKuliah->jenis) }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-gray-700">{{ $matkul->kelas->count() }}</td>
                                <td class="px-4 py-3 text-center text-gray-700">{{ $mhsCount }}</td>
                                <td class="px-4 py-3 text-center text-gray-700">{{ $cpmkCount }}</td>
                                <td class="px-4 py-3">
                                    @if($cpmkCount > 0)
                                        <span class="text-emerald-700">Siap dianalisis</span>
                                    @else
                                        <span class="text-amber-700">Belum ada CPMK</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('pimpinan.cpmk-report.show', $matkul->id) }}"
                                       class="inline-flex items-center bg-amber-600 hover:bg-amber-700 text-white px-3 py-1.5 rounded-md text-sm font-medium">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($mataKuliahList->total() > 0)
                <div class="px-4 py-3 border-t border-gray-200 flex justify-center">
                    {{ $mataKuliahList->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="bg-white border border-gray-200 rounded-xl px-5 py-12 text-center">
            <p class="text-sm font-medium text-gray-900">Tidak ada mata kuliah</p>
            <p class="text-sm text-gray-500 mt-1">Belum ada mata kuliah untuk tahun ajaran yang dipilih.</p>
            <a href="{{ route('pimpinan.dashboard') }}" class="inline-flex items-center mt-4 bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                Kembali ke Dashboard
            </a>
        </div>
    @endif
</div>
@endsection
