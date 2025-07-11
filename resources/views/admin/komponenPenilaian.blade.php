@extends('layouts.layout')
@section('title', 'Komponen Penilaian')

@section('header')
<h5 class="mb-4">List Komponen Penilaian</h5>
@endsection

@section('content')
<!-- Tombol Tambah -->
<div class="mb-3 d-flex justify-content-end">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah Komponen</button>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Komponen</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $komponen)
                <tr>
                    <td>{{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}</td>
                    <td>{{ $komponen->nama }}</td>
                    <td class="d-flex justify-content-center gap-2">
                        <button class="btn btn-warning btnEdit" data-id="{{ $komponen->id }}">Edit</button>
                        <button class="btn btn-danger btnDelete" data-id="{{ $komponen->id }}">Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="d-flex justify-content-end mt-3 me-3">{{ $data->links('pagination::bootstrap-5') }}</div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.komponen.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Komponen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Nama Komponen</label>
                    <input type="text" class="form-control" name="nama" required>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary" type="submit">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="editForm" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Komponen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Nama Komponen</label>
                    <input type="text" class="form-control" name="nama" id="editNama" required>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary" type="submit">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Script -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.btnEdit').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.dataset.id;
                fetch(`/admin/komponen/${id}/edit`)
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById('editNama').value = data.nama;
                        document.getElementById('editForm').action = `/admin/komponen/${data.id}`;
                        new bootstrap.Modal(document.getElementById('modalEdit')).show();
                    });
            });
        });

        document.querySelectorAll('.btnDelete').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.dataset.id;
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!',
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/admin/komponen/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        }).then(() => location.reload());
                    }
                });
            });
        });

        @if(session('success'))
            Swal.fire('Berhasil!', '{{ session('success') }}', 'success');
        @endif
    });
</script>
@endsection
