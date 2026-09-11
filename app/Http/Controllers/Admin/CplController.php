<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cpl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class CplController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = Cpl::with('cpmk');

        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function ($sub) use ($searchTerm) {
                $sub->where('kodeCpl', 'like', "%{$searchTerm}%")
                    ->orWhere('deskripsi', 'like', "%{$searchTerm}%");
            });
        }

        $sortField = $request->filled('sort') && in_array($request->sort, ['kodeCpl', 'deskripsi', 'nilaiMinimal', 'targetPersen'])
            ? $request->sort
            : 'kodeCpl';
        $sortDir = $request->filled('dir') && in_array($request->dir, ['asc', 'desc'])
            ? $request->dir
            : 'asc';

        $query->orderBy($sortField, $sortDir);

        $cpl = $query->paginate($this->perPage($request))->withQueryString();

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
            'nilaiMinimal' => 'required|integer|min:0|max:100',
            'targetPersen' => 'required|integer|min:0|max:100',
        ], [
            'kodeCpl.required' => 'Kode CPL wajib diisi',
            'kodeCpl.max' => 'Kode CPL maksimal 20 karakter',
            'kodeCpl.unique' => 'Kode CPL sudah terdaftar',
            'deskripsi.required' => 'Deskripsi CPL wajib diisi',
            'nilaiMinimal.required' => 'Nilai minimal wajib diisi',
            'nilaiMinimal.integer' => 'Nilai minimal harus berupa angka',
            'nilaiMinimal.min' => 'Nilai minimal minimal 0',
            'nilaiMinimal.max' => 'Nilai minimal maksimal 100',
            'targetPersen.required' => 'Target capaian wajib diisi',
            'targetPersen.integer' => 'Target capaian harus berupa angka',
            'targetPersen.min' => 'Target capaian minimal 0',
            'targetPersen.max' => 'Target capaian maksimal 100',
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
                'nilaiMinimal' => (int) $request->nilaiMinimal,
                'targetPersen' => (int) $request->targetPersen,
            ]);

            Cache::forget('pimpinan.cpl-achievement.rows.v5');
            Cache::forget('dashboard.cpl-achievement.v2');

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
            'nilaiMinimal' => 'required|integer|min:0|max:100',
            'targetPersen' => 'required|integer|min:0|max:100',
        ], [
            'kodeCpl.required' => 'Kode CPL wajib diisi',
            'kodeCpl.max' => 'Kode CPL maksimal 20 karakter',
            'kodeCpl.unique' => 'Kode CPL sudah terdaftar',
            'deskripsi.required' => 'Deskripsi CPL wajib diisi',
            'nilaiMinimal.required' => 'Nilai minimal wajib diisi',
            'nilaiMinimal.integer' => 'Nilai minimal harus berupa angka',
            'nilaiMinimal.min' => 'Nilai minimal minimal 0',
            'nilaiMinimal.max' => 'Nilai minimal maksimal 100',
            'targetPersen.required' => 'Target capaian wajib diisi',
            'targetPersen.integer' => 'Target capaian harus berupa angka',
            'targetPersen.min' => 'Target capaian minimal 0',
            'targetPersen.max' => 'Target capaian maksimal 100',
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
                'nilaiMinimal' => (int) $request->nilaiMinimal,
                'targetPersen' => (int) $request->targetPersen,
            ]);

            Cache::forget('pimpinan.cpl-achievement.rows.v5');
            Cache::forget('dashboard.cpl-achievement.v2');

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
            if ($cpl->cpmk()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'CPL tidak dapat dihapus karena masih memiliki CPMK terkait');
            }

            $cpl->delete();
            Cache::forget('pimpinan.cpl-achievement.rows.v5');
            Cache::forget('dashboard.cpl-achievement.v2');

            return redirect()->route('admin.cpl.index')
                ->with('success', 'CPL berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
