@extends('layouts.main')

@section('title', 'Ketua Departemen')

@section('content')
@php $editing = $errors->any(); @endphp
<div class="p-4 md:p-6 max-w-2xl">
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="p-5 border-b border-gray-200 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Ketua Departemen</h1>
                <p class="text-sm text-gray-500 mt-1">Nama dan NIP ini digunakan pada surat capaian CPL dan transkrip PDF.</p>
            </div>
            <button type="button" id="btn-edit" class="{{ $editing ? 'hidden' : '' }} inline-flex items-center justify-center bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                Edit
            </button>
        </div>
        <form method="POST" action="{{ route('admin.institution-settings.update') }}" class="p-5 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="ketua_nama" class="block text-sm font-medium text-gray-700 mb-1">Nama ketua departemen</label>
                <input id="ketua_nama" name="ketua_nama" value="{{ old('ketua_nama', $institution['ketua_nama']) }}" required maxlength="255" @disabled(!$editing)
                    class="kadep-field border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 {{ $editing ? 'bg-white' : 'bg-gray-100' }}">
                @error('ketua_nama') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="ketua_nip" class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                <input id="ketua_nip" name="ketua_nip" value="{{ old('ketua_nip', $institution['ketua_nip']) }}" required inputmode="numeric" maxlength="20" @disabled(!$editing)
                    class="kadep-field border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 {{ $editing ? 'bg-white' : 'bg-gray-100' }}">
                @error('ketua_nip') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div id="form-actions" class="{{ $editing ? 'flex' : 'hidden' }} items-center gap-2">
                <button type="submit" class="inline-flex items-center justify-center bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Simpan</button>
                <button type="button" id="btn-cancel" class="inline-flex items-center justify-center bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Batal</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const editButton = document.getElementById('btn-edit');
    const cancelButton = document.getElementById('btn-cancel');
    const actions = document.getElementById('form-actions');
    const fields = Array.from(document.querySelectorAll('.kadep-field'));
    const initialValues = fields.map((field) => field.value);

    function setEditing(editing) {
        fields.forEach((field) => {
            field.disabled = !editing;
            field.classList.toggle('bg-white', editing);
            field.classList.toggle('bg-gray-100', !editing);
        });
        actions.classList.toggle('hidden', !editing);
        actions.classList.toggle('flex', editing);
        editButton.classList.toggle('hidden', editing);
        if (editing) fields[0]?.focus();
    }

    editButton?.addEventListener('click', function () {
        setEditing(true);
    });

    cancelButton?.addEventListener('click', function () {
        fields.forEach((field, index) => {
            field.value = initialValues[index];
        });
        setEditing(false);
    });
});
</script>
@endpush
