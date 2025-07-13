<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaranMatkul;
use App\Models\DosenPengampu;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MataKuliahController extends Controller
{
    public function __construct()
    {
        $this->middleware('dosen');
    }
    public function index(Request $request)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan.');
        }

        // Start with base query
        $query = TahunAjaranMatkul::whereHas('dosenPengampu', function ($q) use ($dosen) {
            $q->where('dosenId', $dosen->id);
        });

        // Apply filters
        if ($request->filled('tahun_ajaran_id')) {
            $query->whereHas('tahunAjaran', function ($q) use ($request) {
                $q->where('id', $request->tahun_ajaran_id);
            });
        }

        if ($request->filled('jenis')) {
            $query->whereHas('mataKuliah', function ($q) use ($request) {
                $q->where('jenis', $request->jenis);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('mataKuliah', function ($subQ) use ($search) {
                    $subQ->where('namaMatkul', 'like', "%{$search}%")
                         ->orWhere('kodeMatkul', 'like', "%{$search}%");
                });
            });
        }

        // Get results with relationships
        $mataKuliahData = $query->with([
            'mataKuliah',
            'tahunAjaran',
            'dosenPengampu.dosen',
            'kelasMahasiswa.mahasiswa'
        ])
        ->orderBy('created_at', 'desc')
        ->get();

        // Group by mata kuliah and tahun ajaran to avoid duplicate cards
        $mataKuliahDiampu = $mataKuliahData->groupBy(function($item) {
            return $item->mataKuliahId . '_' . $item->tahunAjaranId;
        })->map(function($group) {
            // Take the first item as representative
            $representative = $group->first();

            // Aggregate data from all classes
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

        return view('dosen.mata-kuliah.index', compact(
            'mataKuliahDiampu',
            'dosen',
            'tahunAjaranList',
            'jenisList'
        ));
    }

    /**
     * Display the specified course details.
     */
    public function show($id)
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan.');
        }

        // Get all classes for this mata kuliah that this lecturer teaches
        $mataKuliahClasses = TahunAjaranMatkul::whereHas('dosenPengampu', function ($query) use ($dosen) {
            $query->where('dosenId', $dosen->id);
        })
        ->with([
            'mataKuliah',
            'tahunAjaran',
            'dosenPengampu.dosen',
            'kelasMahasiswa.mahasiswa',
            'cpmkMatKul.cpmk'
        ])
        ->where('id', $id)
        ->orWhere(function($query) use ($id, $dosen) {
            // Get the representative record first
            $representative = TahunAjaranMatkul::find($id);
            if ($representative) {
                $query->where('mataKuliahId', $representative->mataKuliahId)
                      ->where('tahunAjaranId', $representative->tahunAjaranId)
                      ->whereHas('dosenPengampu', function ($q) use ($dosen) {
                          $q->where('dosenId', $dosen->id);
                      });
            }
        })
        ->get();

        if ($mataKuliahClasses->isEmpty()) {
            abort(404);
        }

        // Use the first one as main reference
        $mataKuliahDiampu = $mataKuliahClasses->first();

        // Aggregate all students and classes
        $allMahasiswa = collect();
        $allKelas = collect();
        $allDosenPengampu = collect();

        foreach($mataKuliahClasses as $class) {
            $allMahasiswa = $allMahasiswa->merge($class->kelasMahasiswa->pluck('mahasiswa'));
            $allKelas->push($class->kelas);
            $allDosenPengampu = $allDosenPengampu->merge($class->dosenPengampu);
        }

        // Remove duplicates
        $mahasiswa = $allMahasiswa->unique('id');
        $kelasNumbers = $allKelas->unique()->sort()->values();

        // Get lecturer's role in this course
        $dosenPengampu = $allDosenPengampu->where('dosenId', $dosen->id)->first();

        return view('dosen.mata-kuliah.show', compact(
            'mataKuliahDiampu',
            'mataKuliahClasses',
            'mahasiswa',
            'kelasNumbers',
            'dosen',
            'dosenPengampu'
        ));
    }
}
