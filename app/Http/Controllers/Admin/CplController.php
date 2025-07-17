<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cpl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CplController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = Cpl::with('cpmk');

        // Search by code or description
        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function($sub) use ($searchTerm) {
                $sub->where('kodeCpl', 'like', "%{$searchTerm}%")
                    ->orWhere('deskripsi', 'like', "%{$searchTerm}%");
            });
        }

        // Sort
        $sortField = $request->filled('sort') && in_array($request->sort, ['kodeCpl', 'deskripsi'])
            ? $request->sort
            : 'kodeCpl';
        $sortDir = $request->filled('dir') && in_array($request->dir, ['asc', 'desc'])
            ? $request->dir
            : 'asc';

        $query->orderBy($sortField, $sortDir);

        $cpl = $query->paginate(10)->appends(request()->query());

        return view('admin.cpl.index', compact('cpl'));
    }

    public function create()
    {
        return view('admin.cpl.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kodeCpl' => 'required|string|max:20|unique:cpl,kodeCpl',
            'deskripsi' => 'required|string',
        ], [
            'kodeCpl.required' => 'Kode CPL wajib diisi',
            'kodeCpl.max' => 'Kode CPL maksimal 20 karakter',
            'kodeCpl.unique' => 'Kode CPL sudah terdaftar',
            'deskripsi.required' => 'Deskripsi CPL wajib diisi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            Cpl::create([
                'kodeCpl' => $request->kodeCpl,
                'deskripsi' => $request->deskripsi,
            ]);

            return redirect()->route('admin.cpl.index')
                ->with('success', 'CPL berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Cpl $cpl)
    {
        return view('admin.cpl.edit', compact('cpl'));
    }

    public function update(Request $request, Cpl $cpl)
    {
        $validator = Validator::make($request->all(), [
            'kodeCpl' => 'required|string|max:20|unique:cpl,kodeCpl,' . $cpl->id,
            'deskripsi' => 'required|string',
        ], [
            'kodeCpl.required' => 'Kode CPL wajib diisi',
            'kodeCpl.max' => 'Kode CPL maksimal 20 karakter',
            'kodeCpl.unique' => 'Kode CPL sudah terdaftar',
            'deskripsi.required' => 'Deskripsi CPL wajib diisi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $cpl->update([
                'kodeCpl' => $request->kodeCpl,
                'deskripsi' => $request->deskripsi,
            ]);

            return redirect()->route('admin.cpl.index')
                ->with('success', 'CPL berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Cpl $cpl)
    {
        try {
            // Check if CPL has related CPMK
            if ($cpl->cpmk()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'CPL tidak dapat dihapus karena masih memiliki CPMK terkait');
            }

            $cpl->delete();

            return redirect()->route('admin.cpl.index')
                ->with('success', 'CPL berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
