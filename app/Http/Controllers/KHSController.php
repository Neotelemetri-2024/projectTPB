<?php

namespace App\Http\Controllers;

use App\Models\Bobot;
use App\Models\Nilai;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class KHSController extends Controller
{
    /**
     * Mahasiswa Transcript
     */
    public function index(Request $request)
    {
        $mahasiswa = auth()->user()->mahasiswa;
        $matkulDiambil = [];
        $periodes = [];
        $periodeTerpilih = null;

        if ($mahasiswa) {
            $kelasMahasiswa = $this->loadEnrollments($mahasiswa);
            $periodes = $this->buildPeriods($kelasMahasiswa);
            $periodeTerpilih = $request->input('periode_id') ?? ($periodes[1]['id'] ?? 'all');

            $filteredKelas = $periodeTerpilih === 'all'
                ? $kelasMahasiswa
                : $kelasMahasiswa->filter(function ($km) use ($periodeTerpilih) {
                    return $km->tahunAjaranMatkul->tahunAjaran->id == $periodeTerpilih;
                });

            $matkulDiambil = $this->buildTranscriptRows($mahasiswa->id, $filteredKelas, true);
        }

        return view('mahasiswa.transkrip', compact('matkulDiambil', 'periodes', 'periodeTerpilih', 'mahasiswa'));
    }

    public function exportPDF()
    {
        $mahasiswa = auth()->user()->mahasiswa;

        if (!$mahasiswa) {
            return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan');
        }

        $matkulDiambil = $this->buildTranscriptRows(
            $mahasiswa->id,
            $this->loadEnrollments($mahasiswa),
            false
        );

        $totalBobot = 0;
        $totalSks = 0;
        foreach ($matkulDiambil as $row) {
            if ($row['grade'] && $row['grade'] !== '-') {
                $totalBobot += $this->gradePoint($row['grade']) * $row['sks'];
                $totalSks += $row['sks'];
            }
        }
        $ipk = $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0.00;

        $html = view('exports.transkrip-pdf', compact(
            'mahasiswa',
            'matkulDiambil',
            'totalSks',
            'ipk'
        ))->render();

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $fileName = 'Transkrip_' . str_replace(' ', '_', $mahasiswa->nama ?? 'Mahasiswa') . '_' . date('Y-m-d') . '.pdf';

        return response()->streamDownload(function () use ($dompdf) {
            echo $dompdf->output();
        }, $fileName, ['Content-Type' => 'application/pdf']);
    }

    private function loadEnrollments($mahasiswa): Collection
    {
        return $mahasiswa->kelasMahasiswa()
            ->with('tahunAjaranMatkul.tahunAjaran', 'tahunAjaranMatkul.mataKuliah')
            ->get();
    }

    private function buildPeriods(Collection $kelasMahasiswa): array
    {
        $periodes = $kelasMahasiswa->map(function ($km) {
            $ta = $km->tahunAjaranMatkul->tahunAjaran;

            return [
                'id' => $ta->id,
                'tahun' => $ta->tahun,
                'periode' => $ta->periode,
                'label' => $ta->tahun . ' - ' . $ta->periode,
            ];
        })->unique('id')->sortByDesc('tahun')->sortByDesc('periode')->values()->all();

        array_unshift($periodes, [
            'id' => 'all',
            'tahun' => '',
            'periode' => '',
            'label' => 'Semua Semester',
        ]);

        return $periodes;
    }

    private function buildTranscriptRows(int $mahasiswaId, Collection $kelasMahasiswa, bool $includeDetails): array
    {
        if ($kelasMahasiswa->isEmpty()) {
            return [];
        }

        $tamIds = $kelasMahasiswa->pluck('tahunAjaranMatkul.id')->filter()->unique()->values();
        $bobotByTam = Bobot::with(['komponen', 'cpmk'])
            ->whereIn('tahunAjaranMatkulId', $tamIds)
            ->get()
            ->groupBy('tahunAjaranMatkulId');
        $nilaiByTamAndBobot = Nilai::where('mahasiswaId', $mahasiswaId)
            ->whereIn('tahunAjaranMatkulId', $tamIds)
            ->get()
            ->groupBy('tahunAjaranMatkulId')
            ->map(fn ($nilai) => $nilai->keyBy('bobotId'));

        $rows = [];
        foreach ($kelasMahasiswa->values() as $index => $km) {
            $tam = $km->tahunAjaranMatkul;
            $mataKuliah = $tam->mataKuliah;
            $bobot = $bobotByTam->get($tam->id, collect());
            $nilaiByBobot = $nilaiByTamAndBobot->get($tam->id, collect());
            $nilaiAkhir = $km->getRawOriginal('totalNilai');

            if ($nilaiAkhir === null) {
                $weightedTotal = 0;
                $weightTotal = 0;
                foreach ($bobot as $item) {
                    $nilai = $nilaiByBobot->get($item->id);
                    if ($nilai && $item->bobot > 0) {
                        $weightedTotal += $nilai->nilai * $item->bobot;
                        $weightTotal += $item->bobot;
                    }
                }
                $nilaiAkhir = $weightTotal > 0 ? round($weightedTotal / $weightTotal, 2) : null;
            }

            $grade = $this->calculateGrade($nilaiAkhir);
            $row = [
                'no' => $index + 1,
                'kode' => $mataKuliah->kodeMatkul ?? '-',
                'nama' => $mataKuliah->namaMatkul ?? '-',
                'sks' => $tam->getSks() ?? '-',
                'semester' => $tam->tahunAjaran->tahun . ' - ' . $tam->tahunAjaran->periode,
                'nilai_akhir' => $nilaiAkhir,
                'grade' => $grade,
                'kelas_mahasiswa_id' => $km->id,
                'nilai_mutu' => $this->calculateNilaiMutu($nilaiAkhir),
            ];

            if ($includeDetails) {
                $row['komponen_nilai'] = $this->buildComponentDetails($bobot, $nilaiByBobot);
                $row['cpmk_nilai'] = $this->buildCpmkDetails($bobot, $nilaiByBobot);
            }

            $rows[] = $row;
        }

        return $rows;
    }

    private function buildComponentDetails(Collection $bobot, Collection $nilaiByBobot): array
    {
        return $bobot->groupBy('komponenId')->values()->map(function ($items, $index) use ($nilaiByBobot) {
            return [
                'no' => $index + 1,
                'nama' => optional($items->first()->komponen)->nama ?? '-',
                'nilai' => $this->weightedAverage($items, $nilaiByBobot),
            ];
        })->all();
    }

    private function buildCpmkDetails(Collection $bobot, Collection $nilaiByBobot): array
    {
        return $bobot->groupBy('cpmkId')->values()->map(function ($items, $index) use ($nilaiByBobot) {
            return [
                'no' => $index + 1,
                'kode' => optional($items->first()->cpmk)->kodeCpmk ?? '-',
                'bobot' => $items->sum('bobot'),
                'nilai' => $this->weightedAverage($items, $nilaiByBobot),
            ];
        })->all();
    }

    private function weightedAverage(Collection $bobot, Collection $nilaiByBobot)
    {
        $weightedTotal = 0;
        $weightTotal = 0;
        foreach ($bobot as $item) {
            $nilai = $nilaiByBobot->get($item->id);
            if ($nilai && $item->bobot > 0) {
                $weightedTotal += $nilai->nilai * $item->bobot;
                $weightTotal += $item->bobot;
            }
        }

        return $weightTotal > 0 ? round($weightedTotal / $weightTotal, 2) : '-';
    }

    private function calculateGrade($nilai): ?string
    {
        if ($nilai === null) return null;
        if ($nilai >= 80) return 'A';
        if ($nilai >= 75) return 'A-';
        if ($nilai >= 70) return 'B+';
        if ($nilai >= 65) return 'B';
        if ($nilai >= 60) return 'B-';
        if ($nilai >= 55) return 'C+';
        if ($nilai >= 50) return 'C';
        if ($nilai >= 40) return 'D';

        return 'E';
    }

    private function calculateNilaiMutu($nilai): ?string
    {
        if ($nilai === null) return null;
        if ($nilai < 60) return 'U';
        if ($nilai < 75) return 'C';
        if ($nilai < 90) return 'E';

        return 'X';
    }

    private function gradePoint(string $grade): float
    {
        return [
            'A' => 4.0, 'A-' => 3.7, 'B+' => 3.3, 'B' => 3.0,
            'B-' => 2.7, 'C+' => 2.3, 'C' => 2.0, 'C-' => 1.7,
            'D' => 1.0, 'E' => 0.0,
        ][$grade] ?? 0.0;
    }
}
