@extends('layouts.layout')
@section('title', 'Management User')
@section ('header')
    <h5 class="mb-4">Student List</h5>
@endsection
@section('content')
<!-- Tombol Tambah -->
    <div class="my-2 d-flex  justify-content-end gap-3 mb-3">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahCpmk" > <i class="fa-solid fa-pmkus"></i> Tambah </button>
    </div>
<!-- Basic Bootstrap Table -->
    <div class="card">
        <div class="table-responsive text-nowrap table-striped table-bordered align-middle">
            <table class="table ">
                <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th>Kode CPMK</th>
                        <th>Nama CPMK</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($allCpmk as $cpmk)
                    <tr>
                        <td>{{ $loop->iteration + ($allCpmk->currentPage() - 1) * $allCpmk->perPage() }}</td>
                        <td>{{ $cpmk->kode_cpmk ?? '-'}}</td>
                        <td>{{ $cpmk->nama_cpmk ?? '-' }}</td>
                        <td class="d-flex  justify-content-end gap-3">
                            <button type="button" class="btn btn-outline-warning btnEditCpmk" data-id="{{ $cpmk->id }}" >Edit</button>

                            <button type="button" class="btn btn-danger btnDeleteCpmk" data-id="{{ $cpmk->id }}">Hapus</button>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-end mt-3 me-3">{{ $allCpmk->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>

<!-- Modal Tambah CPMK -->
<div class="modal fade" id="modalTambahCpmk" tabindex="-1" aria-labelledby="modalTambahCpmkLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> {{-- modal-lg biar lebar --}}
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalTambahCpmkLabel">Tambah Data Cpmk</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('cpmk.store') }}">
            @csrf
            <div class="modal-body">
                {{-- Kode CPMK --}}
                <div class="mb-3">
                <label for="kode_cpmk" class="form-label">Kode CPMK</label>
                <input type="text" class="form-control @error('kode_cpmk') is-invalid @enderror" id="kode_cpmk" name="kode_cpmk" value="{{ old('kode_cpmk') }}" required>
                @error('kode_cpmk')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                </div>
                {{-- Deskripsi Cpmk --}}
                <div class="mb-3">
                    <label for="nama_cpmk" class="form-label">Nama CPMK</label>
                    <textarea class="form-control" id="nama_cpmk" name="nama_cpmk" rows="5" required></textarea>
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

<!-- Modal Edit CPMK -->
<div class="modal fade" id="modalEditCpmk" tabindex="-1" aria-labelledby="modalEditCpmkLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> {{-- modal-lg biar lebar --}}
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalEditCpmkLabel">Edit Data Cpmk</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      <form method="POST" id="editForm">
        @csrf
        @method('PUT')
            <div class="modal-body">
                {{-- Kode CPMK --}}
                <div class="mb-3">
                    <label class="form-label">Kode CPMK</label>
                    <input type="text" class="form-control @error('kode_cpmk') is-invalid @enderror" id="edit_kode_cpmk" name="kode_cpmk" value="{{ old('kode_cpmk') }}" required>
                    {{-- @error('kode_cpmk')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror --}}
                </div> 
                {{-- Deskripsi Cpmk --}}
                <div class="mb-3">
                    <label class="form-label mt-4">Nama CPMK</label>
                    <textarea class="form-control" id="edit_nama_cpmk" name="nama_cpmk" required rows="4"></textarea>
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


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // ==== EDIT Cpmk ====
        document.querySelectorAll('.btnEditCpmk').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                fetch(`{{ url('/cpmk') }}/${id}/edit`)
                    .then(response => {
                        if (!response.ok) throw new Error('Gagal fetch data');
                        return response.json();
                    })
                    .then(data => {
                        document.getElementById('edit_kode_cpmk').value = data.kode_cpmk;
                        document.getElementById('edit_nama_cpmk').value = data.nama_cpmk;
                        document.getElementById('editForm').action = `{{ url('cpmk') }}/${data.id}`;
                        new bootstrap.Modal(document.getElementById('modalEditCpmk')).show();
                    })
                    .catch(error => {
                        console.error("Gagal mengambil data:", error);
                        Swal.fire('Oops!', 'Terjadi kesalahan saat mengambil data.', 'error');
                    });
            });
        });

        // ==== HAPUS Cpmk ====
        document.querySelectorAll('.btnDeleteCpmk').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Apakah kamu yakin?',
                    text: 'Data CPMK ini akan dihapus permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`{{ url('cpmk') }}/${id}`, {
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
                                title: 'Berhasil',
                                text: data.message,
                                timer: 1000,
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


    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        timer: 1000,
        showConfirmButton: false
    });
    @endif

    @if ($errors->any())
        const modalTambah = new bootstrap.Modal(document.getElementById('modalTambahCpmk'));
        modalTambah.show();
    @endif
    });
</script>










