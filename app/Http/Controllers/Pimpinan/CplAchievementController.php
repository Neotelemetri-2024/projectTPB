<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cpl;
use App\Models\TahunAjaran;
use App\Models\TahunAjaranMatkul;
use App\Models\KelasMahasiswa;
use App\Models\MataKuliah;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Dompdf\Options;

class CplAchievementController extends Controller
{
    public function index(Request $request)
    {
        $allRows = Cache::remember('pimpinan.cpl-achievement.rows.v3', 600, function () {
            return $this->buildAchievementRows();
        });

        $tahunAjaranList = TahunAjaran::orderBy('tahun', 'desc')->orderBy('periode', 'desc')->get();
        $kurikulumList = MataKuliah::query()
            ->whereNotNull('kurikulum')
            ->where('kurikulum', '!=', '')
            ->distinct()
            ->orderBy('kurikulum')
            ->pluck('kurikulum');

        $selectedTahunAjaranId = $request->get('tahun_ajaran_id');
        $selectedKurikulum = $request->get('kurikulum');

        $rows = $this->filterRows($allRows, $selectedTahunAjaranId, $selectedKurikulum);
        $grouped = $this->groupRows($rows);
        $summary = $this->buildSummary($grouped);
        $chartData = $this->buildChartData($grouped);
        $cplList = Cpl::orderBy('kodeCpl')->get();
        $rolePrefix = auth()->user()->role === 'admin' ? 'admin' : 'pimpinan';

        return view('pimpinan.cpl-achievement.index', compact(
            'rows',
            'grouped',
            'summary',
            'chartData',
            'cplList',
            'tahunAjaranList',
            'kurikulumList',
            'selectedTahunAjaranId',
            'selectedKurikulum',
            'rolePrefix'
        ));
    }

    public function exportPdf(Request $request)
    {
        $allRows = Cache::remember('pimpinan.cpl-achievement.rows.v3', 600, function () {
            return $this->buildAchievementRows();
        });

        $selectedTahunAjaranId = $request->get('tahun_ajaran_id');
        $selectedKurikulum = $request->get('kurikulum');

        $rows = $this->filterRows($allRows, $selectedTahunAjaranId, $selectedKurikulum);
        $grouped = $this->groupRows($rows);
        $summary = $this->buildSummary($grouped);

        $tahunAjaranLabel = 'Semua Tahun Ajaran';
        if ($selectedTahunAjaranId) {
            $ta = TahunAjaran::find($selectedTahunAjaranId);
            if ($ta) {
                $tahunAjaranLabel = $ta->tahun . ' - ' . ucfirst($ta->periode);
            }
        }

        $kurikulumLabel = $selectedKurikulum ?: 'Semua Kurikulum';

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);
        $html = view('pimpinan.cpl-achievement.pdf', compact(
            'grouped',
            'summary',
            'tahunAjaranLabel',
            'kurikulumLabel'
        ))->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'Laporan_Ketercapaian_CPL_' . date('Y-m-d_H-i-s') . '.pdf';

