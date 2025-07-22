<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\TahunAjaranMatkul;
use App\Models\TahunAjaran;
use App\Models\Mahasiswa;
use App\Models\KelasMahasiswa;
use App\Models\Nilai;
use App\Models\Bobot;
use App\Models\Komponen;
use App\Models\DosenPengampu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NilaiController extends Controller
{
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

        // Get filter parameters
        $tahunAjaranId = $request->get('tahun_ajaran_id');
        $jenis = $request->get('jenis');

        // Build query for TahunAjaranMatkul
        $query = TahunAjaranMatkul::with([
            'mataKuliah',
            'tahunAjaran',
            'dosenPengampu.dosen', // Load all dosen pengampu with their dosen data
            'kelasMahasiswa'
        ])->whereHas('dosenPengampu', function($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        });

        // Apply filters
        if ($tahunAjaranId) {
            $query->where('tahunAjaranId', $tahunAjaranId);
        }

        if ($jenis) {
            $query->whereHas('mataKuliah', function($query) use ($jenis) {
                $query->where('jenis', $jenis);
            });
        }

        // Get filtered results
        $allMataKuliah = $query->orderBy('tahunAjaranId', 'desc')
            ->orderBy('mataKuliahId')
            ->get();

        // Group by mata kuliah and tahun ajaran to combine different classes
        $mataKuliahDiampu = $allMataKuliah->groupBy(function($item) {
            return $item->mataKuliahId . '_' . $item->tahunAjaranId;
        })->map(function($group) {
            // Get the first item as representative
            $representative = $group->first();

            // Combine all kelas mahasiswa from all classes
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
            $representative->setRelation('dosenPengampu', $allDosenPengampu->unique('dosenId'));

            // Get unique dosen pengampu with their names
            $uniqueDosenPengampu = $allDosenPengampu->unique('dosenId');
            $dosenNames = $uniqueDosenPengampu->map(function($dosenPengampu) {
                return $dosenPengampu->dosen->nama ?? 'Unknown';
            })->unique()->values();
            $representative->dosenPengampuNames = $dosenNames;

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
    public function show(Request $request, $id)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan.');
        }

        // Get mata kuliah info
        $mataKuliahDiampu = TahunAjaranMatkul::with([
            'mataKuliah',
            'tahunAjaran',
            'dosenPengampu' => function($query) use ($dosen) {
                $query->where('dosenId', $dosen->id);
            }
        ])->findOrFail($id);

        // Verify access
        if (!$mataKuliahDiampu->dosenPengampu->contains('dosenId', $dosen->id)) {
            return redirect()->route('dosen.nilai.index')->with('error', 'Akses ditolak.');
        }

        // Get all related classes for this mata kuliah and tahun ajaran
        $mataKuliahClasses = TahunAjaranMatkul::with([
            'kelasMahasiswa.mahasiswa',
            'dosenPengampu'
        ])
        ->where('mataKuliahId', $mataKuliahDiampu->mataKuliahId)
        ->where('tahunAjaranId', $mataKuliahDiampu->tahunAjaranId)
        ->whereHas('dosenPengampu', function($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })
        ->orderBy('kelas')
        ->get();

        // Get all related tahunAjaranMatkul IDs for queries
        $relatedTahunAjaranMatkulIds = $mataKuliahClasses->pluck('id');

        // Get all unique mahasiswa across all classes
        $allMahasiswa = collect();
        foreach ($mataKuliahClasses as $class) {
            $allMahasiswa = $allMahasiswa->merge($class->kelasMahasiswa->pluck('mahasiswa'));
        }
        $allMahasiswaCollection = $allMahasiswa->unique('id')->values();

        // Handle pagination, sorting, and search - get request parameters
        $perPage = 10; // Number of students per page
        $currentPage = $request->get('page', 1);
        $isBulkMode = $request->get('bulk', false);
        $activeTab = $request->get('tab', 'all'); // Get active tab
        $sortBy = $request->get('sort', 'nim'); // Default sort by NIM
        $sortDirection = $request->get('direction', 'asc'); // Default ascending
        $search = $request->get('search', ''); // Search parameter

        // Apply sorting to all mahasiswa collection
        $allMahasiswaCollection = $allMahasiswaCollection->sortBy(function($mahasiswa) use ($sortBy) {
            switch ($sortBy) {
                case 'nim':
                    return $mahasiswa->nim;
                case 'nama':
                    return $mahasiswa->nama;
                default:
                    return $mahasiswa->nim;
            }
        });

        // Reverse if descending
        if ($sortDirection === 'desc') {
            $allMahasiswaCollection = $allMahasiswaCollection->reverse();
        }

        $allMahasiswaCollection = $allMahasiswaCollection->values();

        // Apply search filter if search term is provided
        if (!empty($search)) {
            $allMahasiswaCollection = $allMahasiswaCollection->filter(function($mahasiswa) use ($search) {
                $searchTerm = strtolower($search);
                return (
                    stripos($mahasiswa->nim, $searchTerm) !== false ||
                    stripos($mahasiswa->nama, $searchTerm) !== false
                );
            })->values();
        }

        // If bulk mode, show all students without pagination
        if ($isBulkMode) {
            $mahasiswa = $allMahasiswaCollection;
            $mahasiswaPaginated = null;
            $mahasiswaByKelas = [];
        } else {
            // Group mahasiswa by kelas first for pagination
            $mahasiswaByKelas = [];
            foreach ($allMahasiswaCollection as $mhs) {
                $kelasNumber = 'Tidak Ada Kelas';

                // Find the class this student belongs to
                foreach($mataKuliahClasses as $class) {
                    $studentInClass = $class->kelasMahasiswa->where('mahasiswaId', $mhs->id)->first();
                    if ($studentInClass) {
                        $kelasNumber = $class->kelas;
                        break;
                    }
                }

                // Convert numeric class to letter
                $kelasHuruf = is_numeric($kelasNumber) ?
                    \App\Models\TahunAjaranMatkul::convertKelasToHuruf($kelasNumber) :
                    $kelasNumber;

                if (!isset($mahasiswaByKelas[$kelasHuruf])) {
                    $mahasiswaByKelas[$kelasHuruf] = [];
                }
                $mahasiswaByKelas[$kelasHuruf][] = $mhs;
            }

            // Sort by kelas
            uksort($mahasiswaByKelas, function($a, $b) {
                if ($a === 'Tidak Ada Kelas') return 1;
                if ($b === 'Tidak Ada Kelas') return -1;
                return strcmp($a, $b);
            });

            // Apply pagination based on active tab
            if ($activeTab === 'all') {
                // Paginate all students
                $totalStudents = $allMahasiswaCollection->count();
                $offset = ($currentPage - 1) * $perPage;
                $mahasiswa = $allMahasiswaCollection->slice($offset, $perPage)->values();

                $mahasiswaPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
                    $mahasiswa,
                    $totalStudents,
                    $perPage,
                    $currentPage,
                    [
                        'path' => $request->url(),
                        'pageName' => 'page',
                    ]
                );
                $mahasiswaPaginated->appends($request->query());
            } else {
                // Paginate specific class
                $kelasSlug = str_replace('kelas-', '', $activeTab);
                $kelasName = null;

                // Find the corresponding class name
                foreach ($mahasiswaByKelas as $kelasHuruf => $students) {
                    if (\Illuminate\Support\Str::slug($kelasHuruf) === $kelasSlug) {
                        $kelasName = $kelasHuruf;
                        break;
                    }
                }

                if ($kelasName && isset($mahasiswaByKelas[$kelasName])) {
                    $kelasStudents = collect($mahasiswaByKelas[$kelasName]);
                    $totalStudents = $kelasStudents->count();
                    $offset = ($currentPage - 1) * $perPage;
                    $mahasiswa = $kelasStudents->slice($offset, $perPage)->values();

                    $mahasiswaPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
                        $mahasiswa,
                        $totalStudents,
                        $perPage,
                        $currentPage,
                        [
                            'path' => $request->url(),
                            'pageName' => 'page',
                        ]
                    );
                    $mahasiswaPaginated->appends($request->query());
                } else {
                    // Fallback to all students if class not found
                    $totalStudents = $allMahasiswaCollection->count();
                    $offset = ($currentPage - 1) * $perPage;
                    $mahasiswa = $allMahasiswaCollection->slice($offset, $perPage)->values();

                    $mahasiswaPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
                        $mahasiswa,
                        $totalStudents,
                        $perPage,
                        $currentPage,
                        [
                            'path' => $request->url(),
                            'pageName' => 'page',
                        ]
                    );
                    $mahasiswaPaginated->appends($request->query());
                }
            }
        }

        // Get all unique kelas numbers for display
        $kelasNumbers = $mataKuliahClasses->pluck('kelas')->unique()->sort()->values();

        // Get all components for this mata kuliah through bobot table
        $allKomponen = Komponen::whereHas('bobot', function($query) use ($relatedTahunAjaranMatkulIds) {
                $query->whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds);
            })
            ->with(['bobot' => function($query) use ($relatedTahunAjaranMatkulIds) {
                $query->whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds);
            }])
            ->orderBy('nama')
            ->get();

        // Get existing nilai for displayed students
        $existingNilai = Nilai::with('bobot')
            ->whereIn('mahasiswaId', $mahasiswa->pluck('id'))
            ->whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds)
            ->get()
            ->groupBy('mahasiswaId');

        // Get final grades from kelasMahasiswa table for displayed students
        $nilaiMahasiswa = KelasMahasiswa::whereIn('mahasiswaId', $mahasiswa->pluck('id'))
            ->whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds)
            ->get()
            ->keyBy('mahasiswaId');

        // Hitung total bobot CPMK dan total bobot setiap komponen penilaian
        $totalBobotCpmk = 0;
        $totalBobotKomponen = [];
        $allBobot = Bobot::whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds)->get();
        foreach ($allBobot as $bobot) {
            $totalBobotCpmk += $bobot->bobotCpmk ?? 0;
            $totalBobotKomponen[$bobot->komponenId] = ($totalBobotKomponen[$bobot->komponenId] ?? 0) + $bobot->bobot;
        }

        return view('dosen.nilai.show', compact(
            'mataKuliahDiampu',
            'mataKuliahClasses',
            'mahasiswa',
            'mahasiswaPaginated',
            'allMahasiswaCollection',
            'mahasiswaByKelas',
            'kelasNumbers',
            'allKomponen',
            'existingNilai',
            'nilaiMahasiswa',
            'dosen',
            'isBulkMode',
            'activeTab',
            'sortBy',
            'sortDirection',
            'search',
            'totalBobotCpmk',
            'totalBobotKomponen',
        ));
    }

    /**
     * Store bulk grades for multiple students.
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
            'student_class_id' => 'nullable|array',
            'student_class_id.*' => 'nullable|exists:tahun_ajaran_matkul,id',
        ]);

        try {
            // Get mata kuliah info
            $matkulClass = TahunAjaranMatkul::findOrFail($matkulId);

            // Get all related classes for this mata kuliah and dosen
            $relatedClasses = TahunAjaranMatkul::where('mataKuliahId', $matkulClass->mataKuliahId)
                ->where('tahunAjaranId', $matkulClass->tahunAjaranId)
                ->whereHas('dosenPengampu', function($query) use ($dosen) {
                    $query->where('dosenId', $dosen->id);
                })
                ->get();

            foreach ($request->nilai as $mahasiswaId => $komponenValues) {
                // Get student's class ID from the request
                $studentClassId = $request->student_class_id[$mahasiswaId] ?? null;

                if (!$studentClassId) {
                    continue; // Skip if no class ID provided
                }

                // Verify the class exists and is taught by this dosen
                $studentClass = $relatedClasses->firstWhere('id', $studentClassId);
                if (!$studentClass) {
                    continue; // Skip if class not found or not taught by this dosen
                }

                // Get dosen pengampu ID for current dosen in this specific class
                $dosenPengampu = DosenPengampu::where('dosenId', $dosen->id)
                    ->where('tahunAjaranMatkulId', $studentClass->id)
                    ->first();

                if (!$dosenPengampu) {
                    continue; // Skip if dosen pengampu not found for this class
                }

                foreach ($komponenValues as $komponenId => $nilaiKomponen) {
                    if ($nilaiKomponen !== null && $nilaiKomponen !== '') {
                        // Get all bobot for this komponen in this student's specific class
                        $bobotList = Bobot::where('komponenId', $komponenId)
                            ->where('tahunAjaranMatkulId', $studentClass->id)
                            ->with('cpmk')
                            ->get();

                        // Create nilai entry for each bobot
                        foreach ($bobotList as $bobot) {
                            // Calculate final score: nilaiKomponen * (bobot / 100)
                            $nilaiAkhir = $nilaiKomponen * ($bobot->bobot / 100);

                            Nilai::updateOrCreate([
                                'mahasiswaId' => $mahasiswaId,
                                'tahunAjaranMatkulId' => $studentClass->id,
                                'bobotId' => $bobot->id,
                            ], [
                                'cpmkId' => $bobot->cpmkId,
                                'dosenPengampuId' => $dosenPengampu->id,
                                'nilai' => $nilaiAkhir,
                            ]);
                        }
                    } else {
                        // Delete if value is empty - remove all nilai for this komponen
                        $bobotIds = Bobot::where('komponenId', $komponenId)
                            ->where('tahunAjaranMatkulId', $studentClass->id)
                            ->pluck('id');

                        Nilai::where('mahasiswaId', $mahasiswaId)
                            ->where('tahunAjaranMatkulId', $studentClass->id)
                            ->whereIn('bobotId', $bobotIds)
                            ->delete();
                    }
                }

                // Calculate and store total for this student
                $this->calculateAndStoreTotal($mahasiswaId, $studentClass->id);
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
            'student_class_id' => 'nullable|array',
            'student_class_id.*' => 'nullable|exists:tahun_ajaran_matkul,id',
        ]);

        try {
            // Get mata kuliah info
            $matkulClass = TahunAjaranMatkul::findOrFail($matkulId);

            // Get all related classes for this mata kuliah and dosen
            $relatedClasses = TahunAjaranMatkul::where('mataKuliahId', $matkulClass->mataKuliahId)
                ->where('tahunAjaranId', $matkulClass->tahunAjaranId)
                ->whereHas('dosenPengampu', function($query) use ($dosen) {
                    $query->where('dosenId', $dosen->id);
                })
                ->get();

            // Process the nilai data
            foreach ($request->nilai as $mahasiswaId => $komponenValues) {
                // Get student's class ID from the request
                $studentClassId = $request->student_class_id[$mahasiswaId] ?? null;

                if (!$studentClassId) {
                    continue; // Skip if no class ID provided
                }

                // Verify the class exists and is taught by this dosen
                $studentClass = $relatedClasses->firstWhere('id', $studentClassId);
                if (!$studentClass) {
                    continue; // Skip if class not found or not taught by this dosen
                }

                // Get dosen pengampu ID for current dosen in this specific class
                $dosenPengampu = DosenPengampu::where('dosenId', $dosen->id)
                    ->where('tahunAjaranMatkulId', $studentClass->id)
                    ->first();

                if (!$dosenPengampu) {
                    continue; // Skip if dosen pengampu not found for this class
                }

                foreach ($komponenValues as $komponenId => $nilaiKomponen) {
                    if ($nilaiKomponen !== null && $nilaiKomponen !== '') {
                        // Get all bobot for this komponen in this student's specific class
                        $bobotList = Bobot::where('komponenId', $komponenId)
                            ->where('tahunAjaranMatkulId', $studentClass->id)
                            ->with('cpmk')
                            ->get();

                        // Create nilai entry for each bobot
                        foreach ($bobotList as $bobot) {
                            // Calculate final score: nilaiKomponen * (bobot / 100)
                            $nilaiAkhir = $nilaiKomponen * ($bobot->bobot / 100);

                            Nilai::updateOrCreate([
                                'mahasiswaId' => $mahasiswaId,
                                'tahunAjaranMatkulId' => $studentClass->id,
                                'bobotId' => $bobot->id,
                            ], [
                                'cpmkId' => $bobot->cpmkId,
                                'dosenPengampuId' => $dosenPengampu->id,
                                'nilai' => $nilaiAkhir,
                            ]);
                        }
                    } else {
                        // Delete if value is empty - remove all nilai for this komponen
                        $bobotIds = Bobot::where('komponenId', $komponenId)
                            ->where('tahunAjaranMatkulId', $studentClass->id)
                            ->pluck('id');

                        Nilai::where('mahasiswaId', $mahasiswaId)
                            ->where('tahunAjaranMatkulId', $studentClass->id)
                            ->whereIn('bobotId', $bobotIds)
                            ->delete();
                    }
                }

                // Calculate and store total for this student
                $this->calculateAndStoreTotal($mahasiswaId, $studentClass->id);
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
            // Get the specific TahunAjaranMatkul record for this class
            $tahunAjaranMatkul = TahunAjaranMatkul::findOrFail($matkulId);

            // Get all nilai for this student in this specific class only
            $allNilai = Nilai::where('mahasiswaId', $mahasiswaId)
                ->where('tahunAjaranMatkulId', $matkulId)
                ->with('bobot')
                ->get();

            // Only calculate and update if there are actual grades
            if ($allNilai->isNotEmpty()) {
                // Calculate total score by summing all nilai
                $totalNilai = $allNilai->sum('nilai');

                // Determine grade based on total score
                $grade = $this->calculateGrade($totalNilai);

                // Find the specific KelasMahasiswa record for this student in this class
                $kelasMahasiswa = KelasMahasiswa::where('mahasiswaId', $mahasiswaId)
                    ->where('tahunAjaranMatkulId', $matkulId)
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
                    ->where('tahunAjaranMatkulId', $matkulId)
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
