<?php

namespace App\Http\Controllers\Lecturer;


use App\Models\Cpmk;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CpmkController extends Controller
{
    public function index()
    {
        $allCpmk = Cpmk::with('matkul')->paginate(10);
        return view('lecturer.cpmk', compact('allCpmk'));
    }

    public function storeCpmk(Request $request)
    {
        $validate = $request->validate([
            'kode_cpmk' => 'required|string|max:255|unique:cpmk,kode_cpmk',
            'nama_cpmk' => 'required|string',
        ]);

        Cpmk::create([
            'kode_cpmk' => $validate['kode_cpmk'],
            'nama_cpmk' => $validate['nama_cpmk'],
        ]);

        return redirect()->back()->with('success', 'Data CPMK Berhasi Ditambahkan');
    }

    public function editCpmk($id)
    {
        $id_cpmk = Cpmk::findOrFail($id);

        return response()->json($id_cpmk);
    }

    public function updateCpmk(Request $request, $id)
    {
        $validate = $request->validate([
            'kode_cpmk' => 'required|string|max:255|unique:cpmk,kode_cpmk',
            'nama_cpmk' => 'required|string',
        ]);

        $data = Cpmk::findOrFail($id);

        $data->update([
            'kode_cpmk' => $validate['kode_cpmk'],
            'nama_cpmk' => $validate['nama_cpmk'],
        ]);
        Cpmk::findOrFail($id)->update($request->only('kode_cpmk', 'nama_cpmk'));
        return redirect()->back()->with('success', 'Data CPL Berhasil Diperbarui.');
    }

    public function destroyCpmk($id)
    {
        $id = Cpmk::findOrFail($id);

        $id->delete($id);

        return response()->json([
            'message' => 'Data CPMK Berhasil Dihapus'
        ]);
    }
}
