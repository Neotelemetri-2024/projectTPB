@extends('layouts.layout')
@section('title', 'Management User')
@section('header')
    <h5 class="mb-4">Mata Kuliah {{ $matkul->nama_matkul }}</h5>
@endsection
@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <!-- Tombol Tambah -->
    <div class="my-2 d-flex  justify-content-end gap-3 mb-3">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahMatkul"> <i
                class="fa-solid fa-plus"></i> Tambah </button>
    </div>

@endsection
