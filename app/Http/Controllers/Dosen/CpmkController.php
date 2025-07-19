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
     * Display a listing of mata kuliah for CPMK management.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan.');
        }

        // Start with base query
        $query = TahunAjaranMatkul::whereHas('dosenPengampu', function ($q) use ($dosen) {
            $q->where('dosenId', $dosen->id);
        });

        // Apply filters
        if ($request->filled('tahun_ajaran_id')) {
            $query->where('tahunAjaranId', $request->tahun_ajaran_id);
        }

        if ($request->filled('jenis')) {
            $query->whereHas('mataKuliah', function ($q) use ($request) {
                $q->where('jenis', $request->jenis);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('mataKuliah', function ($subQ) use ($search) {
                    $subQ->where('namaMatkul', 'like', "%{$search}%")
                         ->orWhere('kodeMatkul', 'like', "%{$search}%");
                });
            });
        }

        // Get results with relationships
        $mataKuliahData = $query->with([
            'mataKuliah',
            'tahunAjaran',
            'dosenPengampu.dosen.user',
            'kelasMahasiswa.mahasiswa',
            'cpmkMatKul.cpmk',
            'cpmkMatKul' => function($query) {
                $query->orderBy('updated_at', 'desc');
            }
        ])
        ->orderBy('created_at', 'desc')
        ->get();

        // Group by mata kuliah and tahun ajaran to avoid duplicate cards
        $mataKuliahDiampu = $mataKuliahData->groupBy(function($item) {
            return $item->mataKuliahId . '_' . $item->tahunAjaranId;
        })->map(function($group) {
            // Take the first item as representative
            $representative = $group->first();

            // Aggregate data from all classes
            $allKelasMahasiswa = collect();
            $allDosenPengampu = collect();
            $allKelas = collect();
            $allCpmkMatKul = collect();

            foreach($group as $item) {
                $allKelasMahasiswa = $allKelasMahasiswa->merge($item->kelasMahasiswa);
                $allDosenPengampu = $allDosenPengampu->merge($item->dosenPengampu);
                $allKelas->push($item->kelas);
                $allCpmkMatKul = $allCpmkMatKul->merge($item->cpmkMatKul);
            }

            // Set aggregated data to representative
            $representative->setRelation('kelasMahasiswa', $allKelasMahasiswa->unique('id'));
            $representative->setRelation('dosenPengampu', $allDosenPengampu->unique('id'));

            // Make CPMK unique based on cpmkId, not the cpmkMatKul record id
            $uniqueCpmkMatKul = $allCpmkMatKul->unique('cpmkId');
            $representative->setRelation('cpmkMatKul', $uniqueCpmkMatKul);

            // Get the latest update timestamp from both CPMK and Bobot tables
            $groupIds = $group->pluck('id');

            // Get latest update from CPMK table (directly from cpmk table, not cpmk_mat_kul)
            $cpmkIds = $allCpmkMatKul->pluck('cpmkId')->unique();
            $latestCpmkUpdate = \App\Models\Cpmk::whereIn('id', $cpmkIds)->max('updated_at');

            // Get latest update from Bobot table
            $latestBobotUpdate = \App\Models\Bobot::whereIn('tahunAjaranMatkulId', $groupIds)->max('updated_at');

            // Compare and get the latest between CPMK and Bobot updates
            $latestUpdate = null;
            if ($latestCpmkUpdate && $latestBobotUpdate) {
                $latestUpdate = max($latestCpmkUpdate, $latestBobotUpdate);
            } elseif ($latestCpmkUpdate) {
                $latestUpdate = $latestCpmkUpdate;
            } elseif ($latestBobotUpdate) {
                $latestUpdate = $latestBobotUpdate;
            }

            $representative->latestCpmkUpdate = $latestUpdate;
            $representative->latestCpmkUpdate = $latestUpdate;

            // Get unique dosen pengampu first
            $uniqueDosenPengampu = $allDosenPengampu->unique('dosenId');

            // Get unique dosen pengampu with their names
            $dosenNames = $uniqueDosenPengampu->map(function($dosenPengampu) {
                return $dosenPengampu->dosen->nama ?? 'Unknown';
            })->unique()->values();
            $representative->dosenPengampuNames = $dosenNames;

            $representative->allKelas = $allKelas->unique()->sort()->values();
            $representative->groupedItems = $group;

            return $representative;
        })->values();

        // Get data for filters
        $tahunAjaranList = \App\Models\TahunAjaran::orderBy('tahun', 'desc')->orderBy('periode', 'desc')->get();
        $jenisList = ['wajib', 'pilihan'];

        return view('dosen.cpmk.index', compact(
            'mataKuliahDiampu',
            'dosen',
            'tahunAjaranList',
            'jenisList'
        ));
    }

    /**
     * Display a listing of CPMK for a specific mata kuliah.
     */
    public function showMataKuliah(Request $request, $tahunAjaranMatkulId)
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

        // Get all TahunAjaranMatkul records for the same mata kuliah and tahun ajaran
        $allTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
            ->whereHas('dosenPengampu', function ($query) use ($dosen) {
                $query->where('dosenId', $dosen->id);
            })
            ->pluck('id');

        // Get CPMK related to this mata kuliah through CpmkMatKul from all classes
        $query = Cpmk::whereHas('cpmkMatKul', function ($q) use ($allTahunAjaranMatkulIds) {
            $q->whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds);
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
            $query->whereHas('cpl', function($q) use ($request) {
                $q->where('cpl.id', $request->cpl_id);
            });
        }

        $cpmkList = $query->orderBy('kodeCpmk')->paginate(10);

        // Load bobot data separately and group by component to avoid duplicates
        foreach ($cpmkList as $cpmk) {
            $bobotData = \App\Models\Bobot::where('cpmkId', $cpmk->id)
                ->whereHas('tahunAjaranMatkul', function($q) use ($allTahunAjaranMatkulIds) {
                    $q->whereIn('id', $allTahunAjaranMatkulIds);
                })
                ->with('komponen')
                ->get()
                ->groupBy('komponenId')
                ->map(function($group) {
                    // Take the first bobot value for each component (they should be the same across classes)
                    return $group->first();
                });

            $cpmk->setRelation('bobot', $bobotData->values());

            // Calculate last modified time from CPMK and Bobot tables
            $cpmkLastUpdate = $cpmk->updated_at;
            $bobotLastUpdate = \App\Models\Bobot::where('cpmkId', $cpmk->id)
                ->whereHas('tahunAjaranMatkul', function($q) use ($allTahunAjaranMatkulIds) {
                    $q->whereIn('id', $allTahunAjaranMatkulIds);
                })
                ->max('updated_at');

            // Get the latest between CPMK and Bobot updates
            $lastModified = null;
            if ($cpmkLastUpdate && $bobotLastUpdate) {
                $lastModified = max($cpmkLastUpdate, $bobotLastUpdate);
            } elseif ($cpmkLastUpdate) {
                $lastModified = $cpmkLastUpdate;
            } elseif ($bobotLastUpdate) {
                $lastModified = $bobotLastUpdate;
            }

            $cpmk->lastModified = $lastModified;
        }

        // Get CPL list for filter
        $cplList = Cpl::orderBy('kodeCpl')->get();

        return view('dosen.cpmk.show', compact('cpmkList', 'tahunAjaranMatkul', 'cplList'));
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

        // Get all classes taught by this dosen for this mata kuliah and tahun ajaran
        $allTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
            ->whereHas('dosenPengampu', function ($query) use ($dosen) {
                $query->where('dosenId', $dosen->id);
            })
            ->with('kelasMahasiswa')
            ->get();

        // Get class information for display
        $kelasInfo = $allTahunAjaranMatkulIds->map(function($item) {
            return [
                'kelas' => $item->kelas,
                'jumlah_mahasiswa' => $item->kelasMahasiswa->count()
            ];
        });

        return view('dosen.cpmk.create', compact('tahunAjaranMatkul', 'cplList', 'kelasInfo'));
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

        // Get all TahunAjaranMatkul records for the same mata kuliah and tahun ajaran
        // that are taught by this dosen
        $allTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
            ->whereHas('dosenPengampu', function ($query) use ($dosen) {
                $query->where('dosenId', $dosen->id);
            })
            ->pluck('id');

        // Custom validation for CPMK uniqueness within scope of mata kuliah + dosen
        $existingCpmk = Cpmk::where('kodeCpmk', $request->kodeCpmk)
            ->whereHas('cpmkMatKul', function ($query) use ($allTahunAjaranMatkulIds) {
                $query->whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds);
            })
            ->first();

        if ($existingCpmk) {
            return redirect()->back()
                ->withErrors(['kodeCpmk' => 'Kode CPMK sudah digunakan untuk mata kuliah ini.'])
                ->withInput();
        }

        $validator = Validator::make($request->all(), [
            'cpl_ids' => 'required|array',
            'cpl_ids.*' => 'exists:cpl,id',
            'kodeCpmk' => 'required|string|max:20', // removed unique
            'deskripsi' => 'required|string|max:1000',
        ], [
            'cpl_ids.required' => 'Minimal satu CPL harus dipilih.',
            'cpl_ids.array' => 'Format CPL tidak valid.',
            'cpl_ids.*.exists' => 'CPL yang dipilih tidak valid.',
            'kodeCpmk.required' => 'Kode CPMK harus diisi.',
            'kodeCpmk.max' => 'Kode CPMK maksimal 20 karakter.',
            'deskripsi.required' => 'Deskripsi CPMK harus diisi.',
            'deskripsi.max' => 'Deskripsi CPMK maksimal 1000 karakter.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Custom validation: kodeCpmk must be unique per mata kuliah (across all classes/years for that mata kuliah)
        $allTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
            ->pluck('id');
        $exists = \App\Models\Cpmk::where('kodeCpmk', $request->kodeCpmk)
            ->whereHas('cpmkMatKul', function($q) use ($allTahunAjaranMatkulIds) {
                $q->whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds);
            })
            ->exists();
        if ($exists) {
            return redirect()->back()
                ->withErrors(['kodeCpmk' => 'Kode CPMK sudah digunakan pada mata kuliah ini.'])
                ->withInput();
        }

        try {
            // Create CPMK
            $cpmk = Cpmk::create([
                'kodeCpmk' => $request->kodeCpmk,
                'deskripsi' => $request->deskripsi,
            ]);

            // Attach CPL relationships
            $cpmk->cpl()->attach($request->cpl_ids);

            // Create relation to ALL mata kuliah classes taught by this dosen
            foreach ($allTahunAjaranMatkulIds as $tahunAjaranMatkulId) {
                $cpmk->cpmkMatKul()->create([
                    'tahunAjaranMatkulId' => $tahunAjaranMatkulId,
                ]);
            }

            return redirect()->route('dosen.cpmk.show', $tahunAjaranMatkulId)
                ->with('success', 'CPMK berhasil ditambahkan untuk ' . $allTahunAjaranMatkulIds->count() . ' kelas yang Anda ampu.');

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

        // Get all TahunAjaranMatkul records for the same mata kuliah and tahun ajaran
        $allTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
            ->whereHas('dosenPengampu', function ($query) use ($dosen) {
                $query->where('dosenId', $dosen->id);
            })
            ->pluck('id');

        $cpmk = Cpmk::with(['cpl', 'cpmkMatKul', 'nilai.mahasiswa'])
            ->whereHas('cpmkMatKul', function ($q) use ($allTahunAjaranMatkulIds) {
                $q->whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds);
            })
            ->findOrFail($id);

        // Hitung statistik dari semua kelas
        $totalCpmk = Cpmk::whereHas('cpmkMatKul', function ($q) use ($allTahunAjaranMatkulIds) {
            $q->whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds);
        })->distinct()->count();

        $sameCplCount = Cpmk::whereHas('cpmkMatKul', function ($q) use ($allTahunAjaranMatkulIds) {
            $q->whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds);
        })->whereHas('cpl', function ($q) use ($cpmk) {
            $q->whereIn('cpl.id', $cpmk->cpl->pluck('id'));
        })->distinct()->count();

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

        // Get all TahunAjaranMatkul records for the same mata kuliah and tahun ajaran
        $allTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
            ->whereHas('dosenPengampu', function ($query) use ($dosen) {
                $query->where('dosenId', $dosen->id);
            })
            ->pluck('id');

        $cpmk = Cpmk::with('cpl')->whereHas('cpmkMatKul', function ($q) use ($allTahunAjaranMatkulIds) {
            $q->whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds);
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

        // Get all TahunAjaranMatkul records for the same mata kuliah and tahun ajaran
        // that are taught by this dosen
        $allTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
            ->whereHas('dosenPengampu', function ($query) use ($dosen) {
                $query->where('dosenId', $dosen->id);
            })
            ->pluck('id');

        $cpmk = Cpmk::whereHas('cpmkMatKul', function ($q) use ($allTahunAjaranMatkulIds) {
            $q->whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds);
        })->findOrFail($id);

        // Custom validation for CPMK uniqueness within scope of mata kuliah + dosen (excluding current CPMK)
        $existingCpmk = Cpmk::where('kodeCpmk', $request->kodeCpmk)
            ->where('id', '!=', $id) // Exclude current CPMK being updated
            ->whereHas('cpmkMatKul', function ($query) use ($allTahunAjaranMatkulIds) {
                $query->whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds);
            })
            ->first();

        if ($existingCpmk) {
            return redirect()->back()
                ->withErrors(['kodeCpmk' => 'Kode CPMK sudah digunakan untuk mata kuliah ini.'])
                ->withInput();
        }

        $validator = Validator::make($request->all(), [
            'cpl_ids' => 'required|array',
            'cpl_ids.*' => 'exists:cpl,id',
            'kodeCpmk' => 'required|string|max:20', // removed unique
            'deskripsi' => 'required|string|max:1000',
        ], [
            'cpl_ids.required' => 'Minimal satu CPL harus dipilih.',
            'cpl_ids.array' => 'Format CPL tidak valid.',
            'cpl_ids.*.exists' => 'CPL yang dipilih tidak valid.',
            'kodeCpmk.required' => 'Kode CPMK harus diisi.',
            'kodeCpmk.max' => 'Kode CPMK maksimal 20 karakter.',
            'deskripsi.required' => 'Deskripsi CPMK harus diisi.',
            'deskripsi.max' => 'Deskripsi CPMK maksimal 1000 karakter.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Custom validation: kodeCpmk must be unique per mata kuliah (across all classes/years for that mata kuliah)
        $allTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
            ->pluck('id');
        $exists = \App\Models\Cpmk::where('kodeCpmk', $request->kodeCpmk)
            ->where('id', '!=', $id)
            ->whereHas('cpmkMatKul', function($q) use ($allTahunAjaranMatkulIds) {
                $q->whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds);
            })
            ->exists();
        if ($exists) {
            return redirect()->back()
                ->withErrors(['kodeCpmk' => 'Kode CPMK sudah digunakan pada mata kuliah ini.'])
                ->withInput();
        }

        try {
            $cpmk->update([
                'kodeCpmk' => $request->kodeCpmk,
                'deskripsi' => $request->deskripsi,
            ]);

            // Sync CPL relationships
            $cpmk->cpl()->sync($request->cpl_ids);

            return redirect()->route('dosen.cpmk.show', $tahunAjaranMatkulId)
                ->with('success', 'CPMK berhasil diperbarui untuk ' . $allTahunAjaranMatkulIds->count() . ' kelas yang Anda ampu.');

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

        // Get all TahunAjaranMatkul records for the same mata kuliah and tahun ajaran
        $allTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
            ->whereHas('dosenPengampu', function ($query) use ($dosen) {
                $query->where('dosenId', $dosen->id);
            })
            ->pluck('id');

        $cpmk = Cpmk::whereHas('cpmkMatKul', function ($q) use ($allTahunAjaranMatkulIds) {
            $q->whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds);
        })->findOrFail($id);

        try {
            // Check if CPMK has related nilai (grades)
            if ($cpmk->nilai()->exists()) {
                return redirect()->back()
                    ->with('error', 'CPMK tidak dapat dihapus karena sudah memiliki data nilai.');
            }

            // Delete related CpmkMatKul records first
            $cpmk->cpmkMatKul()->whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds)->delete();

            // Delete CPMK if no other mata kuliah uses it
            if (!$cpmk->cpmkMatKul()->exists()) {
                $cpmk->delete();
            }

            return redirect()->route('dosen.cpmk.show', $tahunAjaranMatkulId)
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

        // Get all TahunAjaranMatkul records for the same mata kuliah and tahun ajaran
        $allTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
            ->whereHas('dosenPengampu', function ($query) use ($dosen) {
                $query->where('dosenId', $dosen->id);
            })
            ->pluck('id');

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
                    $cpmk = Cpmk::whereHas('cpmkMatKul', function ($q) use ($allTahunAjaranMatkulIds) {
                        $q->whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds);
                    })->find($cpmkId);

                    if ($cpmk) {
                        // Delete related CpmkMatKul records first
                        $cpmk->cpmkMatKul()->whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds)->delete();

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

    /**
     * Show the detail of a specific CPMK.
     */
    public function showDetail($tahunAjaranMatkulId, $id)
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

        // Get all TahunAjaranMatkul records for the same mata kuliah and tahun ajaran
        $allTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
            ->whereHas('dosenPengampu', function ($query) use ($dosen) {
                $query->where('dosenId', $dosen->id);
            })
            ->pluck('id');

        // Get CPMK with all related data
        $cpmk = Cpmk::with(['cpl', 'cpmkMatKul.tahunAjaranMatkul.mataKuliah'])
            ->whereHas('cpmkMatKul', function ($q) use ($allTahunAjaranMatkulIds) {
                $q->whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds);
            })
            ->findOrFail($id);

        // Get related bobot komponen for this CPMK (unique by komponenId to avoid duplicates)
        $bobotKomponen = \App\Models\Bobot::where('cpmkId', $cpmk->id)
            ->whereHas('tahunAjaranMatkul', function ($q) use ($allTahunAjaranMatkulIds) {
                $q->whereIn('id', $allTahunAjaranMatkulIds);
            })
            ->with('komponen')
            ->get()
            ->unique('komponenId') // Remove duplicates based on komponenId
            ->values(); // Reset array keys

        return view('dosen.cpmk.detail', compact('cpmk', 'tahunAjaranMatkul', 'bobotKomponen'));
    }
}
