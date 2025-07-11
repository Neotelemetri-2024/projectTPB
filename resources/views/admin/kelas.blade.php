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
    <!-- Tombol Tambah -->
    <div class="my-2 d-flex  justify-content-end gap-3 mb-3">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahMatkul"> <i
                class="fa-solid fa-plus"></i> Tambah </button>
    </div>

    <!-- Basic Bootstrap Table -->
    <div class="card">
        {{-- <pre>{{ $matkuls }}</pre> --}}
        <div class="table-responsive text-nowrap table-striped table-bordered align-middle">
            <table class="table">
                <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th>Nama Kelas</th>
                        <th>Dosen Pengampu</th>
                        <th>Semester</th>
                        <th>Jumlah Mahasiswa</th>
                        {{-- <th>Semester</th> --}}
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @php $counter = 1 + ($kelas->currentPage() - 1) * $kelas->perPage(); @endphp
                    @forelse ($kelas as $kls)
                        <tr>
                            <td>{{ $counter++ }}</td>
                            <td>{{ $kls->nama ?? '-' }}</td> {{-- Langsung dari objek Kelas --}}
                        <td>
                            {{-- Akses dosen melalui relasi tahunAjaranMatkul lalu dosenPengampus --}}
                            {{-- @forelse ($kls->tahunAjaranMatkul->dosenPengampus as $dosen)
                                {{ $dosen->user->nama ?? $dosen->nip ?? 'N/A' }}<br>
                            @empty
                                -
                            @endforelse --}}
                        </td>
                        {{-- Tahun Ajaran (dari TahunAjaranMatkul -> TahunAjaran) --}}
                        <td>{{ $kls->tahunAjaranMatkul->tahunAjaran->tahun ?? '-' }}</td>
                        {{-- Semester (dari TahunAjaranMatkul -> TahunAjaran) --}}
                        <td>{{ $kls->tahunAjaranMatkul->tahunAjaran->semester ?? '-' }}</td>
                        {{-- Jumlah Mahasiswa (dari relasi mahasiswas) --}}
                        {{-- <td>
                            <span class="jumlah-mahasiswa">{{ $kls->mahasiswas->count() }}</span>
                        </td> --}}
                            <td class="d-flex justify-content-end gap-3">
                                <button type="button" class="btn btn-outline-warning btnEditMatkul" data-bs-toggle="modal"
                                    data-bs-target="#modalEditMatkul" data-id=" ">Edit</button>
                                <button type="button" class="btn btn-danger-light btnDeleteMatkul"
                                    data-id=" ">Hapus</button>
                                {{-- <a href="{{ route('admin.kelas', $pivot->matkul->id) }}" class="btn btn-blue">
                                    Kelas
                                </a> --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-end mt-3 me-3">{{ $kelas->links('pagination::bootstrap-5') }}
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
                                {{-- @foreach ($tahunAjarans as $tahun)
                                    <option value="{{ $tahun->id }}">{{ $tahun->tahun }} - {{ $tahun->semester }}
                                    </option>
                                @endforeach --}}
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
