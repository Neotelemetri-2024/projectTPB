<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Cpmk;
use App\Models\Cpl;
use App\Models\TahunAjaranMatkul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\CpmkMatKul;
use App\Models\Bobot;
use App\Models\TahunAjaran;

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

        // Get the latest tahun ajaran for default filter
        $latestTahunAjaran = TahunAjaran::orderBy('tahun', 'desc')->orderBy('periode', 'desc')->first();

        // Set default filter to latest tahun ajaran if no filter is selected
        $selectedTahunAjaranId = $request->filled('tahun_ajaran_id') ? $request->tahun_ajaran_id : ($latestTahunAjaran ? $latestTahunAjaran->id : null);

        // Start with base query - use new schema with dosen_pengampu_kelas
        $query = TahunAjaranMatkul::whereHas('kelas.dosenPengampuKelas', function ($q) use ($dosen) {
            $q->where('dosenId', $dosen->id);
        });

        // Apply filters
        if ($selectedTahunAjaranId) {
            $query->where('tahunAjaranId', $selectedTahunAjaranId);
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
            'kelas.dosenPengampuKelas.dosen.user',
            'kelas.kelasMahasiswa.mahasiswa',
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
            $allDosenPengampuKelas = collect();
            $allKelas = collect();
            $allCpmkMatKul = collect();

            foreach($group as $item) {
                // Collect all kelas mahasiswa from all classes
                foreach($item->kelas as $kelas) {
                    $allKelasMahasiswa = $allKelasMahasiswa->merge($kelas->kelasMahasiswa);
                    $allDosenPengampuKelas = $allDosenPengampuKelas->merge($kelas->dosenPengampuKelas);
                }
                $allKelas = $allKelas->merge($item->kelas);
                $allCpmkMatKul = $allCpmkMatKul->merge($item->cpmkMatKul);
            }

            // Set aggregated data to representative
            $representative->setRelation('kelasMahasiswa', $allKelasMahasiswa->unique('id'));
            $representative->setRelation('dosenPengampuKelas', $allDosenPengampuKelas->unique('id'));
            $representative->setRelation('kelas', $allKelas->unique('id'));

            // Make CPMK unique based on cpmkId, not the cpmkMatKul record id
            $uniqueCpmkMatKul = $allCpmkMatKul->unique('cpmkId');
            $representative->setRelation('cpmkMatKul', $uniqueCpmkMatKul);

            return $representative;
        })->values();

        // Get filter options
        $tahunAjarans = TahunAjaran::orderBy('tahun', 'desc')->orderBy('periode', 'desc')->get();
        $jenisOptions = ['Teori', 'Praktikum', 'Teori & Praktikum'];

        return view('dosen.cpmk.index', compact('mataKuliahDiampu', 'tahunAjarans', 'jenisOptions', 'selectedTahunAjaranId'));
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

        // Verify that this dosen teaches this mata kuliah - use new schema
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })->with(['mataKuliah', 'tahunAjaran'])->findOrFail($tahunAjaranMatkulId);

        // Get all TahunAjaranMatkul records for the same mata kuliah and tahun ajaran
        $allTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
            ->whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
                $query->where('dosenId', $dosen->id);
            })
            ->pluck('id');

        // Get CPMK related to this mata kuliah through CpmkMatKul from all classes
        $query = Cpmk::whereHas('cpmkMatKul', function ($q) use ($allTahunAjaranMatkulIds) {
            $q->whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds);
        })
        ->with(['cpl', 'cpmkMatKul', 'parent.cpl', 'children.cpl']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kodeCpmk', 'like', "%{$search}%")
                  ->orWhere('deskripsiCpmk', 'like', "%{$search}%");
            });
        }

        if ($request->filled('cpl_id')) {
            $query->whereHas('cpl', function ($q) use ($request) {
                $q->where('cpl.id', $request->cpl_id);
            });
        }

        $cpmkList = $query->orderBy('kodeCpmk')->paginate(10);

        // Group CPMK by hierarchy (parent and children)
        $cpmkHierarchy = $cpmkList->getCollection()->groupBy(function($cpmk) {
            return $cpmk->parent_id ? $cpmk->parent_id : $cpmk->id;
        })->map(function($group) {
            $parent = $group->first();
            if ($parent->parent_id) {
                // This is a child group, find the actual parent with CPL loaded
                $parent = Cpmk::with('cpl')->find($parent->parent_id);
            }

            // Ensure children have CPL loaded and synchronized
            $children = $group->where('parent_id', '!=', null)->map(function($child) use ($parent) {
                if ($parent && $child->parent_id == $parent->id) {
                    // Ensure CPL matches parent
                    $parentCplIds = $parent->cpl->pluck('id')->toArray();
                    $currentCplIds = $child->cpl->pluck('id')->toArray();

                    if (sort($parentCplIds) !== sort($currentCplIds)) {
                        $child->cpl()->sync($parentCplIds);
                        $child->load('cpl');
                    }
                }
                return $child;
            });

            return [
                'parent' => $parent,
                'children' => $children
            ];
        });

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

            // Calculate last modified time from CPMK, Bobot, and CpmkMatKul tables
            $cpmkLastUpdate = $cpmk->updated_at;
            $bobotLastUpdate = Bobot::where('cpmkId', $cpmk->id)
                ->whereHas('tahunAjaranMatkul', function($q) use ($allTahunAjaranMatkulIds) {
                    $q->whereIn('id', $allTahunAjaranMatkulIds);
                })
                ->max('updated_at');
            $cpmkMatKulLastUpdate = CpmkMatKul::where('cpmkId', $cpmk->id)
                ->whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds)
                ->max('updated_at');

            // Get the latest between CPMK, Bobot, and CpmkMatKul updates
            $lastModified = null;
            foreach ([$cpmkLastUpdate, $bobotLastUpdate, $cpmkMatKulLastUpdate] as $dt) {
                if ($dt && ($lastModified === null || $dt > $lastModified)) {
                    $lastModified = $dt;
                }
            }
            $cpmk->lastModified = $lastModified;
        }

        // Ensure CPL synchronization for sub-CPMKs
        foreach ($cpmkList as $cpmk) {
            if ($cpmk->parent_id) {
                // This is a sub-CPMK, ensure CPL matches parent
                $parentCplIds = $cpmk->parent->cpl->pluck('id')->toArray();
                $currentCplIds = $cpmk->cpl->pluck('id')->toArray();

                // If CPL doesn't match, sync it
                if (sort($parentCplIds) !== sort($currentCplIds)) {
                    $cpmk->cpl()->sync($parentCplIds);
                    // Reload the CPL relationship
                    $cpmk->load('cpl');
                }
            }
        }

        // Get CPL list for filter
        $cplList = Cpl::orderBy('kodeCpl')->get();

        // Get all CPMK for parent selection (excluding current CPMK and its descendants)
        $allCpmkForParent = Cpmk::whereHas('cpmkMatKul', function ($q) use ($allTahunAjaranMatkulIds) {
            $q->whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds);
        })->with('children')->get();

        return view('dosen.cpmk.show', compact('cpmkList', 'cpmkHierarchy', 'tahunAjaranMatkul', 'cplList', 'allCpmkForParent'));
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

        // Verify that this dosen teaches this mata kuliah - use new schema
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })->with(['mataKuliah', 'tahunAjaran'])->findOrFail($tahunAjaranMatkulId);

        // Get CPL list
        $cplList = Cpl::orderBy('kodeCpl')->get();

        // Get all classes taught by this dosen for this mata kuliah and tahun ajaran
        $allTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
            ->whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
                $query->where('dosenId', $dosen->id);
            })
            ->with(['kelas.kelasMahasiswa'])
            ->get();

        // Get class information for display
        $kelasInfo = $allTahunAjaranMatkulIds->map(function($item) {
            $totalMahasiswa = $item->kelas->sum(function($kelas) {
                return $kelas->kelasMahasiswa->count();
            });
            return [
                'kelas' => $item->kelas->pluck('namaKelas')->implode(', '),
                'jumlah_mahasiswa' => $totalMahasiswa
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
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })->findOrFail($tahunAjaranMatkulId);

        // Get all TahunAjaranMatkul records for the same mata kuliah and tahun ajaran
        // that are taught by this dosen
        $allTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
            ->whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
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
            ->whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
                $query->where('dosenId', $dosen->id);
            })
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
            // Create CPMK (only parent CPMK, no sub-CPMK)
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

        // Verify that this dosen teaches this mata kuliah - use new schema
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })->with(['mataKuliah', 'tahunAjaran'])->findOrFail($tahunAjaranMatkulId);

        // Get all TahunAjaranMatkul records for the same mata kuliah and tahun ajaran
        $allTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
            ->whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
                $query->where('dosenId', $dosen->id);
            })
            ->pluck('id');

        $cpmk = Cpmk::with(['cpl', 'cpmkMatKul', 'nilai.mahasiswa', 'parent', 'children'])
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

        // Verify that this dosen teaches this mata kuliah - use new schema
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })->with(['mataKuliah', 'tahunAjaran'])->findOrFail($tahunAjaranMatkulId);

        // Get all TahunAjaranMatkul records for the same mata kuliah and tahun ajaran
        $allTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
            ->whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
                $query->where('dosenId', $dosen->id);
            })
            ->pluck('id');

        // Get parent CPMK only (no sub-CPMK)
        $cpmk = Cpmk::with(['cpl', 'children'])->whereHas('cpmkMatKul', function ($q) use ($allTahunAjaranMatkulIds) {
            $q->whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds);
        })->whereNull('parent_id') // Only parent CPMK
          ->findOrFail($id);

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

        // Verify that this dosen teaches this mata kuliah - use new schema
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })->findOrFail($tahunAjaranMatkulId);

        // Get all TahunAjaranMatkul records for the same mata kuliah and tahun ajaran
        // that are taught by this dosen
        $allTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
            ->whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
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
            'parent_id.exists' => 'CPMK parent yang dipilih tidak valid.',
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

            // Update CPL for all sub-CPMKs to match parent CPMK
            $subCpmks = Cpmk::where('parent_id', $cpmk->id)->get();
            foreach ($subCpmks as $subCpmk) {
                $subCpmk->cpl()->sync($request->cpl_ids);
            }

            $message = 'CPMK berhasil diperbarui untuk ' . $allTahunAjaranMatkulIds->count() . ' kelas yang Anda ampu.';
            if ($subCpmks->count() > 0) {
                $message .= ' CPL pada ' . $subCpmks->count() . ' sub-CPMK juga telah diperbarui untuk menyesuaikan dengan parent CPMK.';
            }

            return redirect()->route('dosen.cpmk.show', $tahunAjaranMatkulId)
                ->with('success', $message);

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

        // Verify that this dosen teaches this mata kuliah - use new schema
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })->findOrFail($tahunAjaranMatkulId);

        // Get all TahunAjaranMatkul records for the same mata kuliah and tahun ajaran
        $allTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
            ->whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
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

            // Check if CPMK has sub-CPMKs with nilai
            $subCpmksWithNilai = Cpmk::where('parent_id', $cpmk->id)
                ->whereHas('nilai')
                ->count();

            if ($subCpmksWithNilai > 0) {
                return redirect()->back()
                    ->with('error', 'CPMK tidak dapat dihapus karena sub-CPMK memiliki data nilai.');
            }

            // Get sub-CPMKs count for message
            $subCpmksCount = Cpmk::where('parent_id', $cpmk->id)->count();

            // Delete related CpmkMatKul records first
            $cpmk->cpmkMatKul()->whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds)->delete();

            // Delete CPMK if no other mata kuliah uses it
            if (!$cpmk->cpmkMatKul()->exists()) {
                // Delete all sub-CPMKs first (cascade delete)
                Cpmk::where('parent_id', $cpmk->id)->delete();
                $cpmk->delete();
            }

            $message = 'CPMK berhasil dihapus.';
            if ($subCpmksCount > 0) {
                $message .= ' ' . $subCpmksCount . ' sub-CPMK juga telah dihapus.';
            }

            return redirect()->route('dosen.cpmk.show', $tahunAjaranMatkulId)
                ->with('success', $message);

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

        // Verify that this dosen teaches this mata kuliah - use new schema
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })->findOrFail($tahunAjaranMatkulId);

        // Get all TahunAjaranMatkul records for the same mata kuliah and tahun ajaran
        $allTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
            ->whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
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

                // Check if any CPMK has sub-CPMKs with nilai
                $subCpmksWithNilai = Cpmk::whereIn('parent_id', $cpmkIds)
                    ->whereHas('nilai')
                    ->count();

                if ($subCpmksWithNilai > 0) {
                    return response()->json([
                        'error' => 'Beberapa CPMK tidak dapat dihapus karena sub-CPMK memiliki data nilai.'
                    ], 422);
                }

                $deletedCount = 0;
                $subCpmkDeletedCount = 0;
                foreach ($cpmkIds as $cpmkId) {
                    $cpmk = Cpmk::whereHas('cpmkMatKul', function ($q) use ($allTahunAjaranMatkulIds) {
                        $q->whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds);
                    })->find($cpmkId);

                    if ($cpmk) {
                        // Count sub-CPMKs for this parent
                        $subCpmkCount = Cpmk::where('parent_id', $cpmk->id)->count();
                        $subCpmkDeletedCount += $subCpmkCount;

                        // Delete related CpmkMatKul records first
                        $cpmk->cpmkMatKul()->whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds)->delete();

                        // Delete CPMK if no other mata kuliah uses it
                        if (!$cpmk->cpmkMatKul()->exists()) {
                            // Delete all sub-CPMKs first (cascade delete)
                            Cpmk::where('parent_id', $cpmk->id)->delete();
                            $cpmk->delete();
                        }
                        $deletedCount++;
                    }
                }

                $message = "{$deletedCount} CPMK berhasil dihapus.";
                if ($subCpmkDeletedCount > 0) {
                    $message .= " {$subCpmkDeletedCount} sub-CPMK juga telah dihapus.";
                }

                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat memproses data.'], 500);
        }
    }

    /**
     * Show the form for creating a new sub-CPMK.
     */
    public function createSubCpmk($tahunAjaranMatkulId, $parentId)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan.');
        }

        // Verify that this dosen teaches this mata kuliah
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })->with(['mataKuliah', 'tahunAjaran'])->findOrFail($tahunAjaranMatkulId);

        // Get all TahunAjaranMatkul records for the same mata kuliah and tahun ajaran
        $allTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
            ->whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
                $query->where('dosenId', $dosen->id);
            })
            ->with(['kelas.kelasMahasiswa'])
            ->get();

        // Get parent CPMK
        $parentCpmk = Cpmk::whereHas('cpmkMatKul', function ($q) use ($allTahunAjaranMatkulIds) {
            $q->whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds->pluck('id'));
        })->findOrFail($parentId);

        // Get class information for display
        $kelasInfo = $allTahunAjaranMatkulIds->map(function($item) {
            $totalMahasiswa = $item->kelas->sum(function($kelas) {
                return $kelas->kelasMahasiswa->count();
            });
            return [
                'kelas' => $item->kelas->pluck('namaKelas')->implode(', '),
                'jumlah_mahasiswa' => $totalMahasiswa
            ];
        });

        return view('dosen.cpmk.create-sub-cpmk', compact('tahunAjaranMatkul', 'parentCpmk', 'kelasInfo'));
    }

    /**
     * Store a newly created sub-CPMK in storage.
     */
    public function storeSubCpmk(Request $request, $tahunAjaranMatkulId, $parentId)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan.');
        }

        // Verify that this dosen teaches this mata kuliah
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })->findOrFail($tahunAjaranMatkulId);

        // Get all TahunAjaranMatkul records for the same mata kuliah and tahun ajaran
        $allTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
            ->whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
                $query->where('dosenId', $dosen->id);
            })
            ->pluck('id');

        // Verify parent CPMK exists and belongs to this dosen
        $parentCpmk = Cpmk::whereHas('cpmkMatKul', function ($q) use ($allTahunAjaranMatkulIds) {
            $q->whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds);
        })->findOrFail($parentId);

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
            'kodeCpmk' => 'required|string|max:20',
            'deskripsi' => 'required|string|max:1000',
        ], [
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

        try {
            // Create sub-CPMK with parent_id
            $cpmk = Cpmk::create([
                'kodeCpmk' => $request->kodeCpmk,
                'deskripsi' => $request->deskripsi,
                'parent_id' => $parentId,
            ]);

            // Attach CPL relationships from parent CPMK
            $parentCplIds = $parentCpmk->cpl->pluck('id')->toArray();
            $cpmk->cpl()->attach($parentCplIds);

            // Create relation to ALL mata kuliah classes taught by this dosen
            foreach ($allTahunAjaranMatkulIds as $tahunAjaranMatkulId) {
                $cpmk->cpmkMatKul()->create([
                    'tahunAjaranMatkulId' => $tahunAjaranMatkulId,
                ]);
            }

            return redirect()->route('dosen.cpmk.show', $tahunAjaranMatkulId)
                ->with('success', 'Sub-CPMK berhasil ditambahkan dengan CPL yang sama dengan parent CPMK untuk ' . $allTahunAjaranMatkulIds->count() . ' kelas yang Anda ampu.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan sub-CPMK.')
                ->withInput();
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

        // Verify that this dosen teaches this mata kuliah - use new schema
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })->with(['mataKuliah', 'tahunAjaran'])->findOrFail($tahunAjaranMatkulId);

        // Get all TahunAjaranMatkul records for the same mata kuliah and tahun ajaran
        $allTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
            ->whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
                $query->where('dosenId', $dosen->id);
            })
            ->pluck('id');

        // Get CPMK with all related data
        $cpmk = Cpmk::with(['cpl', 'cpmkMatKul.tahunAjaranMatkul.mataKuliah', 'parent', 'children'])
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

    /**
     * Show edit form for sub-CPMK
     */
    public function editSubCpmk($tahunAjaranMatkulId, $cpmkId)
    {
        $dosen = Auth::user()->dosen;

        // Verify access
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('kelas.dosenPengampuKelas', function($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })->findOrFail($tahunAjaranMatkulId);

        // Get all TahunAjaranMatkul records for the same mata kuliah, tahun ajaran, and dosen
        $allTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
            ->whereHas('kelas.dosenPengampuKelas', function($query) use ($dosen) {
                $query->where('dosenId', $dosen->id);
            })
            ->pluck('id');

        // Get sub-CPMK with parent and CPL relationships
        $cpmk = Cpmk::with(['parent.cpl', 'cpl'])
            ->whereHas('cpmkMatKul', function($q) use ($allTahunAjaranMatkulIds) {
                $q->whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds);
            })
            ->where('parent_id', '!=', null) // Ensure this is a sub-CPMK
            ->findOrFail($cpmkId);

        return view('dosen.cpmk.edit-sub-cpmk', compact('tahunAjaranMatkul', 'cpmk'));
    }

    /**
     * Update sub-CPMK
     */
    public function updateSubCpmk(Request $request, $tahunAjaranMatkulId, $cpmkId)
    {
        $dosen = Auth::user()->dosen;

        // Verify access
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('kelas.dosenPengampuKelas', function($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })->findOrFail($tahunAjaranMatkulId);

        // Get all TahunAjaranMatkul records for the same mata kuliah, tahun ajaran, and dosen
        $allTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
            ->whereHas('kelas.dosenPengampuKelas', function($query) use ($dosen) {
                $query->where('dosenId', $dosen->id);
            })
            ->pluck('id');

        // Get sub-CPMK with parent
        $cpmk = Cpmk::with('parent.cpl')
            ->whereHas('cpmkMatKul', function($q) use ($allTahunAjaranMatkulIds) {
                $q->whereIn('tahunAjaranMatkulId', $allTahunAjaranMatkulIds);
            })
            ->where('parent_id', '!=', null) // Ensure this is a sub-CPMK
            ->findOrFail($cpmkId);

        $validator = Validator::make($request->all(), [
            'kodeCpmk' => 'required|string|max:20',
            'deskripsi' => 'required|string|max:1000',
        ], [
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

        try {
            // Update sub-CPMK
            $cpmk->update([
                'kodeCpmk' => $request->kodeCpmk,
                'deskripsi' => $request->deskripsi,
            ]);

            // Sync CPL relationships with parent CPMK (in case parent CPL changed)
            $parentCplIds = $cpmk->parent->cpl->pluck('id')->toArray();
            $cpmk->cpl()->sync($parentCplIds);

            return redirect()->route('dosen.cpmk.show', $tahunAjaranMatkulId)
                ->with('success', 'Sub-CPMK berhasil diperbarui dengan CPL yang sama dengan parent CPMK.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui sub-CPMK. Silakan coba lagi.');
        }
    }
}
