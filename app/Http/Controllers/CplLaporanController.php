<?php

namespace App\Http\Controllers;

use App\Models\Cpl;
use App\Models\MataKuliah;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Laporan CPL detail (gaya MyUNAND) — tanpa SCP.
 * Rumus capaian: % mahasiswa dengan nilai CPMK rata-rata ≥ nilaiMinimal CPL.
 */
class CplLaporanController extends Controller
{
    public function index(Request $request)
    {
        $tahunAjaranList = TahunAjaran::orderBy('tahun', 'desc')->orderBy('periode', 'desc')->get();
        $kurikulumList = MataKuliah::query()
            ->whereNotNull('kurikulum')
            ->where('kurikulum', '!=', '')
            ->distinct()
            ->orderBy('kurikulum')
            ->pluck('kurikulum');

        $selectedTahunAjaranId = $request->get('tahun_ajaran_id');
        $selectedKurikulum = $request->get('kurikulum');

        $detailRows = $this->buildDetailRows($selectedTahunAjaranId, $selectedKurikulum);
        $chartData = $this->buildChartFromDetails($detailRows);
        $summary = $this->buildSummary($detailRows, $chartData);

        $rolePrefix = auth()->user()->role === 'admin' ? 'admin' : 'pimpinan';

        return view('shared.cpl-laporan', compact(
            'detailRows',
            'chartData',
            'summary',
            'tahunAjaranList',
            'kurikulumList',
            'selectedTahunAjaranId',
            'selectedKurikulum',
            'rolePrefix'
        ));
    }

    public function exportPdf(Request $request)
    {
        $selectedTahunAjaranId = $request->get('tahun_ajaran_id');
        $selectedKurikulum = $request->get('kurikulum');

        $detailRows = $this->buildDetailRows($selectedTahunAjaranId, $selectedKurikulum);
        $chartData = $this->buildChartFromDetails($detailRows);
        $summary = $this->buildSummary($detailRows, $chartData);

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
        $html = view('shared.cpl-laporan-pdf', compact(
            'detailRows',
            'summary',
            'tahunAjaranLabel',
            'kurikulumLabel'
        ))->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return $dompdf->stream('Laporan_CPL_' . date('Y-m-d_H-i-s') . '.pdf');
    }

