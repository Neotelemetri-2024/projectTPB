<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cpl;
use App\Models\Kurikulum;
use App\Models\Bobot;
use App\Models\Nilai;
use App\Models\InstitutionSetting;
use App\Services\CplAssessmentScope;
use App\Services\CplAssessmentCalculator;
use Dompdf\Dompdf;
use Dompdf\Options;

class CapaianController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        $tahunAjaranId = $request->input('tahun_ajaran_id');
        $kurikulumList = $this->studentKurikulumList($mahasiswa->id);
        $kurikulumId = $this->selectedKurikulumId($kurikulumList, $request->input('kurikulum_id'));
        $cplIdTerpilih = $request->input('cpl_id');

        $cplList = Cpl::orderBy('kodeCpl')->get();
        $cplData = $this->buildCplData($mahasiswa->id, $tahunAjaranId, $cplIdTerpilih, true, $kurikulumId);
        $akademik = $this->buildAcademicSummary($mahasiswa);

        $scope = app(CplAssessmentScope::class);
        $hasAssessedMatkul = $kurikulumId !== null && $scope->hasExplicitAssessment($kurikulumId);

        return view('mahasiswa.capaian', [
            'mahasiswa' => $mahasiswa,
            'cplData' => $cplData,
            'cplList' => $cplList,
            'cplIdTerpilih' => $cplIdTerpilih,
            'akademik' => $akademik,
            'institution' => InstitutionSetting::details(),
            'kurikulumList' => $kurikulumList,
            'kurikulumId' => $kurikulumId,
            'hasAssessedMatkul' => $hasAssessedMatkul,
        ]);
    }

    public function exportPDF(Request $request)
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        $kurikulumList = $this->studentKurikulumList($mahasiswa->id);
        $kurikulumId = $this->selectedKurikulumId($kurikulumList, $request->input('kurikulum_id'));

        $cplData = $this->buildCplData($mahasiswa->id, null, null, false, $kurikulumId);
        $akademik = $this->buildAcademicSummary($mahasiswa);
        $institution = InstitutionSetting::details();

        $kurikulumLabel = $kurikulumList->firstWhere('id', $kurikulumId)?->nama ?? '-';

        $logoPath = public_path('images/logo-unand.png');
        $logoBase64 = is_file($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        $html = view('exports.capaian-pdf', [
            'mahasiswa' => $mahasiswa,
            'cplData' => $cplData,
            'akademik' => $akademik,
            'institution' => $institution,
            'logoBase64' => $logoBase64,
            'tanggalCetak' => $this->formatTanggalIndonesia(now()),
            'kurikulumLabel' => $kurikulumLabel,
        ])->render();

        $options = new Options();
        $options->set('defaultFont', 'Times-Roman');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isFontSubsettingEnabled', true);

        $dompdf = new Dompdf($options);

        // Pakai Times New Roman dari Windows jika tersedia
        $timesFonts = [
            ['family' => 'Times New Roman', 'style' => 'normal', 'weight' => 'normal', 'path' => 'C:/Windows/Fonts/times.ttf'],
            ['family' => 'Times New Roman', 'style' => 'normal', 'weight' => 'bold', 'path' => 'C:/Windows/Fonts/timesbd.ttf'],
            ['family' => 'Times New Roman', 'style' => 'italic', 'weight' => 'normal', 'path' => 'C:/Windows/Fonts/timesi.ttf'],
            ['family' => 'Times New Roman', 'style' => 'italic', 'weight' => 'bold', 'path' => 'C:/Windows/Fonts/timesbi.ttf'],
        ];
        foreach ($timesFonts as $font) {
            if (is_file($font['path'])) {
                $dompdf->getFontMetrics()->registerFont(
                    [
                        'family' => $font['family'],
                        'style' => $font['style'],
                        'weight' => $font['weight'],
                    ],
                    $font['path']
                );
            }
        }

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $fileName = 'Surat_Keterangan_Capaian_Pembelajaran_'
            . str_replace(' ', '_', $mahasiswa->nama ?? 'Mahasiswa')
            . '_' . date('Y-m-d') . '.pdf';

        return response()->streamDownload(function () use ($dompdf) {
            echo $dompdf->output();
        }, $fileName, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    private function formatTanggalIndonesia($date): string
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $date->day . ' ' . $bulan[(int) $date->month] . ' ' . $date->year;
    }

    private function studentKurikulumList(int $mahasiswaId)
    {
        return Kurikulum::query()
            ->whereHas('mataKuliah.tahunAjaranMatkul.kelas.kelasMahasiswa', function ($query) use ($mahasiswaId) {
                $query->where('mahasiswaId', $mahasiswaId);
            })
            ->orderBy('kode')
            ->get(['id', 'kode', 'nama']);
    }

    private function selectedKurikulumId($kurikulumList, $requestedId): ?int
    {
        $selected = $kurikulumList->firstWhere('id', $requestedId) ?? $kurikulumList->first();

        return $selected?->id;
    }

    /**
     * Ringkasan akademik mahasiswa untuk surat keterangan.
     */
    private function buildAcademicSummary($mahasiswa): array
    {
        $mkDiambil = $mahasiswa->kelasMahasiswa()
            ->with('kelas.tahunAjaranMatkul.mataKuliah.kurikulumRef')
            ->get();

        $kurikulumNama = $mkDiambil
            ->map(fn ($km) => $km->kelas?->tahunAjaranMatkul?->mataKuliah?->kurikulumRef?->nama)
            ->filter()
            ->unique()
            ->values();

        $tamIds = $mkDiambil
            ->map(fn ($km) => $km->kelas?->tahunAjaranMatkul?->id)
            ->filter()
            ->unique()
            ->values();
        $bobotByTam = Bobot::query()->whereIn('tahunAjaranMatkulId', $tamIds)->get()->groupBy('tahunAjaranMatkulId');
        $nilaiByTam = Nilai::query()
            ->where('mahasiswaId', $mahasiswa->id)
            ->whereIn('tahunAjaranMatkulId', $tamIds)
            ->get()
            ->groupBy('tahunAjaranMatkulId')
            ->map(fn ($rows) => $rows->keyBy('bobotId'));

        $totalSks = 0;
        $totalNilaiBobot = 0;

        foreach ($mkDiambil as $km) {
            $nilaiAkhir = $km->getRawOriginal('totalNilai');
            $tam = $km->kelas?->tahunAjaranMatkul;
            $sks = $tam?->getSks() ?? 0;

            if ($nilaiAkhir === null && $tam) {
                $weightedTotal = 0;
                $weightTotal = 0;
                $nilaiLookup = $nilaiByTam->get($tam->id, collect());
                foreach ($bobotByTam->get($tam->id, collect()) as $item) {
                    $nilai = $nilaiLookup->get($item->id);
                    if ($nilai && $item->bobot > 0) {
                        $weightedTotal += $nilai->nilai * $item->bobot;
                        $weightTotal += $item->bobot;
                    }
                }
                $nilaiAkhir = $weightTotal > 0 ? round($weightedTotal / $weightTotal, 2) : null;
            }

            // Match transcript totals: courses without a recorded final grade
            // do not contribute to either the credit total or the GPA divisor.
            if ($nilaiAkhir === null) {
                continue;
            }

            $bobot = $this->nilaiToBobot($nilaiAkhir);

            $totalSks += $sks;
            $totalNilaiBobot += ($bobot * $sks);
        }

        $ipk = $totalSks > 0 ? round($totalNilaiBobot / $totalSks, 2) : null;

        return [
            'ipk' => $ipk,
            'total_sks' => $totalSks,
            'predikat' => $this->predikatFromIpk($ipk),
            'gelar' => config('institution.gelar', 'S.T'),
            'kurikulum' => $kurikulumNama->count() === 1
                ? $kurikulumNama->first()
                : ($kurikulumNama->count() > 1 ? 'Beragam Kurikulum' : 'Tidak diketahui'),
        ];
    }

    private function nilaiToBobot($nilaiAkhir): float
    {
        if ($nilaiAkhir === null) {
            return 0;
        }

        $nilaiAkhir = (float) $nilaiAkhir;
        $grade = match (true) {
            $nilaiAkhir >= 80 => 'A',
            $nilaiAkhir >= 75 => 'A-',
            $nilaiAkhir >= 70 => 'B+',
            $nilaiAkhir >= 65 => 'B',
            $nilaiAkhir >= 60 => 'B-',
            $nilaiAkhir >= 55 => 'C+',
            $nilaiAkhir >= 50 => 'C',
            $nilaiAkhir >= 40 => 'D',
            default => 'E',
        };

        return [
            'A' => 4.0, 'A-' => 3.7, 'B+' => 3.3, 'B' => 3.0,
            'B-' => 2.7, 'C+' => 2.3, 'C' => 2.0, 'D' => 1.0, 'E' => 0.0,
        ][$grade];
    }

    private function predikatFromIpk(?float $ipk): string
    {
        if ($ipk === null) {
            return '-';
        }
        if ($ipk >= 3.51) {
            return 'Dengan Pujian';
        }
        if ($ipk >= 3.01) {
            return 'Sangat Memuaskan';
        }
        if ($ipk >= 2.76) {
            return 'Memuaskan';
        }
        return '-';
    }

    /**
     * Build CPL/CPMK achievement data with batched queries (no per-CPMK N+1).
     * Hanya pasangan (CPL, matkul) yang ditandai asesmen (opsional satu kurikulum) yang dihitung.
     */
    private function buildCplData(int $mahasiswaId, $tahunAjaranId, $cplIdTerpilih, bool $trackMissing, $kurikulumId = null): array
    {
        $scope = app(CplAssessmentScope::class);
        $kurikulumId = $kurikulumId ? (int) $kurikulumId : null;

        $enrollments = \App\Models\KelasMahasiswa::query()
            ->where('mahasiswaId', $mahasiswaId)
            ->with('kelas.tahunAjaranMatkul.mataKuliah')
            ->get()
            ->filter(function ($enrollment) use ($tahunAjaranId) {
                $tam = $enrollment->kelas?->tahunAjaranMatkul;
                return $tam && (!$tahunAjaranId || (int) $tam->tahunAjaranId === (int) $tahunAjaranId);
            });

        $tamByMataKuliah = $enrollments
            ->map(fn ($enrollment) => $enrollment->kelas->tahunAjaranMatkul)
            ->filter()
            ->unique('id')
            ->groupBy('mataKuliahId');
        $tamIds = $tamByMataKuliah->flatten()->pluck('id')->unique()->values();

        $cplQuery = Cpl::with([
            'cpmk' => fn ($q) => $q->orderBy('kodeCpmk')->with(['children', 'cpmkMatKul.tahunAjaranMatkul.mataKuliah']),
        ])->orderBy('kodeCpl');

        if ($cplIdTerpilih) {
            $cplQuery->where('id', $cplIdTerpilih);
        }

        $cplList = $cplQuery->get();
        $assessedPairs = $scope->assessedPairs($kurikulumId);

        $bobotByTam = Bobot::query()
            ->whereIn('tahunAjaranMatkulId', $tamIds)
            ->get()
            ->groupBy('tahunAjaranMatkulId');

        $allBobotIds = $bobotByTam->flatten()->pluck('id')->unique()->values();

        $nilaiByBobot = Nilai::with(['bobot', 'tahunAjaranMatkul'])
            ->where('mahasiswaId', $mahasiswaId)
            ->whereIn('tahunAjaranMatkulId', $tamIds)
            ->when($allBobotIds->isNotEmpty(), fn ($q) => $q->whereIn('bobotId', $allBobotIds))
            ->get()
            ->keyBy('bobotId');

        $cplData = [];

        foreach ($cplList as $cpl) {
            $nilaiMinimal = (float) ($cpl->nilaiMinimal ?? 55);

            $cpmkData = [];
            $assessedCourses = [];
            $shownCpmkKeys = [];

            foreach ($cpl->cpmk->unique('id') as $cpmk) {
                // Detail dosen hanya menampilkan CPMK leaf pada kelas/TAM yang bersangkutan.
                if ($cpmk->children->isNotEmpty()) {
                    continue;
                }

                $assessedLinks = $cpmk->cpmkMatKul->filter(function ($matkulRel) use ($cpl, $assessedPairs, $tamIds) {
                    $tam = $matkulRel->tahunAjaranMatkul;
                    return $tam
                        && $tamIds->contains($tam->id)
                        && isset($assessedPairs[$cpl->id][(int) $tam->mataKuliahId]);
                })->unique('tahunAjaranMatkulId');

                if ($assessedLinks->isEmpty()) {
                    continue;
                }

                foreach ($assessedLinks as $matkulRel) {
                    $tam = $matkulRel->tahunAjaranMatkul;
                    $detailKey = $tam->id . ':' . $cpmk->id;
                    if (isset($shownCpmkKeys[$detailKey])) {
                        continue;
                    }
                    $shownCpmkKeys[$detailKey] = true;
                    $matkul = $tam->mataKuliah;
                    $nilaiRecords = $nilaiByBobot->values()->filter(fn ($nilai) =>
                        (int) $nilai->tahunAjaranMatkulId === (int) $tam->id
                        && (int) $nilai->cpmkId === (int) $cpmk->id
                        && $nilai->nilai !== null
                        && $nilai->bobot
                        && (float) $nilai->bobot->bobot > 0
                    );
                    $bobotCpmkTotal = $nilaiRecords->sum(fn ($nilai) => (float) $nilai->bobot->bobot);
                    $nilaiCpmkTotal = $nilaiRecords->sum(fn ($nilai) => $nilai->nilai * (float) $nilai->bobot->bobot);
                    $nilaiRataRata = $bobotCpmkTotal > 0 ? $nilaiCpmkTotal / $bobotCpmkTotal : null;
                    $kontribusiCpmk = $bobotCpmkTotal > 0 ? round($nilaiCpmkTotal / 100, 2) : null;

                    $cpmkData[] = [
                        'id' => $cpmk->id,
                        'kode' => $cpmk->kodeCpmk,
                        'deskripsi' => $cpmk->deskripsi,
                        'mata_kuliah_id' => $tam->mataKuliahId,
                        'kode_mk' => $matkul ? $matkul->kodeMatkul : '-',
                        'nama_mk' => $matkul ? $matkul->namaMatkul : '-',
                        'nilai' => $kontribusiCpmk ?? '-',
                        'total' => $kontribusiCpmk ?? '-',
                        'status_capaian' => $nilaiRataRata === null ? '-' : ($nilaiRataRata >= $nilaiMinimal ? 'Tercapai' : 'Belum Tercapai'),
                        'diases' => true,
                    ];
                }
            }

            // Satu kode CPMK per mata kuliah cukup ditampilkan sekali, termasuk
            // ketika impor lama menyimpan beberapa TAM/tautan untuk matkul yang sama.
            $cpmkData = collect($cpmkData)
                ->groupBy(fn ($row) => $row['mata_kuliah_id'] . ':' . $row['kode'])
                ->map(fn ($rows) => $rows->first(fn ($row) => is_numeric($row['nilai'])) ?? $rows->first())
                ->values()
                ->all();

            foreach (($assessedPairs[$cpl->id] ?? []) as $mkId => $_) {
                $tam = $tamByMataKuliah->get((int) $mkId, collect())->first();
                if (!$tam) {
                    continue;
                }

                $bobot = $bobotByTam->get($tam->id, collect())
                    ->filter(fn ($item) => (float) $item->bobot > 0)
                    ->values();
                $assessedCourses[] = $bobot->map(fn ($item) => [
                    'weight' => (float) $item->bobot,
                    'score' => $nilaiByBobot->get($item->id)?->nilai,
                ])->all();
            }

            $assessmentResult = app(CplAssessmentCalculator::class)->calculate($assessedCourses, $nilaiMinimal);
            $total_cpl = $assessmentResult['score'];
            $hasIncomplete = !$assessmentResult['complete'];

            $cplData[] = [
                'id' => $cpl->id,
                'kode' => $cpl->kodeCpl,
                'deskripsi' => $cpl->deskripsi,
                'cpmk' => $cpmkData,
                'total_cpl' => $total_cpl,
                'nilai_minimal' => $nilaiMinimal,
                'nilai_surat' => $total_cpl,
                'status_cpl' => $assessmentResult['status'],
                'missing_cpmk_count' => $assessmentResult['missing_course_count'],
                'nilai_lengkap' => !$hasIncomplete,
            ];
        }

        return $cplData;
    }
}
