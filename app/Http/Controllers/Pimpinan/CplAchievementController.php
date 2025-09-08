<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cpl;
use App\Models\CplCpmk;
use App\Models\Cpmk;
use App\Models\TahunAjaran;
use App\Models\TahunAjaranMatkul;
use App\Models\KelasMahasiswa;
use App\Models\Nilai;

class CplAchievementController extends Controller
{
    public function index(Request $request)
    {
        // Tidak terpengaruh tahun ajaran: ambil seluruh data lintas tahun ajaran
        $cplList = Cpl::orderBy('kodeCpl')->get();
        $rows = [];

        // Ambil seluruh MK lintas tahun ajaran
        $tamList = TahunAjaranMatkul::with('mataKuliah')->get();

        foreach ($cplList as $cpl) {
                // Ambil semua CPMK terkait CPL
                $cpmkIds = CplCpmk::where('cplId', $cpl->id)->pluck('cpmkId');
                $cpmkList = Cpmk::whereIn('id', $cpmkIds)->get();

                foreach ($tamList as $tam) {
                    // Total mahasiswa diases pada MK ini
                    $totalMahasiswa = KelasMahasiswa::whereHas('kelas', function($q) use ($tam) {
                        $q->where('tahunAjaranMatkulId', $tam->id);
                    })->distinct('mahasiswaId')->count();

                    if ($totalMahasiswa === 0) {
                        $rows[] = [
                            'cpl' => $cpl,
                            'kode_mk' => $tam->mataKuliah->kodeMatkul ?? '-',
                            'nama_mk' => $tam->mataKuliah->namaMatkul ?? '-',
                            'total' => 0,
                            'mencapai' => 0,
                            'capai_persen' => 0,
                        ];
                        continue;
                    }

                    // Hitung rata-rata nilai per mahasiswa untuk semua CPMK di CPL ini dalam MK ini
                    $mahasiswaIds = KelasMahasiswa::whereHas('kelas', function($q) use ($tam) {
                        $q->where('tahunAjaranMatkulId', $tam->id);
                    })->pluck('mahasiswaId')->toArray();

                    $avgPerMahasiswa = [];
                    foreach ($mahasiswaIds as $mId) {
                        $nilaiTotal = 0; $bobotTotal = 0; $hasAny = false;
                        foreach ($cpmkList as $cpmk) {
                            $nilaiRecords = Nilai::where('mahasiswaId', $mId)
                                ->where('tahunAjaranMatkulId', $tam->id)
                                ->where('cpmkId', $cpmk->id)
                                ->with('bobot')
                                ->get();
                            foreach ($nilaiRecords as $nr) {
                                if ($nr->bobot && $nr->bobot->bobot > 0) {
                                    $hasAny = true;
                                    $nilaiTotal += ($nr->nilai * $nr->bobot->bobot);
                                    $bobotTotal += $nr->bobot->bobot;
                                }
                            }
                        }
                        if ($hasAny && $bobotTotal > 0) {
                            $avgPerMahasiswa[] = $nilaiTotal / $bobotTotal;
                        }
                    }

                    $mencapai = count(array_filter($avgPerMahasiswa, function($n) { return $n >= 60; }));
                    $capaiPersen = count($avgPerMahasiswa) > 0 ? round(($mencapai / $totalMahasiswa) * 100) : 0;

                    $rows[] = [
                        'cpl' => $cpl,
                        'kode_mk' => $tam->mataKuliah->kodeMatkul ?? '-',
                        'nama_mk' => $tam->mataKuliah->namaMatkul ?? '-',
                        'total' => $totalMahasiswa,
                        'mencapai' => $mencapai,
                        'capai_persen' => $capaiPersen,
                    ];
                }
            }

        return view('pimpinan.cpl-achievement.index', compact('rows', 'cplList'));
    }
}


