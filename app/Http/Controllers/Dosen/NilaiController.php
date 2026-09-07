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
use App\Models\CpmkMatKul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Exports\NilaiTemplateExport;
use App\Imports\NilaiImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Jobs\ProcessNilaiImport;

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

        $latestTahunAjaran = TahunAjaran::orderBy('tahun', 'desc')->orderBy('periode', 'desc')->first();

        if ($request->has('tahun_ajaran_id')) {
            $selectedTahunAjaranId = $request->tahun_ajaran_id;
        } else {
            $selectedTahunAjaranId = $latestTahunAjaran ? $latestTahunAjaran->id : null;
        }

        $jenis = $request->get('jenis');
        $search = $request->get('search');

        $query = TahunAjaranMatkul::with([
            'mataKuliah',
            'tahunAjaran',
            'kelas' => function ($q) {
                $q->withCount('kelasMahasiswa')
                    ->with(['dosenPengampuKelas.dosen']);
            },
        ])->whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        });

        if ($selectedTahunAjaranId) {
            $query->where('tahunAjaranId', $selectedTahunAjaranId);
        }

        if ($jenis) {
            $query->whereHas('mataKuliah', function ($query) use ($jenis) {
                $query->where('jenis', $jenis);
            });
        }

        if ($search) {
            $query->whereHas('mataKuliah', function ($q) use ($search) {
                $q->where('namaMatkul', 'like', "%{$search}%")
                    ->orWhere('kodeMatkul', 'like', "%{$search}%");
            });
        }

        $allMataKuliah = $query->orderBy('tahunAjaranId', 'desc')
            ->orderBy('mataKuliahId')
            ->get();

        $mataKuliahDiampu = $allMataKuliah->groupBy(function ($item) {
            return $item->mataKuliahId . '_' . $item->tahunAjaranId;
        })->map(function ($group) {
            $representative = $group->first();

            $allDosenPengampu = collect();
            $allKelas = collect();
            $mahasiswaCount = 0;

            foreach ($group as $item) {
                foreach ($item->kelas as $kelas) {
                    $allKelas->push($kelas->namaKelas);
                    $allDosenPengampu = $allDosenPengampu->merge($kelas->dosenPengampuKelas);
                    $mahasiswaCount += (int) ($kelas->kelas_mahasiswa_count ?? 0);
                }
            }

            $representative->mahasiswa_count = $mahasiswaCount;
            $representative->setRelation('dosenPengampuKelas', $allDosenPengampu->unique('id'));

            $uniqueDosenPengampu = $allDosenPengampu->unique('dosenId');
            $dosenNames = $uniqueDosenPengampu->map(function ($dosenPengampuKelas) {
                return $dosenPengampuKelas->dosen->nama ?? 'Unknown';
            })->unique()->values();
            $representative->dosenPengampuNames = $dosenNames;
            $representative->kelasNames = $allKelas->unique()->sort()->values();
            $representative->groupedItems = $group;

            return $representative;
        })->values();

        $perPage = 10;
        $page = (int) $request->get('page', 1);
        $mataKuliahDiampu = new \Illuminate\Pagination\LengthAwarePaginator(
            $mataKuliahDiampu->forPage($page, $perPage)->values(),
            $mataKuliahDiampu->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $tahunAjaranList = TahunAjaran::orderBy('tahun', 'desc')->orderBy('periode', 'desc')->get();
        $jenisList = ['wajib', 'pilihan'];

        return view('dosen.nilai.index', compact(
            'mataKuliahDiampu',
            'dosen',
            'tahunAjaranList',
            'jenisList',
            'selectedTahunAjaranId'
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

        // Get mata kuliah info (sekarang ini adalah tahunAjaranMatkul)
        $tahunAjaranMatkul = TahunAjaranMatkul::with([
            'mataKuliah',
            'tahunAjaran',
            'kelas.dosenPengampuKelas' => function ($query) use ($dosen) {
                $query->where('dosenId', $dosen->id);
            }
        ])->findOrFail($id);

        // Verify access - check if dosen has access to any kelas in this tahunAjaranMatkul
        $hasAccess = $tahunAjaranMatkul->kelas->some(function ($kelas) use ($dosen) {
            return $kelas->dosenPengampuKelas->contains('dosenId', $dosen->id);
        });

        if (!$hasAccess) {
            return redirect()->route('dosen.nilai.index')->with('error', 'Akses ditolak.');
        }

        // Get classes for this mata kuliah/tahun ajaran (tanpa load semua mahasiswa)
        $mataKuliahClasses = TahunAjaranMatkul::with([
            'kelas' => fn ($q) => $q->withCount('kelasMahasiswa')->with('dosenPengampuKelas'),
        ])
            ->where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
            ->get()
            ->filter(function ($tam) use ($dosen) {
                return $tam->kelas->some(function ($kelas) use ($dosen) {
                    return $kelas->dosenPengampuKelas->contains('dosenId', $dosen->id);
                });
            })
            ->values();

        $relatedTahunAjaranMatkulIds = $mataKuliahClasses->pluck('id');
        $allowedKelasIds = $mataKuliahClasses->flatMap->kelas
            ->filter(fn ($kelas) => $kelas->dosenPengampuKelas->contains('dosenId', $dosen->id))
            ->pluck('id')
            ->unique()
            ->values();

        // Get all CPMK for this mata kuliah
        $cpmkList = CpmkMatKul::with(['cpmk.cpl'])
            ->whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds)
            ->get()
            ->unique('cpmkId')
            ->map(function ($cpmkMatKul) {
                return $cpmkMatKul->cpmk;
            });

        // Get bobot data for all classes (needed for readiness check)
        $bobotData = Bobot::with(['komponen', 'cpmk'])
            ->whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds)
            ->get();

        $bobotDataGrouped = $bobotData->groupBy('tahunAjaranMatkulId');
        $representativeBobotData = $bobotDataGrouped->get($id) ?? $bobotDataGrouped->first() ?? collect();
        $totalBobotKeseluruhan = $representativeBobotData->sum('bobot');
        $isPenilaianSiap = $totalBobotKeseluruhan == 100;
        $adaCpmk = $cpmkList->count() > 0;

        $perPage = 10;
        $isBulkMode = (bool) $request->get('bulk', false);
        $activeTab = $request->get('tab', 'all');
        $sortBy = $request->get('sort', 'nim');
        $sortDirection = $request->get('direction', 'asc') === 'desc' ? 'desc' : 'asc';
        $search = $request->get('search', '');

        // Jumlah per kelas (untuk tab) — query agregasi, bukan load semua row
        $mahasiswaByKelasCounts = $allowedKelasIds->isEmpty()
            ? collect()
            : KelasMahasiswa::query()
                ->select('kelas.namaKelas', DB::raw('COUNT(DISTINCT kelas_mahasiswa.mahasiswaId) as total'))
                ->join('kelas', 'kelas.id', '=', 'kelas_mahasiswa.kelasId')
                ->whereIn('kelas.id', $allowedKelasIds)
                ->groupBy('kelas.namaKelas')
                ->orderBy('kelas.namaKelas')
                ->pluck('total', 'namaKelas');

        $mahasiswaByKelas = $mahasiswaByKelasCounts->all();
        $totalMahasiswaCount = (int) $mahasiswaByKelasCounts->sum();
        $allMahasiswaCollection = collect()->pad($totalMahasiswaCount, null);

        $mahasiswaQuery = Mahasiswa::query()
            ->select('mahasiswa.*')
            ->join('kelas_mahasiswa', 'kelas_mahasiswa.mahasiswaId', '=', 'mahasiswa.id')
            ->join('kelas', 'kelas.id', '=', 'kelas_mahasiswa.kelasId')
            ->when($allowedKelasIds->isNotEmpty(), fn ($q) => $q->whereIn('kelas.id', $allowedKelasIds))
            ->when($allowedKelasIds->isEmpty(), fn ($q) => $q->whereRaw('1 = 0'))
            ->distinct();

        if ($activeTab !== 'all') {
            $kelasSlug = str_replace('kelas-', '', $activeTab);
            $matchedKelas = $mahasiswaByKelasCounts->keys()->first(function ($kelasNama) use ($kelasSlug) {
                return strtolower(str_replace(' ', '-', $kelasNama)) === $kelasSlug
                    || \Illuminate\Support\Str::slug($kelasNama) === $kelasSlug;
            });

            if ($matchedKelas) {
                $mahasiswaQuery->where('kelas.namaKelas', $matchedKelas);
            } else {
                $mahasiswaQuery->whereRaw('1 = 0');
            }
        }

        if ($search !== '') {
            $mahasiswaQuery->where(function ($q) use ($search) {
                $q->where('mahasiswa.nim', 'like', "%{$search}%")
                    ->orWhere('mahasiswa.nama', 'like', "%{$search}%");
            });
        }

        $sortColumn = $sortBy === 'nama' ? 'mahasiswa.nama' : 'mahasiswa.nim';
        $mahasiswaQuery->orderBy($sortColumn, $sortDirection);

        $mahasiswaPaginated = null;
        if ($isBulkMode) {
            $mahasiswa = $mahasiswaQuery->get();
        } else {
            $mahasiswaPaginated = $mahasiswaQuery->paginate($perPage)->withQueryString();
            $mahasiswa = collect($mahasiswaPaginated->items());
        }

        $kelasNumbers = $mataKuliahClasses->flatMap->kelas->pluck('namaKelas')->unique()->sort()->values();

        // Map kelas untuk mahasiswa yang ditampilkan saja
        $kelasMahasiswaMap = $mahasiswa->isEmpty()
            ? collect()
            : KelasMahasiswa::with('kelas')
                ->whereIn('mahasiswaId', $mahasiswa->pluck('id'))
                ->whereIn('kelasId', $allowedKelasIds)
                ->get()
                ->keyBy('mahasiswaId');

        // Get all components for this mata kuliah through bobot table
        $allKomponen = Komponen::whereHas('bobot', function ($query) use ($relatedTahunAjaranMatkulIds) {
            $query->whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds);
        })
            ->with(['bobot' => function ($query) use ($relatedTahunAjaranMatkulIds) {
                $query->whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds);
            }])
            ->orderBy('nama')
            ->get();

        $displayedMahasiswaIds = $mahasiswa->pluck('id');
        $nilaiData = $displayedMahasiswaIds->isEmpty()
            ? collect()
            : Nilai::with(['mahasiswa', 'cpmk', 'bobot.komponen'])
                ->whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds)
                ->whereIn('mahasiswaId', $displayedMahasiswaIds)
                ->get();

        $existingNilai = $nilaiData
            ->where('tahunAjaranMatkulId', (int) $id)
            ->groupBy('mahasiswaId');

        $nilaiMahasiswa = $displayedMahasiswaIds->isEmpty()
            ? collect()
            : KelasMahasiswa::whereIn('mahasiswaId', $displayedMahasiswaIds)
                ->whereIn('kelasId', $allowedKelasIds)
                ->get()
                ->keyBy('mahasiswaId');

        $totalBobotKomponen = [];
        $allBobotGrouped = $bobotData->groupBy('tahunAjaranMatkulId');
        $representativeBobotForDisplay = $allBobotGrouped->get($id) ?? $allBobotGrouped->first() ?? collect();

        foreach ($representativeBobotForDisplay as $bobot) {
            $totalBobotKomponen[$bobot->komponenId] = ($totalBobotKomponen[$bobot->komponenId] ?? 0) + $bobot->bobot;
        }

        $cpmkValidation = $this->validateCpmkAndBobot($id, $dosen);

        $bulkUrl = request()->fullUrl();
        $bulkUrl .= (strpos($bulkUrl, '?') !== false ? '&' : '?') . 'bulk=1';

        return view('dosen.nilai.show', compact(
            'tahunAjaranMatkul',
            'mataKuliahClasses',
            'mahasiswa',
            'mahasiswaPaginated',
            'allMahasiswaCollection',
            'totalMahasiswaCount',
            'mahasiswaByKelas',
            'kelasNumbers',
            'kelasMahasiswaMap',
            'allKomponen',
            'dosen',
            'isBulkMode',
            'activeTab',
            'sortBy',
            'sortDirection',
            'search',
            'totalBobotKeseluruhan',
            'totalBobotKomponen',
            'isPenilaianSiap',
            'adaCpmk',
            'cpmkList',
            'bobotData',
            'nilaiData',
            'nilaiMahasiswa',
            'bulkUrl',
            'cpmkValidation'
        ));
    }

    /**
     * Validasi CPMK dan Bobot untuk Import Nilai
     */
    private function validateCpmkAndBobot($tahunAjaranMatkulId, $dosen)
    {
        // Ambil semua TahunAjaranMatkul terkait (mata kuliah + tahun ajaran yang sama, diajar oleh dosen ini)
        $relatedTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', function ($q) use ($tahunAjaranMatkulId) {
            $q->select('mataKuliahId')
                ->from('tahun_ajaran_matkul')
                ->where('id', $tahunAjaranMatkulId)
                ->limit(1);
        })
            ->where('tahunAjaranId', function ($q) use ($tahunAjaranMatkulId) {
                $q->select('tahunAjaranId')
                    ->from('tahun_ajaran_matkul')
                    ->where('id', $tahunAjaranMatkulId)
                    ->limit(1);
            })
            ->whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
                $query->where('dosenId', $dosen->id);
            })
            ->pluck('id');

        // Cek apakah ada CPMK yang diatur (unik berdasarkan cpmkId, karena CPMK yang sama
        // bisa saja tercatat di beberapa TahunAjaranMatkul terkait/duplikat)
        $cpmkCount = CpmkMatKul::whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds)
            ->distinct('cpmkId')
            ->count('cpmkId');

        // Bobot komponen sengaja direplikasi ke SETIAP TahunAjaranMatkul terkait (lihat
        // BobotKomponenController::bulkStore) agar tiap kelas punya bobot yang sama.
        // Karena itu, total bobot TIDAK BOLEH dijumlahkan lintas TahunAjaranMatkul (akan
        // menyebabkan penggandaan, mis. 100% + 100% = 200% jika ada 2 TahunAjaranMatkul terkait).
        // Kelompokkan dulu per TahunAjaranMatkul, lalu ambil satu kelompok sebagai representasi.
        $bobotPerTam = Bobot::whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds)
            ->get()
            ->groupBy('tahunAjaranMatkulId');

        // Prioritaskan data dari TahunAjaranMatkul yang sedang dibuka, jika ada.
        // Jika tidak ada, gunakan kelompok manapun yang sudah memiliki bobot.
        $representativeBobot = $bobotPerTam->get($tahunAjaranMatkulId) ?? $bobotPerTam->first() ?? collect();

        // Hitung total bobot CPMK: jumlah seluruh bobot CPMK-Komponen dari tabel bobot (bukan dari cpmk_mat_kul)
        $totalBobotCpmk = $representativeBobot
            ->groupBy('cpmkId')
            ->map(function ($group) {
                return $group->sum('bobot');
            })
            ->sum();

        // Total bobot komponen (jumlah bobot pada tabel bobot untuk satu kelas representatif)
        $totalBobotKomponen = $representativeBobot->sum('bobot');

        return [
            'hasCpmk' => $cpmkCount > 0,
            'cpmkCount' => $cpmkCount,
            'totalBobotCpmk' => $totalBobotCpmk,
            'totalBobotKomponen' => $totalBobotKomponen,
            // Valid jika secara agregat bobot CPMK terdistribusi 100 untuk keseluruhan (per definisi di halaman bobot)
            // Gunakan abs() untuk mengatasi masalah precision floating point
            'isCpmkValid' => $cpmkCount > 0 && abs($totalBobotKomponen - 100) < 0.01,
            'isBobotValid' => $totalBobotKomponen > 0,
            'canImport' => $cpmkCount > 0 && abs($totalBobotKomponen - 100) < 0.01
        ];
    }

    /**
     * Store bulk grades for multiple students.
     */
    public function bulkStore(Request $request, $matkulId)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Data dosen tidak ditemukan.'], 403);
            }
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan.');
        }

        $request->validate([
            'nilai' => 'required|array',
            'nilai.*' => 'array',
            'nilai.*.*' => 'nullable|numeric|min:0|max:100',
            'student_class_id' => 'nullable|array',
            'student_class_id.*' => 'nullable|exists:tahun_ajaran_matkul,id',
        ], [
            'nilai.*.*.max' => 'Nilai tidak boleh lebih dari 100',
            'nilai.*.*.min' => 'Nilai tidak boleh kurang dari 0',
            'nilai.*.*.numeric' => 'Nilai harus berupa angka',
        ]);

        try {
            // Get mata kuliah info
            $matkulClass = TahunAjaranMatkul::findOrFail($matkulId);

            // Get all related classes for this mata kuliah and dosen
            $relatedClasses = TahunAjaranMatkul::where('mataKuliahId', $matkulClass->mataKuliahId)
                ->where('tahunAjaranId', $matkulClass->tahunAjaranId)
                ->whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
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
                $dosenPengampuKelas = \App\Models\DosenPengampuKelas::where('dosenId', $dosen->id)
                    ->whereHas('kelas', function ($query) use ($studentClass) {
                        $query->where('tahunAjaranMatkulId', $studentClass->id);
                    })
                    ->first();

                if (!$dosenPengampuKelas) {
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
                            // Simpan nilai apa adanya (0-100), tidak dikali/dibagi bobot
                            Nilai::updateOrCreate([
                                'mahasiswaId' => $mahasiswaId,
                                'tahunAjaranMatkulId' => $studentClass->id,
                                'bobotId' => $bobot->id,
                            ], [
                                'cpmkId' => $bobot->cpmkId,
                                'dosenPengampuKelasId' => $dosenPengampuKelas->id,
                                'nilai' => $nilaiKomponen,
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

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Nilai berhasil disimpan.']);
            }
            return redirect()->back()->with('success', 'Nilai berhasil disimpan.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan nilai: ' . $e->getMessage()], 500);
            }
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
                ->whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
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
                $dosenPengampuKelas = \App\Models\DosenPengampuKelas::where('dosenId', $dosen->id)
                    ->whereHas('kelas', function ($query) use ($studentClass) {
                        $query->where('tahunAjaranMatkulId', $studentClass->id);
                    })
                    ->first();

                if (!$dosenPengampuKelas) {
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
                            // Simpan nilai apa adanya (0-100), tidak dikali/dibagi bobot
                            Nilai::updateOrCreate([
                                'mahasiswaId' => $mahasiswaId,
                                'tahunAjaranMatkulId' => $studentClass->id,
                                'bobotId' => $bobot->id,
                            ], [
                                'cpmkId' => $bobot->cpmkId,
                                'dosenPengampuKelasId' => $dosenPengampuKelas->id,
                                'nilai' => $nilaiKomponen,
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
                ->with(['bobot.cpmk', 'bobot.komponen'])
                ->get();

            // Only calculate and update if there are actual grades
            if ($allNilai->isNotEmpty()) {
                // Group nilai by CPMK
                $nilaiPerCpmk = $allNilai->groupBy('cpmkId');
                $totalNilaiKeseluruhan = 0;
                $totalBobotKeseluruhan = 0;

                // Calculate total nilai langsung dari semua nilai yang sudah dikalikan bobot
                $totalNilai = 0;
                foreach ($allNilai as $nilai) {
                    if ($nilai->bobot && $nilai->bobot->bobot > 0) {
                        $totalNilai += ($nilai->nilai * $nilai->bobot->bobot / 100);
                    }
                }

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

    /**
     * Export template Excel untuk input nilai
     */
    public function exportTemplate($id)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan.');
        }

        // Verify that this dosen teaches this mata kuliah
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })->with(['mataKuliah', 'tahunAjaran'])->findOrFail($id);

        // Clean filename dari karakter yang tidak diperbolehkan
        $cleanNamaMatkul = preg_replace('/[\/\\\\:*?"<>|]/', '_', $tahunAjaranMatkul->mataKuliah->namaMatkul);
        $cleanTahun = preg_replace('/[\/\\\\:*?"<>|]/', '_', $tahunAjaranMatkul->tahunAjaran->tahun);
        $cleanPeriode = preg_replace('/[\/\\\\:*?"<>|]/', '_', $tahunAjaranMatkul->tahunAjaran->periode);

        $fileName = 'Template_Nilai_' . str_replace(' ', '_', $cleanNamaMatkul) . '_' .
            $cleanTahun . '_' . $cleanPeriode . '.xlsx';

        return Excel::download(new NilaiTemplateExport($id), $fileName);
    }

    /**
     * Import nilai dari Excel
     */
    public function importNilai(Request $request, $id)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan.');
        }

        // Verify that this dosen teaches this mata kuliah
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })->findOrFail($id);

        if (!$request->hasFile('excel_file')) {
            if ($request->ajax()) {
                return response()->json(['error' => 'File Excel harus dipilih.'], 400);
            }
            return redirect()->back()->with('error', 'File Excel harus dipilih.');
        }

        $extension = strtolower($request->file('excel_file')->getClientOriginalExtension());
        if (!in_array($extension, ['xlsx', 'xls'])) {
            if ($request->ajax()) {
                return response()->json(['error' => 'File harus berformat Excel (.xlsx atau .xls).'], 400);
            }
            return redirect()->back()->with('error', 'File harus berformat Excel (.xlsx atau .xls).');
        }

        try {
            // Get all related classes for this mata kuliah and dosen
            $relatedClasses = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
                ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
                ->whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
                    $query->where('dosenId', $dosen->id);
                })
                ->get();

            if ($relatedClasses->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Tidak ada kelas yang ditemukan untuk mata kuliah ini.']);
            }

            // Import Excel data using background job
            $jobId = uniqid('import_');
            Log::info('[Import Nilai] Starting import with jobId: ' . $jobId);

            // Simpan file sementara
            $filePath = $request->file('excel_file')->storeAs(
                'temp_imports', 
                $jobId . '.' . $request->file('excel_file')->getClientOriginalExtension()
            );
            $fullFilePath = storage_path('app/' . $filePath);

            \App\Jobs\ProcessNilaiImport::dispatch($jobId, $fullFilePath, $dosen->id, $id, $relatedClasses);

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'job_id' => $jobId,
                    'message' => 'Proses import sedang berjalan di latar belakang.'
                ]);
            }

            return redirect()->route('dosen.nilai.show', $id)
                ->with('success', 'Proses import sedang berjalan di latar belakang.');
        } catch (\Exception $e) {
            Log::error('Error in importNilai dispatch: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['error' => 'Gagal memulai import: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Gagal memulai import: ' . $e->getMessage());
        }
    }

    /**
     * Check status of background import
     */
    public function checkImportStatus(Request $request, $id)
    {
        $jobId = $request->query('job_id');
        
        if (!$jobId) {
            return response()->json(['error' => 'Job ID tidak ditemukan'], 400);
        }
        
        // Cek apakah ada hasil (selesai/error)
        $result = \Illuminate\Support\Facades\Cache::get('import_result_' . $jobId);
        
        if ($result) {
            // Import selesai
            // Hapus progress dari cache
            \Illuminate\Support\Facades\Cache::forget('import_progress_' . $jobId . '_total');
            \Illuminate\Support\Facades\Cache::forget('import_progress_' . $jobId . '_processed');
            
            // Simpan result sementara di session untuk ditampilkan setelah reload
            if ($result['success']) {
                session()->flash('success', $result['message']);
            } else {
                session()->flash('error', $result['message']);
                if (!empty($result['errors'])) {
                    session()->flash('import_errors', $result['errors']);
                }
            }
            
            return response()->json([
                'status' => 'success',
                'finished' => true,
                'result' => $result
            ]);
        }
        
        // Baca progress
        $total = \Illuminate\Support\Facades\Cache::get('import_progress_' . $jobId . '_total', 0);
        $processed = \Illuminate\Support\Facades\Cache::get('import_progress_' . $jobId . '_processed', 0);
        
        $percentage = 0;
        if ($total > 0) {
            $percentage = round(($processed / $total) * 100);
            if ($percentage > 99) $percentage = 99; // Tetap 99% sampai result ada
        } else {
            $percentage = 0; // Masih menyiapkan data
        }
        
        return response()->json([
            'status' => 'processing',
            'finished' => false,
            'total' => $total,
            'processed' => $processed,
            'percentage' => $percentage
        ]);
    }
    
    /**
     * Reset all grades for a mata kuliah.
     */
    public function resetNilai(Request $request, $id)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Data dosen tidak ditemukan.'], 403);
            }
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan.');
        }

        try {
            // Verify that this dosen teaches this mata kuliah
            $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
                $query->where('dosenId', $dosen->id);
            })->findOrFail($id);

            // Get all related classes for this mata kuliah and dosen
            $relatedClasses = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
                ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
                ->whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
                    $query->where('dosenId', $dosen->id);
                })
                ->pluck('id');

            // Delete all nilai for these classes
            $deletedCount = Nilai::whereIn('tahunAjaranMatkulId', $relatedClasses)->delete();

            // Reset totalNilai and grade in kelasMahasiswa
            // Get all kelasIds from the related classes
            $kelasIds = \App\Models\Kelas::whereIn('tahunAjaranMatkulId', $relatedClasses)->pluck('id');

            DB::table('kelas_mahasiswa')
                ->whereIn('kelasId', $kelasIds)
                ->update([
                    'totalNilai' => null,
                    'grade' => null
                ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Berhasil menghapus {$deletedCount} nilai."
                ]);
            }

            return redirect()->back()->with('success', "Berhasil menghapus {$deletedCount} nilai.");
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus nilai: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus nilai: ' . $e->getMessage());
        }
    }

    /**
     * Get detail nilai for specific student (AJAX)
     */
    public function detailNilai(Request $request, $id)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            return response()->json(['success' => false, 'message' => 'Data dosen tidak ditemukan.'], 403);
        }

        $mahasiswaId = $request->get('mahasiswa_id');
        if (!$mahasiswaId) {
            return response()->json(['success' => false, 'message' => 'ID mahasiswa tidak ditemukan.'], 400);
        }

        try {
            // Verify access
            $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('kelas.dosenPengampuKelas', function ($query) use ($dosen) {
                $query->where('dosenId', $dosen->id);
            })->findOrFail($id);

            // Get mahasiswa data
            $mahasiswa = Mahasiswa::findOrFail($mahasiswaId);

            // Get all CPMK for this mata kuliah - only leaf CPMK (without children)
            $cpmkList = CpmkMatKul::with(['cpmk.cpl', 'cpmk.children'])
                ->where('tahunAjaranMatkulId', $id)
                ->get()
                ->unique('cpmkId')
                ->map(function ($cpmkMatKul) {
                    return $cpmkMatKul->cpmk;
                })
                ->filter(function ($cpmk) {
                    // Only show CPMK that don't have children (leaf nodes)
                    return !$cpmk->hasChildren();
                });

            // Get all komponen
            $allKomponen = Komponen::orderBy('nama')->get();

            // Get bobot data
            $bobotData = Bobot::with(['komponen', 'cpmk'])
                ->where('tahunAjaranMatkulId', $id)
                ->get();

            // Get nilai data for this student
            $nilaiData = Nilai::with(['mahasiswa', 'cpmk', 'bobot.komponen'])
                ->where('tahunAjaranMatkulId', $id)
                ->where('mahasiswaId', $mahasiswaId)
                ->get();

            // Calculate total bobot per komponen
            $totalBobotKomponen = [];
            foreach ($bobotData as $bobot) {
                $komponenId = $bobot->komponenId;
                $totalBobotKomponen[$komponenId] = ($totalBobotKomponen[$komponenId] ?? 0) + $bobot->bobot;
            }

            // Calculate nilai per CPMK
            $nilaiPerCpmk = [];
            foreach ($cpmkList as $cpmk) {
                $nilaiCpmkTotal = 0;
                $bobotCpmkTotal = 0;

                $nilaiRecords = $nilaiData->where('cpmkId', $cpmk->id);

                foreach ($nilaiRecords as $nilai) {
                    if ($nilai->bobot && $nilai->bobot->bobot > 0) {
                        $nilaiCpmkTotal += ($nilai->nilai * $nilai->bobot->bobot);
                        $bobotCpmkTotal += $nilai->bobot->bobot;
                    }
                }

                // Nilai CPMK yang sudah dikalikan bobot CPMK (nilai rata-rata × bobot CPMK)
                $nilaiCpmkTerbobot = $bobotCpmkTotal > 0 ? ($nilaiCpmkTotal / $bobotCpmkTotal) * ($bobotCpmkTotal / 100) : null;

                $nilaiPerCpmk[] = [
                    'cpmk' => $cpmk,
                    'nilai' => $nilaiCpmkTerbobot, // Nilai yang sudah dikalikan bobot
                    'nilai_rata_rata' => $bobotCpmkTotal > 0 ? $nilaiCpmkTotal / $bobotCpmkTotal : null, // Rata-rata untuk perhitungan akhir
                    'bobot_total' => $bobotCpmkTotal,
                    'detail_komponen' => []
                ];

                // Get detail per komponen for this CPMK
                foreach ($allKomponen as $komponen) {
                    $nilaiKomponen = $nilaiData->where('cpmkId', $cpmk->id)
                        ->where('bobot.komponenId', $komponen->id)
                        ->first();

                    $bobotKomponen = $bobotData->where('cpmkId', $cpmk->id)
                        ->where('komponenId', $komponen->id)
                        ->first();

                    $nilaiPerCpmk[count($nilaiPerCpmk) - 1]['detail_komponen'][] = [
                        'komponen' => $komponen,
                        'nilai' => $nilaiKomponen ? $nilaiKomponen->nilai : null,
                        'bobot' => $bobotKomponen ? $bobotKomponen->bobot : 0
                    ];
                }
            }

            // Calculate total nilai akhir langsung dari semua nilai yang sudah dikalikan bobot
            $totalNilaiAkhir = 0;
            foreach ($nilaiData as $nilai) {
                if ($nilai->bobot && $nilai->bobot->bobot > 0) {
                    $totalNilaiAkhir += ($nilai->nilai * $nilai->bobot->bobot / 100);
                }
            }

            // Hitung grade
            $grade = $this->calculateGrade($totalNilaiAkhir);

            // Generate HTML
            $html = view('dosen.nilai.detail', compact(
                'mahasiswa',
                'cpmkList',
                'allKomponen',
                'nilaiPerCpmk',
                'totalNilaiAkhir',
                'totalBobotKomponen',
                'grade'
            ))->render();

            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
