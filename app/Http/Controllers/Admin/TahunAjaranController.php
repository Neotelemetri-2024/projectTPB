<?php

namespace App\Http\Controllers\Admin;

use App\Models\TahunAjaran;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    public function indexTahunAjaran()
    {
        $thnajaran = TahunAjaran::Paginate(10);
        return view('admin.tahunajaran', compact('thnajaran'));
    }

    public function storeTahunAjaran(Request $request)
    {
        $data = $request->validate([
            'semester' => 'required|string|in:Ganjil,Genap',
            'tahun' => 'required|string|max:255',
            
        ]);
        TahunAjaran::create($data);
        return redirect()->back()->with('success', 'Data Tahun Ajaran berhasil ditambahkan.');
    }

}
