@extends('layouts.layout')
@section('title', 'Managemen CPL dan CPMK')
@section('header')
    <h2 class="fw-bold p-1">{{ $matkul->nama_matkul }}</h2>
@endsection

@section('content')
<!-- Tombol Tambah -->
<div class="my-2 d-flex justify-content-end gap-3 mb-3">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahCplCpmk">
        <i class="fa-solid fa-plus"></i> Tambah
    </button>
</div>

<!-- Tabel CPL dan CPMK -->
<div class="card">
    <div class="table-responsive text-nowrap table-striped table-bordered align-middle">
        <table class="table">
            <thead class="text-center">
                <tr>
                <th class="border border-gray-300 px-4 py-2 w-12">No</th>
                <th class="border border-gray-300 px-4 py-2 w-32">Kode CP</th>
                <th class="border border-gray-300 px-4 py-2">Kode CPMK</th>
                <th class="border border-gray-300 px-4 py-2 w-40">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-center align-top">
                @php $no = 1; @endphp
                @foreach($relasi as $cplId => $items)
                    @php 
                        $cpl = $items->first()->cpl;
                        $rowspan = count($items);
                    @endphp
                    <tr>
                        <td class="border border-gray-300 px-4 py-2 text-center align-top" rowspan="{{ $rowspan }}">{{ $no++ }}</td>
                        <td class="border border-gray-300 px-4 py-2 align-top" rowspan="{{ $rowspan }}">{{ $cpl->kode_cpl }}</td>
                        <td class="border border-gray-300 px-4 py-2"> - {{ $items[0]->cpmk->kode_cpmk }}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center align-top" rowspan="{{ $rowspan }}">
                            <div class="d-flex gap-2 justify-content-center flex-wrap">
                                <!-- Tombol Edit -->
                                <button type="button"
                                    class="btn btn-custom-edit d-flex align-items-center gap-2 px-3"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditCplCpmk"
                                    data-id="{{ $items->first()->id }}"
                                    data-cpl="{{ $cpl->id }}"
                                    data-cpmks='@json($items->pluck("cpmk.id"))'>
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </button>


                                <!-- Tombol Hapus -->
                                <button type="button"
                                    class="btn btn-custom-delete d-flex align-items-center gap-2 px-3 btn-delete-relasi"
                                    data-id="{{ $items->first()->id }}">
                                    <i class="fa-solid fa-trash-can"></i> Hapus
                                </button>


                                <!-- Tombol Kelola -->
                                <a href="{{ route('lecturer.komponen.kelola', ['matkul_id' => $matkul->id, 'cpl_id' => $cpl->id]) }}" class="btn btn-custom-manage d-flex align-items-center gap-2 px-3">
                                    <i class="fa-solid fa-folder-open"></i> Kelola
                                </a>
                            </div>
                        </td>
                                           
                    </tr>
                    @foreach($items->skip(1) as $item)
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">- {{ $item->cpmk->kode_cpmk }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah CPL dan CPMK -->
<div class="modal fade" id="modalTambahCplCpmk" tabindex="-1" aria-labelledby="modalTambahCplCpmkLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalTambahCplCpmkLabel">Tambah Data CPL & CPMK</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
            <form action="{{ route('lecturer.komponen.store', $matkul->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Dropdown CPL -->
                    <div class="mb-3">
                        <label for="cpl_id" class="form-label">Pilih CPL</label>
                        <select name="cpl_id" id="cpl_id" class="form-select" required>
                        <option value="" disabled selected>Pilih CPL</option>
                        @foreach($cpls as $cpl)
                            <option value="{{ $cpl->id }}">{{ $cpl->kode_cpl }}</option>
                        @endforeach
                        </select>
                    </div>
                    <!-- Multiselect CPMK -->
                    <div class="mb-3">
                        <label for="cpmk_ids" class="form-label">Pilih CPMK</label>
                        <select name="cpmk_ids[]" id="cpmk_ids" multiple>
                            @foreach($cpmks as $cpmk)
                                <option value="{{ $cpmk->id }}">{{ $cpmk->kode_cpmk }}</option>
                            @endforeach
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

