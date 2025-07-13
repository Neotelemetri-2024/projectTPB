<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bobot;
use App\Models\Komponen;
use App\Models\TahunAjaranMatkul;
use App\Models\DosenPengampu;
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
    public function index($mataKuliahId)
    {
        // Verify that the authenticated dosen has access to this mata kuliah
        $mataKuliah = TahunAjaranMatkul::whereHas('dosenPengampu', function($query) {
            $query->where('dosenId', Auth::user()->dosen->id);
        })->findOrFail($mataKuliahId);

        // Get all komponen
        $komponenList = Komponen::orderBy('nama')->get();

        // Get existing bobot for this mata kuliah with komponen and nilai relationships
        $bobotKomponen = Bobot::where('mataKuliahId', $mataKuliahId)
            ->with(['komponen', 'nilai'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate total bobot
        $totalBobot = $bobotKomponen->sum('bobot');

        return view('dosen.bobot-komponen.index', compact('mataKuliah', 'bobotKomponen', 'totalBobot'));
    }

    /**
     * Show the form for creating a new bobot komponen
     */
    public function create($mataKuliahId)
    {
        // Verify access
        $mataKuliah = TahunAjaranMatkul::whereHas('dosenPengampu', function($query) {
            $query->where('dosenId', Auth::user()->dosen->id);
        })->findOrFail($mataKuliahId);

        // Get komponen yang belum ada bobot-nya
        $existingKomponenIds = Bobot::where('mataKuliahId', $mataKuliahId)->pluck('komponenId');
        $komponen = Komponen::whereNotIn('id', $existingKomponenIds)->orderBy('nama')->get();

        // Calculate existing total bobot
        $totalBobot = Bobot::where('mataKuliahId', $mataKuliahId)->sum('bobot');

        return view('dosen.bobot-komponen.create', compact('mataKuliah', 'komponen', 'totalBobot'));
    }

    /**
     * Store a newly created bobot komponen in storage
     */
    public function store(Request $request, $mataKuliahId)
    {
        // Verify access
        $mataKuliah = TahunAjaranMatkul::whereHas('dosenPengampu', function($query) {
            $query->where('dosenId', Auth::user()->dosen->id);
        })->findOrFail($mataKuliahId);

        // Calculate remaining bobot
        $usedBobot = Bobot::where('mataKuliahId', $mataKuliahId)->sum('bobot');
        $remainingBobot = 100 - $usedBobot;

        $request->validate([
            'komponenId' => 'required|exists:komponen,id|unique:bobot,komponenId,NULL,id,mataKuliahId,' . $mataKuliahId,
            'bobot' => 'required|numeric|min:0.01|max:' . $remainingBobot,
        ], [
            'komponenId.unique' => 'Komponen ini sudah memiliki bobot untuk mata kuliah ini.',
            'bobot.max' => 'Bobot tidak boleh melebihi sisa bobot yang tersedia (' . $remainingBobot . '%).',
        ]);

        try {
            DB::beginTransaction();

            Bobot::create([
                'bobot' => $request->bobot,
                'mataKuliahId' => $mataKuliahId,
                'komponenId' => $request->komponenId,
            ]);

            DB::commit();
            return redirect()->route('dosen.bobot-komponen.index', $mataKuliahId)
                ->with('success', 'Bobot komponen berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified bobot komponen
     */
    public function show($mataKuliahId, $bobotId)
    {
        // Verify access
        $mataKuliah = TahunAjaranMatkul::whereHas('dosenPengampu', function($query) {
            $query->where('dosenId', Auth::user()->dosen->id);
        })->findOrFail($mataKuliahId);

        $bobot = Bobot::where('mataKuliahId', $mataKuliahId)
            ->with(['komponen', 'nilai.mahasiswa'])
            ->findOrFail($bobotId);

        // Get total bobot for this mata kuliah
        $totalBobot = Bobot::where('mataKuliahId', $mataKuliahId)->sum('bobot');

        // Get other komponen bobot for this mata kuliah (excluding current)
        $komponenLain = Bobot::where('mataKuliahId', $mataKuliahId)
            ->where('id', '!=', $bobotId)
            ->with('komponen')
            ->get();

        return view('dosen.bobot-komponen.show', compact('mataKuliah', 'bobot', 'totalBobot', 'komponenLain'));
    }

    /**
     * Show the form for editing the specified bobot komponen
     */
    public function edit($mataKuliahId, $bobotId)
    {
        // Verify access
        $mataKuliah = TahunAjaranMatkul::whereHas('dosenPengampu', function($query) {
            $query->where('dosenId', Auth::user()->dosen->id);
        })->findOrFail($mataKuliahId);

        $bobot = Bobot::where('mataKuliahId', $mataKuliahId)
            ->with(['komponen', 'nilai'])
            ->findOrFail($bobotId);

        // Calculate total bobot from other components (excluding current bobot)
        $totalBobotLain = Bobot::where('mataKuliahId', $mataKuliahId)
            ->where('id', '!=', $bobotId)
            ->sum('bobot');

        return view('dosen.bobot-komponen.edit', compact('mataKuliah', 'bobot', 'totalBobotLain'));
    }

    /**
     * Update the specified bobot komponen in storage
     */
    public function update(Request $request, $mataKuliahId, $bobotId)
    {
        // Verify access
        $mataKuliah = TahunAjaranMatkul::whereHas('dosenPengampu', function($query) {
            $query->where('dosenId', Auth::user()->dosen->id);
        })->findOrFail($mataKuliahId);

        $bobot = Bobot::where('mataKuliahId', $mataKuliahId)->findOrFail($bobotId);

        // Calculate total bobot from other components (excluding current bobot)
        $totalBobotLain = Bobot::where('mataKuliahId', $mataKuliahId)
            ->where('id', '!=', $bobotId)
            ->sum('bobot');
        $maxBobot = 100 - $totalBobotLain;

        $request->validate([
            'bobot' => 'required|numeric|min:0.01|max:' . $maxBobot,
        ], [
            'bobot.max' => 'Bobot tidak boleh melebihi sisa bobot yang tersedia (' . $maxBobot . '%).',
        ]);

        try {
            DB::beginTransaction();

            $bobot->update([
                'bobot' => $request->bobot,
            ]);

            DB::commit();
            return redirect()->route('dosen.bobot-komponen.index', $mataKuliahId)
                ->with('success', 'Bobot komponen berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified bobot komponen from storage
     */
    public function destroy($mataKuliahId, $bobotId)
    {
        // Verify access
        $mataKuliah = TahunAjaranMatkul::whereHas('dosenPengampu', function($query) {
            $query->where('dosenId', Auth::user()->dosen->id);
        })->findOrFail($mataKuliahId);

        $bobot = Bobot::where('mataKuliahId', $mataKuliahId)->findOrFail($bobotId);

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
            return redirect()->route('dosen.bobot-komponen.index', $mataKuliahId)
                ->with('success', 'Bobot komponen berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Bulk create bobot for all komponen
     */
    public function bulkCreate($mataKuliahId)
    {
        // Verify access
        $mataKuliah = TahunAjaranMatkul::whereHas('dosenPengampu', function($query) {
            $query->where('dosenId', Auth::user()->dosen->id);
        })->findOrFail($mataKuliahId);

        // Get komponen yang belum ada bobot-nya
        $existingKomponenIds = Bobot::where('mataKuliahId', $mataKuliahId)->pluck('komponenId');
        $komponen = Komponen::whereNotIn('id', $existingKomponenIds)->orderBy('nama')->get();

        // Get existing bobot
        $existingBobot = Bobot::where('mataKuliahId', $mataKuliahId)
            ->with('komponen')
            ->get();

        if ($komponen->isEmpty()) {
            return redirect()->route('dosen.bobot-komponen.index', $mataKuliahId)
                ->with('info', 'Semua komponen sudah memiliki bobot.');
        }

        return view('dosen.bobot-komponen.bulk-create', compact('mataKuliah', 'komponen', 'existingBobot'));
    }

    /**
     * Store bulk bobot
     */
    public function bulkStore(Request $request, $mataKuliahId)
    {
        // Verify access
        $mataKuliah = TahunAjaranMatkul::whereHas('dosenPengampu', function($query) {
            $query->where('dosenId', Auth::user()->dosen->id);
        })->findOrFail($mataKuliahId);

        $request->validate([
            'bobot' => 'required|array',
            'bobot.*' => 'required|numeric|min:0.01|max:100',
        ]);

        // Validate total bobot not exceeding 100%
        $usedBobot = Bobot::where('mataKuliahId', $mataKuliahId)->sum('bobot');
        $newTotalBobot = array_sum($request->bobot);

        if (($usedBobot + $newTotalBobot) > 100) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Total bobot tidak boleh melebihi 100%. Sisa bobot yang tersedia: ' . (100 - $usedBobot) . '%');
        }

        try {
            DB::beginTransaction();

            foreach ($request->bobot as $komponenId => $bobotValue) {
                if ($bobotValue > 0) {
                    Bobot::create([
                        'bobot' => $bobotValue,
                        'mataKuliahId' => $mataKuliahId,
                        'komponenId' => $komponenId,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('dosen.bobot-komponen.index', $mataKuliahId)
                ->with('success', 'Bobot komponen berhasil ditambahkan secara bulk.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }
}
