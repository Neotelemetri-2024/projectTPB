<div class="modal fade" id="modalEditLecturer" tabindex="-1" aria-labelledby="modalEditLecturerLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="" id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditLecturerLabel">Edit Data Dosen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Nama --}}
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>

                    {{-- NIM --}}
                    <div class="mb-3">
                        <label for="edit_nip" class="form-label">NIP</label>
                        <input type="text" class="form-control" id="edit_nip" name="nip" required>
                    </div>

                    {{-- Role --}}
                    <div class="mb-3">
                        <label for="edit_role" class="form-label">Role</label>
                        <select id="edit_role" name="role" class="form-select" required>
                            {{-- <option value="mahasiswa">Mahasiswa</option> --}}
                            <option value="dosen">Dosen</option>
                            <option value="pimpinan">Pimpinan</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
