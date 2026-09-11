<?php

namespace App\Services;

use App\Models\Kurikulum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Menentukan pasangan (CPL, mata kuliah) yang dihitung sebagai asesmen.
 *
 * Model: asesmen ditetapkan per pasangan CPL x matkul lewat pivot
 * `cpl_mata_kuliah_asesmen`. Kurikulum melekat implisit karena satu baris
 * `mata_kuliah` sudah terikat tepat satu `kurikulumId`. Tidak ada fallback —
 * bila sebuah kurikulum belum punya pasangan asesmen, tidak ada yang dihitung.
 */
class CplAssessmentScope
{
    /**
     * Peta pasangan asesmen: [cplId => [mataKuliahId => true]], opsional per kurikulum.
     */
    public function assessedPairs(?int $kurikulumId = null): array
    {
        $pairs = [];

        $this->basePairQuery($kurikulumId)
            ->get()
            ->each(function ($row) use (&$pairs) {
                $pairs[(int) $row->cplId][(int) $row->mataKuliahId] = true;
            });

        return $pairs;
    }

    /**
     * Peta pasangan asesmen dalam bentuk flat ["cplId:mkId" => true].
     */
    public function assessedPairKeys(?int $kurikulumId = null): array
    {
        $keys = [];

        $this->basePairQuery($kurikulumId)
            ->get()
            ->each(function ($row) use (&$keys) {
                $keys[(int) $row->cplId . ':' . (int) $row->mataKuliahId] = true;
            });

        return $keys;
    }

    /**
     * Apakah kurikulum ini (atau kurikulum mana pun bila null) punya pasangan asesmen.
     */
    public function hasExplicitAssessment(?int $kurikulumId = null): bool
    {
        return $this->basePairQuery($kurikulumId)->exists();
    }

    /**
     * Daftar kurikulum untuk dropdown filter laporan.
     */
    public function kurikulumList(): Collection
    {
        return Kurikulum::query()->orderBy('kode')->get(['id', 'kode', 'nama']);
    }

    /**
     * SQL constraint: hanya baris yang pasangan (CPL, matkul)-nya ada di pivot.
     *
     * @param  string  $cplColumn  kolom query yang memuat cplId (mis. 'cpl.id')
     * @param  string  $mkColumn  kolom query yang memuat mataKuliahId (mis. 'mk.id' atau 'tam.mataKuliahId')
     */
    public function applyAssessedPairConstraint($query, string $cplColumn, string $mkColumn, ?int $kurikulumId = null)
    {
        return $query->whereExists(function ($sub) use ($cplColumn, $mkColumn, $kurikulumId) {
            $sub->select(DB::raw(1))
                ->from('cpl_mata_kuliah_asesmen as cma')
                ->whereColumn('cma.cplId', $cplColumn)
                ->whereColumn('cma.mataKuliahId', $mkColumn);

            if ($kurikulumId) {
                $sub->whereExists(function ($kurikulumSub) use ($kurikulumId) {
                    $kurikulumSub->select(DB::raw(1))
                        ->from('mata_kuliah as mk_kur')
                        ->whereColumn('mk_kur.id', 'cma.mataKuliahId')
                        ->where('mk_kur.kurikulumId', $kurikulumId);
                });
            }
        });
    }

    /**
     * Query dasar pivot, opsional disaring ke satu kurikulum.
     */
    private function basePairQuery(?int $kurikulumId = null)
    {
        return DB::table('cpl_mata_kuliah_asesmen as cma')
            ->join('mata_kuliah as mk', 'mk.id', '=', 'cma.mataKuliahId')
            ->when($kurikulumId, fn ($q) => $q->where('mk.kurikulumId', $kurikulumId))
            ->select('cma.cplId', 'cma.mataKuliahId')
            ->orderBy('cma.cplId')
            ->orderBy('cma.mataKuliahId');
    }
}
