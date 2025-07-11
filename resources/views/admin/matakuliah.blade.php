@extends('layouts.layout')
@section('title', 'Management User')
@section('header')
    <h5 class="mb-4">Mata Kuliah</h5>
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
    <div class="my-2 d-flex  justify-content-end gap-3 mb-3">
        <!-- Tombol Filter -->
        <div id="btnFilter" class="btn btn-outline-warning" style="cursor: pointer;">
            Filter
        </div>
        <!-- Tombol Tambah -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahMatkul"> <i
                class="fa-solid fa-plus"></i> Tambah </button>
    </div>

    <!-- Dropdown Filter -->
    <form id="filterDropdown" method="GET" action="{{ route('admin.matakuliah') }}" class="card shadow position-absolute"
        style="display: none; width: 230px; top: 50px; right: 0; border-radius: 8px; padding: 12px 16px; gap: 8px; z-index: 1000;">
        <div class="form-check mb-2">
            <label class="form-check-label d-flex justify-content-between align-items-center w-100" for="filterAll">
                <span>Semua</span>
                <input class="form-check-input" type="radio" name="jenis" value="" id="filterAll"
                    onchange="this.form.submit()" {{ request('jenis') == '' ? 'checked' : '' }}>
            </label>
        </div>
        <div class="form-check mb-2">
            <label class="form-check-label d-flex justify-content-between align-items-center w-100">
                <span>Wajib</span>
                <input class="form-check-input" type="radio" name="jenis" value="Wajib" onchange="this.form.submit()"
                    {{ request('jenis') == 'Wajib' ? 'checked' : '' }}>
            </label>
        </div>
        <div class="form-check">
            <label class="form-check-label d-flex justify-content-between align-items-center w-100">
                <span>Pilihan</span>
                <input class="form-check-input" type="radio" name="jenis" value="Pilihan" onchange="this.form.submit()"
                    {{ request('jenis') == 'Pilihan' ? 'checked' : '' }}>
            </label>
        </div>
    </form>

    <!-- Basic Bootstrap Table -->
    <div class="card">
        {{-- <pre>{{ $matkuls }}</pre> --}}
        <div class="table-responsive text-nowrap table-striped table-bordered align-middle">
            <table class="table">
                <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th>Kode Matakuliah</th>
                        <th>Nama Matakuliah</th>
                        <th>Status</th>
                        <th>Tahun Ajaran</th>
                        {{-- <th>Semester</th> --}}
                        <th>SKS</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @php $counter = 1 + ($tahunAjaranMatkuls->currentPage() - 1) * $tahunAjaranMatkuls->perPage(); @endphp
                    @foreach ($tahunAjaranMatkuls as $pivot)
                        <tr>
                            <td>{{ $counter++ }}</td>
                            <td>{{ $pivot->matkul->kode_matkul ?? '-' }}</td>
                            <td>{{ $pivot->matkul->nama_matkul ?? '-' }}</td>
                            <td>{{ $pivot->matkul->jenis ?? '-' }}</td>
                            {{-- <td>{{ $pivot->$tahunAjaran->tahun . ' ' . $pivot->$tahunAjaran->tahun ?? '-' }}</td> --}}
                            <td>{{ $pivot->tahunAjaran->semester . ' ' . $pivot->tahunAjaran->tahun ?? '-' }}</td>
                            {{-- <td>{{ $pivot->semester_studi ?? '-' }}</td>  --}}
                            <td>{{ $pivot->sks ?? '-' }}</td>
                            <td class="d-flex justify-content-end gap-3">
                                <button type="button" class="btn btn-outline-warning btnEditMatkul" data-bs-toggle="modal"
                                    data-bs-target="#modalEditMatkul" data-id="{{ $pivot->matkul->id }}">Edit</button>
                                <button type="button" class="btn btn-danger-light btnDeleteMatkul"
                                    data-id="{{ $pivot->matkul->id }}">Hapus</button>
                                <a href="{{ route('admin.kelas', $pivot->matkul->id) }}" class="btn btn-blue">
                                    Kelas
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-end mt-3 me-3">{{ $tahunAjaranMatkuls->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <!-- Modal Tambah Matkul -->
    <div class="modal fade" id="modalTambahMatkul" tabindex="-1" aria-labelledby="modalTambahMatkulLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg"> {{-- modal-lg biar lebar --}}
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahMatkulLabel">Tambah Data Matakuliah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('admin.matakuliah.storematkul') }}">
                    @csrf
                    <div class="modal-body">
                        {{-- Tahun Ajaran --}}
                        <div class="mb-3">
                            <label for="tahun_ajaran_id" class="form-label">Tahun Ajaran</label>
                            <select class="form-select" id="tahun_ajaran_id" name="tahun_ajaran_id" required>
                                <option value="" disabled selected>Pilih Tahun Ajaran</option>
                                @foreach ($tahunAjarans as $tahun)
                                    <option value="{{ $tahun->id }}">{{ $tahun->tahun }} - {{ $tahun->semester }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Kode Matkul --}}
                        <div class="mb-3">
                            <label for="kode_matkul" class="form-label">kode Matakuliah</label>
                            <input type="text" class="form-control" id="kode_matkul" name="kode_matkul" required>
                        </div>
                        {{-- Nama Matkul --}}
                        <div class="mb-3">
                            <label for="nama_matkul" class="form-label">Nama Matakuliah</label>
                            <input type="text" class="form-control" id="nama_matkul" name="nama_matkul" required>
                        </div>

                        {{-- Jenis Matkul --}}
                        <div class="mb-3">
                            <label for="jenis" class="form-label">Status Matakuliah</label>
                            <select class="form-select" id="jenis" name="jenis" required>
                                <option value="" disabled selected>Pilih Status Matakuliah</option>
                                <option value="Wajib">Wajib</option>
                                <option value="Pilihan">Pilihan</option>
                            </select>

                            {{-- SKS --}}
                            <div class="mb-3">
                                <label for="sks" class="form-label">SKS</label>
                                <input type="number" class="form-control" id="sks" name="sks" required>
                            </div>

                            {{-- Semester Studi --}}
                            <div class="mb-3">
                                <label for="semester_studi" class="form-label">Semester</label>
                                <input type="number" class="form-control" id="semester_studi" name="semester_studi"
                                    required>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Matkul -->
    <div class="modal fade" id="modalEditMatkul" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" id="formEdit">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Mata Kuliah</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_id" name="id">

                        {{-- ------------------------------ --}}
                        <div class="mb-3">
                            <label for="edit-kode_matkul" class="form-label">Kode Matakuliah</label>
                            <input type="text" class="form-control" id="edit-kode_matkul" name="kode_matkul">
                        </div>

                        <div class="mb-3">
                            <label for="edit-nama_matkul" class="form-label">Nama Matakuliah</label>
                            <input type="text" class="form-control" id="edit-nama_matkul" name="nama_matkul">
                        </div>

                        <div class="mb-3">
                            <label for="edit-sks" class="form-label">SKS</label>
                            <input type="text" class="form-control" id="edit-sks" name="sks">
                        </div>

                        <div class="mb-3">
                            <label for="edit-jenis" class="form-label">Status Matakuliah</label>
                            <select class="form-select" id="edit-jenis" name="jenis" required>
                                <option value="Wajib">Wajib</option>
                                <option value="Pilihan">Pilihan</option>
                            </select>
                        </div>
                        {{-- ------------------ --}}
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
    document.addEventListener("DOMContentLoaded", function() {
        // ==== EDIT MATA KULIAH ====
        document.querySelectorAll('.btnEditMatkul').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                fetch(`/admin/matakuliah/${id}/get`)
                    .then(response => {
                        if (!response.ok) throw new Error('Gagal ambil data');
                        return response.json();
                    })
                    .then(data => {
                        document.getElementById('edit_id').value = data.id;
                        document.getElementById('edit-kode_matkul').value = data
                            .kode_matkul;
                        document.getElementById('edit-nama_matkul').value = data
                            .nama_matkul;
                        document.getElementById('edit-jenis').value = data.jenis;
                        document.getElementById('edit-sks').value = data.sks;

                        // set form action ke route update
                        document.getElementById('formEdit').action =
                            `/admin/matakuliah/${data.id}`;
                    })
                    .catch(error => {
                        console.error('Gagal:', error);
                        Swal.fire('Oops!', 'Gagal ambil data dari server.', 'error');
                    });
            });
        });

        // ==== HAPUS Matkul ====
        document.querySelectorAll('.btnDeleteMatkul').forEach(button => {
            button.addEventListener('click', function() {
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
                        fetch(`/admin/matakuliah/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(response => {
                                if (!response.ok) throw new Error(
                                    'Gagal menghapus');
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
                                Swal.fire('Oops',
                                    'Terjadi kesalahan saat menghapus.', 'error'
                                );
                            });
                    }
                });
            });
        });
        // ==== FILTER ====
        const btnFilter = document.getElementById('btnFilter');
        const dropdown = document.getElementById('filterDropdown');

        btnFilter.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.style.display = (dropdown.style.display === 'none' || dropdown.style.display ===
                '') ? 'block' : 'none';
        });

        document.addEventListener('click', function(e) {
            if (!btnFilter.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    });
</script>
