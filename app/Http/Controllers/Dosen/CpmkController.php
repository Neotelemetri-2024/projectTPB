<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Cpmk;
use App\Models\Cpl;
use App\Models\TahunAjaranMatkul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CpmkController extends Controller
{
    public function __construct()
    {
        $this->middleware('dosen');
    }

    /**
     * Display a listing of CPMK for a specific mata kuliah.
     */
    public function index(Request $request, $tahunAjaranMatkulId)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan.');
        }

        // Verify that this dosen teaches this mata kuliah
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('dosenPengampu', function ($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })->with(['mataKuliah', 'tahunAjaran'])->findOrFail($tahunAjaranMatkulId);

        // Get CPMK related to this mata kuliah through CpmkMatKul
        $query = Cpmk::whereHas('cpmkMatKul', function ($q) use ($tahunAjaranMatkulId) {
            $q->where('tahunAjaranMatkulId', $tahunAjaranMatkulId);
        })->with(['cpl', 'cpmkMatKul']);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kodeCpmk', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        // Apply CPL filter
        if ($request->filled('cpl_id')) {
            $query->where('idCpl', $request->cpl_id);
        }

        $cpmkList = $query->orderBy('kodeCpmk')->paginate(10);

        // Get CPL list for filter
        $cplList = Cpl::orderBy('kodeCpl')->get();

        return view('dosen.cpmk.index', compact('cpmkList', 'tahunAjaranMatkul', 'cplList'));
    }

    /**
     * Show the form for creating a new CPMK.
     */
    public function create($tahunAjaranMatkulId)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan.');
        }

        // Verify that this dosen teaches this mata kuliah
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('dosenPengampu', function ($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })->with(['mataKuliah', 'tahunAjaran'])->findOrFail($tahunAjaranMatkulId);

        // Get CPL list
        $cplList = Cpl::orderBy('kodeCpl')->get();

        return view('dosen.cpmk.create', compact('tahunAjaranMatkul', 'cplList'));
    }

    /**
     * Store a newly created CPMK in storage.
     */
    public function store(Request $request, $tahunAjaranMatkulId)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan.');
        }

        // Verify that this dosen teaches this mata kuliah
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('dosenPengampu', function ($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })->findOrFail($tahunAjaranMatkulId);

        $validator = Validator::make($request->all(), [
            'idCpl' => 'required|exists:cpl,id',
            'kodeCpmk' => 'required|string|max:20|unique:cpmk,kodeCpmk',
            'deskripsi' => 'required|string|max:1000',
        ], [
            'idCpl.required' => 'CPL harus dipilih.',
            'idCpl.exists' => 'CPL yang dipilih tidak valid.',
            'kodeCpmk.required' => 'Kode CPMK harus diisi.',
            'kodeCpmk.unique' => 'Kode CPMK sudah digunakan.',
            'kodeCpmk.max' => 'Kode CPMK maksimal 20 karakter.',
            'deskripsi.required' => 'Deskripsi CPMK harus diisi.',
            'deskripsi.max' => 'Deskripsi CPMK maksimal 1000 karakter.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Create CPMK
            $cpmk = Cpmk::create([
                'idCpl' => $request->idCpl,
                'kodeCpmk' => $request->kodeCpmk,
                'deskripsi' => $request->deskripsi,
            ]);

            // Create relation to mata kuliah through CpmkMatKul
            $cpmk->cpmkMatKul()->create([
                'tahunAjaranMatkulId' => $tahunAjaranMatkulId,
            ]);

            return redirect()->route('dosen.cpmk.index', $tahunAjaranMatkulId)
                ->with('success', 'CPMK berhasil ditambahkan.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan CPMK.')
                ->withInput();
        }
    }

    /**
     * Display the specified CPMK.
     */
    public function show($tahunAjaranMatkulId, $id)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan.');
        }

        // Verify that this dosen teaches this mata kuliah
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('dosenPengampu', function ($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })->with(['mataKuliah', 'tahunAjaran'])->findOrFail($tahunAjaranMatkulId);

        $cpmk = Cpmk::with(['cpl', 'cpmkMatKul', 'nilai.mahasiswa'])
            ->whereHas('cpmkMatKul', function ($q) use ($tahunAjaranMatkulId) {
                $q->where('tahunAjaranMatkulId', $tahunAjaranMatkulId);
            })
            ->findOrFail($id);

        // Hitung statistik
        $totalCpmk = Cpmk::whereHas('cpmkMatKul', function ($q) use ($tahunAjaranMatkulId) {
            $q->where('tahunAjaranMatkulId', $tahunAjaranMatkulId);
        })->count();

        $sameCplCount = Cpmk::whereHas('cpmkMatKul', function ($q) use ($tahunAjaranMatkulId) {
            $q->where('tahunAjaranMatkulId', $tahunAjaranMatkulId);
        })->where('idCpl', $cpmk->idCpl)->count();

        return view('dosen.cpmk.show', compact('cpmk', 'tahunAjaranMatkul', 'totalCpmk', 'sameCplCount'));
    }

    /**
     * Show the form for editing the specified CPMK.
     */
    public function edit($tahunAjaranMatkulId, $id)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan.');
        }

        // Verify that this dosen teaches this mata kuliah
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('dosenPengampu', function ($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })->with(['mataKuliah', 'tahunAjaran'])->findOrFail($tahunAjaranMatkulId);

        $cpmk = Cpmk::whereHas('cpmkMatKul', function ($q) use ($tahunAjaranMatkulId) {
            $q->where('tahunAjaranMatkulId', $tahunAjaranMatkulId);
        })->findOrFail($id);

        // Get CPL list
        $cplList = Cpl::orderBy('kodeCpl')->get();

        return view('dosen.cpmk.edit', compact('cpmk', 'tahunAjaranMatkul', 'cplList'));
    }

    /**
     * Update the specified CPMK in storage.
     */
    public function update(Request $request, $tahunAjaranMatkulId, $id)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan.');
        }

        // Verify that this dosen teaches this mata kuliah
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('dosenPengampu', function ($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })->findOrFail($tahunAjaranMatkulId);

        $cpmk = Cpmk::whereHas('cpmkMatKul', function ($q) use ($tahunAjaranMatkulId) {
            $q->where('tahunAjaranMatkulId', $tahunAjaranMatkulId);
        })->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'idCpl' => 'required|exists:cpl,id',
            'kodeCpmk' => 'required|string|max:20|unique:cpmk,kodeCpmk,' . $id,
            'deskripsi' => 'required|string|max:1000',
        ], [
            'idCpl.required' => 'CPL harus dipilih.',
            'idCpl.exists' => 'CPL yang dipilih tidak valid.',
            'kodeCpmk.required' => 'Kode CPMK harus diisi.',
            'kodeCpmk.unique' => 'Kode CPMK sudah digunakan.',
            'kodeCpmk.max' => 'Kode CPMK maksimal 20 karakter.',
            'deskripsi.required' => 'Deskripsi CPMK harus diisi.',
            'deskripsi.max' => 'Deskripsi CPMK maksimal 1000 karakter.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $cpmk->update([
                'idCpl' => $request->idCpl,
                'kodeCpmk' => $request->kodeCpmk,
                'deskripsi' => $request->deskripsi,
            ]);

            return redirect()->route('dosen.cpmk.index', $tahunAjaranMatkulId)
                ->with('success', 'CPMK berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui CPMK.')
                ->withInput();
        }
    }

    /**
     * Remove the specified CPMK from storage.
     */
    public function destroy($tahunAjaranMatkulId, $id)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan.');
        }

        // Verify that this dosen teaches this mata kuliah
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('dosenPengampu', function ($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })->findOrFail($tahunAjaranMatkulId);

        $cpmk = Cpmk::whereHas('cpmkMatKul', function ($q) use ($tahunAjaranMatkulId) {
            $q->where('tahunAjaranMatkulId', $tahunAjaranMatkulId);
        })->findOrFail($id);

        try {
            // Check if CPMK has related nilai (grades)
            if ($cpmk->nilai()->exists()) {
                return redirect()->back()
                    ->with('error', 'CPMK tidak dapat dihapus karena sudah memiliki data nilai.');
            }

            // Delete related CpmkMatKul records first
            $cpmk->cpmkMatKul()->where('tahunAjaranMatkulId', $tahunAjaranMatkulId)->delete();

            // Delete CPMK if no other mata kuliah uses it
            if (!$cpmk->cpmkMatKul()->exists()) {
                $cpmk->delete();
            }

            return redirect()->route('dosen.cpmk.index', $tahunAjaranMatkulId)
                ->with('success', 'CPMK berhasil dihapus.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus CPMK.');
        }
    }

    /**
     * Bulk operations for CPMK.
     */
    public function bulkAction(Request $request, $tahunAjaranMatkulId)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            return response()->json(['error' => 'Data dosen tidak ditemukan.'], 403);
        }

        // Verify that this dosen teaches this mata kuliah
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('dosenPengampu', function ($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })->findOrFail($tahunAjaranMatkulId);

        $validator = Validator::make($request->all(), [
            'action' => 'required|in:delete',
            'cpmk_ids' => 'required|array',
            'cpmk_ids.*' => 'exists:cpmk,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Data tidak valid.'], 422);
        }

        try {
            if ($request->action === 'delete') {
                $cpmkIds = $request->cpmk_ids;

                // Check if any CPMK has nilai
                $cpmkWithNilai = Cpmk::whereIn('id', $cpmkIds)
                    ->whereHas('nilai')
                    ->count();

                if ($cpmkWithNilai > 0) {
                    return response()->json([
                        'error' => 'Beberapa CPMK tidak dapat dihapus karena sudah memiliki data nilai.'
                    ], 422);
                }

                $deletedCount = 0;
                foreach ($cpmkIds as $cpmkId) {
                    $cpmk = Cpmk::whereHas('cpmkMatKul', function ($q) use ($tahunAjaranMatkulId) {
                        $q->where('tahunAjaranMatkulId', $tahunAjaranMatkulId);
                    })->find($cpmkId);

                    if ($cpmk) {
                        // Delete related CpmkMatKul records first
                        $cpmk->cpmkMatKul()->where('tahunAjaranMatkulId', $tahunAjaranMatkulId)->delete();

                        // Delete CPMK if no other mata kuliah uses it
                        if (!$cpmk->cpmkMatKul()->exists()) {
                            $cpmk->delete();
                        }
                        $deletedCount++;
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => "{$deletedCount} CPMK berhasil dihapus."
                ]);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat memproses data.'], 500);
        }
    }
}
