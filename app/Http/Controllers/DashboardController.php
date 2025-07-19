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
                $totalBobotCpl = 0;
                $totalNilaiCpl = 0;
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

                    $cpmkMatKul = $cpmk->cpmkMatKul->first();
                    $kodeMataKuliah = $cpmkMatKul->tahunAjaranMatkul->mataKuliah->kodeMatkul ?? '';
                    $label = $cpmk->kodeCpmk . ' - ' . $kodeMataKuliah;

                    $totalNilai = $nilaiPerKomponen->sum();
                    $tahunAjaranMatkulId = $cpmkMatKul->tahunAjaranMatkul->id ?? null;
                    $totalBobot = \App\Models\Bobot::where('cpmkId', $cpmk->id)
                        ->whereHas('tahunAjaranMatkul', function($q) use ($tahunAjaranMatkulId) {
                            $q->where('id', $tahunAjaranMatkulId);
                        })
                        ->sum('bobot');
                    $nilaiNormal = ($totalBobot > 0) ? round(($totalNilai / $totalBobot) * 100, 2) : 0;

                    $cpmk_data[] = [
                        'label' => $label,
                        'komponen_nilai' => $nilaiPerKomponen->toArray(),
                        'total_nilai' => $totalNilai,
                        'total_bobot' => $totalBobot,
                        'nilai_normal' => $nilaiNormal
                    ];
                    $totalBobotCpl += $totalBobot;
                    $totalNilaiCpl += $totalNilai;
                }
                // Hitung nilai CPL (maksimal 100)
                $nilai_cpl = ($totalBobotCpl > 0) ? round(($totalNilaiCpl / $totalBobotCpl) * 100, 2) : 0;
                $cpl_cpmk_data[] = [
                    'cpl_label' => $cpl->kodeCpl,
                    'cpmk_data' => $cpmk_data,
                    'nilai_cpl' => $nilai_cpl,
                    'total_bobot_cpl' => $totalBobotCpl,
                    'total_nilai_cpl' => $totalNilaiCpl
                ];
                $realCplLabels[] = $cpl->kodeCpl;
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
