<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Redirect user to appropriate dashboard based on their role
     */
    public function index()
    {
        $user = Auth::user();
        
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'dosen':
                return redirect()->route('dosen.dashboard');
            case 'mahasiswa':
                return redirect()->route('mahasiswa.dashboard');
            case 'pimpinan':
                return redirect()->route('pimpinan.dashboard');
            default:
                return redirect()->route('login');
        }
    }

    /**
     * Admin Dashboard
     */
    public function adminDashboard()
    {
        $user = Auth::user();
        return view('admin.dashboard', compact('user'));
    }

    /**
     * Dosen Dashboard
     */
    public function dosenDashboard()
    {
        $user = Auth::user();
        return view('dosen.dashboard', compact('user'));
    }

    /**
     * Mahasiswa Dashboard
     */
    public function mahasiswaDashboard()
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;
        $mahasiswaId = $mahasiswa->id;

        $cpls = \App\Models\Cpl::with(['cpmk' => function($q) use ($mahasiswaId) {
            $q->whereHas('nilai', function($n) use ($mahasiswaId) {
                $n->where('mahasiswaId', $mahasiswaId);
            })->with(['cpmkMatKul.tahunAjaranMatkul.mataKuliah']);
        }])->get();

        $cpl_cpmk_data = [];
        $realCplLabels = [];
        if ($cpls->count() > 0) {
            foreach ($cpls as $cpl) {
                $cpmks = $cpl->cpmk;
                $cpmk_data = [];
                foreach ($cpmks as $cpmk) {
                    // Ambil nilai per komponen untuk CPMK ini melalui bobot
                    $nilaiPerKomponen = \App\Models\Nilai::where('mahasiswaId', $mahasiswaId)
                        ->where('cpmkId', $cpmk->id)
                        ->with('bobot.komponen')
                        ->get()
                        ->groupBy('bobot.komponenId')
                        ->map(function($nilaiGroup) {
                            return $nilaiGroup->avg('nilai');
                        });

                    // Ambil kode mata kuliah dari relasi
                    $cpmkMatKul = $cpmk->cpmkMatKul->first();
                    $kodeMataKuliah = $cpmkMatKul->tahunAjaranMatkul->mataKuliah->kodeMatkul ?? '';
                    $label = $cpmk->kodeCpmk . ' - ' . $kodeMataKuliah;
                    
                    // Jika ada nilai, gunakan nilai tersebut. Jika tidak, gunakan 0
                    if ($nilaiPerKomponen->count() > 0) {
                        $totalNilai = $nilaiPerKomponen->sum();
                    } else {
                        // Jika tidak ada nilai, buat dummy dengan nilai 0
                        $nilaiPerKomponen = collect([
                            1 => 0, // Kuis
                            2 => 0, // UAS
                            3 => 0, // UTS
                            4 => 0, // TB
                            5 => 0, // Tugas
                        ]);
                        $totalNilai = 0;
                    }
                    
                    $cpmk_data[] = [
                        'label' => $label,
                        'komponen_nilai' => $nilaiPerKomponen->toArray(),
                        'total_nilai' => $totalNilai
                    ];
                }
                $top = collect($cpmk_data)->sortByDesc('total_nilai')->take(5)->values();
                if ($top->count() > 0) {
                    $cpl_cpmk_data[] = [
                        'cpl_label' => $cpl->kodeCpl,
                        'cpmk_data' => $top->toArray(),
                    ];
                    $realCplLabels[] = $cpl->kodeCpl;
                }
            }
        }

        return view('mahasiswa.dashboard', compact('user', 'cpl_cpmk_data'));
    }
    


    /**
     * Pimpinan Dashboard
     */
    public function pimpinanDashboard()
    {
        $user = Auth::user();
        return view('pimpinan.dashboard', compact('user'));
    }
}
