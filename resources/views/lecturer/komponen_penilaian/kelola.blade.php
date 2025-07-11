@extends('layouts.layout')
@section('title', 'Kelola Komponen Penilaian')
@section('header')
    <h2 class="fw-bold p-1">{{ $cpl->kode_cpl }}</h2>
@endsection

@section('content')
<!-- Tombol Tambah -->
<div class="my-2 d-flex justify-content-end gap-3 mb-3">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKomponenGlobal">
        <i class="fa-solid fa-plus"></i> Tambah
    </button>
</div>

<!-- Tabel Komponen Penilaian -->
<div class="card">
    <div class="table-responsive text-nowrap table-striped table-bordered align-middle">
        <table class="table">
            <thead class="text-center">
                <tr>
                    <th class="border border-gray-300 px-4 py-2 w-12">No</th>
                    <th class="border border-gray-300 px-4 py-2 w-32">Kode CPMK</th>
                    <th class="border border-gray-300 px-4 py-2">Nama Komponen</th>
                    <th class="border border-gray-300 px-4 py-2 w-24">Bobot (%)</th>
                    <th class="border border-gray-300 px-4 py-2 w-40">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-center align-top">
                @php $no = 1; @endphp
                @foreach($matkulCplCpmk as $item)
                    @php $komponenCount = count($item->komponenPenilaian); @endphp
                    @foreach($item->komponenPenilaian as $index => $komponen)
                        <tr>
                            @if($index == 0)
                                <td class="border px-2 py-2 align-middle text-center" rowspan="{{ $komponenCount }}">{{ $no++ }}</td>
                                <td class="border px-2 py-2 align-middle text-center" rowspan="{{ $komponenCount }}">{{ $item->cpmk->kode_cpmk }}</td>
                            @endif
                            <td class="border px-2 py-2">{{ $komponen->komponen->nama }}</td>
                            <td class="border px-2 py-2">{{ $komponen->bobot }}%</td>
                    @if($index == 0)
                        <td class="border px-2 py-2 text-center align-middle" rowspan="{{ $komponenCount }}">
                            <div class="d-flex justify-content-center flex-wrap gap-2">
                                <!-- Tombol Edit -->
                                <button type="button"
                                    class="btn btn-custom-edit d-flex align-items-center gap-2 px-3"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditKomponen"
                                    data-id="{{ $item->id }}"
                                    data-cpmk="{{ $item->cpmk->kode_cpmk }}"
                                    data-komponen='@json($item->komponenPenilaian)'>
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </button>

                                <!-- Tombol Hapus -->
                                <form action="{{ route('lecturer.komponen.deleteAllByCpmk', $item->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Yakin ingin menghapus semua komponen CPMK ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-custom-delete d-flex align-items-center gap-2 px-3">
                                        <i class="fa-solid fa-trash-can"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    @endif
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<!-- Modal Tahap 1: Pilih CPMK -->
<div class="modal fade" id="modalTambahKomponenGlobal" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog my-lg-5">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih CPMK</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <label for="pilihCPMK" class="form-label">Pilih CPMK</label>
                <select id="pilihCPMK" class="form-select">
                    <option disabled selected value="">-- Pilih CPMK --</option>
                    @foreach($matkulCplCpmk as $item)
                        <option value="{{ $item->id }}">{{ $item->cpmk->kode_cpmk }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary w-100" id="btnSelanjutnya">Selanjutnya</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tahap 2: Pilih Komponen dan Bobot -->
<div class="modal fade" id="modalKomponenBobot" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="formTambahKomponen" action="">
            @csrf
            <input type="hidden" name="matkul_cpl_cpmk_id" id="hiddenCpmkId">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title m-0">Komponen</h5>
                    <div class="d-flex align-items-center gap-2">
                        <!-- Tombol Tambah -->
                        <button type="button" class="btn btn-primary btn-sm" id="btnTambahBaris" title="Tambah Baris">
                            <i class="fa fa-plus"></i>
                        </button>

                        <!-- Tombol Tutup -->
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                </div>
                <div class="modal-body">
                    <div id="containerKomponen">
                        <div class="row mb-2">
                            <div class="col-md-7">
                                <select name="komponen_id[]" class="form-select" required>
                                    <option value="" disabled selected>-- Pilih Komponen --</option>
                                    @foreach ($komponenList as $komponen)
                                        <option value="{{ $komponen->id }}">{{ $komponen->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="number" name="bobot[]" class="form-control" placeholder="Bobot (%)" required min="0" max="100">
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="w-100 gap-4 d-flex justify-content-between align-items-center">
                        <!-- Tombol Kembali (Outline) -->
                        <button type="button" class="btn btn-outline-newprimary w-100" data-bs-dismiss="modal">
                            Kembali
                        </button>
                        <!-- Tombol Simpan -->
                        <button type="submit" class="btn btn-primary w-100">
                            Simpan
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
<!-- Modal Edit Komponen -->
<div class="modal fade" id="modalEditKomponen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="formEditKomponen" action="">
            @csrf
            @method('PUT')
            <input type="hidden" name="matkul_cpl_cpmk_id" id="editCpmkId">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Komponen untuk <span id="editKodeCPMK"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body" id="editContainerKomponen">
                    <!-- Akan diisi via JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>



@endsection

@section('scripts')
<script>
    let selectedCPMKId = null;

    document.getElementById('btnSelanjutnya').addEventListener('click', function () {
        selectedCPMKId = document.getElementById('pilihCPMK').value;
        if (!selectedCPMKId) return alert('Silakan pilih CPMK terlebih dahulu.');
        // Isi input hidden
        document.getElementById('hiddenCpmkId').value = selectedCPMKId;
        // Set action form
        document.getElementById('formTambahKomponen').action = `/lecturer/komponen/cpmk/${selectedCPMKId}/store`;

        // Tampilkan modal kedua
        let modal1 = bootstrap.Modal.getInstance(document.getElementById('modalTambahKomponenGlobal'));
        modal1.hide();
        new bootstrap.Modal(document.getElementById('modalKomponenBobot')).show();
    });

    document.getElementById('btnTambahBaris').addEventListener('click', function () {
        const container = document.getElementById('containerKomponen');
        const baris = `
        <div class="row mb-2">
            <div class="col-md-7">
                <select name="komponen_id[]" class="form-select" required>
                    <option value="" disabled selected>-- Pilih Komponen --</option>
                    @foreach ($komponenList as $komponen)
                        <option value="{{ $komponen->id }}">{{ $komponen->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <input type="number" name="bobot[]" class="form-control" placeholder="Bobot (%)" required min="0" max="100">
            </div>
        </div>`;
        container.insertAdjacentHTML('beforeend', baris);
    });
    
    const komponenMaster = @json($komponenList);

    document.querySelectorAll('[data-bs-target="#modalEditKomponen"]').forEach(button => {
        button.addEventListener('click', function () {
            const cpmkId = this.dataset.id;
            const kodeCpmk = this.dataset.cpmk;
            const komponenList = JSON.parse(this.dataset.komponen);

            document.getElementById('editCpmkId').value = cpmkId;
            document.getElementById('editKodeCPMK').textContent = kodeCpmk;
            document.getElementById('formEditKomponen').action = `/lecturer/komponen/cpmk/${cpmkId}/update`;

            let html = '';
            komponenList.forEach(item => {
                html += `
                <div class="row mb-2">
                    <div class="col-md-7">
                        <select name="komponen_id[]" class="form-select" required>
                            <option value="" disabled>-- Pilih Komponen --</option>`;

                komponenMaster.forEach(k => {
                    html += `<option value="${k.id}" ${k.id == item.komponen_id ? 'selected' : ''}>${k.nama}</option>`;
                });

                html += `</select>
                    </div>
                    <div class="col-md-4">
                        <input type="number" name="bobot[]" class="form-control" value="${item.bobot}" required min="0" max="100">
                    </div>
                </div>`;
            });

            document.getElementById('editContainerKomponen').innerHTML = html;
        });
    });
</script>
@endsection

