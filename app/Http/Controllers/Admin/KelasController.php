<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\DosenPengampuKelas;
use App\Models\KelasMahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KelasController extends Controller
{
    public function show($id)
    {
        $kelas = Kelas::with([
            'tahunAjaranMatkul.tahunAjaran',
            'tahunAjaranMatkul.mataKuliah',
            'dosenPengampuKelas.dosen.user',
            'kelasMahasiswa.mahasiswa.user'
        ])->findOrFail($id);

        return view('admin.kelas.show', compact('kelas'));
    }

    public function manageMahasiswa(Request $request, $id)
    {
        $kelas = Kelas::with([
            'tahunAjaranMatkul.tahunAjaran',
            'tahunAjaranMatkul.mataKuliah',
            'kelasMahasiswa.mahasiswa'
        ])->findOrFail($id);

        // Query mahasiswa yang belum di kelas ini
        $existingMahasiswaIds = $kelas->kelasMahasiswa->pluck('mahasiswaId');
        $query = Mahasiswa::whereNotIn('id', $existingMahasiswaIds);

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

        return view('admin.kelas.manage-mahasiswa', compact(
            'kelas', 
            'availableMahasiswas', 
            'tahunMasukOptions'
        ));
    }

    public function manageDosen(Request $request, $id)
    {
        $kelas = Kelas::with([
            'tahunAjaranMatkul.tahunAjaran',
            'tahunAjaranMatkul.mataKuliah',
            'dosenPengampuKelas.dosen'
        ])->findOrFail($id);

        // Get available dosens (not teaching this kelas)
        $existingDosenIds = $kelas->dosenPengampuKelas->pluck('dosenId');
        $availableDosens = Dosen::whereNotIn('id', $existingDosenIds)->get();

        return view('admin.kelas.manage-dosen', compact('kelas', 'availableDosens'));
    }

    public function addMahasiswa(Request $request, $id)
    {
        $request->validate([
            'mahasiswa_ids' => 'required|array|min:1',
            'mahasiswa_ids.*' => 'exists:mahasiswa,id'
        ]);

        $kelas = Kelas::findOrFail($id);

        $addedCount = 0;
        foreach ($request->mahasiswa_ids as $mahasiswaId) {
            // Check if already exists
            $existing = KelasMahasiswa::where('mahasiswaId', $mahasiswaId)
                ->where('kelasId', $kelas->id)
                ->first();

            if (!$existing) {
                KelasMahasiswa::create([
                    'mahasiswaId' => $mahasiswaId,
                    'kelasId' => $kelas->id
                ]);
                $addedCount++;
            }
        }

        return back()->with('success', "$addedCount mahasiswa berhasil ditambahkan ke kelas.");
    }

    public function removeMahasiswa($id, $mahasiswaId)
    {
        KelasMahasiswa::where('mahasiswaId', $mahasiswaId)
            ->where('kelasId', $id)
            ->delete();

        return back()->with('success', 'Mahasiswa berhasil dihapus dari kelas.');
    }

    public function addDosen(Request $request, $id)
    {
        $request->validate([
            'dosenIds' => 'required|array|min:1',
            'dosenIds.*' => 'exists:dosen,id'
        ]);

        $kelas = Kelas::findOrFail($id);

        $addedCount = 0;
        foreach ($request->dosenIds as $dosenId) {
            // Check if already exists
            $existing = DosenPengampuKelas::where('dosenId', $dosenId)
                ->where('kelasId', $kelas->id)
                ->first();

            if (!$existing) {
                DosenPengampuKelas::create([
                    'dosenId' => $dosenId,
                    'kelasId' => $kelas->id
                ]);
                $addedCount++;
            }
        }

        return back()->with('success', "$addedCount dosen berhasil ditambahkan ke kelas.");
    }

    public function removeDosen($id, $dosenId)
    {
        DosenPengampuKelas::where('dosenId', $dosenId)
            ->where('kelasId', $id)
            ->delete();

        return back()->with('success', 'Dosen berhasil dihapus dari kelas.');
    }

    public function bulkAddMahasiswa(Request $request, $id)
    {
        $request->validate([
            'mahasiswa_ids' => 'required|array|min:1',
            'mahasiswa_ids.*' => 'exists:mahasiswa,id'
        ]);

        $kelas = Kelas::findOrFail($id);

        $addedCount = 0;
        foreach ($request->mahasiswa_ids as $mahasiswaId) {
            // Check if already exists
            $existing = KelasMahasiswa::where('mahasiswaId', $mahasiswaId)
                ->where('kelasId', $kelas->id)
                ->first();

            if (!$existing) {
                KelasMahasiswa::create([
                    'mahasiswaId' => $mahasiswaId,
                    'kelasId' => $kelas->id
                ]);
                $addedCount++;
            }
        }

        return back()->with('success', "$addedCount mahasiswa berhasil ditambahkan ke kelas.");
    }
}