<!-- Modal Edit CPL dan CPMK -->
<div class="modal fade" id="modalEditCplCpmk" tabindex="-1" aria-labelledby="modalEditCplCpmkLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditCplCpmkLabel">Edit Data CPL & CPMK</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formEditCplCpmk" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <input type="hidden" name="id" id="edit_id">
          <!-- CPL -->
          <div class="mb-3">
            <label for="edit_cpl_id" class="form-label">Pilih CPL</label>
            <select name="cpl_id" id="edit_cpl_id" class="form-select" required>
              <option value="" disabled>Pilih CPL</option>
              @foreach($cpls as $cpl)
                <option value="{{ $cpl->id }}">{{ $cpl->kode_cpl }}</option>
              @endforeach
            </select>
          </div>
          <!-- CPMK -->
          <div class="mb-3">
            <label for="edit_cpmk_ids" class="form-label">Pilih CPMK</label>
            <select name="cpmk_ids[]" id="edit_cpmk_ids" multiple>
              @foreach($cpmks as $cpmk)
                <option value="{{ $cpmk->id }}">{{ $cpmk->kode_cpmk }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>


@endsection


@section('scripts')
    <script src="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag@4.0.1/dist/js/multi-select-tag.min.js"></script>
    <script>
        new MultiSelectTag('cpmk_ids', {
            maxSelection: null,
            placeholder: 'Pilih CPMK',
            required: true
        });
        // Modal EDIT
        const modalEdit = document.getElementById('modalEditCplCpmk');
        modalEdit.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const cplId = button.getAttribute('data-cpl');
            const cpmkIds = JSON.parse(button.getAttribute('data-cpmks'));

            // Set action form
            const form = document.getElementById('formEditCplCpmk');
            form.action = `/lecturer/komponen/cpl-cpmk/update/${id}`; // Laravel route
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_cpl_id').value = cplId;

            // Reset pilihan dulu
            const cpmkSelect = document.getElementById('edit_cpmk_ids');
            [...cpmkSelect.options].forEach(option => {
                option.selected = false;
            });

            // Pilih CPMK yang sesuai
            [...cpmkSelect.options].forEach(option => {
                if (cpmkIds.includes(parseInt(option.value))) {
                    option.selected = true;
                }
            });
            const container = cpmkSelect.nextElementSibling;
            if (!container || !container.classList.contains('multi-select-tag')) {
                new MultiSelectTag('edit_cpmk_ids', {
                    placeholder: 'Pilih CPMK'
                });
            }

        });

            // Reset saat modal edit ditutup agar tidak dobel
    modalEdit.addEventListener('hidden.bs.modal', function () {
        const cpmkSelect = document.getElementById('edit_cpmk_ids');
        [...cpmkSelect.options].forEach(option => option.selected = false);

        // Hapus tampilan plugin jika ada
        const container = cpmkSelect.nextElementSibling;
        if (container && container.classList.contains('multi-select-tag')) {
            container.remove();
            cpmkSelect.classList.remove('multi-select-tag');
        }
    });

        // DELETE
        document.querySelectorAll('.btn-delete-relasi').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.dataset.id;

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data ini tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#aaa',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.action = `/lecturer/komponen/cpl-cpmk/delete/${id}`;
                        form.method = 'POST';

                        const csrf = document.createElement('input');
                        csrf.type = 'hidden';
                        csrf.name = '_token';
                        csrf.value = '{{ csrf_token() }}';

                        const method = document.createElement('input');
                        method.type = 'hidden';
                        method.name = '_method';
                        method.value = 'DELETE';

                        form.appendChild(csrf);
                        form.appendChild(method);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });

        // Sukses
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session("success") }}',
                timer: 1000,
                showConfirmButton: false
            });
        @endif  
        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '{{ session('error') }}',
            timer: 3000,
            showConfirmButton: false
        });
        @endif

    </script>
@endsection





