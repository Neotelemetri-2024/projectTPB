<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bobot;
use App\Models\Komponen;
use App\Models\TahunAjaranMatkul;
use App\Models\DosenPengampu;
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
        // Verify access
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('dosenPengampu', function($query) {
            $query->where('dosenId', Auth::user()->dosen->id);
        })->findOrFail($tahunAjaranMatkulId);

        // Get CPMK that are assigned to this mata kuliah (from all classes)
        $cpmkList = CpmkMatKul::whereHas('tahunAjaranMatkul', function($query) use ($tahunAjaranMatkul) {
            $query->where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
                  ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId);
        })
        ->with('cpmk')
        ->get()
        ->unique('cpmkId'); // Remove duplicates based on cpmkId

        // dd($cpmkList);

        if ($cpmkList->isEmpty()) {
            return redirect()->route('dosen.cpmk.show', $tahunAjaranMatkulId)
                ->with('error', 'Tidak dapat mengatur bobot karena belum ada CPMK yang ditetapkan untuk mata kuliah ini.');
        }

        // Get all komponen
        $komponen = Komponen::orderBy('nama')->get();

        // Get existing bobot with all necessary relationships
        $existingBobot = Bobot::where('tahunAjaranMatkulId', $tahunAjaranMatkulId)
            ->with(['komponen', 'cpmk', 'nilai'])
            ->get();

        // Get existing bobot from all classes with same mataKuliahId and tahunAjaranId for usedKomponenIds
        $allExistingBobot = Bobot::whereHas('tahunAjaranMatkul', function($query) use ($tahunAjaranMatkul) {
            $query->where('mataKuliahId', $tahunAjaranMatkul->mataKuliahId)
                  ->where('tahunAjaranId', $tahunAjaranMatkul->tahunAjaranId);
        })
        ->with(['komponen', 'cpmk', 'nilai'])
        ->get();

        // Create array of existing combinations for easier lookup
        $existingCombinations = $existingBobot->mapWithKeys(function($bobot) {
            $key = $bobot->cpmkId . '_' . $bobot->komponenId;
            return [$key => $bobot->bobot];
        })->toArray();

        // Calculate used bobot
        $usedBobot = $existingBobot->sum('bobot');

        // Check if any bobot has nilai (for locking mechanism)
        $bobotWithNilai = $existingBobot->filter(function($bobot) {
            return $bobot->nilai->count() > 0;
        })->mapWithKeys(function($bobot) {
            $key = $bobot->cpmkId . '_' . $bobot->komponenId;
            return [$key => true];
        })->toArray();

        // Get unique component IDs that are already used in existing bobot (from all classes)
        $usedKomponenIds = $allExistingBobot->pluck('komponenId')->unique()->toArray();

        return view('dosen.bobot-komponen.bulk-create', compact(
            'tahunAjaranMatkul',
            'cpmkList',
            'komponen',
            'existingBobot',
            'allExistingBobot',
            'existingCombinations',
            'usedBobot',
            'bobotWithNilai',
            'usedKomponenIds'
        ));
    }

    /**
     * Store/Update bulk bobot
     */
    public function bulkStore(Request $request, $tahunAjaranMatkulId)
    {
        // Verify access
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('dosenPengampu', function($query) {
            $query->where('dosenId', Auth::user()->dosen->id);
        })->findOrFail($tahunAjaranMatkulId);

        $request->validate([
            'bobot' => 'required|array',
            'bobot.*' => 'nullable|numeric|min:0|max:100',
        ]);

        // Calculate total new bobot
        $newTotalBobot = array_sum(array_filter($request->bobot, function($value) {
            return $value > 0;
        }));

        // Only check if total exceeds 100%, allow saving even if less than 100%
        if ($newTotalBobot > 100) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Total bobot tidak boleh melebihi 100%. Total yang diinput: ' . $newTotalBobot . '%');
        }

        try {
            DB::beginTransaction();

            // Get existing bobot that have nilai (cannot be deleted/modified)
            $bobotWithNilai = Bobot::where('tahunAjaranMatkulId', $tahunAjaranMatkulId)
                ->whereHas('nilai')
                ->get()
                ->mapWithKeys(function($bobot) {
                    $key = $bobot->cpmkId . '_' . $bobot->komponenId;
                    return [$key => $bobot];
                });

            // Delete existing bobot that don't have nilai and are not in the new input
            Bobot::where('tahunAjaranMatkulId', $tahunAjaranMatkulId)
                ->whereDoesntHave('nilai')
                ->delete();

            // Create/Update bobot from form
            foreach ($request->bobot as $combination => $bobotValue) {
                if ($bobotValue > 0) {
                    // Skip if this combination has nilai (locked)
                    if (isset($bobotWithNilai[$combination])) {
                        continue;
                    }

                    // Parse combination (format: cpmkId_komponenId)
                    list($cpmkId, $komponenId) = explode('_', $combination);

                    // Verify CPMK is assigned to this mata kuliah
                    $cpmkMatKul = CpmkMatKul::where('tahunAjaranMatkulId', $tahunAjaranMatkulId)
                        ->where('cpmkId', $cpmkId)
                        ->first();

                    if ($cpmkMatKul) {
                        Bobot::updateOrCreate(
                            [
                                'tahunAjaranMatkulId' => $tahunAjaranMatkulId,
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

            DB::commit();
            return redirect()->route('dosen.cpmk.show', $tahunAjaranMatkulId)
                ->with('success', 'Bobot komponen berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

}
