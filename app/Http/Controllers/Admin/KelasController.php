<?php

namespace App\Http\Controllers\Admin;

use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\TahunAjaranMatkul;
use App\Models\Matkul;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class KelasController extends Controller
{
    public function showByMatkul($matkulId)
    {
        // 1. Temukan Matakuliah berdasarkan ID
        $matakuliah = Matkul::findOrFail($matkulId);

        // 2. Ambil data kelas yang terkait dengan Matakuliah ini
        //    Asumsi: Di model Matakuliah, ada relasi hasMany ke model Kelas,
        //            dan di model Kelas ada foreign key 'matakuliah_id'.
        $daftarKelas = Kelas::where('matkul_id', $matakuliah->id)->get(); // Atau gunakan relasi langsung jika sudah didefinisikan di model Matakuliah

        // Jika kamu ingin mengambil relasi dengan tahun ajaran matkul
        // $daftarKelas = $matakuliah->tahunAjaranMatkuls()->with('tahunAjaran', 'kelas')->get();
        // Ini tergantung bagaimana relasi tahun_ajaran_matkuls didefinisikan.

        // 3. Ambil data Tahun Ajaran (jika diperlukan untuk dropdown filter)
        $tahunAjarans = TahunAjaran::all(); // Untuk filter Tahun Ajaran di tabel

        // 4. Tampilkan view 'kelas.index' (atau 'kelas.show' jika lebih spesifik)
        //    dan kirimkan data $matakuliah, $daftarKelas, dan $tahunAjarans ke view
        return view('admin.kelas', compact('matakuliah', 'daftarKelas', 'tahunAjarans'));
    }
}
