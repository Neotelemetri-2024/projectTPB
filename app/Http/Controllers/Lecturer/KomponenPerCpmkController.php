<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Komponen;
use App\Models\Cpl;
use App\Models\Cpmk;
use App\Models\Matkul;
use App\Models\MatkulCplCpmk;
use App\Models\KomponenPerCpmk;

class KomponenPerCpmkController extends Controller
{
    public function kelola($matkul_id, $cpl_id)
    {
        $cpl = Cpl::findOrFail($cpl_id);
        $matkul = Matkul::findOrFail($matkul_id);
        $matkulCplCpmk = MatkulCplCpmk::where('cpl_id', $cpl_id)
            ->where('matkul_id', $matkul_id)
            ->with(['cpmk', 'komponenPenilaian.komponen'])
            ->get();

        $komponenList = Komponen::all();

        return view('lecturer.komponen_penilaian.kelola', compact('cpl', 'matkul', 'matkulCplCpmk', 'komponenList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'komponen_id' => 'required|array',
            'komponen_id.*' => 'exists:komponen,id',
            'bobot' => 'required|array',
            'bobot.*' => 'numeric|min:0|max:100',
            'matkul_cpl_cpmk_id' => 'required|exists:matkul_cpl_cpmk,id',
        ]);

        foreach ($request->komponen_id as $index => $komponenId) {
            $cek = KomponenPerCpmk::where('matkul_cpl_cpmk_id', $request->matkul_cpl_cpmk_id)
                ->where('komponen_id', $komponenId)
                ->first();
            if ($cek) {
                return back()->withErrors(['Komponen sudah pernah ditambahkan pada CPMK ini.']);
            }

            KomponenPerCpmk::create([
                'matkul_cpl_cpmk_id' => $request->matkul_cpl_cpmk_id,
                'komponen_id' => $komponenId,
                'bobot' => $request->bobot[$index],
            ]);
        }


        return back()->with('success', 'Semua komponen penilaian berhasil ditambahkan.');
    }

    public function update(Request $request, $matkul_cpl_cpmk_id)
    {
        $request->validate([
            'komponen_id' => 'required|array',
            'komponen_id.*' => 'exists:komponen,id',
            'bobot' => 'required|array',
            'bobot.*' => 'numeric|min:0|max:100',
        ]);

        // Hapus semua komponen lama terlebih dahulu
        KomponenPerCpmk::where('matkul_cpl_cpmk_id', $matkul_cpl_cpmk_id)->delete();

        // Simpan yang baru
        foreach ($request->komponen_id as $index => $komponenId) {
            KomponenPerCpmk::create([
                'matkul_cpl_cpmk_id' => $matkul_cpl_cpmk_id,
                'komponen_id' => $komponenId,
                'bobot' => $request->bobot[$index],
            ]);
        }

        return back()->with('success', 'Komponen CPMK berhasil diperbarui.');
    }

    public function destroyByCpmk($matkul_cpl_cpmk_id)
    {
        KomponenPerCpmk::where('matkul_cpl_cpmk_id', $matkul_cpl_cpmk_id)->delete();

        return back()->with('success', 'Semua komponen pada CPMK ini berhasil dihapus.');
    }
}
