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
     * Display a listing of bobot komponen for specific mata kuliah
     */
    public function index($tahunAjaranMatkulId)
    {
        // Verify that the authenticated dosen has access to this mata kuliah
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('dosenPengampu', function($query) {
            $query->where('dosenId', Auth::user()->dosen->id);
        })->findOrFail($tahunAjaranMatkulId);

        // Get all komponen
        $komponenList = Komponen::orderBy('nama')->get();

        // Get existing bobot for this mata kuliah with relationships
        $bobotKomponen = Bobot::where('tahunAjaranMatkulId', $tahunAjaranMatkulId)
            ->with(['komponen', 'cpmk', 'nilai'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate total bobot
        $totalBobot = $bobotKomponen->sum('bobot');

        // Get CPMK that are assigned to this mata kuliah
        $cpmkList = CpmkMatKul::where('tahunAjaranMatkulId', $tahunAjaranMatkulId)
            ->with('cpmk')
            ->get()
            ->pluck('cpmk')
            ->unique('id');

        return view('dosen.bobot-komponen.index', compact('tahunAjaranMatkul', 'bobotKomponen', 'totalBobot', 'cpmkList'));
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

        // Get CPMK that are assigned to this mata kuliah
        $cpmkList = CpmkMatKul::where('tahunAjaranMatkulId', $tahunAjaranMatkulId)
            ->with('cpmk')
            ->get();

        if ($cpmkList->isEmpty()) {
            return redirect()->route('dosen.bobot-komponen.index', $tahunAjaranMatkulId)
                ->with('error', 'Tidak dapat mengatur bobot karena belum ada CPMK yang ditetapkan untuk mata kuliah ini.');
        }

        // Get all komponen
        $komponen = Komponen::orderBy('nama')->get();

        // Get existing bobot with all necessary relationships
        $existingBobot = Bobot::where('tahunAjaranMatkulId', $tahunAjaranMatkulId)
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

        return view('dosen.bobot-komponen.bulk-create', compact(
            'tahunAjaranMatkul',
            'cpmkList',
            'komponen',
            'existingBobot',
            'existingCombinations',
            'usedBobot',
            'bobotWithNilai'
        ));
    }

    /**
     * Display the specified bobot komponen
     */
    public function show($tahunAjaranMatkulId, $bobotId)
    {
        // Verify access
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('dosenPengampu', function($query) {
            $query->where('dosenId', Auth::user()->dosen->id);
        })->findOrFail($tahunAjaranMatkulId);

        $bobot = Bobot::where('tahunAjaranMatkulId', $tahunAjaranMatkulId)
            ->with(['komponen', 'cpmk', 'nilai.mahasiswa'])
            ->findOrFail($bobotId);

        // Get total bobot for this mata kuliah
        $totalBobot = Bobot::where('tahunAjaranMatkulId', $tahunAjaranMatkulId)->sum('bobot');

        // Get other komponen bobot for this mata kuliah (excluding current)
        $komponenLain = Bobot::where('tahunAjaranMatkulId', $tahunAjaranMatkulId)
            ->where('id', '!=', $bobotId)
            ->with(['komponen', 'cpmk'])
            ->get();

        return view('dosen.bobot-komponen.show', compact('tahunAjaranMatkul', 'bobot', 'totalBobot', 'komponenLain'));
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
            return redirect()->route('dosen.bobot-komponen.index', $tahunAjaranMatkulId)
                ->with('success', 'Bobot komponen berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified bobot komponen from storage
     */
    public function destroy($tahunAjaranMatkulId, $bobotId)
    {
        // Verify access
        $tahunAjaranMatkul = TahunAjaranMatkul::whereHas('dosenPengampu', function($query) {
            $query->where('dosenId', Auth::user()->dosen->id);
        })->findOrFail($tahunAjaranMatkulId);

        $bobot = Bobot::where('tahunAjaranMatkulId', $tahunAjaranMatkulId)->findOrFail($bobotId);

        try {
            DB::beginTransaction();

            // Check if there are nilai records using this bobot
            $nilaiCount = $bobot->nilai()->count();

            if ($nilaiCount > 0) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat menghapus bobot komponen karena sudah ada nilai yang menggunakan bobot ini.');
            }

            $bobot->delete();

            DB::commit();
            return redirect()->route('dosen.bobot-komponen.index', $tahunAjaranMatkulId)
                ->with('success', 'Bobot komponen berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

}
