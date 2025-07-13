@extends('layouts.main')

@section('title', 'Tambah CPL')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Tambah Capaian Pembelajaran Lulusan (CPL)</h2>
                <a href="{{ route('admin.cpl.index') }}" 
                   class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center transition-colors">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </a>
            </div>
        </div>

        <form action="{{ route('admin.cpl.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="grid gap-4 mb-4 grid-cols-1">
                <div>
                    <label for="kodeCpl" class="block mb-2 text-sm font-medium text-gray-900">Kode CPL</label>
                    <input type="text" 
                           name="kodeCpl" 
                           id="kodeCpl" 
                           value="{{ old('kodeCpl') }}"
                           class="bg-gray-50 border {{ $errors->has('kodeCpl') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5"
                           placeholder="Contoh: CPL-01"
                           required>
                    @error('kodeCpl')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="deskripsi" class="block mb-2 text-sm font-medium text-gray-900">Deskripsi CPL</label>
                    <textarea name="deskripsi" 
                              id="deskripsi" 
                              rows="6"
                              class="bg-gray-50 border {{ $errors->has('deskripsi') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 resize-none"
                              placeholder="Masukkan deskripsi lengkap capaian pembelajaran lulusan..."
                              required>{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="flex items-center justify-end space-x-2">
                <a href="{{ route('admin.cpl.index') }}" 
                   class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="text-white bg-amber-600 hover:bg-amber-700 focus:ring-4 focus:outline-none focus:ring-amber-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors">
                    Simpan CPL
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
