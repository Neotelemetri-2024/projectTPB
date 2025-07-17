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
            'dosenPengampu' => function($query) use ($dosen) {
                $query->where('dosenId', $dosen->id);
            },
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
        $mahasiswa = $allMahasiswa->unique('id')->values();

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

        // Get existing nilai for all students in all related classes
        $existingNilai = Nilai::with('bobot')
            ->whereIn('mahasiswaId', $mahasiswa->pluck('id'))
            ->whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds)
            ->get()
            ->groupBy('mahasiswaId');

        // Get final grades from kelasMahasiswa table for all students
        $nilaiMahasiswa = KelasMahasiswa::whereIn('mahasiswaId', $mahasiswa->pluck('id'))
            ->whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds)
            ->get()
            ->keyBy('mahasiswaId');

        return view('dosen.nilai.show', compact(
            'mataKuliahDiampu',
            'mataKuliahClasses',
            'mahasiswa',
            'kelasNumbers',
            'allKomponen',
            'existingNilai',
            'nilaiMahasiswa',
            'dosen'
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
