<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use App\Models\MataKuliah;
use App\Models\TahunAjaranMatkul;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\DosenPengampu;
use App\Models\KelasMahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TahunAjaranMatkulController extends Controller
{
    public function index(Request $request)
    {
        $query = TahunAjaranMatkul::with(['tahunAjaran', 'mataKuliah', 'dosenPengampu.dosen', 'kelasMahasiswa.mahasiswa']);

        // Get the latest tahun ajaran for default filter
        $latestTahunAjaran = TahunAjaran::orderBy('tahun', 'desc')->orderBy('periode', 'desc')->first();
        
        // Set default filter to latest tahun ajaran if no filter is selected
        $selectedTahunAjaranId = $request->filled('tahun_ajaran_id') ? $request->tahun_ajaran_id : ($latestTahunAjaran ? $latestTahunAjaran->id : null);

        // Filter by tahun ajaran (default to latest)
        if ($selectedTahunAjaranId) {
            $query->where('tahunAjaranId', $selectedTahunAjaranId);
        }

        // Search by mata kuliah
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('mataKuliah', function($q) use ($search) {
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
            'kelas' => 'required|integer|min:1',
            'dosenIds' => 'required|array|min:1',
            'dosenIds.*' => 'exists:dosen,id'
        ]);

        // Check if combination already exists
        $existing = TahunAjaranMatkul::where('tahunAjaranId', $request->tahunAjaranId)
            ->where('mataKuliahId', $request->mataKuliahId)
            ->where('kelas', $request->kelas)
            ->first();

        if ($existing) {
            return back()->withErrors(['kelas' => 'Kombinasi tahun ajaran, mata kuliah, dan kelas sudah ada.']);
        }

        DB::beginTransaction();
        try {
            $tahunAjaranMatkul = TahunAjaranMatkul::create([
                'tahunAjaranId' => $request->tahunAjaranId,
                'mataKuliahId' => $request->mataKuliahId,
                'kelas' => $request->kelas
            ]);

            // Create dosen pengampu
            foreach ($request->dosenIds as $dosenId) {
                DosenPengampu::create([
                    'dosenId' => $dosenId,
                    'tahunAjaranMatkulId' => $tahunAjaranMatkul->id
                ]);
            }

            DB::commit();
            return redirect()->route('admin.tahun-ajaran-matkul.index')->with('success', 'Mata kuliah berhasil ditambahkan ke tahun ajaran.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data.']);
        }
    }

    public function show(Request $request, $id)
    {
        $tahunAjaranMatkul = TahunAjaranMatkul::with([
            'tahunAjaran', 
            'mataKuliah', 
            'dosenPengampu.dosen'
        ])->findOrFail($id);

        // Query untuk mahasiswa yang ada di kelas dengan search
        $query = KelasMahasiswa::with('mahasiswa.user')
            ->where('tahunAjaranMatkulId', $id);

        // Search mahasiswa berdasarkan nama atau NIM
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('mahasiswa', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $kelasMahasiswa = $query->paginate(10);
        $kelasMahasiswa->appends($request->query());

        $mahasiswas = Mahasiswa::whereNotIn('id', $kelasMahasiswa->pluck('mahasiswaId'))->get();
        $dosens = Dosen::whereNotIn('id', $tahunAjaranMatkul->dosenPengampu->pluck('dosenId'))->get();

        return view('admin.tahun-ajaran-matkul.show', compact('tahunAjaranMatkul', 'kelasMahasiswa', 'mahasiswas', 'dosens'));
    }

    public function edit($id)
    {
        $tahunAjaranMatkul = TahunAjaranMatkul::with(['dosenPengampu.dosen'])->findOrFail($id);
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
            'kelas' => 'required|integer|min:1',
            'dosenIds' => 'required|array|min:1',
            'dosenIds.*' => 'exists:dosen,id'
        ]);

        $tahunAjaranMatkul = TahunAjaranMatkul::findOrFail($id);

        // Check if combination already exists (excluding current record)
        $existing = TahunAjaranMatkul::where('tahunAjaranId', $request->tahunAjaranId)
            ->where('mataKuliahId', $request->mataKuliahId)
            ->where('kelas', $request->kelas)
            ->where('id', '!=', $id)
            ->first();

        if ($existing) {
            return back()->withErrors(['kelas' => 'Kombinasi tahun ajaran, mata kuliah, dan kelas sudah ada.']);
        }

        DB::beginTransaction();
        try {
            $tahunAjaranMatkul->update([
                'tahunAjaranId' => $request->tahunAjaranId,
                'mataKuliahId' => $request->mataKuliahId,
                'kelas' => $request->kelas
            ]);

            // Update dosen pengampu
            $tahunAjaranMatkul->dosenPengampu()->delete();
            foreach ($request->dosenIds as $dosenId) {
                DosenPengampu::create([
                    'dosenId' => $dosenId,
                    'tahunAjaranMatkulId' => $tahunAjaranMatkul->id
                ]);
            }

            DB::commit();
            return redirect()->route('admin.tahun-ajaran-matkul.index')->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui data.']);
        }
    }

    public function destroy($id)
    {
        $tahunAjaranMatkul = TahunAjaranMatkul::findOrFail($id);
        
        DB::beginTransaction();
        try {
            // Delete related records
            $tahunAjaranMatkul->dosenPengampu()->delete();
            $tahunAjaranMatkul->kelasMahasiswa()->delete();
            $tahunAjaranMatkul->nilai()->delete();
            
            $tahunAjaranMatkul->delete();
            
            DB::commit();
            return redirect()->route('admin.tahun-ajaran-matkul.index')->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus data.']);
        }
    }

    // Method untuk menambahkan mahasiswa ke kelas
    public function addMahasiswa(Request $request, $id)
    {
        $request->validate([
            'mahasiswaIds' => 'required|array|min:1',
            'mahasiswaIds.*' => 'exists:mahasiswa,id'
        ]);

        $tahunAjaranMatkul = TahunAjaranMatkul::findOrFail($id);

        foreach ($request->mahasiswaIds as $mahasiswaId) {
            // Check if already exists
            $existing = KelasMahasiswa::where('mahasiswaId', $mahasiswaId)
                ->where('tahunAjaranMatkulId', $id)
                ->first();

            if (!$existing) {
                KelasMahasiswa::create([
                    'mahasiswaId' => $mahasiswaId,
                    'tahunAjaranMatkulId' => $id
                ]);
            }
        }

        return back()->with('success', 'Mahasiswa berhasil ditambahkan ke kelas.');
    }

    // Method untuk menghapus mahasiswa dari kelas
    public function removeMahasiswa($id, $mahasiswaId)
    {
        KelasMahasiswa::where('mahasiswaId', $mahasiswaId)
            ->where('tahunAjaranMatkulId', $id)
            ->delete();

        return back()->with('success', 'Mahasiswa berhasil dihapus dari kelas.');
    }

    // Method untuk menambahkan dosen pengampu
    public function addDosen(Request $request, $id)
    {
        $request->validate([
            'dosenIds' => 'required|array|min:1',
            'dosenIds.*' => 'exists:dosen,id'
        ]);

        foreach ($request->dosenIds as $dosenId) {
            // Check if already exists
            $existing = DosenPengampu::where('dosenId', $dosenId)
                ->where('tahunAjaranMatkulId', $id)
                ->first();

            if (!$existing) {
                DosenPengampu::create([
                    'dosenId' => $dosenId,
                    'tahunAjaranMatkulId' => $id
                ]);
            }
        }

        return back()->with('success', 'Dosen pengampu berhasil ditambahkan.');
    }

    // Method untuk menghapus dosen pengampu
    public function removeDosen($id, $dosenId)
    {
        DosenPengampu::where('dosenId', $dosenId)
            ->where('tahunAjaranMatkulId', $id)
            ->delete();

        return back()->with('success', 'Dosen pengampu berhasil dihapus.');
    }

    // Method untuk halaman manage mahasiswa
    public function manageMahasiswa(Request $request, $id)
    {
        $tahunAjaranMatkul = TahunAjaranMatkul::with([
            'tahunAjaran', 
            'mataKuliah', 
            'kelasMahasiswa.mahasiswa'
        ])->findOrFail($id);

        // Query mahasiswa yang belum di kelas ini
        $query = Mahasiswa::whereNotIn('id', $tahunAjaranMatkul->kelasMahasiswa->pluck('mahasiswaId'));

        // Filter by tahun masuk
        if ($request->filled('tahun_masuk')) {
            $query->where('tahunMasuk', $request->tahun_masuk);
        }

        // Search by nama atau NIM
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
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
            'mahasiswa_ids.*' => 'exists:mahasiswa,id'
        ]);

        $tahunAjaranMatkul = TahunAjaranMatkul::findOrFail($id);

        $addedCount = 0;
        foreach ($request->mahasiswa_ids as $mahasiswaId) {
            // Check if already exists
            $existing = KelasMahasiswa::where('mahasiswaId', $mahasiswaId)
                ->where('tahunAjaranMatkulId', $id)
                ->first();

            if (!$existing) {
                KelasMahasiswa::create([
                    'mahasiswaId' => $mahasiswaId,
                    'tahunAjaranMatkulId' => $id
                ]);
                $addedCount++;
            }
        }

        return back()->with('success', "$addedCount mahasiswa berhasil ditambahkan ke kelas.");
    }
} 