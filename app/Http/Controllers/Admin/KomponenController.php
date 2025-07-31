<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Komponen;

class KomponenController extends Controller
{
    public function index()
    {
        $komponen = Komponen::orderBy('id', 'asc')->get();
        return view('admin.komponen.index', compact('komponen'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
        ]);
        Komponen::create([
            'nama' => $request->nama,
        ]);
        return redirect()->back()->with('success', 'Komponen berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
        ]);
        $komponen = Komponen::findOrFail($id);
        $komponen->update([
            'nama' => $request->nama,
        ]);
        return redirect()->back()->with('success', 'Komponen berhasil diupdate.');
    }

    public function destroy($id)
    {
        $komponen = Komponen::findOrFail($id);
        $komponen->delete();
        return redirect()->back()->with('success', 'Komponen berhasil dihapus.');
    }
} 