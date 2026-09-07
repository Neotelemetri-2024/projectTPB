<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use App\Models\MataKuliah;
use App\Models\TahunAjaranMatkul;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\DosenPengampuKelas;
use App\Models\KelasMahasiswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Exports\TahunAjaranMatkulTemplateExport;
use App\Imports\TahunAjaranMatkulImport;

class TahunAjaranMatkulController extends Controller
{
    public function index(Request $request)
    {
        $query = TahunAjaranMatkul::with([
            'tahunAjaran',
            'mataKuliah',
            'kelas.dosenPengampuKelas.dosen',
            'kelas.kelasMahasiswa.mahasiswa'
        ]);

        // Get the latest tahun ajaran for default filter
        $latestTahunAjaran = TahunAjaran::orderBy('tahun', 'desc')->orderBy('periode', 'desc')->first();

        // Set default filter to latest tahun ajaran if no filter is selected
        // Check if tahun_ajaran_id parameter exists in request (even if empty)
        if ($request->has('tahun_ajaran_id')) {
            // Parameter exists, use the value (could be empty for "Semua Tahun Ajaran")
            $selectedTahunAjaranId = $request->tahun_ajaran_id;
        } else {
            // Parameter doesn't exist, use default (latest tahun ajaran)
            $selectedTahunAjaranId = $latestTahunAjaran ? $latestTahunAjaran->id : null;
        }

        // Filter by tahun ajaran (only if selectedTahunAjaranId is not empty)
        if ($selectedTahunAjaranId) {
            $query->where('tahunAjaranId', $selectedTahunAjaranId);
        }

        // Search by mata kuliah
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('mataKuliah', function ($q) use ($search) {
                $q->where('namaMatkul', 'like', "%{$search}%")
                    ->orWhere('kodeMatkul', 'like', "%{$search}%");
            });
        }

        $tahunAjaranMatkuls = $query->orderBy('created_at', 'desc')->paginate(10);

        // Append query parameters to pagination links
        $tahunAjaranMatkuls->appends($request->query());

        // Order tahun ajaran by latest first
        $tahunAjarans = TahunAjaran::orderBy('tahun', 'desc')->orderBy('periode', 'desc')->get();
        $mataKuliahs = MataKuliah::all();
        $dosens = Dosen::all();

        if ($request->ajax() || $request->wantsJson()) {
            return view('admin.tahun-ajaran-matkul._table', compact('tahunAjaranMatkuls'));
        }

