@extends('layouts.main')

@section('title', 'Daftar Dosen')

@section('content')
<div class="p-4 md:p-6 space-y-4 min-w-0">
    <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Daftar Dosen</h1>
                <p class="text-sm text-gray-500 mt-1">Nama dan NIP dosen di portal</p>
            </div>
            <form method="GET" class="flex gap-2 w-full sm:w-auto">
                <input name="search" value="{{ $search }}" placeholder="Cari nama atau NIP" class="min-w-0 flex-1 rounded-lg border-gray-300 text-sm">
                <button class="rounded-lg bg-amber-600 hover:bg-amber-700 px-4 py-2 text-white text-sm">Cari</button>
            </form>
        </div>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-600"><tr><th class="px-4 py-3">Nama Dosen</th><th class="px-4 py-3">NIP</th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($dosen as $item)
                    <tr><td class="px-4 py-3 text-gray-900">{{ $item->nama }}</td><td class="px-4 py-3 text-gray-700 whitespace-nowrap">{{ $item->nip ?: '-' }}</td></tr>
                    @empty
                    <tr><td colspan="2" class="px-4 py-10 text-center text-gray-500">Tidak ada dosen yang cocok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $dosen->links() }}</div>
    </div>
</div>
@endsection
