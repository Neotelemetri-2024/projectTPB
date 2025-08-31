<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cpmk;
use App\Models\Cpl;
use App\Models\TahunAjaran;
use App\Models\TahunAjaranMatkul;
use App\Models\MataKuliah;
use Dompdf\Dompdf;
use Dompdf\Options;

class CpmkReportController extends Controller
{
    public function index(Request $request)
    {
        // List tahun ajaran untuk filter
        $tahunAjaranList = TahunAjaran::orderBy('tahun', 'desc')->orderBy('periode', 'desc')->get();
        $selectedTahunAjaranId = $request->get('tahun_ajaran_id');
        if (!$selectedTahunAjaranId) {
            $selectedTahunAjaranId = $tahunAjaranList->first() ? $tahunAjaranList->first()->id : null;
        }

        // Semua mata kuliah (tahun ajaran terpilih) - pimpinan bisa lihat semua
        $mataKuliahList = TahunAjaranMatkul::where('tahunAjaranId', $selectedTahunAjaranId)
            ->with(['mataKuliah', 'kelas.kelasMahasiswa.mahasiswa', 'cpmkMatKul.cpmk.nilai'])
            ->get();

        return view('pimpinan.cpmk-report.index', compact(
            'mataKuliahList',
            'tahunAjaranList',
            'selectedTahunAjaranId'
        ));
    }

    public function show(Request $request, $tahunAjaranMatkulId)
    {
        // Pimpinan bisa lihat semua mata kuliah
        $tahunAjaranMatkul = TahunAjaranMatkul::where('id', $tahunAjaranMatkulId)
            ->with(['mataKuliah', 'kelas.kelasMahasiswa.mahasiswa', 'cpmkMatKul.cpmk.nilai'])
            ->first();

        if (!$tahunAjaranMatkul) {
            return redirect()->back()->with('error', 'Mata kuliah tidak ditemukan.');
        }

        // Ambil data CPMK dan nilai
        $cpmkData = $this->getCpmkData($tahunAjaranMatkul);

        return view('pimpinan.cpmk-report.show', compact(
            'tahunAjaranMatkul',
            'cpmkData'
        ));
    }

    private function getCpmkData($tahunAjaranMatkul)
    {
        // Ambil semua CPMK yang terkait dengan mata kuliah ini
        $cpmks = Cpmk::whereHas('cpmkMatKul', function($q) use ($tahunAjaranMatkul) {
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

            // Cek semua nilai untuk CPMK ini (tanpa filter mahasiswa)
            $allNilai = \App\Models\Nilai::where('cpmkId', $cpmk->id)
                ->where('tahunAjaranMatkulId', $tahunAjaranMatkul->id)
                ->get();

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
                    ];
                });

            $data[] = [
                'cpmk' => $cpmk,
                'total_mahasiswa' => $totalMahasiswaMatkul,
                'mahasiswa_dengan_nilai' => $mahasiswaDenganNilai,
                'nilai_list' => $nilaiList,
                'nilai_per_mahasiswa' => $nilaiPerMahasiswa,
                'average_nilai' => $averageNilai,
                'distribution' => $distribution,
                'histogram_data' => $histogramData,
                'competent_count' => $competentCount,
                'not_competent_count' => $notCompetentCount,
                'competent_percentage' => $competentPercentage,
                'not_competent_percentage' => $notCompetentPercentage,
                'bobot_komponen' => $bobotKomponen,
                'nilai_ranges' => $nilaiRanges
            ];
        }

        return $data;
    }

    public function exportPdf(Request $request, $tahunAjaranMatkulId)
    {
        // Pimpinan bisa lihat semua mata kuliah
        $tahunAjaranMatkul = TahunAjaranMatkul::where('id', $tahunAjaranMatkulId)
            ->with(['mataKuliah', 'kelas.kelasMahasiswa.mahasiswa', 'cpmkMatKul.cpmk.nilai'])
            ->first();

        if (!$tahunAjaranMatkul) {
            return redirect()->back()->with('error', 'Mata kuliah tidak ditemukan.');
        }

        // Ambil data CPMK dan nilai
        $cpmkData = $this->getCpmkData($tahunAjaranMatkul);

        // Generate PDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);
        $html = view('pimpinan.cpmk-report.pdf', compact('tahunAjaranMatkul', 'cpmkData'))->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'Laporan_CPMK_' . $tahunAjaranMatkul->mataKuliah->kodeMatkul . '_' . date('Y-m-d_H-i-s') . '.pdf';

        return $dompdf->stream($filename);
    }
}