        return view('admin.tahun-ajaran-matkul.index', compact('tahunAjaranMatkuls', 'tahunAjarans', 'mataKuliahs', 'dosens', 'selectedTahunAjaranId'));
    }

    public function create()
    {
        $tahunAjarans = TahunAjaran::all();
        $mataKuliahs = MataKuliah::all();
        $dosens = Dosen::all();

        return view('admin.tahun-ajaran-matkul.create', compact('tahunAjarans', 'mataKuliahs', 'dosens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahunAjaranId' => 'required|exists:tahun_ajaran,id',
            'mataKuliahId' => 'required|exists:mata_kuliah,id',
            'semester' => 'nullable|integer|min:1|max:8',
            'kelasNames' => 'required|array|min:1',
            'kelasNames.*' => 'required|string|max:10',
            'dosenOption' => 'required|in:same,different',
            'dosenIds' => 'required_if:dosenOption,same|array|min:1',
            'dosenIds.*' => 'exists:dosen,id',
            'dosenPerKelas' => 'required_if:dosenOption,different|array',
            'dosenPerKelas.*' => 'required|array|min:1',
            'dosenPerKelas.*.*' => 'exists:dosen,id'
        ]);

        // Filter out empty kelas names
        $kelasNames = array_filter($request->kelasNames, function ($name) {
            return !empty(trim($name));
        });

        if (empty($kelasNames)) {
            return back()->withErrors(['kelasNames' => 'Minimal satu nama kelas harus diisi.'])->withInput();
        }

        // Check if combination already exists (tahun ajaran + mata kuliah)
        $existing = TahunAjaranMatkul::where('tahunAjaranId', $request->tahunAjaranId)
            ->where('mataKuliahId', $request->mataKuliahId)
            ->first();

        if ($existing) {
            return back()->withErrors(['mataKuliahId' => 'Mata kuliah ini sudah ada di tahun ajaran yang dipilih. Silakan pilih mata kuliah lain atau tahun ajaran lain.'])->withInput();
        }

        DB::beginTransaction();
        try {
            $tahunAjaranMatkul = TahunAjaranMatkul::create([
                'tahunAjaranId' => $request->tahunAjaranId,
                'mataKuliahId' => $request->mataKuliahId,
                'semester' => $request->semester ?? 1
            ]);

            // Create kelas for each kelas name
            foreach ($kelasNames as $kelasName) {
                $kelasName = trim($kelasName);
                if (!empty($kelasName)) {
                    $kelas = Kelas::create([
                        'namaKelas' => $kelasName,
                        'tahunAjaranMatkulId' => $tahunAjaranMatkul->id
                    ]);

                    // Create dosen pengampu based on option
                    if ($request->dosenOption === 'same') {
                        // Same dosen for all classes
                        if ($request->has('dosenIds')) {
                            foreach ($request->dosenIds as $dosenId) {
                                DosenPengampuKelas::create([
                                    'dosenId' => $dosenId,
                                    'kelasId' => $kelas->id
                                ]);
                            }
                        }
                    } else {
                        // Different dosen per class
                        if ($request->has("dosenPerKelas.{$kelasName}")) {
                            foreach ($request->input("dosenPerKelas.{$kelasName}") as $dosenId) {
                                DosenPengampuKelas::create([
                                    'dosenId' => $dosenId,
                                    'kelasId' => $kelas->id
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.tahun-ajaran-matkul.index')->with('success', 'Mata kuliah berhasil ditambahkan ke tahun ajaran.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(Request $request, $id)
    {
        $tahunAjaranMatkul = TahunAjaranMatkul::with([
            'tahunAjaran',
            'mataKuliah',
            'kelas.dosenPengampuKelas.dosen',
            'kelas.kelasMahasiswa.mahasiswa.user'
        ])->findOrFail($id);

        // Ambil semua kelasId untuk tahun ajaran matkul ini
        $kelasIds = $tahunAjaranMatkul->kelas->pluck('id');

        // Query untuk mahasiswa yang ada di kelas dengan search
        $query = KelasMahasiswa::with(['mahasiswa.user', 'kelas'])
            ->whereIn('kelasId', $kelasIds);

        // Search mahasiswa berdasarkan nama atau NIM
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('mahasiswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $kelasMahasiswa = $query->paginate(10);
        $kelasMahasiswa->appends($request->query());

        // Get all unique mahasiswa IDs that are already in any class of this tahun ajaran matkul
        $existingMahasiswaIds = $tahunAjaranMatkul->kelas->flatMap->kelasMahasiswa->pluck('mahasiswaId')->unique();

        // Get all unique dosen IDs that are already teaching any class of this tahun ajaran matkul
        $existingDosenIds = $tahunAjaranMatkul->kelas->flatMap->dosenPengampuKelas->pluck('dosenId')->unique();

        // Get available mahasiswas (not in any class of this tahun ajaran matkul)
        $availableMahasiswas = Mahasiswa::whereNotIn('id', $existingMahasiswaIds)
            ->orderBy('nama')
            ->paginate(20, ['*'], 'mahasiswa_page')
            ->withQueryString();

        // Get available dosens (not teaching any class of this tahun ajaran matkul)
        $availableDosens = Dosen::whereNotIn('id', $existingDosenIds)
            ->orderBy('nama')
            ->paginate(20, ['*'], 'dosen_page')
            ->withQueryString();

        return view('admin.tahun-ajaran-matkul.show', compact(
            'tahunAjaranMatkul',
            'kelasMahasiswa',
            'availableMahasiswas',
            'availableDosens'
        ));
    }

    public function edit($id)
    {
        $tahunAjaranMatkul = TahunAjaranMatkul::with(['kelas.dosenPengampuKelas.dosen'])->findOrFail($id);
        $tahunAjarans = TahunAjaran::all();
        $mataKuliahs = MataKuliah::all();
        $dosens = Dosen::all();

        return view('admin.tahun-ajaran-matkul.edit', compact('tahunAjaranMatkul', 'tahunAjarans', 'mataKuliahs', 'dosens'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tahunAjaranId' => 'required|exists:tahun_ajaran,id',
            'mataKuliahId' => 'required|exists:mata_kuliah,id',
            'semester' => 'nullable|integer|min:1|max:8',
            'kelasNames' => 'required|array|min:1',
            'kelasNames.*' => 'required|string|max:10',
            'dosenType' => 'required|in:same,different',
            'dosenIds' => 'required_if:dosenType,same|array|min:1',
            'dosenIds.*' => 'exists:dosen,id',
            'dosenPerKelas' => 'required_if:dosenType,different|array',
            'dosenPerKelas.*' => 'required|array|min:1',
            'dosenPerKelas.*.*' => 'exists:dosen,id'
        ]);

        $tahunAjaranMatkul = TahunAjaranMatkul::findOrFail($id);

        // Check if combination already exists (excluding current record)
        $existing = TahunAjaranMatkul::where('tahunAjaranId', $request->tahunAjaranId)
            ->where('mataKuliahId', $request->mataKuliahId)
            ->where('id', '!=', $id)
            ->first();

        if ($existing) {
            return back()->withErrors(['mataKuliahId' => 'Kombinasi tahun ajaran dan mata kuliah sudah ada.']);
        }

        DB::beginTransaction();
        try {
            $tahunAjaranMatkul->update([
                'tahunAjaranId' => $request->tahunAjaranId,
                'mataKuliahId' => $request->mataKuliahId,
                'semester' => $request->semester ?? 1
            ]);

            // Get existing kelas for proper comparison
            $existingKelas = $tahunAjaranMatkul->kelas;
            $submittedKelasNames = $request->kelasNames;

            // Delete classes that are no longer in the submitted list
            $kelasToDelete = $existingKelas->filter(function ($kelas) use ($submittedKelasNames) {
                return !in_array($kelas->namaKelas, $submittedKelasNames);
            });

            foreach ($kelasToDelete as $kelas) {
                $kelasIds = [$kelas->id];
                // Get related DosenPengampuKelas IDs
                $dosenPengampuKelasIds = DosenPengampuKelas::whereIn('kelasId', $kelasIds)->pluck('id');
                // Delete nilai associated with this class
                $tahunAjaranMatkul->nilai()->whereIn('dosenPengampuKelasId', $dosenPengampuKelasIds)->delete();
                // Delete kelas_mahasiswa
                KelasMahasiswa::whereIn('kelasId', $kelasIds)->delete();
                // Delete dosen_pengampu_kelas
                DosenPengampuKelas::whereIn('kelasId', $kelasIds)->delete();
                // Delete kelas
                $kelas->delete();
            }

            // Create new kelas or get existing ones
            foreach ($submittedKelasNames as $index => $kelasName) {
                $kelas = $tahunAjaranMatkul->kelas()->where('namaKelas', $kelasName)->first();
                if (!$kelas) {
                    $kelas = Kelas::create([
                        'namaKelas' => $kelasName,
                        'tahunAjaranMatkulId' => $tahunAjaranMatkul->id
                    ]);
                } else {
                    // Clear existing dosen_pengampu_kelas for this class so we can recreate them
                    DosenPengampuKelas::where('kelasId', $kelas->id)->delete();
                }

                // Mahasiswa data is kept automatically for existing classes.
                // We only create classes here, so they won't have mahasiswa initially.

                // Create dosen pengampu for this kelas
                if ($request->dosenType === 'same') {
                    // Same dosen for all kelas
                    if ($request->has('dosenIds')) {
                        foreach ($request->dosenIds as $dosenId) {
                            DosenPengampuKelas::create([
                                'dosenId' => $dosenId,
                                'kelasId' => $kelas->id
                            ]);
                        }
                    }
                } else {
                    // Different dosen per kelas - use kelas name as key
                    if ($request->has("dosenPerKelas.{$kelasName}")) {
                        foreach ($request->input("dosenPerKelas.{$kelasName}") as $dosenId) {
                            DosenPengampuKelas::create([
                                'dosenId' => $dosenId,
                                'kelasId' => $kelas->id
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.tahun-ajaran-matkul.index')->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $tahunAjaranMatkul = TahunAjaranMatkul::findOrFail($id);

        DB::beginTransaction();
        try {
            // Delete related records in correct order
            // 1. Delete nilai records (if any)
            $tahunAjaranMatkul->nilai()->delete();

            // 2. Delete kelas_mahasiswa records
            $kelasIds = $tahunAjaranMatkul->kelas->pluck('id');
            KelasMahasiswa::whereIn('kelasId', $kelasIds)->delete();

            // 3. Delete dosen_pengampu_kelas records
            DosenPengampuKelas::whereIn('kelasId', $kelasIds)->delete();

            // 4. Delete kelas records
            $tahunAjaranMatkul->kelas()->delete();

            // 5. Delete tahun_ajaran_matkul
            $tahunAjaranMatkul->delete();

            DB::commit();
            return redirect()->route('admin.tahun-ajaran-matkul.index')->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()]);
        }
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:tahun_ajaran_matkul,id'
        ]);

        DB::beginTransaction();
        try {
            $tahunAjaranMatkuls = TahunAjaranMatkul::whereIn('id', $request->ids)->get();

            foreach ($tahunAjaranMatkuls as $tahunAjaranMatkul) {
                // 1. Delete nilai records (if any)
                $tahunAjaranMatkul->nilai()->delete();

                // 2. Delete kelas_mahasiswa records
                $kelasIds = $tahunAjaranMatkul->kelas->pluck('id');
                KelasMahasiswa::whereIn('kelasId', $kelasIds)->delete();

                // 3. Delete dosen_pengampu_kelas records
                DosenPengampuKelas::whereIn('kelasId', $kelasIds)->delete();

                // 4. Delete kelas records
                $tahunAjaranMatkul->kelas()->delete();

                // 5. Delete tahun_ajaran_matkul
                $tahunAjaranMatkul->delete();
            }

            DB::commit();
            return redirect()->route('admin.tahun-ajaran-matkul.index')->with('success', count($request->ids) . ' data berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus data terpilih: ' . $e->getMessage()]);
        }
    }

    // Method untuk menambahkan mahasiswa ke kelas
    public function addMahasiswa(Request $request, $id)
    {
        $request->validate([
            'mahasiswa_ids' => 'required|array|min:1',
            'mahasiswa_ids.*' => 'exists:mahasiswa,id',
            'kelasId' => 'required|exists:kelas,id'
        ]);

        $tahunAjaranMatkul = TahunAjaranMatkul::findOrFail($id);

        $addedCount = 0;
        foreach ($request->mahasiswa_ids as $mahasiswaId) {
            // Check if already exists
            $existing = KelasMahasiswa::where('mahasiswaId', $mahasiswaId)
                ->where('kelasId', $request->kelasId)
                ->first();

            if (!$existing) {
                KelasMahasiswa::create([
                    'mahasiswaId' => $mahasiswaId,
                    'kelasId' => $request->kelasId
                ]);
                $addedCount++;
            }
        }

        return back()->with('success', "$addedCount mahasiswa berhasil ditambahkan ke kelas.");
    }

    // Method untuk menghapus mahasiswa dari kelas
    public function removeMahasiswa($id, $mahasiswaId, $kelasId)
    {
        KelasMahasiswa::where('mahasiswaId', $mahasiswaId)
            ->where('kelasId', $kelasId)
            ->delete();

        return back()->with('success', 'Mahasiswa berhasil dihapus dari kelas.');
    }

    // Method untuk menambahkan dosen pengampu
    public function addDosen(Request $request, $id)
    {
        $request->validate([
            'dosenIds' => 'required|array|min:1',
            'dosenIds.*' => 'exists:dosen,id',
            'kelasId' => 'required|exists:kelas,id'
        ]);

        $tahunAjaranMatkul = TahunAjaranMatkul::findOrFail($id);

        foreach ($request->dosenIds as $dosenId) {
            // Check if already exists
            $existing = DosenPengampuKelas::where('dosenId', $dosenId)
                ->where('kelasId', $request->kelasId)
                ->first();

            if (!$existing) {
                DosenPengampuKelas::create([
                    'dosenId' => $dosenId,
                    'kelasId' => $request->kelasId
                ]);
            }
        }

        return back()->with('success', 'Dosen pengampu berhasil ditambahkan.');
    }

    // Method untuk menghapus dosen pengampu
    public function removeDosen($id, $dosenId, $kelasId)
    {
        DosenPengampuKelas::where('dosenId', $dosenId)
            ->where('kelasId', $kelasId)
            ->delete();

        return back()->with('success', 'Dosen pengampu berhasil dihapus.');
    }

    // Method untuk halaman manage mahasiswa
    public function manageMahasiswa(Request $request, $id)
    {
        $tahunAjaranMatkul = TahunAjaranMatkul::with([
            'tahunAjaran',
            'mataKuliah',
            'kelas.kelasMahasiswa.mahasiswa'
        ])->findOrFail($id);

        // Query mahasiswa yang belum di kelas ini
        $existingMahasiswaIds = $tahunAjaranMatkul->kelas->flatMap->kelasMahasiswa->pluck('mahasiswaId')->unique();
        $query = Mahasiswa::whereNotIn('id', $existingMahasiswaIds);

        // Filter by tahun masuk
        if ($request->filled('tahun_masuk')) {
            $query->where('tahunMasuk', $request->tahun_masuk);
        }

        // Search by nama atau NIM
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $availableMahasiswas = $query->orderBy('nama')->paginate(20);
        $availableMahasiswas->appends($request->query());

        // Get unique tahun masuk untuk filter
        $tahunMasukOptions = Mahasiswa::select('tahunMasuk')
            ->distinct()
            ->orderBy('tahunMasuk', 'desc')
            ->pluck('tahunMasuk');

        return view('admin.tahun-ajaran-matkul.manage-mahasiswa', compact(
            'tahunAjaranMatkul',
            'availableMahasiswas',
            'tahunMasukOptions'
        ));
    }

    // Method untuk bulk add mahasiswa
    public function bulkAddMahasiswa(Request $request, $id)
    {
        $request->validate([
            'mahasiswa_ids' => 'required|array|min:1',
            'mahasiswa_ids.*' => 'exists:mahasiswa,id',
            'kelasId' => 'required|exists:kelas,id'
        ]);

        $tahunAjaranMatkul = TahunAjaranMatkul::findOrFail($id);

        $addedCount = 0;
        foreach ($request->mahasiswa_ids as $mahasiswaId) {
            // Check if already exists
            $existing = KelasMahasiswa::where('mahasiswaId', $mahasiswaId)
                ->where('kelasId', $request->kelasId)
                ->first();

            if (!$existing) {
                KelasMahasiswa::create([
                    'mahasiswaId' => $mahasiswaId,
                    'kelasId' => $request->kelasId
                ]);
                $addedCount++;
            }
        }

        return back()->with('success', "$addedCount mahasiswa berhasil ditambahkan ke kelas.");
    }

    // Method untuk tambah kelas baru
    public function addKelas(Request $request, $id)
    {
        $request->validate([
            'kelasNames' => 'required|array|min:1',
            'kelasNames.*' => 'required|string|max:10',
            'dosenOption' => 'required|in:same,different'
        ]);

        $tahunAjaranMatkul = TahunAjaranMatkul::findOrFail($id);

        // Filter out empty kelas names
        $kelasNames = array_filter($request->kelasNames, function ($name) {
            return !empty(trim($name));
        });

        if (empty($kelasNames)) {
            return back()->withErrors(['kelasNames' => 'Minimal satu nama kelas harus diisi.'])->withInput();
        }

        // Check for duplicate kelas names
        $existingKelasNames = $tahunAjaranMatkul->kelas->pluck('namaKelas')->toArray();
        $duplicateKelas = array_intersect($kelasNames, $existingKelasNames);

        if (!empty($duplicateKelas)) {
            return back()->withErrors(['kelasNames' => 'Kelas ' . implode(', ', $duplicateKelas) . ' sudah ada.'])->withInput();
        }

        // Validate dosen selection based on option
        if ($request->dosenOption === 'same') {
            $request->validate([
                'dosenIds' => 'required|array|min:1',
                'dosenIds.*' => 'exists:dosen,id'
            ]);
        } else {
            // For different dosen per class, validate that each class has at least one dosen
            foreach ($kelasNames as $kelasName) {
                $kelasName = trim($kelasName);
                if (!empty($kelasName)) {
                    if (!$request->has("dosenPerKelas.{$kelasName}") || empty($request->input("dosenPerKelas.{$kelasName}"))) {
                        return back()->withErrors(['dosenPerKelas' => "Kelas {$kelasName} harus memiliki minimal satu dosen pengampu."])->withInput();
                    }
                }
            }
        }

        DB::beginTransaction();
        try {
            foreach ($kelasNames as $kelasName) {
                $kelasName = trim($kelasName);
                if (!empty($kelasName)) {
                    $kelas = Kelas::create([
                        'namaKelas' => $kelasName,
                        'tahunAjaranMatkulId' => $tahunAjaranMatkul->id
                    ]);

                    // Create dosen pengampu based on option
                    if ($request->dosenOption === 'same') {
                        // Same dosen for all classes
                        if ($request->has('dosenIds')) {
                            foreach ($request->dosenIds as $dosenId) {
                                DosenPengampuKelas::create([
                                    'dosenId' => $dosenId,
                                    'kelasId' => $kelas->id
                                ]);
                            }
                        }
                    } else {
                        // Different dosen per class
                        if ($request->has("dosenPerKelas.{$kelasName}")) {
                            foreach ($request->input("dosenPerKelas.{$kelasName}") as $dosenId) {
                                DosenPengampuKelas::create([
                                    'dosenId' => $dosenId,
                                    'kelasId' => $kelas->id
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();
            return back()->with('success', 'Kelas berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Export template Excel untuk import tahun ajaran mata kuliah
     */
    public function exportTemplate(Request $request)
    {
        $tahunAjaranId = $request->get('tahun_ajaran_id');
        return Excel::download(new TahunAjaranMatkulTemplateExport($tahunAjaranId), 'template_tahun_ajaran_matkul.xlsx');
    }

    /**
     * Import data tahun ajaran mata kuliah dari Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|max:2048',
        ], [
            'file.required' => 'File Excel wajib diupload',
            'file.max' => 'Ukuran file maksimal 2MB',
        ]);

        // Validate extension manually as mimes:xlsx,xls can be unreliable
        $extension = strtolower($request->file('file')->getClientOriginalExtension());
        if (!in_array($extension, ['xlsx', 'xls'])) {
            return redirect()->back()
                ->with('error', 'File harus berformat Excel (.xlsx atau .xls)')
                ->withInput();
        }

        try {
            $jobId = uniqid('import_');
            Log::info('[Import] Starting import with jobId: ' . $jobId);
            
            $import = new TahunAjaranMatkulImport($jobId);
            
            // This will automatically push to the queue because the class implements ShouldQueue
            Excel::import($import, $request->file('file'));

            Log::info('[Import] Excel::import dispatched successfully for jobId: ' . $jobId);

            return response()->json([
                'status' => 'success',
                'job_id' => $jobId,
                'message' => 'File Excel sedang diproses di latar belakang.'
            ]);
        } catch (\Exception $e) {
            Log::error('[Import] Exception: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal import data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function importStatus(Request $request)
    {
        $jobId = $request->query('job_id');
        
        if (!$jobId) {
            return response()->json(['error' => 'Job ID required'], 400);
        }

        $total = Cache::get('import_progress_' . $jobId . '_total', 0);
        $processed = Cache::get('import_progress_' . $jobId . '_processed', 0);
        
        $percentage = $total > 0 ? min(100, round(($processed / $total) * 100)) : 0;

        Log::info('[Import Status] jobId=' . $jobId . ', total=' . $total . ', processed=' . $processed . ', pct=' . $percentage);

        return response()->json([
            'total' => $total,
            'processed' => $processed,
            'percentage' => $percentage,
            'finished' => ($total > 0 && $processed >= $total)
        ]);
    }

    /**
     * Duplicate mata kuliah dari tahun ajaran sebelumnya
     */
    public function duplicateFromPreviousYear(Request $request)
    {
        $request->validate([
            'source_tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'target_tahun_ajaran_id' => 'required|exists:tahun_ajaran,id|different:source_tahun_ajaran_id',
        ], [
            'source_tahun_ajaran_id.required' => 'Tahun ajaran sumber wajib dipilih',
            'target_tahun_ajaran_id.required' => 'Tahun ajaran target wajib dipilih',
            'target_tahun_ajaran_id.different' => 'Tahun ajaran target harus berbeda dengan sumber',
        ]);

        try {
            DB::beginTransaction();

            $sourceMatkuls = TahunAjaranMatkul::where('tahunAjaranId', $request->source_tahun_ajaran_id)
                ->with(['mataKuliah', 'kelas'])
                ->get();

            $duplicatedCount = 0;
            foreach ($sourceMatkuls as $sourceMatkul) {
                // Check if already exists in target year
                $existing = TahunAjaranMatkul::where('tahunAjaranId', $request->target_tahun_ajaran_id)
                    ->where('mataKuliahId', $sourceMatkul->mataKuliahId)
                    ->first();

                if (!$existing) {
                    // Create new tahun ajaran mata kuliah
                    $newMatkul = TahunAjaranMatkul::create([
                        'tahunAjaranId' => $request->target_tahun_ajaran_id,
                        'mataKuliahId' => $sourceMatkul->mataKuliahId,
                    ]);

                    // Duplicate kelas structure
                    foreach ($sourceMatkul->kelas as $kelas) {
                        $newKelas = Kelas::create([
                            'tahunAjaranMatkulId' => $newMatkul->id,
                            'namaKelas' => $kelas->namaKelas,
                        ]);

                        // Duplicate dosen pengampu (optional)
                        foreach ($kelas->dosenPengampuKelas as $dosenPengampu) {
                            DosenPengampuKelas::create([
                                'kelasId' => $newKelas->id,
                                'dosenId' => $dosenPengampu->dosenId,
                            ]);
                        }
                    }

                    $duplicatedCount++;
                }
            }

            DB::commit();

            return redirect()->route('admin.tahun-ajaran-matkul.index')
                ->with('success', "Berhasil menduplikasi {$duplicatedCount} mata kuliah dari tahun ajaran sebelumnya!");
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Gagal menduplikasi data: ' . $e->getMessage())
                ->withInput();
        }
    }
}
