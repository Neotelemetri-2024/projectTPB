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


    <!-- Tombol -->
    <div class="my-2 d-flex justify-content-end gap-3 position-relative mb-4">
        <!-- Tombol Filter -->
        <div id="btnFilter" class="btn btn-outline-warning" style="cursor: pointer;">
            Filter
        </div>

        <!-- Tombol Tambah -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahCpl">
            <i class="fa-solid fa-plus"></i> Tambah
        </button>

        <!-- Dropdown Filter -->
        <form id="filterDropdown" method="GET" action="{{ route('admin.tahunajaran') }}" class="card shadow position-absolute" style="display: none; width: 230px; top: 50px; right: 0; border-radius: 8px; padding: 12px 16px; gap: 8px; z-index: 1000;">
            <div class="form-check mb-2">
                <label class="form-check-label d-flex justify-content-between align-items-center w-100">
                    <span>Ganjil</span>
                    <input class="form-check-input" type="radio" name="semester" value="Ganjil" onchange="this.form.submit()" {{ request('semester') == 'Ganjil' ? 'checked' : '' }}>
                </label>
            </div>
            <div class="form-check">
                <label class="form-check-label d-flex justify-content-between align-items-center w-100">
                    <span>Genap</span>
                    <input class="form-check-input" type="radio" name="semester" value="Genap" onchange="this.form.submit()" {{ request('semester') == 'Genap' ? 'checked' : '' }}>
                </label>
            </div>
        </form>
    </div>
    <!-- Basic Bootstrap Table -->
    <div class="card">
        <div class="table-responsive text-nowrap table-striped table-bordered ">
            <table class="table table-bordered text-center align-middle ">
                <thead class="text-center">
                    <tr>
                        <th class="w-16">No</th>
                        <th>Tahun Ajaran</th>
                        <th class="w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($thnajaran as $thajaran)
                        <tr>
                            <td class="px-4 py-3 border text-center align-middle">{{ $loop->iteration + ($thnajaran->currentPage() - 1) * $thnajaran->perPage() }}</td>
                            <td>{{ $thajaran->semester . ' ' . $thajaran->tahun ?? '-' }}</td>

                            <td class="d-flex  justify-content-center gap-3">
                                <button type="button" class="btn btn-outline-warning btnEditTahunAjaran"
                                data-id="{{ $thajaran->id }}" data-bs-toggle="modal" data-bs-target="#modalEditTahunAjaran">Edit</button>
                                <button type="button" class="btn btn-danger btnDeleteTahunAjaran" data-id="{{ $thajaran->id }}">Hapus</button>
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

    <!-- Modal Edit -->
    <div class="modal fade" id="modalEditTahunAjaran" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" id="formEdit">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Tahun Ajaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="mb-3">
                            <label for="edit-semester" class="form-label">Semester</label>
                            <select class="form-select" id="edit-semester" name="semester" required>
                                <option value="Ganjil">Ganjil</option>
                                <option value="Genap">Genap</option>
                            </select>
                        </div>


                        <div class="mb-3">
                            <label for="edit-tahun" class="form-label">Tahun</label>
                            <input type="text" class="form-control" id="edit-tahun" name="tahun">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // ==== EDIT TAHUN AJARAN ====
        document.querySelectorAll('.btnEditTahunAjaran').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                fetch(`/admin/tahunajaran/${id}/get`)
                    .then(response => {
                        if (!response.ok) throw new Error('Gagal ambil data');
                        return response.json();
                    })
                    .then(data => {
                        document.getElementById('edit_id').value = data.id;
                        document.getElementById('edit-semester').value = data.semester;
                        document.getElementById('edit-tahun').value = data.tahun;

                        // set form action ke route update
                        document.getElementById('formEdit').action = `/admin/tahunajaran/${data.id}`;
                    })
                    .catch(error => {
                        console.error('Gagal:', error);
                        Swal.fire('Oops!', 'Gagal ambil data dari server.', 'error');
                    });
            });
        });

        // ==== HAPUS TAHUN AJARAN ====
        document.querySelectorAll('.btnDeleteTahunAjaran').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Apakah kamu yakin?',
                    text: 'Data Tahun Ajaran ini akan dihapus permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/admin/tahunajaran/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('Gagal menghapus');
                            return response.json();
                        })
                        .then(data => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
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
        // ==== FILTER ====
        const btnFilter = document.getElementById('btnFilter');
        const dropdown = document.getElementById('filterDropdown');

        btnFilter.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdown.style.display = (dropdown.style.display === 'none' || dropdown.style.display === '') ? 'block' : 'none';
        });

        document.addEventListener('click', function (e) {
            if (!btnFilter.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });   
    });

    
</script>




