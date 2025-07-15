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
use Illuminate\Support\Facades\Log;

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
                      ->whereHas('dosenPengampu', function($q) use ($dosen) {
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
        $relatedTahunAjaranMatkulIds = $mataKuliahClasses->pluck('id');

        // Get lecturer's role in this course
        $dosenPengampu = $allDosenPengampu->where('dosenId', $dosen->id)->first();

        // Get all komponen for this mata kuliah and tahun ajaran (from all related classes)
        $allKomponen = \App\Models\Komponen::whereHas('bobot.tahunAjaranMatkul', function($query) use ($relatedTahunAjaranMatkulIds) {
            $query->whereIn('id', $relatedTahunAjaranMatkulIds);
        })
        ->with(['bobot' => function($query) use ($relatedTahunAjaranMatkulIds) {
            $query->whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds)->with(['cpmk']);
        }])
        ->orderBy('nama')
        ->get();

        // Get existing grades for all students - we'll calculate from bobot values (from all related classes)
        $existingNilai = \App\Models\Nilai::whereIn('mahasiswaId', $mahasiswa->pluck('id'))
            ->whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds)
            ->with('bobot.komponen')
            ->get()
            ->groupBy('mahasiswaId');

        // Get final grades from kelasMahasiswa table (from all related classes)
        $nilaiMahasiswa = KelasMahasiswa::whereIn('mahasiswaId', $mahasiswa->pluck('id'))
            ->whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds)
            ->with('mahasiswa')
            ->get()
            ->keyBy('mahasiswaId');

        return view('dosen.nilai.show', compact(
            'mataKuliahDiampu',
            'mataKuliahClasses',
            'mahasiswa',
            'kelasNumbers',
            'dosen',
            'dosenPengampu',
            'allKomponen',
            'existingNilai',
            'nilaiMahasiswa'
        ));
    }

    /**
     * Show form for inputting student grades.
     * NOTE: This method is currently not used as we use inline input in the show view
     */
    /*
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

        // Get all related TahunAjaranMatkul records for the same mata kuliah, tahun ajaran, and dosen
        $relatedTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $mataKuliahDiampu->mataKuliahId)
            ->where('tahunAjaranId', $mataKuliahDiampu->tahunAjaranId)
            ->whereHas('dosenPengampu', function($query) use ($dosen) {
                $query->where('dosenId', $dosen->id);
            })
            ->pluck('id');

        // Get CPMK untuk mata kuliah ini berdasarkan mataKuliahId dan tahunAjaranId (dari semua kelas dengan dosen yang sama)
        $cpmkMatKul = \App\Models\CpmkMatKul::whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds)
        ->with(['cpmk'])
        ->get();

        // Ambil cpmkId dari CpmkMatKul
        $cpmkIds = $cpmkMatKul->pluck('cpmkId')->unique();

        // Get all bobot berdasarkan cpmkId yang ada (dari semua kelas dengan dosen yang sama)
        $allBobot = \App\Models\Bobot::whereIn('cpmkId', $cpmkIds)
            ->whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds)
            ->with(['komponen', 'cpmk'])
            ->get();

        // Get CPMK data yang tersedia
        $availableCpmk = $cpmkMatKul->pluck('cpmk')->filter()->unique('id');

        // Get student data
        $mahasiswa = Mahasiswa::findOrFail($mahasiswaId);

        // Verify student is enrolled in this course (check all related classes)
        $isEnrolled = KelasMahasiswa::where('mahasiswaId', $mahasiswaId)
            ->whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds)
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
    */

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

            // Calculate total score for this student and update nilaiMahasiswa table
            $this->calculateAndStoreTotal($mahasiswaId, $matkulId);

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
                $hasValidGrades = false; // Track if this student has any valid grades

                foreach ($komponenValues as $komponenId => $nilaiKomponen) {
                    if ($nilaiKomponen !== null && $nilaiKomponen !== '') {
                        $hasValidGrades = true; // Mark that this student has valid grades

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

                // Only calculate and store total if there are valid grades for this student
                // or if we need to update existing grades
                $this->calculateAndStoreTotal($mahasiswaId, $matkulId);
            }

            return redirect()->back()->with('success', 'Nilai berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan nilai: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Store individual student grades (AJAX).
     */
    public function storeIndividual(Request $request, $matkulId)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            return response()->json(['success' => false, 'message' => 'Data dosen tidak ditemukan.'], 403);
        }

        $request->validate([
            'nilai.*.*' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            // Get mata kuliah info
            $matkulClass = TahunAjaranMatkul::findOrFail($matkulId);

            // Get dosen pengampu ID for current dosen
            $dosenPengampu = \App\Models\DosenPengampu::where('dosenId', $dosen->id)
                ->where('tahunAjaranMatkulId', $matkulId)
                ->first();

            if (!$dosenPengampu) {
                return response()->json(['success' => false, 'message' => 'Data dosen pengampu tidak ditemukan.'], 404);
            }

            // Process the nilai data
            foreach ($request->nilai as $mahasiswaId => $komponenValues) {
                $hasValidGrades = false; // Track if this student has any valid grades

                foreach ($komponenValues as $komponenId => $nilaiKomponen) {
                    if ($nilaiKomponen !== null && $nilaiKomponen !== '') {
                        $hasValidGrades = true; // Mark that this student has valid grades

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

                // Only calculate and store total if there are valid grades for this student
                // or if we need to update existing grades
                $this->calculateAndStoreTotal($mahasiswaId, $matkulId);
            }

            return response()->json(['success' => true, 'message' => 'Nilai berhasil disimpan.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan nilai: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Calculate total score for a student and store in kelasMahasiswa table
     */
    private function calculateAndStoreTotal($mahasiswaId, $matkulId)
    {
        try {
            // Get the main TahunAjaranMatkul record
            $tahunAjaranMatkul = TahunAjaranMatkul::findOrFail($matkulId);

            // Get all related TahunAjaranMatkul records for the same mata kuliah, tahun ajaran, and dosen
            $relatedTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
                ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
                ->whereHas('dosenPengampu', function($query) {
                    $query->where('dosenId', Auth::user()->dosen->id);
                })
                ->pluck('id');

            // Get all nilai for this student in this mata kuliah (from all related classes)
            $allNilai = Nilai::where('mahasiswaId', $mahasiswaId)
                ->whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds)
                ->with('bobot')
                ->get();

            // Only calculate and update if there are actual grades
            if ($allNilai->isNotEmpty()) {
                // Calculate total score by summing all nilai
                $totalNilai = $allNilai->sum('nilai');

                // Determine grade based on total score
                $grade = $this->calculateGrade($totalNilai);

                // Find the specific KelasMahasiswa record for this student (which class they're enrolled in)
                $kelasMahasiswa = KelasMahasiswa::where('mahasiswaId', $mahasiswaId)
                    ->whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds)
                    ->first();

                if ($kelasMahasiswa) {
                    $kelasMahasiswa->update([
                        'totalNilai' => $totalNilai,
                        'grade' => $grade,
                    ]);
                }
            } else {
                // If no grades exist, don't set total to 0, leave as is or set to null
                $kelasMahasiswa = KelasMahasiswa::where('mahasiswaId', $mahasiswaId)
                    ->whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds)
                    ->first();

                if ($kelasMahasiswa && $kelasMahasiswa->totalNilai === null) {
                    // Only update if totalNilai is currently null (first time)
                    // Don't overwrite existing grades with 0
                    // This preserves existing grades if they exist
                }
            }

        } catch (\Exception $e) {
            Log::error('Error calculating total score: ' . $e->getMessage());
        }
    }

    /**
     * Calculate grade based on total score
     */
    private function calculateGrade($totalScore)
    {
        // Don't assign grade if total score is null (no grades inputted)
        if ($totalScore === null) {
            return null;
        }

        // If total score is 0 or negative, assign grade E
        if ($totalScore <= 0) {
            return 'E';
        }

        if ($totalScore >= 80) {
            return 'A';
        } elseif ($totalScore >= 75) {
            return 'A-';
        } elseif ($totalScore >= 70) {
            return 'B+';
        } elseif ($totalScore >= 65) {
            return 'B';
        } elseif ($totalScore >= 60) {
            return 'B-';
        } elseif ($totalScore >= 55) {
            return 'C+';
        } elseif ($totalScore >= 50) {
            return 'C';
        } elseif ($totalScore >= 45) {
            return 'D';
        } else {
            return 'E';
        }
    }
}