        return $dompdf->stream($filename);
    }

    /**
     * Display-only filter — does not change per-row achievement formula.
     */
    private function filterRows(array $rows, $tahunAjaranId = null, $kurikulum = null): array
    {
        return collect($rows)
            ->filter(function ($row) use ($tahunAjaranId, $kurikulum) {
                if ($tahunAjaranId && (string) ($row['tahun_ajaran_id'] ?? '') !== (string) $tahunAjaranId) {
                    return false;
                }
                if ($kurikulum !== null && $kurikulum !== '' && ($row['kurikulum'] ?? '') !== $kurikulum) {
                    return false;
                }
                return true;
            })
            ->values()
            ->all();
    }

    private function groupRows(array $rows): array
    {
        $grouped = [];

        foreach (collect($rows)->groupBy(fn ($r) => $r['cpl_id']) as $cplId => $items) {
            $items = $items->values();
            $first = $items->first();
            $sumTotal = $items->sum('total');
            $sumMencapai = $items->sum('mencapai');
            $sumPersen = $sumTotal > 0 ? round(($sumMencapai / $sumTotal) * 100) : 0;

            $grouped[] = [
                'cpl_id' => $cplId,
                'kode_cpl' => $first['kode_cpl'] ?? '-',
                'deskripsi' => $first['deskripsi'] ?? '',
                'cpmk_count' => $first['cpmk_count'] ?? 0,
                'cpmk_kodes' => $first['cpmk_kodes'] ?? '',
                'nilai_minimal' => (int) ($first['nilai_minimal'] ?? 60),
                'target_persen' => (int) ($first['target_persen'] ?? 60),
                'sum_total' => $sumTotal,
                'sum_mencapai' => $sumMencapai,
                'sum_persen' => $sumPersen,
                'mk_count' => $items->unique(fn ($r) => $r['kode_mk'])->count(),
                'items' => $items->all(),
            ];
        }

        return $grouped;
    }

    private function buildSummary(array $grouped): array
    {
        $totalCpl = count($grouped);
        $avgPersen = $totalCpl > 0
            ? round(collect($grouped)->avg('sum_persen'))
            : 0;
        $totalMk = collect($grouped)->sum('mk_count');
        $totalDiases = collect($grouped)->sum('sum_total');
        $totalMencapai = collect($grouped)->sum('sum_mencapai');
        $cplTercapai = collect($grouped)->filter(function ($g) {
            return $g['sum_persen'] >= ($g['target_persen'] ?? 60);
        })->count();

        return [
            'total_cpl' => $totalCpl,
            'avg_persen' => $avgPersen,
            'total_mk' => $totalMk,
            'total_diases' => $totalDiases,
            'total_mencapai' => $totalMencapai,
            'cpl_tercapai' => $cplTercapai,
        ];
    }

    private function buildChartData(array $grouped): array
    {
        $labels = [];
        $capaian = [];
        $target = [];

        foreach ($grouped as $group) {
            $labels[] = $group['kode_cpl'];
            $capaian[] = $group['sum_persen'];
            $target[] = (int) ($group['target_persen'] ?? 60);
        }

        if (empty($labels)) {
            $labels = ['Belum Ada Data'];
            $capaian = [0];
            $target = [60];
        }

        return [
            'labels' => $labels,
            'capaian' => $capaian,
            'target' => $target,
        ];
    }

    private function buildAchievementRows(): array
    {
        $cplList = Cpl::with(['cpmk' => fn ($q) => $q->orderBy('kodeCpmk')])
            ->orderBy('kodeCpl')
            ->get();

        $tamList = TahunAjaranMatkul::with(['mataKuliah', 'tahunAjaran'])
            ->whereHas('mataKuliah', fn ($q) => $q->where('jenis', 'wajib'))
            ->get();

        if ($cplList->isEmpty() || $tamList->isEmpty()) {
            return [];
        }

        $tamIds = $tamList->pluck('id');
        $allCpmkIds = $cplList->flatMap(fn ($cpl) => $cpl->cpmk->pluck('id'))->unique()->values();

        $kelasMahasiswa = KelasMahasiswa::query()
            ->select('kelas_mahasiswa.mahasiswaId', 'kelas.tahunAjaranMatkulId')
            ->join('kelas', 'kelas.id', '=', 'kelas_mahasiswa.kelasId')
            ->whereIn('kelas.tahunAjaranMatkulId', $tamIds)
            ->get()
            ->groupBy('tahunAjaranMatkulId');

        // Weighted avg per (cplId, tahunAjaranMatkulId, mahasiswaId) — aggregate in SQL
        $weightedAvgs = collect();
        if ($allCpmkIds->isNotEmpty()) {
            $weightedAvgs = DB::table('nilai as n')
                ->join('bobot as b', 'n.bobotId', '=', 'b.id')
                ->join('cpmk_cpl as cc', 'n.cpmkId', '=', 'cc.cpmkId')
                ->select('cc.cplId', 'n.tahunAjaranMatkulId', 'n.mahasiswaId')
                ->selectRaw('SUM(n.nilai * b.bobot) / SUM(b.bobot) as weighted_avg')
                ->whereIn('n.tahunAjaranMatkulId', $tamIds)
                ->whereIn('cc.cpmkId', $allCpmkIds)
                ->where('b.bobot', '>', 0)
                ->groupBy('cc.cplId', 'n.tahunAjaranMatkulId', 'n.mahasiswaId')
                ->get()
                ->groupBy(fn ($row) => $row->cplId . ':' . $row->tahunAjaranMatkulId);
        }

        $rows = [];

        foreach ($cplList as $cpl) {
            $cpmkIds = $cpl->cpmk->pluck('id')->all();
            $cpmkKodes = $cpl->cpmk->pluck('kodeCpmk')->implode(', ');
            $cpmkCount = $cpl->cpmk->count();
            $nilaiMinimal = (int) ($cpl->nilaiMinimal ?? 60);
            $targetPersen = (int) ($cpl->targetPersen ?? 60);

            foreach ($tamList as $tam) {
                $mahasiswaIds = ($kelasMahasiswa->get($tam->id) ?? collect())
                    ->pluck('mahasiswaId')
                    ->unique()
                    ->values();
                $totalMahasiswa = $mahasiswaIds->count();

                $tahunAjaran = $tam->tahunAjaran;
                $tahunAjaranLabel = $tahunAjaran
                    ? ($tahunAjaran->tahun . ' - ' . ucfirst($tahunAjaran->periode))
                    : '-';

                $baseMeta = [
                    'cpl_id' => $cpl->id,
                    'kode_cpl' => $cpl->kodeCpl,
                    'deskripsi' => $cpl->deskripsi,
                    'nilai_minimal' => $nilaiMinimal,
                    'target_persen' => $targetPersen,
                    'cpmk_count' => $cpmkCount,
                    'cpmk_kodes' => $cpmkKodes,
                    'kode_mk' => $tam->mataKuliah->kodeMatkul ?? '-',
                    'nama_mk' => $tam->mataKuliah->namaMatkul ?? '-',
                    'kurikulum' => $tam->mataKuliah->kurikulum ?? '',
                    'tahun_ajaran_id' => $tam->tahunAjaranId,
                    'tahun_ajaran_label' => $tahunAjaranLabel,
                    'tahun_ajaran_matkul_id' => $tam->id,
                ];

                if ($totalMahasiswa === 0 || empty($cpmkIds)) {
                    $rows[] = array_merge($baseMeta, [
                        'total' => 0,
                        'mencapai' => 0,
                        'capai_persen' => 0,
                    ]);
                    continue;
                }

                $key = $cpl->id . ':' . $tam->id;
                $avgRows = $weightedAvgs->get($key, collect());
                $enrolledSet = array_flip($mahasiswaIds->all());

                $avgPerMahasiswa = $avgRows
                    ->filter(fn ($row) => isset($enrolledSet[$row->mahasiswaId]))
                    ->pluck('weighted_avg')
                    ->map(fn ($v) => (float) $v)
                    ->all();

                $mencapai = count(array_filter($avgPerMahasiswa, fn ($n) => $n >= $nilaiMinimal));
                $capaiPersen = count($avgPerMahasiswa) > 0
                    ? round(($mencapai / $totalMahasiswa) * 100)
                    : 0;

                $rows[] = array_merge($baseMeta, [
                    'total' => $totalMahasiswa,
                    'mencapai' => $mencapai,
                    'capai_persen' => $capaiPersen,
                ]);
            }
        }

        return $rows;
    }
}
