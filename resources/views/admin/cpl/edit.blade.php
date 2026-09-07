@extends('layouts.main')

@section('title', 'Edit CPL')

@section('content')
<div class="p-4 md:p-6 space-y-4">
    <div class="bg-white border border-gray-200 rounded-xl">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Edit Capaian Pembelajaran Lulusan (CPL)</h2>
                <a href="{{ route('admin.cpl.index') }}" 
                   class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center transition-colors">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </a>
            </div>
        </div>

        <form action="{{ route('admin.cpl.update', $cpl->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid gap-4 mb-4 grid-cols-1 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="kodeCpl" class="block mb-2 text-sm font-medium text-gray-900">Kode CPL</label>
                    <input type="text" 
                           name="kodeCpl" 
                           id="kodeCpl" 
                           value="{{ old('kodeCpl', $cpl->kodeCpl) }}"
                           class="bg-gray-50 border {{ $errors->has('kodeCpl') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5"
                           placeholder="Contoh: CPL-01"
                           required>
                    @error('kodeCpl')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="deskripsi" class="block mb-2 text-sm font-medium text-gray-900">Deskripsi CPL</label>
                    <textarea name="deskripsi" 
                              id="deskripsi" 
                              rows="6"
                              class="bg-gray-50 border {{ $errors->has('deskripsi') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 resize-none"
                              placeholder="Masukkan deskripsi lengkap capaian pembelajaran lulusan..."
                              required>{{ old('deskripsi', $cpl->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="nilaiMinimal" class="block mb-2 text-sm font-medium text-gray-900">Nilai Minimal</label>
                    <input type="number"
                           name="nilaiMinimal"
                           id="nilaiMinimal"
                           min="0"
                           max="100"
                           value="{{ old('nilaiMinimal', $cpl->nilaiMinimal ?? 60) }}"
                           class="bg-gray-50 border {{ $errors->has('nilaiMinimal') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5"
                           required>
                    <p class="mt-1 text-xs text-gray-500">Ambang nilai mahasiswa dianggap mencapai CPL (0â€“100).</p>
                    @error('nilaiMinimal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="targetPersen" class="block mb-2 text-sm font-medium text-gray-900">Target Capaian (%)</label>
                    <input type="number"
                           name="targetPersen"
                           id="targetPersen"
                           min="0"
                           max="100"
                           value="{{ old('targetPersen', $cpl->targetPersen ?? 60) }}"
                           class="bg-gray-50 border {{ $errors->has('targetPersen') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5"
                           required>
                    <p class="mt-1 text-xs text-gray-500">Persentase mahasiswa yang ditargetkan mencapai nilai minimal.</p>
                    @error('targetPersen')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="flex items-center justify-end space-x-2">
                <a href="{{ route('admin.cpl.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white focus:ring-4 focus:outline-none focus:ring-gray-300 rounded-lg text-sm font-medium px-5 py-2.5 focus:z-10 transition-colors duration-200">
                    Batal
                </a>
                <button type="submit" 
                        class="text-white bg-amber-600 hover:bg-amber-700 focus:ring-4 focus:outline-none focus:ring-amber-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors">
                    Update CPL
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
