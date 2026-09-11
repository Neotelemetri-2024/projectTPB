@extends('layouts.main')

@section('title', 'Edit Kurikulum')

@section('content')
<div class="p-4 md:p-6 space-y-4">
    <div class="bg-white border border-gray-200 rounded-xl">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Edit Kurikulum</h2>
                <a href="{{ route('admin.kurikulum.index') }}" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center transition-colors">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </a>
            </div>
        </div>

        <form action="{{ route('admin.kurikulum.update', $kurikulum->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <div class="grid gap-4 mb-4 grid-cols-1 md:grid-cols-2">
                <div>
                    <label for="kode" class="block mb-2 text-sm font-medium text-gray-900">Kode Kurikulum</label>
                    <input type="text" name="kode" id="kode" value="{{ old('kode', $kurikulum->kode) }}"
                           class="bg-gray-50 border {{ $errors->has('kode') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" required>
                    @error('kode')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="nama" class="block mb-2 text-sm font-medium text-gray-900">Nama Kurikulum</label>
                    <input type="text" name="nama" id="nama" value="{{ old('nama', $kurikulum->nama) }}"
                           class="bg-gray-50 border {{ $errors->has('nama') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" required>
                    @error('nama')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="tahun" class="block mb-2 text-sm font-medium text-gray-900">Tahun</label>
                    <input type="number" name="tahun" id="tahun" value="{{ old('tahun', $kurikulum->tahun) }}" min="1900" max="2100"
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5">
                    @error('tahun')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="isAktif" class="block mb-2 text-sm font-medium text-gray-900">Status</label>
                    <select name="isAktif" id="isAktif" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5">
                        <option value="1" {{ old('isAktif', $kurikulum->isAktif) ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ !old('isAktif', $kurikulum->isAktif) ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label for="deskripsi" class="block mb-2 text-sm font-medium text-gray-900">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="4"
                              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 resize-none">{{ old('deskripsi', $kurikulum->deskripsi) }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-2">
                <a href="{{ route('admin.kurikulum.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm font-medium px-5 py-2.5">Batal</a>
                <button type="submit" class="text-white bg-amber-600 hover:bg-amber-700 rounded-lg text-sm font-medium px-5 py-2.5">Update Kurikulum</button>
            </div>
        </form>
    </div>
</div>
@endsection
