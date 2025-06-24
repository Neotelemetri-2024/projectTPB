@extends('layouts.layout')
@section('title', 'Management User')
@section ('header')
    <h5 class="mb-4">Student List</h5>
@endsection
@section('content')
<!-- Tombol Tambah -->
    <div class="my-2 d-flex  justify-content-end gap-3 mb-3">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahCpl" > <i class="fa-solid fa-plus"></i> Tambah </button>
    </div>
<!-- Basic Bootstrap Table -->
    <div class="card">
        <div class="table-responsive text-nowrap table-striped table-bordered align-middle">
            <table class="table ">
                <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th>Kode CP</th>
                        <th>Nama CP</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($cpls as $cpl)
                    <tr>
                        <td>{{ $loop->iteration + ($cpls->currentPage() - 1) * $cpls->perPage() }}</td>
                        <td>{{ $cpl->kode_cpl ?? '-'}}</td>
                        <td>{{ $cpl->deskripsi ?? '-' }}</td>
                        <td class="d-flex  justify-content-end gap-3">
                            <button type="button" class="btn btn-outline-warning btnEditCpl" data-id="{{ $cpl->id }}">Edit</button>

                            <button type="button" class="btn btn-danger btnDeleteCpl" data-id="{{ $cpl->id }}">Hapus</button>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-end mt-3 me-3">{{ $cpls->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>

<!-- Modal Tambah CPL -->
<div class="modal fade" id="modalTambahCpl" tabindex="-1" aria-labelledby="modalTambahCplLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> {{-- modal-lg biar lebar --}}
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalTambahCplLabel">Tambah Data Cpl</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('admin.cpl.storeCpl') }}">
            @csrf
            <div class="modal-body">
                {{-- Kode CP --}}
                <div class="mb-3">
                <label for="kode_cpl" class="form-label">Kode CP</label>
                <input type="text" class="form-control @error('kode_cpl') is-invalid @enderror" id="kode_cpl" name="kode_cpl" value="{{ old('kode_cpl') }}" required>
                @error('kode_cpl')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                </div>
                {{-- Deskripsi CPL --}}
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi CPL</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5" required></textarea>
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

<!-- Modal Edit CPL -->
<div class="modal fade" id="modalEditCpl" tabindex="-1" aria-labelledby="modalEditCplLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" id="editForm">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title">Edit Data CPL</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Kode CPL</label>
            <input type="text" class="form-control @error('kode_cpl') is-invalid @enderror" id="edit_kode_cpl" name="kode_cpl" value="{{ old('kode_cpl') }}" required>
            @error('kode_cpl')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          <div class="mb-3">
            <label class="form-label mt-4">Deskripsi</label>
            <textarea class="form-control" id="edit_deskripsi" name="deskripsi" required rows="4"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>



@endsection


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // ==== EDIT CPL ====
        document.querySelectorAll('.btnEditCpl').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                fetch(`{{ url('admin/cpl') }}/${id}/edit`)
                    .then(response => {
                        if (!response.ok) throw new Error('Gagal fetch data');
                        return response.json();
                    })
                    .then(data => {
                        document.getElementById('edit_kode_cpl').value = data.kode_cpl;
                        document.getElementById('edit_deskripsi').value = data.deskripsi;
                        document.getElementById('editForm').action = `{{ url('admin/cpl') }}/${data.id}`;
                        new bootstrap.Modal(document.getElementById('modalEditCpl')).show();
                    })
                    .catch(error => {
                        console.error("Gagal mengambil data:", error);
                        Swal.fire('Oops!', 'Terjadi kesalahan saat mengambil data.', 'error');
                    });
            });
        });

        // ==== HAPUS CPL ====
        document.querySelectorAll('.btnDeleteCpl').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Apakah kamu yakin?',
                    text: 'Data CPL ini akan dihapus permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`{{ url('admin/cpl') }}/${id}`, {
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
        const modalTambah = new bootstrap.Modal(document.getElementById('modalTambahCpl'));
        modalTambah.show();
    @endif
    });
</script>










