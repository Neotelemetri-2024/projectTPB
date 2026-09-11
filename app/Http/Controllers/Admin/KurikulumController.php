<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kurikulum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KurikulumController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = Kurikulum::withCount('mataKuliah');

        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function ($sub) use ($searchTerm) {
                $sub->where('kode', 'like', "%{$searchTerm}%")
                    ->orWhere('nama', 'like', "%{$searchTerm}%");
            });
        }

        $sortField = $request->filled('sort') && in_array($request->sort, ['kode', 'nama', 'tahun'])
            ? $request->sort
            : 'kode';
        $sortDir = $request->filled('dir') && in_array($request->dir, ['asc', 'desc'])
            ? $request->dir
            : 'asc';

        $query->orderBy($sortField, $sortDir);

        $kurikulum = $query->paginate($this->perPage($request))->withQueryString();

        return view('admin.kurikulum.index', compact('kurikulum'));
    }

    public function create()
    {
        return view('admin.kurikulum.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode' => 'required|string|max:50|unique:kurikulum,kode',
            'nama' => 'required|string|max:100',
            'tahun' => 'nullable|integer|min:1900|max:2100',
            'deskripsi' => 'nullable|string',
        ], [
            'kode.required' => 'Kode kurikulum wajib diisi',
            'kode.unique' => 'Kode kurikulum sudah terdaftar',
            'nama.required' => 'Nama kurikulum wajib diisi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Kurikulum::create([
            'kode' => $request->kode,
            'nama' => $request->nama,
            'tahun' => $request->tahun ?: null,
            'deskripsi' => $request->deskripsi,
            'isAktif' => $request->boolean('isAktif', true),
        ]);

        return redirect()->route('admin.kurikulum.index')
            ->with('success', 'Kurikulum berhasil ditambahkan');
    }

    public function edit(Kurikulum $kurikulum)
    {
        return view('admin.kurikulum.edit', compact('kurikulum'));
    }

    public function update(Request $request, Kurikulum $kurikulum)
    {
        $validator = Validator::make($request->all(), [
            'kode' => 'required|string|max:50|unique:kurikulum,kode,' . $kurikulum->id,
            'nama' => 'required|string|max:100',
            'tahun' => 'nullable|integer|min:1900|max:2100',
            'deskripsi' => 'nullable|string',
        ], [
            'kode.required' => 'Kode kurikulum wajib diisi',
            'kode.unique' => 'Kode kurikulum sudah terdaftar',
            'nama.required' => 'Nama kurikulum wajib diisi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $kurikulum->update([
            'kode' => $request->kode,
            'nama' => $request->nama,
            'tahun' => $request->tahun ?: null,
            'deskripsi' => $request->deskripsi,
            'isAktif' => $request->boolean('isAktif'),
        ]);

        return redirect()->route('admin.kurikulum.index')
            ->with('success', 'Kurikulum berhasil diperbarui');
    }

    public function destroy(Kurikulum $kurikulum)
    {
        if ($kurikulum->mataKuliah()->exists()) {
            return redirect()->back()
                ->with('error', 'Kurikulum tidak dapat dihapus karena masih memiliki mata kuliah terkait');
        }

        $kurikulum->delete();

        return redirect()->route('admin.kurikulum.index')
            ->with('success', 'Kurikulum berhasil dihapus');
    }

    /**
     * Layar penetapan matkul asesmen untuk satu kurikulum.
     */
    public function editMatkulAsesmen(Kurikulum $kurikulum)
    {
        $mataKuliah = $kurikulum->mataKuliah()->orderBy('kodeMatkul')->get();
        $selectedIds = $mataKuliah->where('isAsesmen', true)->pluck('id')->map(fn ($id) => (int) $id)->all();

        return view('admin.kurikulum.matkul-asesmen', compact('kurikulum', 'mataKuliah', 'selectedIds'));
    }

    public function updateMatkulAsesmen(Request $request, Kurikulum $kurikulum)
    {
        $validator = Validator::make($request->all(), [
            'mata_kuliah_ids' => 'nullable|array',
            'mata_kuliah_ids.*' => 'integer|exists:mata_kuliah,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $selected = collect($request->input('mata_kuliah_ids', []))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        // Hanya boleh mengeset matkul yang memang milik kurikulum ini.
        $kurikulum->mataKuliah()->update(['isAsesmen' => false]);
        if (!empty($selected)) {
            $kurikulum->mataKuliah()->whereIn('id', $selected)->update(['isAsesmen' => true]);
        }

        \Illuminate\Support\Facades\Cache::forget('pimpinan.cpl-achievement.rows.v5');
        \Illuminate\Support\Facades\Cache::forget('dashboard.cpl-achievement.v2');

        return redirect()->route('admin.kurikulum.matkul-asesmen.edit', $kurikulum)
            ->with('success', 'Matkul asesmen kurikulum berhasil disimpan.');
    }
}
