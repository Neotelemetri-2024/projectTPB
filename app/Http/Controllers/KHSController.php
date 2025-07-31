<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
            $periodeTerpilih = $request->input('periode_id') ?? ($periodes[0]['id'] ?? null);
            $filteredKelas = $kelasMahasiswa->filter(function($km) use ($periodeTerpilih) {
                return $km->tahunAjaranMatkul->tahunAjaran->id == $periodeTerpilih;
            });
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
                    'nilai_akhir' => $nilaiAkhir,
                    'grade' => $grade,
                    'kelas_mahasiswa_id' => $km->id,
                    'komponen_nilai' => $komponenNilai,
                    'cpmk_nilai' => $cpmkNilai,
                ];
            }
        }
        return view("mahasiswa.transkrip", compact('matkulDiambil', 'periodes', 'periodeTerpilih'));
    }
}


