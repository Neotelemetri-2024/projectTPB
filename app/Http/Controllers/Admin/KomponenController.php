<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Komponen;

class KomponenController extends Controller
{
    public function index()
    {
        $data = Komponen::paginate(10);
        return view('admin.komponenPenilaian', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:255']);
        Komponen::create(['nama' => $request->nama]);

        return back()->with('success', 'Komponen berhasil ditambahkan.');
    }

    public function edit($id)
    {
        return response()->json(Komponen::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['nama' => 'required|string|max:255']);
        Komponen::findOrFail($id)->update(['nama' => $request->nama]);

        return back()->with('success', 'Komponen berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Komponen::findOrFail($id)->delete();

        return response()->json(['message' => 'Komponen berhasil dihapus.']);
    }
}
