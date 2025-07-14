<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaranMatkul;
use App\Models\DosenPengampu;
use App\Models\TahunAjaran;
use App\Models\Mahasiswa;
use App\Models\KelasMahasiswa;
use App\Models\Nilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NilaiController extends Controller
{
    public function __construct()
    {
        $this->middleware('dosen');
    }

    /**
     * Display a listing of mata kuliah for nilai management.
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
            'dosenPengampu.dosen',
            'kelasMahasiswa.mahasiswa'
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

            foreach($group as $item) {
                $allKelasMahasiswa = $allKelasMahasiswa->merge($item->kelasMahasiswa);
                $allDosenPengampu = $allDosenPengampu->merge($item->dosenPengampu);
                $allKelas->push($item->kelas);
            }

            // Set aggregated data to representative
            $representative->setRelation('kelasMahasiswa', $allKelasMahasiswa->unique('id'));
            $representative->setRelation('dosenPengampu', $allDosenPengampu->unique('id'));
            $representative->allKelas = $allKelas->unique()->sort()->values();
            $representative->groupedItems = $group;

            return $representative;
        })->values();

        // Get data for filters
        $tahunAjaranList = TahunAjaran::orderBy('tahun', 'desc')->orderBy('periode', 'desc')->get();
        $jenisList = ['wajib', 'pilihan'];

        return view('dosen.nilai.index', compact(
            'mataKuliahDiampu',
            'dosen',
            'tahunAjaranList',
            'jenisList'
        ));
    }

    /**
     * Display students list for a specific mata kuliah.
     */
    public function show($id)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan.');
        }

        // Get all classes for this mata kuliah that this lecturer teaches
        $mataKuliahClasses = TahunAjaranMatkul::whereHas('dosenPengampu', function ($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })
        ->with([
            'mataKuliah',
            'tahunAjaran',
            'dosenPengampu.dosen',
            'kelasMahasiswa.mahasiswa',
            'cpmkMatKul.cpmk'
        ])
        ->where('id', $id)
        ->orWhere(function($query) use ($id, $dosen) {
            // Get the representative record first
            $representative = TahunAjaranMatkul::find($id);
            if ($representative) {
                $query->where('mataKuliahId', $representative->mataKuliahId)
                      ->where('tahunAjaranId', $representative->tahunAjaranId)
                      ->whereHas('dosenPengampu', function ($q) use ($dosen) {
                          $q->where('dosenId', $dosen->id);
                      });
            }
        })
        ->get();

        if ($mataKuliahClasses->isEmpty()) {
            abort(404);
        }

        // Use the first one as main reference
        $mataKuliahDiampu = $mataKuliahClasses->first();

        // Aggregate all students and classes
        $allMahasiswa = collect();
        $allKelas = collect();
        $allDosenPengampu = collect();

        foreach($mataKuliahClasses as $class) {
            $allMahasiswa = $allMahasiswa->merge($class->kelasMahasiswa->pluck('mahasiswa'));
            $allKelas->push($class->kelas);
            $allDosenPengampu = $allDosenPengampu->merge($class->dosenPengampu);
        }

        // Remove duplicates
        $mahasiswa = $allMahasiswa->unique('id');
        $kelasNumbers = $allKelas->unique()->sort()->values();

        // Get lecturer's role in this course
        $dosenPengampu = $allDosenPengampu->where('dosenId', $dosen->id)->first();

        // Get all komponen for this mata kuliah and tahun ajaran (from all bobot)
        $allKomponen = \App\Models\Komponen::whereHas('bobot.tahunAjaranMatkul', function($query) use ($mataKuliahDiampu) {
            $query->where('mataKuliahId', $mataKuliahDiampu->mataKuliahId)
                  ->where('tahunAjaranId', $mataKuliahDiampu->tahunAjaranId);
        })
        ->with(['bobot' => function($query) use ($mataKuliahDiampu) {
            $query->whereHas('tahunAjaranMatkul', function($q) use ($mataKuliahDiampu) {
                $q->where('mataKuliahId', $mataKuliahDiampu->mataKuliahId)
                  ->where('tahunAjaranId', $mataKuliahDiampu->tahunAjaranId);
            })->with(['cpmk']);
        }])
        ->orderBy('nama')
        ->get();

        // Get existing grades for all students - we'll calculate from bobot values
        $existingNilai = \App\Models\Nilai::whereIn('mahasiswaId', $mahasiswa->pluck('id'))
            ->whereHas('bobot.tahunAjaranMatkul', function($query) use ($mataKuliahDiampu) {
                $query->where('mataKuliahId', $mataKuliahDiampu->mataKuliahId)
                      ->where('tahunAjaranId', $mataKuliahDiampu->tahunAjaranId);
            })
            ->with('bobot.komponen')
            ->get()
            ->groupBy('mahasiswaId');

        return view('dosen.nilai.show', compact(
            'mataKuliahDiampu',
            'mataKuliahClasses',
            'mahasiswa',
            'kelasNumbers',
            'dosen',
            'dosenPengampu',
            'allKomponen',
            'existingNilai'
        ));
    }

    /**
     * Show form for inputting student grades.
     */
    public function input($matkulId, $mahasiswaId)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan.');
        }

        // Get mata kuliah data
        $mataKuliahDiampu = TahunAjaranMatkul::whereHas('dosenPengampu', function ($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })
        ->with([
            'mataKuliah',
            'tahunAjaran',
        ])
        ->findOrFail($matkulId);

        // Get CPMK untuk mata kuliah ini berdasarkan mataKuliahId dan tahunAjaranId (dari semua kelas)
        $cpmkMatKul = \App\Models\CpmkMatKul::whereHas('tahunAjaranMatkul', function($query) use ($mataKuliahDiampu) {
            $query->where('mataKuliahId', $mataKuliahDiampu->mataKuliahId)
                  ->where('tahunAjaranId', $mataKuliahDiampu->tahunAjaranId);
        })
        ->with(['cpmk'])
        ->get();

        // Ambil cpmkId dari CpmkMatKul
        $cpmkIds = $cpmkMatKul->pluck('cpmkId')->unique();

        // Get all bobot berdasarkan cpmkId yang ada
        $allBobot = \App\Models\Bobot::whereIn('cpmkId', $cpmkIds)
            ->whereHas('tahunAjaranMatkul', function($query) use ($mataKuliahDiampu) {
                $query->where('mataKuliahId', $mataKuliahDiampu->mataKuliahId)
                      ->where('tahunAjaranId', $mataKuliahDiampu->tahunAjaranId);
            })
            ->with(['komponen', 'cpmk'])
            ->get();

        // Get CPMK data yang tersedia
        $availableCpmk = $cpmkMatKul->pluck('cpmk')->filter()->unique('id');

        // Get student data
        $mahasiswa = Mahasiswa::findOrFail($mahasiswaId);

        // Verify student is enrolled in this course
        $isEnrolled = KelasMahasiswa::where('mahasiswaId', $mahasiswaId)
            ->whereHas('tahunAjaranMatkul', function($query) use ($mataKuliahDiampu) {
                $query->where('mataKuliahId', $mataKuliahDiampu->mataKuliahId)
                      ->where('tahunAjaranId', $mataKuliahDiampu->tahunAjaranId);
            })
            ->exists();

        if (!$isEnrolled) {
            return redirect()->back()->with('error', 'Mahasiswa tidak terdaftar di mata kuliah ini.');
        }

        // Get existing grades for this student
        $existingNilai = Nilai::where('mahasiswaId', $mahasiswaId)
            ->where('tahunAjaranMatkulId', $matkulId)
            ->with(['bobot.komponen', 'cpmk'])
            ->get();

        return view('dosen.nilai.input', compact(
            'mataKuliahDiampu',
            'mahasiswa',
            'existingNilai',
            'allBobot',
            'availableCpmk',
            'cpmkMatKul'
        ));
    }

    /**
     * Store student grades.
     */
    public function store(Request $request, $matkulId, $mahasiswaId)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan.');
        }

        $request->validate([
            'nilai' => 'required|array',
            'nilai.*' => 'required|numeric|min:0|max:100',
        ]);

        try {
            foreach ($request->nilai as $bobotId => $nilaiValue) {
                // Find the bobot record
                $bobot = \App\Models\Bobot::findOrFail($bobotId);

                Nilai::updateOrCreate([
                    'mahasiswaId' => $mahasiswaId,
                    'tahunAjaranMatkulId' => $matkulId,
                    'bobotId' => $bobot->id,
                ], [
                    'cpmkId' => $bobot->cpmkId,
                    'nilai' => $nilaiValue,
                ]);
            }

            return redirect()->route('dosen.nilai.show', $matkulId)
                ->with('success', 'Nilai berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan nilai.')
                ->withInput();
        }
    }

    /**
     * Store multiple student grades at once.
     */
    public function bulkStore(Request $request, $matkulId)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan.');
        }

        $request->validate([
            'nilai' => 'required|array',
            'nilai.*' => 'array',
            'nilai.*.*' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            // Get mata kuliah info
            $matkulClass = \App\Models\TahunAjaranMatkul::findOrFail($matkulId);

            // Get dosen pengampu ID for current dosen
            $dosenPengampu = \App\Models\DosenPengampu::where('dosenId', $dosen->id)
                ->where('tahunAjaranMatkulId', $matkulId)
                ->first();

            if (!$dosenPengampu) {
                return redirect()->back()->with('error', 'Data dosen pengampu tidak ditemukan.');
            }

            foreach ($request->nilai as $mahasiswaId => $komponenValues) {
                foreach ($komponenValues as $komponenId => $nilaiKomponen) {
                    if ($nilaiKomponen !== null && $nilaiKomponen !== '') {
                        // Get all bobot for this komponen in this mata kuliah
                        $bobotList = \App\Models\Bobot::where('komponenId', $komponenId)
                            ->whereHas('tahunAjaranMatkul', function($query) use ($matkulClass) {
                                $query->where('mataKuliahId', $matkulClass->mataKuliahId)
                                      ->where('tahunAjaranId', $matkulClass->tahunAjaranId);
                            })
                            ->with('cpmk')
                            ->get();

                        // Create nilai entry for each bobot
                        foreach ($bobotList as $bobot) {
                            // Calculate final score: nilaiKomponen * (bobot / 100)
                            $nilaiAkhir = $nilaiKomponen * ($bobot->bobot / 100);

                            Nilai::updateOrCreate([
                                'mahasiswaId' => $mahasiswaId,
                                'tahunAjaranMatkulId' => $matkulId,
                                'bobotId' => $bobot->id,
                            ], [
                                'cpmkId' => $bobot->cpmkId,
                                'dosenPengampuId' => $dosenPengampu->id,
                                'nilai' => $nilaiAkhir,
                            ]);
                        }
                    } else {
                        // Delete if value is empty - remove all nilai for this komponen
                        $bobotIds = \App\Models\Bobot::where('komponenId', $komponenId)
                            ->whereHas('tahunAjaranMatkul', function($query) use ($matkulClass) {
                                $query->where('mataKuliahId', $matkulClass->mataKuliahId)
                                      ->where('tahunAjaranId', $matkulClass->tahunAjaranId);
                            })
                            ->pluck('id');

                        Nilai::where('mahasiswaId', $mahasiswaId)
                            ->where('tahunAjaranMatkulId', $matkulId)
                            ->whereIn('bobotId', $bobotIds)
                            ->delete();
                    }
                }
            }

            return redirect()->back()->with('success', 'Nilai berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan nilai: ' . $e->getMessage())
                ->withInput();
        }
    }
}
