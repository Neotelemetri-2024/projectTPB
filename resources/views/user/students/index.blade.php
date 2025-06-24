@extends('layouts.layout')
@section('title', 'Management User')
@section ('header')
    <h5 class="mb-4">Student List</h5>
@endsection
@section('content')
<!-- Tombol Tambah -->
    <div class="my-2 d-flex  justify-content-end gap-3 mb-3">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahUser" > <i class="fa-solid fa-plus"></i> Tambah </button>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalImportExcel">Import Excel </button>
    </div>
<!-- Basic Bootstrap Table -->
    <div class="card">
        <div class="table-responsive text-nowrap table-striped table-bordered align-middle">
            <table class="table ">
                <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIM</th>
                        <th>Email</th>
                        <th>Tahun Masuk</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($students as $student)
                    <tr>
                        <td>{{ $loop->iteration + ($students->currentPage() - 1) * $students->perPage() }}</td>
                        <td>{{ $student->user->name ?? '-'}}</td>
                        <td>{{ $student->nim ?? '-' }}</td>
                        <td>{{ $student->user->email ?? '-'}}</td>
                        <td>{{ $student->tahun_masuk ?? '-' }}</td>
                        <td>{{ $student->user->role }}</td>
                        <td class="d-flex  justify-content-end gap-3">
                            <button type="button" class="btn btn-outline-warning btnEditStudent" data-id="{{ $student->id }}">Edit</button>

                            <button type="button" class="btn btn-danger btnDeleteStudent" data-id="{{ $student->id }}">Hapus</button>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-end mt-3 me-3">{{ $students->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>

<!-- Modal Tambah Mahasiswa -->
<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-labelledby="modalTambahUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> {{-- modal-lg biar lebar --}}
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalTambahUserLabel">Tambah Data User</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('admin.users.students.store') }}">
            @csrf
            <div class="modal-body">
                {{-- Nama --}}
                <div class="mb-3">
                <label for="name" class="form-label">Nama</label>
                <input type="text" class="form-control" id="name" name="name" required>
                </div>

                {{-- Email --}}
                <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
                </div>

                {{-- NIM --}}
                <div class="mb-3">
                <label for="nim" class="form-label">NIM</label>
                <input type="text" class="form-control" id="nim" name="nim" required>
                </div>

                {{-- Tahun Masuk --}}
                <div class="mb-3">
                <label for="tahun_masuk" class="form-label">Tahun Masuk</label>
                <input type="text" class="form-control" id="tahun_masuk" name="tahun_masuk" required>
                </div>
                {{-- Role --}}
                {{-- <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select id="role" name="role" class="form-select" required>
                        <option value="" disabled selected>Pilih Role</option>
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="dosen">Dosen</option>
                        <option value="pimpinan">Pimpinan</option>
                        <option value="admin">Admin</option>
                    </select>
                </div> --}}
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
        </div>
    </div>
</div>


<!-- Modal Import Excel -->
<div class="modal fade" id="modalImportExcel" tabindex="-1" aria-labelledby="modalImportExcelLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('admin.users.students.import') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="modalImportExcelLabel">Import Data Mahasiswa dari Excel</h5>
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

@include('user.students.edit')
@endsection

<!-- Pastikan di layout utama sudah ada ini: -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // ==== Edit ====
        const buttons = document.querySelectorAll(".btnEditStudent");

        buttons.forEach(button => {
            button.addEventListener("click", function () {
                const id = this.getAttribute("data-id");

                fetch(`/admin/users/students/${id}/edit`)
                    .then(response => {
                        if (!response.ok) throw new Error('Gagal fetch data');
                        return response.json();
                    })
                    .then(data => {
                        document.getElementById('edit_name').value = data.user.name;
                        document.getElementById('edit_email').value = data.user.email;
                        document.getElementById('edit_nim').value = data.nim;
                        document.getElementById('edit_tahun_masuk').value = data.tahun_masuk;
                        // document.getElementById('edit_role').value = data.user.role;

                        document.getElementById('editForm').action = `/admin/users/students/${data.id}`;

                        const modal = new bootstrap.Modal(document.getElementById('modalEditUser'));
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

        // ==== Delete ====
        const deleteButtons = document.querySelectorAll(".btnDeleteStudent");

        deleteButtons.forEach(button => {
            button.addEventListener("click", function () {
                const id = this.getAttribute("data-id");

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data mahasiswa akan dihapus permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#aaa',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    backdrop: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/admin/users/students/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message
                            }).then(() => {
                                location.reload();
                            });
                        })
                        .catch(error => {
                            console.error("Gagal hapus data:", error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Terjadi kesalahan saat menghapus.'
                            });
                        });
                    }
                });
            });
        });
    });
</script>







