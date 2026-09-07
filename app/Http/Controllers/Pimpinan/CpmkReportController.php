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

        // Semua mata kuliah (tahun ajaran terpilih) — paginated
        $mataKuliahList = TahunAjaranMatkul::where('tahunAjaranId', $selectedTahunAjaranId)
            ->with([
                'mataKuliah',
                'cpmkMatKul.cpmk',
                'kelas' => fn ($q) => $q->withCount('kelasMahasiswa'),
            ])
            ->orderBy('id')
            ->paginate(12)
            ->withQueryString();

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
            ->with(['mataKuliah', 'kelas.kelasMahasiswa.mahasiswa', 'cpmkMatKul.cpmk'])
            ->first();

        if (!$tahunAjaranMatkul) {
            return redirect()->back()->with('error', 'Mata kuliah tidak ditemukan.');
        }

        // Ambil data CPMK dan nilai
        $cpmkData = $this->getCpmkData($tahunAjaranMatkul);
        $chartPayload = collect($cpmkData)->map(function ($row) {
            return [
                'kode' => $row['cpmk']->kodeCpmk ?? '',
                'distribution' => $row['distribution'] ?? [],
                'histogram_data' => $row['histogram_data'] ?? [],
            ];
        })->values()->all();

        return view('pimpinan.cpmk-report.show', compact(
            'tahunAjaranMatkul',
            'cpmkData',
            'chartPayload'
        ));
    }

    private function getCpmkData($tahunAjaranMatkul)
    {
        $cpmks = Cpmk::whereHas('cpmkMatKul', function ($q) use ($tahunAjaranMatkul) {
            $q->where('tahunAjaranMatkulId', $tahunAjaranMatkul->id);
        })->get();

        $mahasiswaIds = \App\Models\KelasMahasiswa::whereHas('kelas', function ($q) use ($tahunAjaranMatkul) {
            $q->where('tahunAjaranMatkulId', $tahunAjaranMatkul->id);
        })->pluck('mahasiswaId')->unique()->values()->all();

        $totalMahasiswaMatkul = count($mahasiswaIds);
        $cpmkIds = $cpmks->pluck('id');

        $nilaiByCpmk = \App\Models\Nilai::query()
            ->where('tahunAjaranMatkulId', $tahunAjaranMatkul->id)
            ->when(!empty($mahasiswaIds), fn ($q) => $q->whereIn('mahasiswaId', $mahasiswaIds))
            ->when($cpmkIds->isNotEmpty(), fn ($q) => $q->whereIn('cpmkId', $cpmkIds))
            ->get()
            ->groupBy('cpmkId');

        $bobotByCpmk = \App\Models\Bobot::with('komponen')
            ->where('tahunAjaranMatkulId', $tahunAjaranMatkul->id)
            ->when($cpmkIds->isNotEmpty(), fn ($q) => $q->whereIn('cpmkId', $cpmkIds))
            ->get()
            ->groupBy('cpmkId');

        $nilaiRanges = [
            'U' => ['min' => 0, 'max' => 59, 'label' => 'Uncompetence', 'color' => '#3B82F6'],
            'C' => ['min' => 60, 'max' => 74, 'label' => 'Competence', 'color' => '#EF4444'],
            'E' => ['min' => 75, 'max' => 89, 'label' => 'Excellent', 'color' => '#10B981'],
            'X' => ['min' => 90, 'max' => 100, 'label' => 'Extraordinary', 'color' => '#8B5CF6']
        ];

        $histogramRanges = [
            ['min' => 0, 'max' => 19, 'label' => '0-19'],
            ['min' => 20, 'max' => 39, 'label' => '20-39'],
            ['min' => 40, 'max' => 59, 'label' => '40-59'],
            ['min' => 60, 'max' => 69, 'label' => '60-69'],
            ['min' => 70, 'max' => 79, 'label' => '70-79'],
            ['min' => 80, 'max' => 89, 'label' => '80-89'],
            ['min' => 90, 'max' => 100, 'label' => '90-100']
        ];

        $data = [];

        foreach ($cpmks as $cpmk) {
            $cpmkNilai = $nilaiByCpmk->get($cpmk->id, collect());
            if ($cpmkNilai->isEmpty()) {
                continue;
            }

            $nilaiPerMahasiswa = $cpmkNilai
                ->groupBy('mahasiswaId')
                ->map(fn ($rows) => $rows->avg('nilai'))
                ->values()
                ->all();

            $mahasiswaDenganNilai = count($nilaiPerMahasiswa);
            if ($mahasiswaDenganNilai === 0) {
                continue;
            }

            $nilaiList = $cpmkNilai->pluck('nilai')->filter()->values()->all();

            $distribution = [];
            foreach ($nilaiRanges as $grade => $range) {
                $count = count(array_filter($nilaiPerMahasiswa, function ($nilai) use ($range) {
                    return $nilai >= $range['min'] && $nilai <= $range['max'];
                }));
                $distribution[$grade] = [
                    'count' => $count,
                    'percentage' => round(($count / $mahasiswaDenganNilai) * 100, 2),
                    'label' => $range['label'],
                    'color' => $range['color']
                ];
            }

            $histogramData = [];
            foreach ($histogramRanges as $range) {
                $count = count(array_filter($nilaiPerMahasiswa, function ($nilai) use ($range) {
                    return $nilai >= $range['min'] && $nilai <= $range['max'];
                }));
                $histogramData[] = [
                    'range' => $range['label'],
                    'count' => $count,
                    'percentage' => round(($count / $mahasiswaDenganNilai) * 100, 2)
                ];
            }

            $averageNilai = round(array_sum($nilaiPerMahasiswa) / $mahasiswaDenganNilai, 2);
            $competentCount = count(array_filter($nilaiPerMahasiswa, fn ($nilai) => $nilai >= 60));
            $notCompetentCount = $mahasiswaDenganNilai - $competentCount;

            $bobotKomponen = ($bobotByCpmk->get($cpmk->id) ?? collect())
                ->map(function ($bobot) {
                    return [
                        'komponen' => $bobot->komponen->nama ?? '-',
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
                'competent_percentage' => round(($competentCount / $mahasiswaDenganNilai) * 100, 2),
                'not_competent_percentage' => round(($notCompetentCount / $mahasiswaDenganNilai) * 100, 2),
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
            ->with(['mataKuliah', 'kelas.kelasMahasiswa.mahasiswa', 'cpmkMatKul.cpmk'])
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
