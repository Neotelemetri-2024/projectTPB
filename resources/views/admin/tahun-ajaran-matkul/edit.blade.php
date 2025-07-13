@extends('layouts.main')
@section('title', 'Edit Mata Kuliah Tahun Ajaran')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Edit Mata Kuliah Tahun Ajaran</h2>
            </div>
        </div>

        <div class="p-6">
            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.tahun-ajaran-matkul.update', $tahunAjaranMatkul->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tahunAjaranId" class="block text-sm font-medium text-gray-700 mb-2">
                            Tahun Ajaran <span class="text-red-500">*</span>
                        </label>
                        <select name="tahunAjaranId" id="tahunAjaranId"
                                class="bg-gray-50 border {{ $errors->has('tahunAjaranId') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5"
                                required>
                            <option value="">Pilih Tahun Ajaran</option>
                            @foreach($tahunAjarans as $tahunAjaran)
                                <option value="{{ $tahunAjaran->id }}" {{ old('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId) == $tahunAjaran->id ? 'selected' : '' }}>
                                    {{ $tahunAjaran->tahun }}-{{ $tahunAjaran->periode }}
                                </option>
                            @endforeach
                        </select>
                        @error('tahunAjaranId')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mataKuliahId" class="block text-sm font-medium text-gray-700 mb-2">
                            Mata Kuliah <span class="text-red-500">*</span>
                        </label>
                        <select name="mataKuliahId" id="mataKuliahId"
                                class="bg-gray-50 border {{ $errors->has('mataKuliahId') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5"
                                required>
                            <option value="">Pilih Mata Kuliah</option>
                            @foreach($mataKuliahs as $mataKuliah)
                                <option value="{{ $mataKuliah->id }}" {{ old('mataKuliahId', $tahunAjaranMatkul->mataKuliahId) == $mataKuliah->id ? 'selected' : '' }}>
                                    {{ $mataKuliah->namaMatkul }} ({{ $mataKuliah->kodeMatkul }})
                                </option>
                            @endforeach
                        </select>
                        @error('mataKuliahId')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                         <div>
                         <label for="kelas" class="block text-sm font-medium text-gray-700 mb-2">
                             Kelas <span class="text-red-500">*</span>
                         </label>
                         <select name="kelas" id="kelas" class="bg-gray-50 border {{ $errors->has('kelas') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5" required>
                             <option value="">Pilih Kelas</option>
                             @for($i = 1; $i <= 26; $i++)
                                 <option value="{{ $i }}" {{ (old('kelas') ?? $tahunAjaranMatkul->kelas) == $i ? 'selected' : '' }}>{{ \App\Models\TahunAjaranMatkul::convertKelasToHuruf(collect([$i]))->first() }}</option>
                             @endfor
                         </select>
                         @error('kelas')
                             <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                         @enderror
                     </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Dosen Pengampu <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($dosens as $dosen)
                            <div class="flex items-center">
                                <input type="checkbox"
                                       name="dosenIds[]"
                                       value="{{ $dosen->id }}"
                                       id="dosen_{{ $dosen->id }}"
                                       class="rounded border-gray-300 text-amber-600 shadow-sm focus:border-amber-300 focus:ring focus:ring-amber-200 focus:ring-opacity-50 {{ $errors->has('dosenIds') ? 'border-red-500' : '' }}"
                                       {{ in_array($dosen->id, old('dosenIds', $tahunAjaranMatkul->dosenPengampu->pluck('dosenId')->toArray())) ? 'checked' : '' }}>
                                <label for="dosen_{{ $dosen->id }}" class="ml-2 text-sm text-gray-700">
                                    {{ $dosen->nama }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('dosenIds')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-between pt-6 border-t border-gray-200">
                    <a href="{{ request('back_url', route('admin.tahun-ajaran-matkul.index')) }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                    <button type="submit"
                            class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
