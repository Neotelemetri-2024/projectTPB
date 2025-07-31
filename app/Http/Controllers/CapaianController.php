<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cpl;
use App\Models\CplCpmk;
use App\Models\Cpmk;
use App\Models\Bobot;
use App\Models\Nilai;
use App\Models\Mahasiswa;

class CapaianController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        $tahunAjaranId = $request->input('tahun_ajaran_id'); // opsional filter

        // Ambil semua CPL
        $cplList = Cpl::orderBy('kodeCpl')->get();
        $cplIdTerpilih = $request->input('cpl_id');
        $cplData = [];
        foreach ($cplList as $cpl) {
            if ($cplIdTerpilih && $cpl->id != $cplIdTerpilih) continue;
            // Ambil CPMK yang mendukung CPL ini
            $cpmkIds = CplCpmk::where('cplId', $cpl->id)->pluck('cpmkId');
            $cpmkList = Cpmk::whereIn('id', $cpmkIds)->orderBy('kodeCpmk')->get();
            $cpmkData = [];
            $totalCpmkArr = [];
            foreach ($cpmkList as $cpmk) {
                // Ambil semua bobot untuk CPMK ini (dari semua matkul yang diambil mahasiswa)
                $bobotIds = Bobot::where('cpmkId', $cpmk->id)
                    ->when($tahunAjaranId, function($q) use ($tahunAjaranId) {
                        $q->where('tahunAjaranId', $tahunAjaranId);
                    })
                    ->pluck('id');
                // Ambil nilai CPMK mahasiswa dengan bobot yang benar
                $nilaiCpmkTotal = 0;
                $bobotCpmkTotal = 0;
                
                $nilaiRecords = Nilai::where('mahasiswaId', $mahasiswa->id)
                    ->whereIn('bobotId', $bobotIds)
                    ->with('bobot')
                    ->get();
                
                foreach ($nilaiRecords as $nilai) {
                    if ($nilai->bobot && $nilai->bobot->bobot > 0) {
                        $nilaiCpmkTotal += ($nilai->nilai * $nilai->bobot->bobot);
                        $bobotCpmkTotal += $nilai->bobot->bobot;
                    }
                }
                
                $nilaiCpmk = $bobotCpmkTotal > 0 ? $nilaiCpmkTotal / $bobotCpmkTotal : null;
                // Ambil data matkul terkait CPMK ini (bisa lebih dari satu, ambil semua)
                $matkuls = $cpmk->cpmkMatKul()->with('mataKuliah')->get();
                foreach ($matkuls as $matkulRel) {
                    $matkul = $matkulRel->mataKuliah;
                    // Hitung total nilai (skala 100, nilaiCpmk sudah dalam skala 0-100)
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
            $total_cpl = count($totalCpmkArr) > 0 ? round(array_sum($totalCpmkArr) / count($totalCpmkArr), 2) : '-';
            $status_cpl = ($total_cpl !== '-' && $total_cpl > 55) ? 'Tercapai' : 'Belum Tercapai';
            $cplData[] = [
                'id' => $cpl->id,
                'kode' => $cpl->kodeCpl,
                'deskripsi' => $cpl->deskripsi,
                'cpmk' => $cpmkData,
                'total_cpl' => $total_cpl,
                'status_cpl' => $status_cpl,
            ];
        }
        return view('mahasiswa.capaian', [
            'cplData' => $cplData,
            'cplList' => $cplList,
            'cplIdTerpilih' => $cplIdTerpilih,
        ]);
    }
}
