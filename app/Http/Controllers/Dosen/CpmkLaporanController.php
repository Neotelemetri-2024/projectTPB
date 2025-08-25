<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Dompdf\Options;

class CpmkLaporanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $dosen = $user->dosen;
        
        if (!$dosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan.');
        }

        // List tahun ajaran untuk filter
        $tahunAjaranList = \App\Models\TahunAjaran::orderBy('tahun', 'desc')->orderBy('periode', 'desc')->get();
        $selectedTahunAjaranId = $request->get('tahun_ajaran_id');
        if (!$selectedTahunAjaranId) {
            $selectedTahunAjaranId = $tahunAjaranList->first() ? $tahunAjaranList->first()->id : null;
        }

        // Mata kuliah diampu dosen (tahun ajaran terpilih)
        $mataKuliahDiampu = \App\Models\TahunAjaranMatkul::whereHas('kelas.dosenPengampuKelas', function($q) use ($dosen) {
            $q->where('dosenId', $dosen->id);
        })
        ->where('tahunAjaranId', $selectedTahunAjaranId)
        ->with(['mataKuliah', 'kelas.kelasMahasiswa.mahasiswa', 'cpmkMatKul.cpmk.nilai'])
        ->get();

        return view('dosen.cpmk-laporan.index', compact(
            'mataKuliahDiampu',
            'tahunAjaranList',
            'selectedTahunAjaranId'
        ));
    }

    public function show(Request $request, $tahunAjaranMatkulId)
    {
        $user = Auth::user();
        $dosen = $user->dosen;
        
        if (!$dosen) {
            return redirect()->back()->with('error', 'Data dosen tidak ditemukan.');
        }

        // Validasi bahwa dosen mengampu mata kuliah ini
        $tahunAjaranMatkul = \App\Models\TahunAjaranMatkul::whereHas('kelas.dosenPengampuKelas', function($q) use ($dosen) {
            $q->where('dosenId', $dosen->id);
        })
        ->where('id', $tahunAjaranMatkulId)
        ->with(['mataKuliah', 'kelas.kelasMahasiswa.mahasiswa', 'cpmkMatKul.cpmk.nilai'])
        ->first();

        if (!$tahunAjaranMatkul) {
            return redirect()->back()->with('error', 'Mata kuliah tidak ditemukan atau Anda tidak mengampu mata kuliah ini.');
        }

        // Ambil data CPMK dan nilai
        $cpmkData = $this->getCpmkData($tahunAjaranMatkul);
        
        return view('dosen.cpmk-laporan.show', compact(
            'tahunAjaranMatkul',
            'cpmkData'
        ));
    }

    private function getCpmkData($tahunAjaranMatkul)
    {
        // Ambil semua CPMK yang terkait dengan mata kuliah ini
        $cpmks = \App\Models\Cpmk::whereHas('cpmkMatKul', function($q) use ($tahunAjaranMatkul) {
            $q->where('tahunAjaranMatkulId', $tahunAjaranMatkul->id);
        })
        ->with(['nilai' => function($q) use ($tahunAjaranMatkul) {
            $q->whereHas('tahunAjaranMatkul', function($tam) use ($tahunAjaranMatkul) {
                $tam->where('id', $tahunAjaranMatkul->id);
            });
        }])
        ->get();

        $data = [];
        
        // Hitung total mahasiswa yang mengambil mata kuliah ini
        $totalMahasiswaMatkul = \App\Models\KelasMahasiswa::whereHas('kelas', function($q) use ($tahunAjaranMatkul) {
            $q->where('tahunAjaranMatkulId', $tahunAjaranMatkul->id);
        })->distinct('mahasiswaId')->count();

        $nilaiRanges = [
            'U' => ['min' => 0, 'max' => 59, 'label' => 'Uncompetence', 'color' => '#3B82F6'],
            'C' => ['min' => 60, 'max' => 74, 'label' => 'Competence', 'color' => '#EF4444'],
            'E' => ['min' => 75, 'max' => 89, 'label' => 'Excellent', 'color' => '#10B981'],
            'X' => ['min' => 90, 'max' => 100, 'label' => 'Extraordinary', 'color' => '#8B5CF6']
        ];

        foreach ($cpmks as $cpmk) {
            // Ambil nilai hanya untuk mahasiswa yang mengambil mata kuliah ini
            $mahasiswaIds = \App\Models\KelasMahasiswa::whereHas('kelas', function($q) use ($tahunAjaranMatkul) {
                $q->where('tahunAjaranMatkulId', $tahunAjaranMatkul->id);
            })->pluck('mahasiswaId')->toArray();
            
            \Log::info("CPMK {$cpmk->kodeCpmk}: Mahasiswa IDs", $mahasiswaIds);
            
            // Cek semua nilai untuk CPMK ini (tanpa filter mahasiswa)
            $allNilai = \App\Models\Nilai::where('cpmkId', $cpmk->id)
                ->where('tahunAjaranMatkulId', $tahunAjaranMatkul->id)
                ->get();
            
            \Log::info("CPMK {$cpmk->kodeCpmk}: All nilai count", ['count' => $allNilai->count()]);
            
            // Ambil nilai untuk mahasiswa yang mengambil mata kuliah ini
            $nilaiList = \App\Models\Nilai::where('cpmkId', $cpmk->id)
                ->where('tahunAjaranMatkulId', $tahunAjaranMatkul->id)
                ->whereIn('mahasiswaId', $mahasiswaIds)
                ->pluck('nilai')
                ->filter()
                ->toArray();
            
            // Hitung jumlah mahasiswa yang memiliki nilai (bukan jumlah nilai)
            $mahasiswaDenganNilai = \App\Models\Nilai::where('cpmkId', $cpmk->id)
                ->where('tahunAjaranMatkulId', $tahunAjaranMatkul->id)
                ->whereIn('mahasiswaId', $mahasiswaIds)
                ->distinct('mahasiswaId')
                ->count();
            
            \Log::info("CPMK {$cpmk->kodeCpmk}: Nilai count", ['nilai_count' => count($nilaiList), 'mahasiswa_count' => $mahasiswaDenganNilai]);
            
            if ($mahasiswaDenganNilai == 0) continue;

            // Hitung rata-rata nilai per mahasiswa
            $nilaiPerMahasiswa = \App\Models\Nilai::where('cpmkId', $cpmk->id)
                ->where('tahunAjaranMatkulId', $tahunAjaranMatkul->id)
                ->whereIn('mahasiswaId', $mahasiswaIds)
                ->selectRaw('mahasiswaId, AVG(nilai) as rata_nilai')
                ->groupBy('mahasiswaId')
                ->pluck('rata_nilai')
                ->toArray();

            $distribution = [];
            $histogramData = [];
            
            // Hitung distribusi nilai untuk pie chart berdasarkan rata-rata per mahasiswa
            foreach ($nilaiRanges as $grade => $range) {
                $count = count(array_filter($nilaiPerMahasiswa, function($nilai) use ($range) {
                    return $nilai >= $range['min'] && $nilai <= $range['max'];
                }));
                
                $percentage = count($nilaiPerMahasiswa) > 0 ? round(($count / count($nilaiPerMahasiswa)) * 100, 2) : 0;
                
                $distribution[$grade] = [
                    'count' => $count,
                    'percentage' => $percentage,
                    'label' => $range['label'],
                    'color' => $range['color']
                ];
            }

            // Buat histogram data dengan range yang lebih detail berdasarkan rata-rata per mahasiswa
            $histogramRanges = [
                ['min' => 0, 'max' => 19, 'label' => '0-19'],
                ['min' => 20, 'max' => 39, 'label' => '20-39'],
                ['min' => 40, 'max' => 59, 'label' => '40-59'],
                ['min' => 60, 'max' => 69, 'label' => '60-69'],
                ['min' => 70, 'max' => 79, 'label' => '70-79'],
                ['min' => 80, 'max' => 89, 'label' => '80-89'],
                ['min' => 90, 'max' => 100, 'label' => '90-100']
            ];

            foreach ($histogramRanges as $range) {
                $count = count(array_filter($nilaiPerMahasiswa, function($nilai) use ($range) {
                    return $nilai >= $range['min'] && $nilai <= $range['max'];
                }));
                
                $histogramData[] = [
                    'range' => $range['label'],
                    'count' => $count,
                    'percentage' => count($nilaiPerMahasiswa) > 0 ? round(($count / count($nilaiPerMahasiswa)) * 100, 2) : 0
                ];
            }

            // Hitung rata-rata nilai dari rata-rata per mahasiswa
            $averageNilai = count($nilaiPerMahasiswa) > 0 ? round(array_sum($nilaiPerMahasiswa) / count($nilaiPerMahasiswa), 2) : 0;

            // Hitung kompeten vs tidak kompeten berdasarkan rata-rata per mahasiswa
            $competentCount = count(array_filter($nilaiPerMahasiswa, function($nilai) {
                return $nilai >= 60;
            }));
            $notCompetentCount = count($nilaiPerMahasiswa) - $competentCount;
            
            $competentPercentage = count($nilaiPerMahasiswa) > 0 ? round(($competentCount / count($nilaiPerMahasiswa)) * 100, 2) : 0;
            $notCompetentPercentage = count($nilaiPerMahasiswa) > 0 ? round(($notCompetentCount / count($nilaiPerMahasiswa)) * 100, 2) : 0;

            // Ambil bobot komponen untuk CPMK ini
            $bobotKomponen = \App\Models\Bobot::where('cpmkId', $cpmk->id)
                ->where('tahunAjaranMatkulId', $tahunAjaranMatkul->id)
                ->with('komponen')
                ->get()
                ->map(function($bobot) {
                    return [
                        'komponen' => $bobot->komponen->namaKomponen,
                        'bobot' => $bobot->bobot,
                        'persentase' => $bobot->bobot . '%'
                    ];
                })
                ->toArray();

            // Hitung total bobot
            $totalBobot = collect($bobotKomponen)->sum('bobot');

            $data[] = [
                'cpmk' => $cpmk,
                'totalMahasiswa' => $totalMahasiswaMatkul,
                'mahasiswaDenganNilai' => $mahasiswaDenganNilai,
                'averageNilai' => $averageNilai,
                'distribution' => $distribution,
                'histogramData' => $histogramData,
                'competentCount' => $competentCount,
                'notCompetentCount' => $notCompetentCount,
                'competentPercentage' => $competentPercentage,
                'notCompetentPercentage' => $notCompetentPercentage,
                'nilaiList' => $nilaiList,
                'bobotKomponen' => $bobotKomponen,
                'totalBobot' => $totalBobot
            ];
        }

        return $data;
    }

    public function exportPdf($tahunAjaranMatkulId)
    {
        $tahunAjaranMatkul = \App\Models\TahunAjaranMatkul::with(['mataKuliah', 'tahunAjaran'])->findOrFail($tahunAjaranMatkulId);
        $cpmkData = $this->getCpmkData($tahunAjaranMatkul);

        $html = view('exports.cpmk-laporan-pdf', [
            'tahunAjaranMatkul' => $tahunAjaranMatkul,
            'cpmkData' => $cpmkData
        ])->render();

        // Configure DOMPDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isRemoteEnabled', true);

        // Create DOMPDF instance
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $fileName = 'laporan-cpmk-' . $tahunAjaranMatkul->mataKuliah->kodeMatkul . '_' . date('Y-m-d') . '.pdf';

        return response()->streamDownload(function() use ($dompdf) {
            echo $dompdf->output();
        }, $fileName, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function exportExcel($tahunAjaranMatkulId)
    {
        $tahunAjaranMatkul = \App\Models\TahunAjaranMatkul::with(['mataKuliah', 'tahunAjaran'])->findOrFail($tahunAjaranMatkulId);
        $cpmkData = $this->getCpmkData($tahunAjaranMatkul);

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CpmkLaporanExport($tahunAjaranMatkul, $cpmkData), 
            'laporan-cpmk-' . $tahunAjaranMatkul->mataKuliah->kodeMatkul . '_' . date('Y-m-d') . '.xlsx');
    }
}
