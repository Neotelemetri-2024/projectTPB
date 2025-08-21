<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bobot;
use App\Models\Komponen;
use App\Models\TahunAjaranMatkul;
use App\Models\Cpmk;
use App\Models\CpmkMatKul;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BobotKomponenController extends Controller
{
    public function __construct()
    {
        $this->middleware('dosen');
    }

    /**
     * Bulk create/edit bobot for all available CPMK-Komponen combinations
     */
    public function bulkCreate($tahunAjaranMatkulId)
    {
        $dosen = Auth::user()->dosen;

        // Verify access - use new schema
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('kelas.dosenPengampuKelas', function($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })->findOrFail($tahunAjaranMatkulId);

        // Get all TahunAjaranMatkul records for the same mata kuliah, tahun ajaran, and dosen
        $relatedTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
            ->whereHas('kelas.dosenPengampuKelas', function($query) use ($dosen) {
                $query->where('dosenId', $dosen->id);
            })
            ->pluck('id');

        // Get CPMK that are assigned to this mata kuliah (from all classes with same dosen)
        $cpmkList = CpmkMatKul::whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds)
            ->with(['cpmk.cpl', 'cpmk.children', 'cpmk.parent']) // Include CPL, children, and parent relationships
            ->get()
            ->unique('cpmkId') // Remove duplicates based on cpmkId
            ->map(function($cpmkMatKul) {
                return $cpmkMatKul->cpmk;
            })
            ->filter(function($cpmk) {
                return $cpmk !== null; // Filter out null CPMK
            });

        if ($cpmkList->isEmpty()) {
            return redirect()->route('dosen.cpmk.show', $tahunAjaranMatkulId)
                ->with('error', 'Tidak dapat mengatur bobot karena belum ada CPMK yang ditetapkan untuk mata kuliah ini.');
        }

        // Get all komponen
        $komponen = Komponen::orderBy('nama')->get();

        // Get existing bobot with all necessary relationships (from all related classes)
        $existingBobot = Bobot::whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds)
            ->with(['komponen', 'cpmk.cpl', 'cpmk.children', 'cpmk.parent', 'nilai'])
            ->get();

        // Get existing bobot from all classes with same mataKuliahId, tahunAjaranId, and dosen for usedKomponenIds
        $allExistingBobot = Bobot::whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds)
            ->with(['komponen', 'cpmk.cpl', 'cpmk.children', 'cpmk.parent', 'nilai'])
            ->get();

        // Create array of existing combinations for easier lookup
        $existingCombinations = $existingBobot->mapWithKeys(function($bobot) {
            $key = $bobot->cpmkId . '_' . $bobot->komponenId;
            return [$key => $bobot->bobot];
        })->toArray();

        // Calculate used bobot
        $usedBobot = $existingBobot->sum('bobot');

        // Check if any bobot has nilai (for locking mechanism) - fresh query to avoid cache
        $bobotIds = $existingBobot->pluck('id');
        $bobotWithNilaiIds = \App\Models\Nilai::whereIn('bobotId', $bobotIds)->pluck('bobotId')->unique();

        $bobotWithNilai = $existingBobot->filter(function($bobot) use ($bobotWithNilaiIds) {
            return $bobotWithNilaiIds->contains($bobot->id);
        })->mapWithKeys(function($bobot) {
            $key = $bobot->cpmkId . '_' . $bobot->komponenId;
            return [$key => true];
        })->toArray();

        // Get unique component IDs that are already used in existing bobot (from all classes)
        $usedKomponenIds = $allExistingBobot->pluck('komponenId')->unique()->toArray();

        // Get class information for display
        $kelasInfo = TahunAjaranMatkul::whereIn('id', $relatedTahunAjaranMatkulIds)
            ->with(['kelas.kelasMahasiswa'])
            ->get()
            ->map(function($item) {
                $totalMahasiswa = $item->kelas->sum(function($kelas) {
                    return $kelas->kelasMahasiswa->count();
                });
                return [
                    'kelas' => $item->kelas->pluck('namaKelas')->implode(', '),
                    'jumlah_mahasiswa' => $totalMahasiswa
                ];
            });

        return view('dosen.bobot-komponen.bulk-create', compact(
            'tahunAjaranMatkul',
            'cpmkList',
            'komponen',
            'existingBobot',
            'allExistingBobot',
            'existingCombinations',
            'usedBobot',
            'bobotWithNilai',
            'usedKomponenIds',
            'relatedTahunAjaranMatkulIds',
            'kelasInfo'
        ));
    }

    /**
     * Store/Update bulk bobot
     */
    public function bulkStore(Request $request, $tahunAjaranMatkulId)
    {
        $dosen = Auth::user()->dosen;

        // Verify access - use new schema
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('kelas.dosenPengampuKelas', function($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })->findOrFail($tahunAjaranMatkulId);

        // Get all TahunAjaranMatkul records for the same mata kuliah, tahun ajaran, and dosen
        $relatedTahunAjaranMatkulIds = TahunAjaranMatkul::where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
            ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId)
            ->whereHas('kelas.dosenPengampuKelas', function($query) use ($dosen) {
                $query->where('dosenId', $dosen->id);
            })
            ->pluck('id');

        $request->validate([
            'bobot' => 'required|array',
            'bobot.*' => 'nullable|numeric|min:0|max:100',
        ]);

        // Get CPMK list to check hierarchy
        $cpmkList = CpmkMatKul::whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds)
            ->with(['cpmk.children', 'cpmk.parent'])
            ->get()
            ->unique('cpmkId')
            ->map(function($cpmkMatKul) {
                return $cpmkMatKul->cpmk;
            })
            ->filter(function($cpmk) {
                return $cpmk !== null;
            });

        // Check if any parent CPMK with children has bobot set (should not be allowed)
        $parentCpmkWithChildren = $cpmkList->filter(function($cpmk) {
            return !$cpmk->parent_id && $cpmk->children && $cpmk->children->count() > 0;
        });

        foreach ($parentCpmkWithChildren as $parentCpmk) {
            foreach ($request->bobot as $combination => $bobotValue) {
                if ($bobotValue > 0) {
                    list($cpmkId, $komponenId) = explode('_', $combination);
                    if ($cpmkId == $parentCpmk->id) {
                        return redirect()->back()
                            ->withInput()
                            ->with('error', "CPMK {$parentCpmk->kodeCpmk} memiliki sub-CPMK. Bobot hanya boleh diatur pada sub-CPMK, bukan pada parent CPMK.");
                    }
                }
            }
        }

        // Validasi total bobot keseluruhan (bukan per komponen)
        $totalBobot = 0;
        foreach ($request->bobot as $combination => $bobotValue) {
            if ($bobotValue > 0) {
                $totalBobot += $bobotValue;
            }
        }
        if ($totalBobot > 100) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Total bobot seluruh kombinasi CPMK-Komponen melebihi 100%. Mohon periksa kembali.');
        }

        try {
            DB::beginTransaction();

            // Get existing bobot that have nilai (cannot be deleted/modified) from all related classes
            $bobotWithNilai = Bobot::whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds)
                ->whereHas('nilai')
                ->get()
                ->mapWithKeys(function($bobot) {
                    $key = $bobot->cpmkId . '_' . $bobot->komponenId;
                    return [$key => $bobot];
                });

            // Delete existing bobot that don't have nilai and are not in the new input from all related classes
            Bobot::whereIn('tahunAjaranMatkulId', $relatedTahunAjaranMatkulIds)
                ->whereDoesntHave('nilai')
                ->delete();

            // Create/Update bobot from form - apply to all related classes
            foreach ($request->bobot as $combination => $bobotValue) {
                if ($bobotValue > 0) {
                    // Skip if this combination has nilai (locked)
                    if (isset($bobotWithNilai[$combination])) {
                        continue;
                    }

                    // Parse combination (format: cpmkId_komponenId)
                    list($cpmkId, $komponenId) = explode('_', $combination);

                    // Create bobot for all related tahunAjaranMatkulId
                    foreach ($relatedTahunAjaranMatkulIds as $relatedId) {
                        // Verify CPMK is assigned to this mata kuliah
                        $cpmkMatKul = CpmkMatKul::where('tahunAjaranMatkulId', $relatedId)
                            ->where('cpmkId', $cpmkId)
                            ->first();

                        if ($cpmkMatKul) {
                            Bobot::updateOrCreate(
                                [
                                    'tahunAjaranMatkulId' => $relatedId,
                                    'cpmkId' => $cpmkId,
                                    'komponenId' => $komponenId,
                                ],
                                [
                                    'bobot' => $bobotValue,
                                ]
                            );
                        }
                    }
                }
            }

            DB::commit();

            $jumlahKelas = $relatedTahunAjaranMatkulIds->count();
            return redirect()->route('dosen.cpmk.show', $tahunAjaranMatkulId)
                ->with('success', 'Bobot komponen berhasil disimpan untuk ' . $jumlahKelas . ' kelas yang Anda ampu. Parent CPMK dengan sub-CPMK menggunakan bobot dari sub-CPMK.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

}
