<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaranMatkul;
use Illuminate\Http\Request;

class TahunAjaranMatkulController extends Controller
{
    // Jika kamu ingin menampilkan semua relasi (opsional)
    public function index()
    {
        $data = TahunAjaranMatkul::with(['matkul', 'tahunAjaran'])->get();
        return view('admin.tahunajaranmatkul.index', compact('data'));
    }

    // Menyimpan data baru (biasanya dari modal matkul)
    public function store(Request $request)
    {
        $request->validate([
            'matkul_id' => 'required|exists:matkul,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'sks' => 'required|numeric|min:0',
            'semester_studi' => 'nullable|string',
        ]);


        TahunAjaranMatkul::create([
            'matkul_id' => $request->matkul_id,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'sks' => $request->sks,
            'semester_studi' => $request->semester_studi, // opsional
        ]);

        return redirect()->back()->with('success', 'Data SKS berhasil ditambahkan.');
    }
}
