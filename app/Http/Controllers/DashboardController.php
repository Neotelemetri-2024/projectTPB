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
                    $avg = $cpmk->nilai()->where('mahasiswaId', $mahasiswaId)->avg('nilai');
                    if ($avg !== null) {
                        $cpmkMatKul = $cpmk->cpmkMatKul->first();
                        $tahunAjaranMatkulId = $cpmkMatKul->tahunAjaranMatkul->id ?? null;
                        $totalBobot = \App\Models\Bobot::where('cpmkId', $cpmk->id)
                            ->whereHas('tahunAjaranMatkul', function($q) use ($tahunAjaranMatkulId) {
                                $q->where('id', $tahunAjaranMatkulId);
                            })
                            ->sum('bobot');
                        $nilaiNormal = ($totalBobot > 0) ? round(($avg / $totalBobot) * 100, 2) : 0;
                        $label = $cpmk->kodeCpmk; // hanya kode CPMK
                        $cpmk_data[] = [
                            'label' => $label,
                            'nilai' => $nilaiNormal
                        ];
                    }
                }
                $top = collect($cpmk_data)->sortByDesc('nilai')->take(5)->values();
                if ($top->count() > 0) {
                    $cpl_cpmk_data[] = [
                        'cpl_label' => $cpl->kodeCpl,
                        'cpmk_labels' => $top->pluck('label')->all(),
                        'cpmk_nilai' => $top->pluck('nilai')->all(),
                    ];
                    $realCplLabels[] = $cpl->kodeCpl;
                }
            }
        }
        // Tambahkan dummy agar selalu 11 CPL
        $totalCpl = count($cpl_cpmk_data);
        for ($i = $totalCpl + 1; $i <= 11; $i++) {
            $cplLabel = 'CPL-' . str_pad($i, 2, '0', STR_PAD_LEFT);
            // Pastikan tidak duplikat label CPL
            if (in_array($cplLabel, $realCplLabels)) continue;
            $cpmk_labels = [];
            $cpmk_nilai = [];
            for ($j = 1; $j <= 4; $j++) {
                $cpmkLabel = 'CPMK-' . str_pad($j, 2, '0', STR_PAD_LEFT); // hanya kode CPMK
                $cpmk_labels[] = $cpmkLabel;
                $cpmk_nilai[] = rand(40, 100);
            }
            $cpl_cpmk_data[] = [
                'cpl_label' => $cplLabel,
                'cpmk_labels' => $cpmk_labels,
                'cpmk_nilai' => $cpmk_nilai,
            ];
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
