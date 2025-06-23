<?php

namespace App\Http\Controllers\Admin;

use App\Models\TahunAjaran;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{

    public function indexTahunAjaran(Request $request)
    {
        $query = TahunAjaran::query();

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        $thnajaran = $query->orderBy('tahun', 'desc')->paginate(10);

        return view('admin.tahunajaran', compact('thnajaran'));
    }
    // public function indexTahunAjaran()
    // {
    //     $thnajaran = TahunAjaran::Paginate(10);
    //     return view('admin.tahunajaran', compact('thnajaran'));
    // }

    public function storeTahunAjaran(Request $request)
    {
        $data = $request->validate([
            'semester' => 'required|string|in:Ganjil,Genap',
            'tahun' => 'required|string|max:255',

        ]);
        TahunAjaran::create($data);
        return redirect()->back()->with('success', 'Data Tahun Ajaran berhasil ditambahkan.');
    }

    public function getTahunAjaran($id)
    {
        return response()->json(TahunAjaran::findOrFail($id));
    }

    public function updateTahunAjaran(Request $request, $id)
    {
        $data = $request->validate([
            'semester' => 'required',
            'tahun' => 'required',
        ]);

        TahunAjaran::findOrFail($id)->update($data);
        return back()->with('success', 'Data berhasil diperbarui.');
    }
    public function destroyTahunAjaran($id)
    {
        $ta = TahunAjaran::findOrFail($id);
        $ta->delete();

        return response()->json([
            'message' => 'Data Tahun Ajaran berhasil dihapus.'
        ]);
    }
}
