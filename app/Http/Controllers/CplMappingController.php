<?php

namespace App\Http\Controllers;

use App\Models\Cpl;
use App\Models\MataKuliah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CplMappingController extends Controller
{
    public function index(Request $request)
    {
        $kurikulumList = MataKuliah::query()
            ->whereNotNull('kurikulum')
            ->where('kurikulum', '!=', '')
            ->distinct()
            ->orderBy('kurikulum')
            ->pluck('kurikulum');

        $selectedKurikulum = $request->get('kurikulum');

        $cpls = Cpl::orderBy('kodeCpl')->get();

        $mataKuliahQuery = MataKuliah::query()->orderBy('kodeMatkul');
        if ($selectedKurikulum) {
            $mataKuliahQuery->where('kurikulum', $selectedKurikulum);
        }
        $mataKuliahList = $mataKuliahQuery->get();

        $mkIds = $mataKuliahList->pluck('id');

        // Mapping: mata_kuliah_id => cpl_id => [cpmk labels]
        $raw = collect();
        if ($mkIds->isNotEmpty() && $cpls->isNotEmpty()) {
            $raw = DB::table('mata_kuliah as mk')
                ->join('tahun_ajaran_matkul as tam', 'tam.mataKuliahId', '=', 'mk.id')
                ->join('cpmk_mat_kul as cmk', 'cmk.tahunAjaranMatkulId', '=', 'tam.id')
                ->join('cpmk', 'cpmk.id', '=', 'cmk.cpmkId')
                ->join('cpmk_cpl as cc', 'cc.cpmkId', '=', 'cpmk.id')
                ->whereIn('mk.id', $mkIds)
                ->select(
                    'mk.id as mata_kuliah_id',
                    'cc.cplId as cpl_id',
                    'cpmk.id as cpmk_id',
                    'cpmk.kodeCpmk',
                    'mk.kodeMatkul'
                )
                ->distinct()
                ->get();
        }

        $cells = [];
        foreach ($raw as $row) {
            $mkId = $row->mata_kuliah_id;
            $cplId = $row->cpl_id;
            $label = $row->kodeCpmk;
            $cells[$mkId][$cplId][$row->cpmk_id] = $label;
        }

        $rows = $mataKuliahList->map(function ($mk) use ($cpls, $cells) {
            $mapped = [];
            $mappedCount = 0;
            foreach ($cpls as $cpl) {
                $labels = array_values($cells[$mk->id][$cpl->id] ?? []);
                $mapped[$cpl->id] = $labels;
                if (!empty($labels)) {
                    $mappedCount++;
                }
            }

            return [
                'id' => $mk->id,
                'kode' => $mk->kodeMatkul,
                'nama' => $mk->namaMatkul,
                'sks' => $mk->sks,
                'kurikulum' => $mk->kurikulum,
                'jenis' => $mk->jenis,
                'mapped' => $mapped,
                'mapped_count' => $mappedCount,
            ];
        })->values();

        $rolePrefix = auth()->user()->role === 'admin' ? 'admin' : 'pimpinan';

        return view('shared.cpl-mapping', compact(
            'cpls',
            'rows',
            'kurikulumList',
            'selectedKurikulum',
            'rolePrefix'
        ));
    }
}
