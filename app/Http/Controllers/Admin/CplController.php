<?php

namespace App\Http\Controllers\Admin;

use App\Models\Cpl;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CplController extends Controller
{
    public function indexCpl()
    {
        $cpls = Cpl::Paginate(10);
        return view('admin.cpl', compact('cpls'));
    }

    public function storeCpl(Request $request)
    {
        $data = $request->validate([
            'kode_cpl' => 'required|unique:cpl,kode_cpl',
            'deskripsi' => 'required',
            'bobot'      => 'required|integer|min:0|max:100',
        ]);
        Cpl::create($data);
        return redirect()->back()->with('success', 'Data cpl berhasil ditambahkan.');
    }

    public function editCpl($id)
    {
        $cpl = Cpl::findOrFail($id);
        return response()->json($cpl);
    }


    public function updateCpl(Request $request, $id)
    {
        $request->validate([
            'kode_cpl' => 'required|unique:cpl,kode_cpl,' . $id,
            'deskripsi' => 'required',
            'bobot' => 'required|integer|min:0|max:100',
        ]);

        Cpl::findOrFail($id)->update($request->only('kode_cpl', 'deskripsi', 'bobot'));
        return back()->with('success', 'Data CPL berhasil diperbarui.');
    }
    public function destroyCpl($id)
    {
        $getdata = Cpl::findOrFail($id);
        $getdata->delete();

        return response()->json([
            'message' => 'Data CPL berhasil dihapus.'
        ]);
    }
}
