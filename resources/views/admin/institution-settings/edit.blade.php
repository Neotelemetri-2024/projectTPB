@extends('layouts.main')

@section('title', 'Ketua Departemen')

@section('content')
<div class="p-4 md:p-6 max-w-2xl">
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="p-5 border-b border-gray-200">
            <h1 class="text-xl font-semibold text-gray-900">Ketua Departemen</h1>
            <p class="text-sm text-gray-500 mt-1">Nama dan NIP ini digunakan pada surat capaian CPL dan transkrip PDF.</p>
        </div>
        <form method="POST" action="{{ route('admin.institution-settings.update') }}" class="p-5 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="ketua_nama" class="block text-sm font-medium text-gray-700 mb-1">Nama ketua departemen</label>
                <input id="ketua_nama" name="ketua_nama" value="{{ old('ketua_nama', $institution['ketua_nama']) }}" required maxlength="255" class="w-full rounded-lg border-gray-300">
                @error('ketua_nama') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="ketua_nip" class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                <input id="ketua_nip" name="ketua_nip" value="{{ old('ketua_nip', $institution['ketua_nip']) }}" required inputmode="numeric" maxlength="20" class="w-full rounded-lg border-gray-300">
                @error('ketua_nip') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="w-full sm:w-auto rounded-lg bg-amber-600 hover:bg-amber-700 px-4 py-2 text-white">Simpan</button>
        </form>
    </div>
</div>
@endsection
