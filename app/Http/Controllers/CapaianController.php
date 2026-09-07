<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cpl;
use App\Models\Bobot;
use App\Models\Nilai;
use Dompdf\Dompdf;
use Dompdf\Options;

class CapaianController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        $tahunAjaranId = $request->input('tahun_ajaran_id');
        $cplIdTerpilih = $request->input('cpl_id');

        $cplList = Cpl::orderBy('kodeCpl')->get();
        $cplData = $this->buildCplData($mahasiswa->id, $tahunAjaranId, $cplIdTerpilih, true);
        $akademik = $this->buildAcademicSummary($mahasiswa);

        return view('mahasiswa.capaian', [
            'mahasiswa' => $mahasiswa,
            'cplData' => $cplData,
            'cplList' => $cplList,
            'cplIdTerpilih' => $cplIdTerpilih,
            'akademik' => $akademik,
            'institution' => config('institution'),
        ]);
    }

    public function exportPDF(Request $request)
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;

        // Surat keterangan selalu menampilkan seluruh CPL (tanpa filter)
        $cplData = $this->buildCplData($mahasiswa->id, null, null, false);
        $akademik = $this->buildAcademicSummary($mahasiswa);
        $institution = config('institution');

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
        ])->render();

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
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
            'gelar' => config('institution.gelar', 'S.TP'),
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
     */
    private function buildCplData(int $mahasiswaId, $tahunAjaranId, $cplIdTerpilih, bool $trackMissing): array
    {
        $cplQuery = Cpl::with([
            'cpmk' => fn ($q) => $q->orderBy('kodeCpmk')->with(['cpmkMatKul.mataKuliah']),
        ])->orderBy('kodeCpl');

        if ($cplIdTerpilih) {
            $cplQuery->where('id', $cplIdTerpilih);
        }

        $cplList = $cplQuery->get();
        $allCpmkIds = $cplList->flatMap(fn ($cpl) => $cpl->cpmk->pluck('id'))->unique()->values();

        $bobotByCpmk = Bobot::query()
            ->when($tahunAjaranId, fn ($q) => $q->where('tahunAjaranId', $tahunAjaranId))
            ->when($allCpmkIds->isNotEmpty(), fn ($q) => $q->whereIn('cpmkId', $allCpmkIds))
            ->get()
            ->groupBy('cpmkId');

        $allBobotIds = $bobotByCpmk->flatten()->pluck('id')->unique()->values();

        $nilaiByBobot = Nilai::with('bobot')
            ->where('mahasiswaId', $mahasiswaId)
            ->when($allBobotIds->isNotEmpty(), fn ($q) => $q->whereIn('bobotId', $allBobotIds))
            ->get()
            ->groupBy('bobotId');

        $cplData = [];

        foreach ($cplList as $cpl) {
            $cpmkData = [];
            $totalCpmkArr = [];
            $hasMissingCpmk = false;
            $missingCpmkCount = 0;

            foreach ($cpl->cpmk as $cpmk) {
                $bobotIds = ($bobotByCpmk->get($cpmk->id) ?? collect())->pluck('id');
                $nilaiCpmkTotal = 0;
                $bobotCpmkTotal = 0;

                foreach ($bobotIds as $bobotId) {
                    foreach ($nilaiByBobot->get($bobotId, collect()) as $nilai) {
                        if ($nilai->bobot && $nilai->bobot->bobot > 0) {
                            $nilaiCpmkTotal += ($nilai->nilai * $nilai->bobot->bobot);
                            $bobotCpmkTotal += $nilai->bobot->bobot;
                        }
                    }
                }

                $nilaiCpmk = $bobotCpmkTotal > 0 ? $nilaiCpmkTotal / $bobotCpmkTotal : null;

                if ($trackMissing && $nilaiCpmk === null) {
                    $hasMissingCpmk = true;
                    $missingCpmkCount++;
                }

                foreach ($cpmk->cpmkMatKul as $matkulRel) {
                    $matkul = $matkulRel->mataKuliah;
                    $total = $nilaiCpmk !== null ? round($nilaiCpmk, 2) : null;
                    if ($total !== null && is_numeric($total)) {
                        $totalCpmkArr[] = $total;
                    }
                    $status_capaian = ($total !== null && $total > 55) ? 'Tercapai' : 'Belum Tercapai';
                    $cpmkData[] = [
                        'id' => $cpmk->id,
                        'kode' => $cpmk->kodeCpmk,
                        'deskripsi' => $cpmk->deskripsi,
                        'kode_mk' => $matkul ? $matkul->kodeMatkul : '-',
                        'nama_mk' => $matkul ? $matkul->namaMatkul : '-',
                        'nilai' => $nilaiCpmk !== null ? round($nilaiCpmk, 2) : '-',
                        'total' => $total !== null ? $total : '-',
                        'status_capaian' => $total !== null ? $status_capaian : '-',
                    ];
                }
            }

            $total_cpl = count($totalCpmkArr) > 0 ? max($totalCpmkArr) : '-';

            if ($trackMissing) {
                $status_cpl = (!$hasMissingCpmk && $total_cpl !== '-' && $total_cpl > 55)
                    ? 'Tercapai'
                    : 'Belum Tercapai';
            } else {
                $status_cpl = ($total_cpl !== '-' && $total_cpl > 55) ? 'Tercapai' : 'Belum Tercapai';
            }

            $entry = [
                'id' => $cpl->id,
                'kode' => $cpl->kodeCpl,
                'deskripsi' => $cpl->deskripsi,
                'cpmk' => $cpmkData,
                'total_cpl' => $total_cpl,
                'nilai_surat' => is_numeric($total_cpl) ? (int) round($total_cpl) : '-',
                'status_cpl' => $status_cpl,
            ];

            if ($trackMissing) {
                $entry['missing_cpmk_count'] = $missingCpmkCount;
            }

            $cplData[] = $entry;
        }

        return $cplData;
    }
}
