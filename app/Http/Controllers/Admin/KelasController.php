<?php

namespace App\Http\Controllers\Admin;

use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\TahunAjaranMatkul;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class KelasController extends Controller
{
    public function showByMatkul($matkulId)
    {
        // 1. Temukan Matakuliah berdasarkan ID
        // $matakuliah = Matkul::findOrFail($matkulId);

        // 2. Ambil data kelas yang terkait dengan Matakuliah ini
        //    Asumsi: Di model Matakuliah, ada relasi hasMany ke model Kelas,
        //            dan di model Kelas ada foreign key 'matakuliah_id'.
        // $daftarKelas = Kelas::where('matkul_id', $matakuliah->id)->get(); // Atau gunakan relasi langsung jika sudah didefinisikan di model Matakuliah

        // Jika kamu ingin mengambil relasi dengan tahun ajaran matkul
        // $daftarKelas = $matakuliah->tahunAjaranMatkuls()->with('tahunAjaran', 'kelas')->get();
        // Ini tergantung bagaimana relasi tahun_ajaran_matkuls didefinisikan.

        // 3. Ambil data Tahun Ajaran (jika diperlukan untuk dropdown filter)
        // $tahunAjarans = TahunAjaran::all(); // Untuk filter Tahun Ajaran di tabel

        // 4. Tampilkan view 'kelas.index' (atau 'kelas.show' jika lebih spesifik)
        //    dan kirimkan data $matakuliah, $daftarKelas, dan $tahunAjarans ke view
        // return view('admin.kelas', compact('matakuliah', 'daftarKelas', 'tahunAjarans'));
    }

    public function indexKelas(Request $request)
    {
        $query = Kelas::query();

        // Eager load semua relasi yang dibutuhkan untuk tampilan tabel
        $query->with([
            'tahunAjaranMatkul.matkul',
            'tahunAjaranMatkul.tahunAjaran',
            // 'tahunAjaranMatkul.dosenPengampus.user', // Penting untuk nama dosen
            // 'mahasiswas' // Untuk menghitung jumlah mahasiswa
        ]);

        // Filter berdasarkan Nama Kelas
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('nama_kelas', 'like', '%' . $search . '%');
        }

        // Filter berdasarkan Dosen Pengampu
        // if ($request->filled('id_dosen')) {
        //     $query->whereHas('tahunAjaranMatkul.dosenPengampus', function($q) use ($request) {
        //         $q->where('dosen.id', $request->id_dosen);
        //     });
        // }

        // Filter berdasarkan Tahun Ajaran (dari TahunAjaranMatkul -> TahunAjaran)
        if ($request->filled('tahun_ajaran_id')) {
            $query->whereHas('tahunAjaranMatkul.tahunAjaran', function($q) use ($request) {
                $q->where('id', $request->id_tahun_ajaran);
            });
        }

        // Filter berdasarkan Mata Kuliah & Tahun Ajaran Matkul (jika ada dropdown TAM)
        if ($request->filled('id_tahun_ajaran_matkul')) {
            $query->where('id_tahun_ajaran_matkul', $request->id_tahun_ajaran_matkul);
        }

        $kelas = $query->orderBy('nama_kelas', 'asc')->paginate(10);

        // // Data untuk dropdown filter di view
        // $tahunAjaranMatkulsFilter = TahunAjaranMatkul::with(['matkul', 'tahunAjaran'])
        //                             ->get()
        //                             ->sortBy(function($tam) {
        //                                 return ($tam->tahunAjaran->tahun ?? 0) . ($tam->tahunAjaran->semester == 'Ganjil' ? '1' : '2') . ($tam->matkul->nama_matkul ?? '');
        //                             });
        // $dosenFilter = Dosen::with('user')->get()->sortBy('user.nama');
        // $tahunAjaransFilter = TahunAjaran::orderBy('tahun', 'desc')->orderBy('semester', 'desc')->get();

        return view('admin.kelas', compact('kelas'));
    }

    /**
     * Show the form for creating a new resource.
     * Menampilkan form untuk membuat kelas baru.
     */
    public function create(Request $request)
    {
        // Jika datang dari halaman detail TahunAjaranMatkul, id_tahun_ajaran_matkul bisa langsung diisi
        $selectedTahunAjaranMatkulId = $request->query('id_tahun_ajaran_matkul');

        $tahunAjaranMatkuls = TahunAjaranMatkul::with(['matkul', 'tahunAjaran'])
                                ->get()
                                ->sortBy(function($tam) {
                                    return ($tam->tahunAjaran->tahun ?? 0) . ($tam->tahunAjaran->semester == 'Ganjil' ? '1' : '2') . ($tam->matkul->nama_matkul ?? '');
                                });

        return view('admin.kelas', compact('tahunAjaranMatkuls', 'selectedTahunAjaranMatkulId'));
    }

    /**
     * Store a newly created resource in storage.
     * Menyimpan kelas baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'id_tahun_ajaran_matkul' => 'required|exists:tahun_ajaran_matkuls,id',
        ]);

        $kelas = Kelas::create($validatedData);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Kelas berhasil ditambahkan!', 'data' => $kelas]);
        }

        // return redirect()->route('kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
        return redirect()->back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     * Menampilkan detail kelas tertentu.
     */
}
