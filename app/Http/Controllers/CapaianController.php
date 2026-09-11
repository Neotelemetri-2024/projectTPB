<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cpl;
use App\Models\Bobot;
use App\Models\Nilai;
use App\Services\CplAssessmentScope;
use Dompdf\Dompdf;
use Dompdf\Options;

class CapaianController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        $tahunAjaranId = $request->input('tahun_ajaran_id');
        $kurikulumId = $request->input('kurikulum_id');
        $cplIdTerpilih = $request->input('cpl_id');

        $cplList = Cpl::orderBy('kodeCpl')->get();
        $cplData = $this->buildCplData($mahasiswa->id, $tahunAjaranId, $cplIdTerpilih, true, $kurikulumId);
        $akademik = $this->buildAcademicSummary($mahasiswa);

        $scope = app(CplAssessmentScope::class);
        $kurikulumList = $scope->kurikulumList();
        $hasAssessedMatkul = $scope->hasExplicitAssessment($kurikulumId ? (int) $kurikulumId : null);

        return view('mahasiswa.capaian', [
            'mahasiswa' => $mahasiswa,
            'cplData' => $cplData,
            'cplList' => $cplList,
            'cplIdTerpilih' => $cplIdTerpilih,
            'akademik' => $akademik,
            'institution' => config('institution'),
            'kurikulumList' => $kurikulumList,
            'kurikulumId' => $kurikulumId,
            'hasAssessedMatkul' => $hasAssessedMatkul,
        ]);
    }

    public function exportPDF(Request $request)
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        $kurikulumId = $request->input('kurikulum_id');

        $cplData = $this->buildCplData($mahasiswa->id, null, null, false, $kurikulumId);
        $akademik = $this->buildAcademicSummary($mahasiswa);
        $institution = config('institution');

        $kurikulumLabel = 'Semua Kurikulum';
        if ($kurikulumId) {
            $kurikulumLabel = \App\Models\Kurikulum::find($kurikulumId)?->nama ?? $kurikulumLabel;
        }

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

    /**
     * Ringkasan akademik mahasiswa untuk surat keterangan.
     */
    private function buildAcademicSummary($mahasiswa): array
    {
        $mkDiambil = $mahasiswa->kelasMahasiswa()
            ->with('kelas.tahunAjaranMatkul.mataKuliah')
            ->get();

        $totalSks = 0;
        $totalNilaiBobot = 0;
        $sksLulus = 0;

        foreach ($mkDiambil as $km) {
            $nilaiAkhir = $km->getRawOriginal('totalNilai');
            $sks = $km->kelas->tahunAjaranMatkul->getSks() ?? 0;
            $bobot = $this->nilaiToBobot($nilaiAkhir);

            $totalSks += $sks;
            $totalNilaiBobot += ($bobot * $sks);

            if ($nilaiAkhir !== null && (float) $nilaiAkhir >= 40) {
                $sksLulus += $sks;
            }
        }

        $ipk = $totalSks > 0 ? round($totalNilaiBobot / $totalSks, 2) : null;

        return [
            'ipk' => $ipk,
            'total_sks' => $sksLulus > 0 ? $sksLulus : $totalSks,
            'predikat' => $this->predikatFromIpk($ipk),
            'gelar' => config('institution.gelar', 'S.T'),
        ];
    }

    private function nilaiToBobot($nilaiAkhir): float
    {
        if ($nilaiAkhir === null) {
            return 0;
        }

        $nilaiAkhir = (float) $nilaiAkhir;
        if ($nilaiAkhir >= 80) return 4;
        if ($nilaiAkhir >= 75) return 3.75;
        if ($nilaiAkhir >= 70) return 3.5;
        if ($nilaiAkhir >= 65) return 3;
        if ($nilaiAkhir >= 60) return 2.75;
        if ($nilaiAkhir >= 55) return 2.5;
        if ($nilaiAkhir >= 50) return 2;
        if ($nilaiAkhir >= 40) return 1;
        return 0;
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

        $cplQuery = Cpl::with([
            'cpmk' => fn ($q) => $q->orderBy('kodeCpmk')->with(['cpmkMatKul.mataKuliah', 'cpmkMatKul.tahunAjaranMatkul']),
        ])->orderBy('kodeCpl');

        if ($cplIdTerpilih) {
            $cplQuery->where('id', $cplIdTerpilih);
        }

        $cplList = $cplQuery->get();
        $allCpmkIds = $cplList->flatMap(fn ($cpl) => $cpl->cpmk->pluck('id'))->unique()->values();
        $assessedPairs = $scope->assessedPairs($kurikulumId);

        $bobotByCpmk = Bobot::query()
            ->when($tahunAjaranId, fn ($q) => $q->where('tahunAjaranId', $tahunAjaranId))
            ->when($allCpmkIds->isNotEmpty(), fn ($q) => $q->whereIn('cpmkId', $allCpmkIds))
            ->get()
            ->groupBy('cpmkId');

        $allBobotIds = $bobotByCpmk->flatten()->pluck('id')->unique()->values();

        $nilaiByBobot = Nilai::with(['bobot', 'tahunAjaranMatkul'])
            ->where('mahasiswaId', $mahasiswaId)
            ->when($allBobotIds->isNotEmpty(), fn ($q) => $q->whereIn('bobotId', $allBobotIds))
            ->get()
            ->groupBy('bobotId');

        $cplData = [];

        foreach ($cplList as $cpl) {
            $nilaiMinimal = (float) ($cpl->nilaiMinimal ?? 55);

            $cpmkData = [];
            $totalCpmkArr = [];
            $missingCpmkCount = 0;

            foreach ($cpl->cpmk as $cpmk) {
                $bobotIds = ($bobotByCpmk->get($cpmk->id) ?? collect())->pluck('id');
                $assessedLinks = $cpmk->cpmkMatKul->filter(function ($matkulRel) use ($cpl, $assessedPairs) {
                    $mkId = $matkulRel->mataKuliah->id
                        ?? $matkulRel->tahunAjaranMatkul->mataKuliahId
                        ?? null;
                    return $mkId && isset($assessedPairs[$cpl->id][(int) $mkId]);
                });

                if ($assessedLinks->isEmpty()) {
                    continue;
                }

                $cpmkHasAnyScore = false;

                foreach ($assessedLinks as $matkulRel) {
                    $matkul = $matkulRel->mataKuliah;
                    $mkId = (int) ($matkul->id ?? $matkulRel->tahunAjaranMatkul->mataKuliahId ?? 0);

                    $nilaiCpmkTotal = 0;
                    $bobotCpmkTotal = 0;

                    foreach ($bobotIds as $bobotId) {
                        foreach ($nilaiByBobot->get($bobotId, collect()) as $nilai) {
                            $nilaiMkId = (int) ($nilai->tahunAjaranMatkul->mataKuliahId ?? 0);
                            if ($nilaiMkId !== $mkId) {
                                continue;
                            }
                            if ($nilai->bobot && $nilai->bobot->bobot > 0) {
                                $nilaiCpmkTotal += ($nilai->nilai * $nilai->bobot->bobot);
                                $bobotCpmkTotal += $nilai->bobot->bobot;
                            }
                        }
                    }

                    $nilaiCpmk = $bobotCpmkTotal > 0 ? $nilaiCpmkTotal / $bobotCpmkTotal : null;
                    if ($nilaiCpmk !== null) {
                        $cpmkHasAnyScore = true;
                    }

                    $total = $nilaiCpmk !== null ? round($nilaiCpmk, 2) : null;
                    if ($total !== null && is_numeric($total)) {
                        $totalCpmkArr[] = $total;
                    }

                    $status_capaian = ($total !== null && $total >= $nilaiMinimal) ? 'Tercapai' : 'Belum Tercapai';
                    $cpmkData[] = [
                        'id' => $cpmk->id,
                        'kode' => $cpmk->kodeCpmk,
                        'deskripsi' => $cpmk->deskripsi,
                        'kode_mk' => $matkul ? $matkul->kodeMatkul : '-',
                        'nama_mk' => $matkul ? $matkul->namaMatkul : '-',
                        'nilai' => $nilaiCpmk !== null ? round($nilaiCpmk, 2) : '-',
                        'total' => $total !== null ? $total : '-',
                        'status_capaian' => $total !== null ? $status_capaian : '-',
                        'diases' => true,
                    ];
                }

                if (!$cpmkHasAnyScore) {
                    $missingCpmkCount++;
                }
            }

            $total_cpl = count($totalCpmkArr) > 0 ? max($totalCpmkArr) : '-';

            // Status mengikuti nilai CPL yang ditampilkan (CPMK pendukung tertinggi).
            // Kelengkapan nilai dilacak terpisah lewat nilai_lengkap / missing_cpmk_count.
            $status_cpl = ($total_cpl !== '-' && is_numeric($total_cpl) && (float) $total_cpl >= $nilaiMinimal)
                ? 'Tercapai'
                : 'Belum Tercapai';

            $cplData[] = [
                'id' => $cpl->id,
                'kode' => $cpl->kodeCpl,
                'deskripsi' => $cpl->deskripsi,
                'cpmk' => $cpmkData,
                'total_cpl' => $total_cpl,
                'nilai_minimal' => $nilaiMinimal,
                'nilai_surat' => is_numeric($total_cpl) ? (int) round($total_cpl) : '-',
                'status_cpl' => $status_cpl,
                'missing_cpmk_count' => $missingCpmkCount,
                'nilai_lengkap' => $missingCpmkCount === 0,
            ];
        }

        return $cplData;
    }
}
