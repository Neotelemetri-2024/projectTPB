@extends('layouts.layout')
@section('title', 'Management User')
@section('header')
    <h5 class="mb-4">Tahun Ajaran</h5>
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
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahCpl"> <i
                class="fa-solid fa-plus"></i> Tambah </button>
    </div>
    <!-- Basic Bootstrap Table -->
    <div class="card">
        <div class="table-responsive text-nowrap table-striped table-bordered align-middle">
            <table class="table ">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tahun Ajaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($thnajaran as $thajaran)
                        <tr>
                            <td>{{ $loop->iteration + ($thnajaran->currentPage() - 1) * $thnajaran->perPage() }}</td>
                            <td>{{ $thajaran->semester . ' ' . $thajaran->tahun ?? '-' }}</td>

                            <td class="d-flex  justify-content-end gap-3">
                                <button type="button" class="btn btn-outline-warning btnEditCpl"
                                    data-id=" ">Edit</button>
                                <button type="button" class="btn btn-danger btnDeleteCpl" data-id=" ">Hapus</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-end mt-3 me-3">{{ $thnajaran->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>

    <!-- Modal Tambah Tahun Ajaran -->
    <div class="modal fade" id="modalTambahCpl" tabindex="-1" aria-labelledby="modalTambahCplLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg"> {{-- modal-lg biar lebar --}}
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahCplLabel">Tambah Data Tahun Ajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('admin.admin.tahunajaran.storeTahunAjaran') }}">
                    @csrf
                    <div class="modal-body">
                        {{-- Tahun Ajaran --}}
                        <div class="mb-3">
                            <label for="semester" class="form-label">Semester</label>
                            <input type="text" class="form-control" id="semester" name="semester" required>
                        </div>
                        {{-- Deskripsi CPL --}}
                        <div class="mb-3">
                            <label for="tahun" class="form-label">Tahun</label>
                            <input type="text" class="form-control" id="tahun" name="tahun" required>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
