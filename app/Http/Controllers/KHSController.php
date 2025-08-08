<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Dompdf\Options;

class KHSController extends Controller
{
    /**
     * Mahasiswa Transcript
     */

    public function index(Request $request)
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        $matkulDiambil = [];
        $periodes = [];
        $periodeTerpilih = null;
        if ($mahasiswa) {
            $kelasMahasiswa = $mahasiswa->kelasMahasiswa()->with('tahunAjaranMatkul.tahunAjaran', 'tahunAjaranMatkul.mataKuliah')->get();
            $periodes = $kelasMahasiswa->map(function($km) {
                $ta = $km->tahunAjaranMatkul->tahunAjaran;
                return [
                    'id' => $ta->id,
                    'tahun' => $ta->tahun,
                    'periode' => $ta->periode,
                    'label' => $ta->tahun . ' - ' . $ta->periode
                ];
            })->unique('id')->sortByDesc('tahun')->sortByDesc('periode')->values()->all();

            // Add "All" option at the beginning
            array_unshift($periodes, [
                'id' => 'all',
                'tahun' => '',
                'periode' => '',
                'label' => 'Semua Semester'
            ]);

            $periodeTerpilih = $request->input('periode_id') ?? ($periodes[1]['id'] ?? 'all');

            // Filter classes based on selected period
            if ($periodeTerpilih === 'all') {
                $filteredKelas = $kelasMahasiswa;
            } else {
                $filteredKelas = $kelasMahasiswa->filter(function($km) use ($periodeTerpilih) {
                    return $km->tahunAjaranMatkul->tahunAjaran->id == $periodeTerpilih;
                });
            }
            $no = 1;
            foreach ($filteredKelas as $km) {
                $mataKuliah = $km->tahunAjaranMatkul->mataKuliah;
                $nilaiAkhir = $km->totalNilai;
                $grade = null;
                if ($nilaiAkhir !== null) {
                    if ($nilaiAkhir >= 80) $grade = 'A';
                    elseif ($nilaiAkhir >= 75) $grade = 'A-';
                    elseif ($nilaiAkhir >= 70) $grade = 'B+';
                    elseif ($nilaiAkhir >= 65) $grade = 'B';
                    elseif ($nilaiAkhir >= 60) $grade = 'B-';
                    elseif ($nilaiAkhir >= 55) $grade = 'C+';
                    elseif ($nilaiAkhir >= 50) $grade = 'C';
                    elseif ($nilaiAkhir >= 40) $grade = 'D';
                    else $grade = 'E';
                }
                // Komponen nilai
                $komponenNilai = [];
                $bobotKomponen = \App\Models\Bobot::with('komponen')
                    ->where('tahunAjaranMatkulId', $km->tahunAjaranMatkul->id)
                    ->get();
                $komponenGrouped = $bobotKomponen->groupBy('komponenId');
                $idxKom = 1;
                foreach ($komponenGrouped as $komponenId => $listBobot) {
                    $komponen = $listBobot->first()->komponen;
                    $nilaiInput = null;
                    $totalNilai = 0;
                    $totalBobot = 0;

                    foreach ($listBobot as $bobot) {
                        $nilaiRecord = \App\Models\Nilai::where('mahasiswaId', $mahasiswa->id)
                            ->where('bobotId', $bobot->id)
                            ->first();
                        if ($nilaiRecord && $bobot->bobot > 0) {
                            $totalNilai += ($nilaiRecord->nilai * $bobot->bobot);
                            $totalBobot += $bobot->bobot;
                        }
                    }

                    if ($totalBobot > 0) {
                        $nilaiInput = $totalNilai / $totalBobot;
                    }

                    $komponenNilai[] = [
                        'no' => $idxKom++,
                        'nama' => $komponen->nama ?? '-',
                        'nilai' => $nilaiInput !== null ? round($nilaiInput, 2) : '-',
                    ];
                }
                // CPMK nilai
                $cpmkNilai = [];
                $bobotCpmk = \App\Models\Bobot::with('cpmk')
                    ->where('tahunAjaranMatkulId', $km->tahunAjaranMatkul->id)
                    ->get();
                $cpmkGrouped = $bobotCpmk->groupBy('cpmkId');
                $idxCpmk = 1;
                foreach ($cpmkGrouped as $cpmkId => $listBobot) {
                    $cpmk = $listBobot->first()->cpmk;
                    $bobotTotal = $listBobot->sum('bobot');

                    // Hitung nilai CPMK dengan bobot yang benar
                    $nilaiCpmkTotal = 0;
                    $bobotCpmkTotal = 0;

                    foreach ($listBobot as $bobot) {
                        $nilaiRecord = \App\Models\Nilai::where('mahasiswaId', $mahasiswa->id)
                            ->where('bobotId', $bobot->id)
                            ->first();

                        if ($nilaiRecord && $bobot->bobot > 0) {
                            $nilaiCpmkTotal += ($nilaiRecord->nilai * $bobot->bobot);
                            $bobotCpmkTotal += $bobot->bobot;
                        }
                    }

                    $nilaiCpmk = $bobotCpmkTotal > 0 ? $nilaiCpmkTotal / $bobotCpmkTotal : null;

                    $cpmkNilai[] = [
                        'no' => $idxCpmk++,
                        'kode' => $cpmk->kodeCpmk ?? '-',
                        'bobot' => $bobotTotal,
                        'nilai' => $nilaiCpmk !== null ? round($nilaiCpmk, 2) : '-',
                    ];
                }
                $matkulDiambil[] = [
                    'no' => $no++,
                    'kode' => $mataKuliah->kodeMatkul ?? '-',
                    'nama' => $mataKuliah->namaMatkul ?? '-',
                    'sks' => $mataKuliah->sks ?? '-',
                    'semester' => $km->tahunAjaranMatkul->tahunAjaran->tahun . ' - ' . $km->tahunAjaranMatkul->tahunAjaran->periode,
                    'nilai_akhir' => $nilaiAkhir,
                    'grade' => $grade,
                    'kelas_mahasiswa_id' => $km->id,
                    'komponen_nilai' => $komponenNilai,
                    'cpmk_nilai' => $cpmkNilai,
                ];
            }
        }
        return view("mahasiswa.transkrip", compact('matkulDiambil', 'periodes', 'periodeTerpilih', 'mahasiswa'));
    }

    public function exportPDF()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        $matkulDiambil = [];

        if ($mahasiswa) {
            $kelasMahasiswa = $mahasiswa->kelasMahasiswa()->with('tahunAjaranMatkul.tahunAjaran', 'tahunAjaranMatkul.mataKuliah')->get();

            $no = 1;
            $totalBobot = 0;
            $totalSks = 0;

            foreach ($kelasMahasiswa as $km) {
                $mataKuliah = $km->tahunAjaranMatkul->mataKuliah;
                $nilaiAkhir = $km->totalNilai;
                $grade = null;

                if ($nilaiAkhir !== null) {
                    if ($nilaiAkhir >= 80) $grade = 'A';
                    elseif ($nilaiAkhir >= 75) $grade = 'A-';
                    elseif ($nilaiAkhir >= 70) $grade = 'B+';
                    elseif ($nilaiAkhir >= 65) $grade = 'B';
                    elseif ($nilaiAkhir >= 60) $grade = 'B-';
                    elseif ($nilaiAkhir >= 55) $grade = 'C+';
                    elseif ($nilaiAkhir >= 50) $grade = 'C';
                    elseif ($nilaiAkhir >= 40) $grade = 'D';
                    else $grade = 'E';
                }

                // Calculate GPA
                if ($grade && $grade !== '-') {
                    $bobot = 0;
                    switch($grade) {
                        case 'A': $bobot = 4.0; break;
                        case 'A-': $bobot = 3.7; break;
                        case 'B+': $bobot = 3.3; break;
                        case 'B': $bobot = 3.0; break;
                        case 'B-': $bobot = 2.7; break;
                        case 'C+': $bobot = 2.3; break;
                        case 'C': $bobot = 2.0; break;
                        case 'C-': $bobot = 1.7; break;
                        case 'D': $bobot = 1.0; break;
                        case 'E': $bobot = 0.0; break;
                    }
                    $totalBobot += $bobot * $mataKuliah->sks;
                    $totalSks += $mataKuliah->sks;
                }

                $matkulDiambil[] = [
                    'no' => $no++,
                    'kode' => $mataKuliah->kodeMatkul ?? '-',
                    'nama' => $mataKuliah->namaMatkul ?? '-',
                    'sks' => $mataKuliah->sks ?? '-',
                    'semester' => $km->tahunAjaranMatkul->tahunAjaran->tahun . ' - ' . $km->tahunAjaranMatkul->tahunAjaran->periode,
                    'nilai_akhir' => $nilaiAkhir,
                    'grade' => $grade,
                ];
            }

            $ipk = $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0.00;

            // Generate HTML content
            $html = view('exports.transkrip-pdf', [
                'mahasiswa' => $mahasiswa,
                'matkulDiambil' => $matkulDiambil,
                'totalSks' => $totalSks,
                'ipk' => $ipk,
            ])->render();

            // Configure DOMPDF
            $options = new Options();
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', true);

            // Create DOMPDF instance
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $fileName = 'Transkrip_' . str_replace(' ', '_', $mahasiswa->nama ?? 'Mahasiswa') . '_' . date('Y-m-d') . '.pdf';

            return response()->streamDownload(function() use ($dompdf) {
                echo $dompdf->output();
            }, $fileName, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan');
    }
}


