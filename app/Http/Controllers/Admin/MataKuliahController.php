<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataKuliah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MataKuliahController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }
    
    public function index(Request $request)
    {
        $query = MataKuliah::query();

        // Search by name or code
        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function($sub) use ($searchTerm) {
                $sub->where('namaMatkul', 'like', "%{$searchTerm}%")
                    ->orWhere('kodeMatkul', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by type
        if ($request->filled('jenis') && in_array($request->jenis, ['wajib', 'pilihan'])) {
            $query->where('jenis', $request->jenis);
        }

        // Filter by SKS
        if ($request->filled('sks')) {
            $query->where('sks', $request->sks);
        }

        // Sort
        $sortField = $request->filled('sort') && in_array($request->sort, ['kodeMatkul', 'namaMatkul', 'sks']) 
            ? $request->sort 
            : 'namaMatkul';
        $sortDir = $request->filled('dir') && in_array($request->dir, ['asc', 'desc']) 
            ? $request->dir 
            : 'asc';
        
        $query->orderBy($sortField, $sortDir);

        $mataKuliah = $query->paginate(10)->withQueryString();
        
        return view('admin.mata-kuliah.index', compact('mataKuliah'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kodeMatkul' => 'required|string|max:20|unique:mata_kuliah,kodeMatkul',
            'namaMatkul' => 'required|string|max:255',
            'jenis' => 'required|string|max:50',
            'sks' => 'required|integer|min:1|max:6',
        ], [
            'kodeMatkul.required' => 'Kode mata kuliah wajib diisi',
            'kodeMatkul.max' => 'Kode mata kuliah maksimal 20 karakter',
            'kodeMatkul.unique' => 'Kode mata kuliah sudah terdaftar',
            'namaMatkul.required' => 'Nama mata kuliah wajib diisi',
            'namaMatkul.max' => 'Nama mata kuliah maksimal 255 karakter',
            'jenis.required' => 'Jenis mata kuliah wajib diisi',
            'jenis.max' => 'Jenis mata kuliah maksimal 50 karakter',
            'sks.required' => 'SKS wajib diisi',
            'sks.integer' => 'SKS harus berupa angka',
            'sks.min' => 'SKS minimal 1',
            'sks.max' => 'SKS maksimal 6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            MataKuliah::create([
                'kodeMatkul' => $request->kodeMatkul,
                'namaMatkul' => $request->namaMatkul,
                'jenis' => $request->jenis,
                'sks' => $request->sks,
            ]);

            return redirect()->route('admin.mata-kuliah.index')
                ->with('success', 'Mata kuliah berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(MataKuliah $mataKuliah)
    {
        return response()->json([
            'status' => 'success',
            'data' => $mataKuliah
        ]);
    }

    public function update(Request $request, MataKuliah $mataKuliah)
    {
        $validator = Validator::make($request->all(), [
            'kodeMatkul' => 'required|string|max:20|unique:mata_kuliah,kodeMatkul,' . $mataKuliah->id,
            'namaMatkul' => 'required|string|max:255',
            'jenis' => 'required|string|max:50',
            'sks' => 'required|integer|min:1|max:6',
        ], [
            'kodeMatkul.required' => 'Kode mata kuliah wajib diisi',
            'kodeMatkul.max' => 'Kode mata kuliah maksimal 20 karakter',
            'kodeMatkul.unique' => 'Kode mata kuliah sudah terdaftar',
            'namaMatkul.required' => 'Nama mata kuliah wajib diisi',
            'namaMatkul.max' => 'Nama mata kuliah maksimal 255 karakter',
            'jenis.required' => 'Jenis mata kuliah wajib diisi',
            'jenis.max' => 'Jenis mata kuliah maksimal 50 karakter',
            'sks.required' => 'SKS wajib diisi',
            'sks.integer' => 'SKS harus berupa angka',
            'sks.min' => 'SKS minimal 1',
            'sks.max' => 'SKS maksimal 6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $mataKuliah->update([
                'kodeMatkul' => $request->kodeMatkul,
                'namaMatkul' => $request->namaMatkul,
                'jenis' => $request->jenis,
                'sks' => $request->sks,
            ]);

            return redirect()->route('admin.mata-kuliah.index')
                ->with('success', 'Mata kuliah berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(MataKuliah $mataKuliah)
    {
        try {
            $mataKuliah->delete();
            
            return redirect()->route('admin.mata-kuliah.index')
                ->with('success', 'Mata kuliah berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
} 