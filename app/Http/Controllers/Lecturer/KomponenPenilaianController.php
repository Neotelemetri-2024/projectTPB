<?php

namespace App\Http\Controllers\Lecturer;

use App\Models\Cpl;
use App\Models\Cpmk;
use App\Models\Matkul;
use App\Models\CplCpmk;
use App\Models\MatkulCplCpmk;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KomponenPenilaianController extends Controller
{
    public function index()
    {
        $matkuls = Matkul::with('tahunAjaranMatkuls.tahunAjaran')->paginate(10);
        return view('lecturer.komponen_penilaian.index', compact('matkuls'));
    }
    public function show($matkul_id)
    {
        $matkul = Matkul::findOrFail($matkul_id);

        $relasi = MatkulCplCpmk::with(['cpl', 'cpmk'])
            ->where('matkul_id', $matkul_id)
            ->get()
            ->groupBy('cpl_id');

        $cpls = Cpl::all();
        $cpmks = Cpmk::all();

        return view('lecturer.komponen_penilaian.show', compact('matkul', 'relasi', 'cpls', 'cpmks'));
    }

    // Menyimpan hubungan CPL dan CPMK di tabel pivot
    public function storeCplCpmk(Request $request, $matkul_id)
    {
        $validated = $request->validate([
            'cpl_id' => 'required|exists:cpl,id',
            'cpmk_ids' => 'required|array',
        ]);

        foreach ($request->cpmk_ids as $cpmk_id) {
            $exists = MatkulCplCpmk::where([
                'matkul_id' => $matkul_id,
                'cpl_id' => $request->cpl_id,
                'cpmk_id' => $cpmk_id,
            ])->exists();

            if (!$exists) {
                MatkulCplCpmk::create([
                    'matkul_id' => $matkul_id,
                    'cpl_id' => $request->cpl_id,
                    'cpmk_id' => $cpmk_id,
                ]);
            }
        }

        return redirect()->route('lecturer.komponen.show', $matkul_id)->with('success', 'CPL dan CPMK berhasil ditambahkan!');
    }

    public function editCplCpmk($id)
    {
        // Mencari data pivot berdasarkan ID
        $cplCpmk = CplCpmk::findOrFail($id);

        // Mengambil data CPL dan CPMK untuk form edit
        $cpls = Cpl::all();
        $cpmks = Cpmk::all();

        return view('lecturer.komponen_penilaian.edit', compact('cplCpmk', 'cpls', 'cpmks'));
    }

    public function updateCplCpmk(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'cpl_id' => 'required|exists:cpl,id',
            'cpmk_ids' => 'required|array',
        ]);

        // Ambil data lama
        $relasiLama = MatkulCplCpmk::findOrFail($id);
        $matkul_id = $relasiLama->matkul_id;
        $cplBaru = $request->cpl_id;
        $cpmkBaru = $request->cpmk_ids;

        // CEK apakah relasi baru sudah ada di database (selain relasi yang sedang diupdate)
        foreach ($cpmkBaru as $cpmk_id) {
            $duplikat = MatkulCplCpmk::where('matkul_id', $matkul_id)
                ->where('cpl_id', $cplBaru)
                ->where('cpmk_id', $cpmk_id)
                ->where('id', '!=', $id) // abaikan relasi yang sedang diupdate
                ->exists();

            if ($duplikat) {
                return redirect()->back()->with('error', 'Data CPL yang kamu masukkan sudah ada!');
            }
        }

        // Jika aman, hapus semua relasi lama untuk ID ini
        MatkulCplCpmk::where('id', $id)->delete();

        // Simpan relasi baru (bisa lebih dari 1 karena multiselect)
        foreach ($cpmkBaru as $cpmk_id) {
            MatkulCplCpmk::create([
                'matkul_id' => $matkul_id,
                'cpl_id'    => $cplBaru,
                'cpmk_id'   => $cpmk_id
            ]);
        }

        return redirect()->route('lecturer.komponen.show', $matkul_id)
            ->with('success', 'Relasi CPL dan CPMK berhasil diperbarui!');
    }


    public function deleteCplCpmk($id)
    {
        $relasi = MatkulCplCpmk::findOrFail($id);
        $matkul_id = $relasi->matkul_id;

        $relasi->delete();

        return redirect()->route('lecturer.komponen.show', $matkul_id)
            ->with('success', 'Relasi CPL dan CPMK berhasil dihapus!');
    }
}
