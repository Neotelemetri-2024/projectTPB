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
            });
        }])->get();

        $cpl_cpmk_data = [];
        foreach ($cpls as $cpl) {
            $cpmks = $cpl->cpmk;
            $cpmk_data = [];
            foreach ($cpmks as $cpmk) {
                $avg = $cpmk->nilai()->where('mahasiswaId', $mahasiswaId)->avg('nilai');
                if ($avg !== null) {
                    $cpmk_data[] = [
                        'label' => $cpmk->kodeCpmk,
                        'nilai' => round($avg, )
                    ];
                }
            }
            // Urutkan CPMK berdasarkan nilai tertinggi, ambil 5 teratas
            $top = collect($cpmk_data)->sortByDesc('nilai')->take(5)->values();
            if ($top->count() > 0) {
                $cpl_cpmk_data[] = [
                    'cpl_label' => $cpl->kodeCpl,
                    'cpmk_labels' => $top->pluck('label')->all(),
                    'cpmk_nilai' => $top->pluck('nilai')->all(),
                ];
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