    private function buildDetailRows($tahunAjaranId = null, $kurikulum = null): array
    {
        $query = DB::table('cpmk')
            ->join('cpmk_cpl as cc', 'cc.cpmkId', '=', 'cpmk.id')
            ->join('cpl', 'cpl.id', '=', 'cc.cplId')
            ->join('cpmk_mat_kul as cmk', 'cmk.cpmkId', '=', 'cpmk.id')
            ->join('tahun_ajaran_matkul as tam', 'tam.id', '=', 'cmk.tahunAjaranMatkulId')
            ->join('mata_kuliah as mk', 'mk.id', '=', 'tam.mataKuliahId')
            ->join('tahun_ajaran as ta', 'ta.id', '=', 'tam.tahunAjaranId')
            ->select(
                'cpl.id as cpl_id',
                'cpl.kodeCpl',
                'cpl.deskripsi as cpl_deskripsi',
                'cpl.nilaiMinimal as nilai_minimal',
                'cpl.targetPersen as target_persen',
                'cpmk.id as cpmk_id',
                'cpmk.kodeCpmk',
                'cpmk.deskripsi as cpmk_deskripsi',
                'mk.id as mk_id',
                'mk.kodeMatkul',
                'mk.namaMatkul',
                'mk.kurikulum',
                'tam.id as tahun_ajaran_matkul_id',
                'ta.id as tahun_ajaran_id',
                'ta.tahun',
                'ta.periode'
            )
            ->orderBy('cpl.kodeCpl')
            ->orderBy('mk.kodeMatkul')
            ->orderBy('cpmk.kodeCpmk');

        if ($tahunAjaranId) {
            $query->where('tam.tahunAjaranId', $tahunAjaranId);
        }
        if ($kurikulum) {
            $query->where('mk.kurikulum', $kurikulum);
        }

        $baseRows = $query->get();
        if ($baseRows->isEmpty()) {
            return [];
        }

        $tamIds = $baseRows->pluck('tahun_ajaran_matkul_id')->unique()->values();
        $cpmkIds = $baseRows->pluck('cpmk_id')->unique()->values();

        // Dosen per TAM
        $dosenByTam = DB::table('dosen_pengampu_kelas as dpk')
            ->join('kelas', 'kelas.id', '=', 'dpk.kelasId')
            ->join('dosen', 'dosen.id', '=', 'dpk.dosenId')
            ->whereIn('kelas.tahunAjaranMatkulId', $tamIds)
            ->select('kelas.tahunAjaranMatkulId', 'dosen.nama')
            ->distinct()
            ->get()
            ->groupBy('tahunAjaranMatkulId')
            ->map(fn ($g) => $g->pluck('nama')->unique()->implode(', '));

        // Bobot/komponen per CPMK × TAM
        $bobotRows = DB::table('bobot as b')
            ->join('komponen as k', 'k.id', '=', 'b.komponenId')
            ->whereIn('b.tahunAjaranMatkulId', $tamIds)
            ->whereIn('b.cpmkId', $cpmkIds)
            ->select('b.tahunAjaranMatkulId', 'b.cpmkId', 'k.nama as komponen', 'b.bobot')
            ->get()
            ->groupBy(fn ($r) => $r->tahunAjaranMatkulId . ':' . $r->cpmkId);

        // Enrollment per TAM
        $enrolledByTam = DB::table('kelas_mahasiswa as km')
            ->join('kelas', 'kelas.id', '=', 'km.kelasId')
            ->whereIn('kelas.tahunAjaranMatkulId', $tamIds)
            ->select('kelas.tahunAjaranMatkulId', 'km.mahasiswaId')
            ->get()
            ->groupBy('tahunAjaranMatkulId')
            ->map(fn ($g) => $g->pluck('mahasiswaId')->unique()->count());

        // Avg nilai per mahasiswa per CPMK × TAM
        $avgNilai = DB::table('nilai')
            ->whereIn('tahunAjaranMatkulId', $tamIds)
            ->whereIn('cpmkId', $cpmkIds)
            ->select('tahunAjaranMatkulId', 'cpmkId', 'mahasiswaId')
            ->selectRaw('AVG(nilai) as avg_nilai')
            ->groupBy('tahunAjaranMatkulId', 'cpmkId', 'mahasiswaId')
            ->get()
            ->groupBy(fn ($r) => $r->tahunAjaranMatkulId . ':' . $r->cpmkId);

        $rows = [];
        foreach ($baseRows as $row) {
            $nilaiMin = (int) ($row->nilai_minimal ?? 60);
            $targetPersen = (int) ($row->target_persen ?? 60);
            $key = $row->tahun_ajaran_matkul_id . ':' . $row->cpmk_id;
            $avgs = ($avgNilai->get($key) ?? collect())->pluck('avg_nilai')->map(fn ($v) => (float) $v);
            $denganNilai = $avgs->count();
            $totalMhs = $enrolledByTam[$row->tahun_ajaran_matkul_id] ?? 0;
            $mencapai = $avgs->filter(fn ($n) => $n >= $nilaiMin)->count();
            $capaian = $totalMhs > 0 && $denganNilai > 0
                ? round(($mencapai / $totalMhs) * 100, 1)
                : null;

            $sumber = ($bobotRows->get($key) ?? collect())
                ->map(fn ($b) => trim(($b->komponen ?? '-') . ' ' . rtrim(rtrim(number_format((float) $b->bobot, 1), '0'), '.') . '%'))
                ->implode(', ');

            $rows[] = [
                'cpl_id' => $row->cpl_id,
                'kode_cpl' => $row->kodeCpl,
                'cpl_deskripsi' => $row->cpl_deskripsi,
                'cpmk_id' => $row->cpmk_id,
                'kode_cpmk' => $row->kodeCpmk,
                'cpmk_deskripsi' => $row->cpmk_deskripsi,
                'kode_mk' => $row->kodeMatkul,
                'nama_mk' => $row->namaMatkul,
                'kurikulum' => $row->kurikulum,
                'tahun_ajaran_label' => $row->tahun . ' - ' . ucfirst($row->periode),
                'tahun_ajaran_matkul_id' => $row->tahun_ajaran_matkul_id,
                'dosen' => $dosenByTam[$row->tahun_ajaran_matkul_id] ?? '—',
                'sumber_penilaian' => $sumber ?: '—',
                'nilai_minimal' => $nilaiMin,
                'target_persen' => $targetPersen,
                'total_mhs' => $totalMhs,
                'mencapai' => $mencapai,
                'capaian' => $capaian,
            ];
        }

        return $rows;
    }

    private function buildChartFromDetails(array $detailRows): array
    {
        $byCpl = collect($detailRows)->groupBy('kode_cpl');
        $labels = [];
        $capaian = [];
        $target = [];

        foreach ($byCpl as $kode => $items) {
            $labels[] = $kode;
            $vals = $items->pluck('capaian')->filter(fn ($v) => $v !== null);
            $capaian[] = $vals->isNotEmpty() ? round($vals->avg(), 1) : 0;
            $target[] = (int) ($items->first()['target_persen'] ?? 60);
        }

        if (empty($labels)) {
            return [
                'labels' => ['Belum Ada Data'],
                'capaian' => [0],
                'target' => [60],
            ];
        }

        return compact('labels', 'capaian', 'target');
    }

    private function buildSummary(array $detailRows, array $chartData): array
    {
        $cplCount = collect($detailRows)->pluck('cpl_id')->unique()->count();
        $mkCount = collect($detailRows)->pluck('kode_mk')->unique()->count();
        $cpmkCount = collect($detailRows)->pluck('cpmk_id')->unique()->count();
        $avgCapaian = collect($chartData['capaian'] ?? [])->avg();

        return [
            'total_cpl' => $cplCount,
            'total_mk' => $mkCount,
            'total_cpmk' => $cpmkCount,
            'avg_capaian' => $avgCapaian !== null ? round($avgCapaian, 1) : 0,
            'total_baris' => count($detailRows),
        ];
    }
}
