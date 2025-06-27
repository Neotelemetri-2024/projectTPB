<?php

namespace App\Http\Controllers\Admin;

use App\Models\Matkul;
use App\Http\Controllers\Controller;
use App\Models\TahunAjaranMatkul;
use Illuminate\Http\Request;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class MatkulController extends Controller
{
    // public function indexMatkul(Request $request)
    // {

    //     // Ambil semua matkul dan relasi tahun ajarannya (beserta sks)
    //     $query = Matkul::with('tahunAjaranMatkuls.tahunAjaran');

    //     // return view('matkul.index', compact('matkuls'));

    //     // $query = Matkul::query();
    //     // Filter berdasarkan Jenis Mata Kuliah (Wajib/Pilihan)
    //     if ($request->filled('jenis_matkul')) {
    //         $jenisMatkul = $request->input('jenis_matkul');
    //         $query->where('jenis', $jenisMatkul);
    //     }

    //     // Filter berdasarkan Kode atau Nama Mata Kuliah (pencarian teks)
    //     if ($request->filled('search')) {
    //         $search = $request->input('search');
    //         $query->where(function ($q) use ($search) {
    //             $q->where('kode_matkul', 'like', '%' . $search . '%')
    //                 ->orWhere('nama_matkul', 'like', '%' . $search . '%');
    //         });
    //     }

    //     // Urutkan hasil
    //     // $query->orderBy('nama_matkul', 'asc');
    //     $matkuls = $query->orderBy('nama_matkul', 'asc')->paginate(10);

    //     // // Paginasi hasil
    //     // $matkuls = $query->paginate(10);
    //     $tahunAjarans = TahunAjaran::all();

    //     return view('admin.matakuliah', compact('matkuls', 'tahunAjarans'));
    // }

    public function indexMatkul(Request $request)
    {
        $query = TahunAjaranMatkul::with(['matkul', 'tahunAjaran']);

        if ($request->filled('jenis')) {
            $query->whereHas('matkul', function ($q) use ($request) {
                $q->where('jenis', $request->input('jenis'));
            });
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('matkul', function ($q) use ($search) {
                $q->where('kode_matkul', 'like', '%' . $search . '%')
                    ->orWhere('nama_matkul', 'like', '%' . $search . '%');
            });
        }

        $tahunAjaranMatkuls = $query->orderBy('created_at', 'desc')->paginate(10);
        $tahunAjarans = TahunAjaran::all();

        return view('admin.matakuliah', compact('tahunAjaranMatkuls', 'tahunAjarans'));
    }

    /**
     * Show the form for creating a new resource.
     * Menampilkan form untuk membuat mata kuliah baru.
     */
    // public function create()
    // {
    //     return view('matkuls.create');
    // }

    /**
     * Store a newly created resource in storage.
     * Menyimpan mata kuliah baru ke database.
     */
    public function store(Request $request)
    {

        try {
            // dd($request->all());
            $request->validate([
                'kode_matkul' => 'required|string|max:255|unique:matkuls,kode_matkul',
                'nama_matkul' => 'required|string|max:255',
                'jenis' => ['required', Rule::in(['Wajib', 'Pilihan'])],
                'sks' => 'required',
                'semester_studi' => 'required',
                'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id', // Pastikan id_tahun_ajaran valid
            ]);
            // Validasi data yang diterima

            // Simpan ke tabel matkul

            Log::info('Request data: ', $request->all());
            $matkul = Matkul::create([
                'kode_matkul' => $request->kode_matkul,
                'nama_matkul' => $request->nama_matkul,
                'jenis' => $request->jenis,
            ]);

            // // Simpan ke tabel tahun_ajaran_matkul (pivot)
            TahunAjaranMatkul::create([
                'matkul_id' => $matkul->id,
                'tahun_ajaran_id' => $request->tahun_ajaran_id,
                'sks' => $request->sks,
                'semester_studi' => $request->semester_studi,
            ]);
            // Matkul::create($data);

        } catch (\Exception $e) {
            Log::error('Error storing matkul: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan data mata kuliah. Pastikan semua field terisi dengan benar.');
        }


        return redirect()->back()->with('success', 'Data Tahun Ajaran berhasil ditambahkan.');
    }

    public function getMatkul($id)
    {
        // $matkul = Matkul::findOrFail($id);
        // return response()->json($matkul);

        $matkul = Matkul::with(['tahunAjaranMatkuls' => function ($q) {
            $q->latest()->limit(1); // ambil SKS terakhir
        }])->findOrFail($id);

        $data = [
            'id' => $matkul->id,
            'kode_matkul' => $matkul->kode_matkul,
            'nama_matkul' => $matkul->nama_matkul,
            'jenis' => $matkul->jenis,
            'sks' => optional($matkul->tahunAjaranMatkuls->first())->sks, // ambil nilai sks dari relasi
        ];

        return response()->json($data);
        // return response()->json(Matkul::findOrFail($id));
    }

    public function updateMatkul(Request $request, $id)
    {
        $data = $request->validate([
            'kode_matkul' => 'required',
            'nama_matkul' => 'required',
            'jenis' => 'required',
            'sks' => 'required|numeric|min:0',
        ]);

        // Matkul::findOrFail($id)->update($data);
        $matkul = Matkul::findOrFail($id);
        $matkul->update($request->only(['kode_matkul', 'nama_matkul', 'jenis']));

        // update sks di tabel tahun_ajaran_matkuls (ambil tahun ajaran terakhir)
        $relasi = TahunAjaranMatkul::where('matkul_id', $id)->latest()->first();
        if ($relasi) {
            $relasi->sks = $request->sks;
            $relasi->save();
        }
        return back()->with('success', 'Data berhasil diperbarui.');
    }

    public function destroyMatkul($id)
    {
        // $matkul = Matkul::findOrFail($id);
        // $matkul->delete();

        // return response()->json([
        //     'message' => 'Data Mata Kuliah berhasil dihapus.'
        // ]);

        try {
            // Hapus relasi SKS di tabel pivot tahun_ajaran_matkuls
            TahunAjaranMatkul::where('matkul_id', $id)->delete();

            // Hapus data mata kuliah
            Matkul::findOrFail($id)->delete();

            // return redirect()->back()->with('success', 'Data mata kuliah berhasil dihapus.');
            return response()->json(['message' => 'Mata Kuliah berhasil dihapus']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
