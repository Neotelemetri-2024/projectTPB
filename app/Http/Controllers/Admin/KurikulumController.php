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
     * Layar penetapan matkul asesmen per pasangan (CPL, matkul) untuk satu kurikulum.
     */
    public function editMatkulAsesmen(Kurikulum $kurikulum)
    {
        $mataKuliah = $kurikulum->mataKuliah()->orderBy('kodeMatkul')->get();
        $cpl = \App\Models\Cpl::orderBy('kodeCpl')->get();

        $mkIds = $mataKuliah->pluck('id')->all();
        $selectedPairs = [];
        if (!empty($mkIds)) {
            $pairs = \Illuminate\Support\Facades\DB::table('cpl_mata_kuliah_asesmen')
                ->whereIn('mataKuliahId', $mkIds)
                ->get(['cplId', 'mataKuliahId']);

            foreach ($pairs as $pair) {
                $selectedPairs[(int) $pair->cplId . ':' . (int) $pair->mataKuliahId] = true;
            }
        }

        return view('admin.kurikulum.matkul-asesmen', compact('kurikulum', 'mataKuliah', 'cpl', 'selectedPairs'));
    }

    public function updateMatkulAsesmen(Request $request, Kurikulum $kurikulum)
    {
        $validator = Validator::make($request->all(), [
            'asesmen' => 'nullable|array',
            'asesmen.*' => 'array',
            'asesmen.*.*' => 'integer|exists:mata_kuliah,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Hanya boleh mengeset matkul yang memang milik kurikulum ini.
        $mkIds = $kurikulum->mataKuliah()->pluck('id')->map(fn ($id) => (int) $id)->all();
        $allowed = array_flip($mkIds);
        $cplIds = \App\Models\Cpl::pluck('id')->map(fn ($id) => (int) $id)->all();
        $allowedCpl = array_flip($cplIds);

        $rows = [];
        $now = now();
        foreach ((array) $request->input('asesmen', []) as $cplId => $mkIdsSelected) {
            $cplId = (int) $cplId;
            if (!isset($allowedCpl[$cplId])) {
                continue;
            }
            foreach ((array) $mkIdsSelected as $mkId) {
                $mkId = (int) $mkId;
                if (isset($allowed[$mkId])) {
                    $rows[] = [
                        'cplId' => $cplId,
                        'mataKuliahId' => $mkId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($mkIds, $rows) {
            \Illuminate\Support\Facades\DB::table('cpl_mata_kuliah_asesmen')
                ->whereIn('mataKuliahId', $mkIds)
                ->delete();

            if (!empty($rows)) {
                \Illuminate\Support\Facades\DB::table('cpl_mata_kuliah_asesmen')->insert($rows);
            }
        });

        \Illuminate\Support\Facades\Cache::forget('pimpinan.cpl-achievement.rows.v6');
        \Illuminate\Support\Facades\Cache::forget('cpl-laporan.rows.v2');
        \Illuminate\Support\Facades\Cache::forget('dashboard.cpl-achievement.v2');

        return redirect()->route('admin.kurikulum.matkul-asesmen.edit', $kurikulum)
            ->with('success', 'Asesmen CPL per matkul berhasil disimpan.');
    }
}
