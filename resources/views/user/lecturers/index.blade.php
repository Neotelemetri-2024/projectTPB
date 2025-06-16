@extends('layouts.layout') {{-- Sesuaikan dengan layout milikmu --}}
@section('title', 'Data Dosen')
@section ('header')
    <h5 class="mb-4">Lecturer List</h5>
@endsection
@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
<!-- Tombol Tambah -->
<div class="my-2 d-flex  justify-content-end gap-3">
    <button type="button" class="btn btn-outline-warning">Filter</button>
    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalTambahLecturer">Tambah </button>
    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalImportExcel">Import Excel </button>
</div>
    <div class="card">
        <div class="table-responsive text-nowrap table-striped table-bordered align-middle">
           <table class="table ">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>NIP</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lecturers as $lecturer)
                        <tr>
                            <td>{{ $loop->iteration + ($lecturers->currentPage() - 1) * $lecturers->perPage() }}</td>
                            <td>{{ $lecturer->user->name }}</td>
                            <td>{{ $lecturer->user->email }}</td>
                            <td>{{ $lecturer->nip }}</td>
                            <td>{{ $lecturer->user->role }}</td>
                            <td class="d-flex  justify-content-end gap-3">
                                <button type="button" class="btn btn-outline-warning btnEditLecturer" data-id="{{ $lecturer->id }}">Edit</button>
                                <button type="button" class="btn btn-danger btnDeleteLecturer" data-id="{{ $lecturer->id }}">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada data dosen.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-end mt-3 me-3">{{ $lecturers->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>

<!-- Modal Tambah Dosen -->
<div class="modal fade" id="modalTambahLecturer" tabindex="-1" aria-labelledby="modalTambahLecturerLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> {{-- modal-lg biar lebar --}}
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalTambahLevturerLabel">Tambah Data User</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('admin.users.lecturers.store') }}">
            @csrf
            <div class="modal-body">
                {{-- Nama --}}
                <div class="mb-3">
                <label for="name" class="form-label">Nama</label>
                <input type="text" class="form-control" id="name" name="name" required>
                </div>

                {{-- NIP --}}
                <div class="mb-3">
                <label for="nip" class="form-label">NIP</label>
                <input type="text" class="form-control" id="nip" name="nip" required>
                </div>

                {{-- Email --}}
                <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
                </div>

                {{-- Role --}}
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select id="role" name="role" class="form-select" required>
                        <option value="" disabled selected>Pilih Role</option>
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="dosen">Dosen</option>
                        <option value="pimpinan">Pimpinan</option>
                        <option value="admin">Admin</option>
                    </select>
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
<!-- Modal Import Excel untuk Dosen -->
<div class="modal fade" id="modalImportExcel" tabindex="-1" aria-labelledby="modalImportExcelLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('admin.users.lecturers.import') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="modalImportExcelLabel">Import Data Dosen dari Excel</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="file_excel" class="form-label">Pilih File Excel</label>
            <input type="file" name="file" class="form-control" required accept=".xls,.xlsx">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Import</button>
        </div>
      </form>
    </div>
  </div>
</div>

@include('user.lecturers.edit')

@endsection
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // ==== Edit ====
        const buttons = document.querySelectorAll(".btnEditLecturer");

        buttons.forEach(button => {
            button.addEventListener("click", function () {
                const id = this.getAttribute("data-id");

                fetch(`/admin/users/lecturers/${id}/edit`)
                    .then(response => {
                        if (!response.ok) throw new Error('Gagal fetch data');
                        return response.json();
                    })
                    .then(data => {
                        document.getElementById('edit_name').value = data.user.name;
                        document.getElementById('edit_email').value = data.user.email;
                        document.getElementById('edit_nip').value = data.nip;
                        document.getElementById('edit_role').value = data.user.role;

                        document.getElementById('editForm').action = `/admin/users/lecturers/${data.id}`;

                        const modal = new bootstrap.Modal(document.getElementById('modalEditLecturer'));
                        modal.show();
                    })
                    .catch(error => {
                        console.error("Gagal mengambil data:", error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops!',
                            text: 'Terjadi kesalahan saat mengambil data.'
                        });
                    });
            });
        });


        // ==== HAPUS ====
        const deleteButtons = document.querySelectorAll(".btnDeleteLecturer");

        deleteButtons.forEach(button => {
            button.addEventListener("click", function () {
                const id = this.getAttribute("data-id");

                Swal.fire({
                    title: 'Apakah kamu yakin?',
                    text: "Data dosen ini akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/admin/users/lecturers/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('Gagal menghapus');
                            return response.json();
                        })
                        .then(data => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Dihapus!',
                                text: data.message || 'Data dosen berhasil dihapus.',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Oops', 'Terjadi kesalahan saat menghapus.', 'error');
                        });
                    }
                });
            });
        });

    });
</script>








