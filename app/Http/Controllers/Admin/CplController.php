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
            'kode_cpl' => [
                'required',
                'regex:/^CP-\d+$/',
                'unique:cpl,kode_cpl'
            ],
            'deskripsi' => 'required',
        ], [
            'kode_cpl.required' => 'Kode CPL wajib diisi.',
            'kode_cpl.regex' => 'Format kode harus seperti CP-1, CP-2, dst.',
            'kode_cpl.unique' => 'Kode CPL sudah digunakan.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
        ]);

        Cpl::create($data);
        return redirect()->back()->with('success', 'Data CPL Berhasil Ditambahkan.');
    }

    public function editCpl($id)
    {
        $cpl = Cpl::findOrFail($id);
        return response()->json($cpl);
    }


    public function updateCpl(Request $request, $id)
    {
        $request->validate([
            'kode_cpl' => [
                'required',
                'regex:/^CP-\d+$/',
                'unique:cpl,kode_cpl,' . $id
            ],
            'deskripsi' => 'required',
        ], [
            'kode_cpl.required' => 'Kode CPL wajib diisi.',
            'kode_cpl.regex' => 'Format kode harus seperti CP-1',
            'kode_cpl.unique' => 'Kode CPL sudah digunakan.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
        ]);

        Cpl::findOrFail($id)->update($request->only('kode_cpl', 'deskripsi', 'bobot'));
        return redirect()->back()->with('success', 'Data CPL Berhasil Diperbarui.');
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
