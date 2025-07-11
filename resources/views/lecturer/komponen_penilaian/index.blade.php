@extends('layouts.layout')
@section('title', 'Home')
@section('header')
  <h2 class="fw-bold p-1">
    Kelola Penilaian
  </h2>
@endsection
@section('content')
<!-- Tombol Tambah -->
    {{-- <div class="my-2 d-flex  justify-content-end gap-3 mb-3">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="" > <i class="fa-solid fa-pmkus"></i> Tambah </button>
    </div> --}}
<!-- Basic Bootstrap Table -->
    <div class="card">
        <div class="table-responsive text-nowrap table-striped table-bordered align-middle">
            <table class="table ">
                <thead class="text-center table">
                    <tr>
                        <th>No</th>
                        <th>Kode Matakuliah</th>
                        <th>Nama Matakuliah</th>
                        <th>Tahun Ajaran</th>
                        <th>Semester</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach($matkuls as $index => $matkul)
                        <tr>
                            <td>{{ $matkuls->firstItem() + $index }}</td>
                            <td>{{ $matkul->kode_matkul }}</td>
                            <td>{{ $matkul->nama_matkul }}</td>
                            <td>
                                @foreach($matkul->tahunAjaranMatkuls as $tahunAjaranMatkul)
                                    <span>{{ $tahunAjaranMatkul->tahunAjaran->semester . ' ' . $tahunAjaranMatkul->tahunAjaran->tahun ?? '-' }}</span>
                                @endforeach
                            </td>
                            <td>
                                @foreach($matkul->tahunAjaranMatkuls as $tahunAjaranMatkul)
                                    <span>{{ $tahunAjaranMatkul->semester_studi ?? '-' }}</span>
                                @endforeach
                            </td>
                            <td>
                                <a href="{{ route('lecturer.komponen.show', $matkul->id) }}" class="btn btn-primary">Kelola</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-end mt-3 me-3">{{ $matkuls->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
@endsection