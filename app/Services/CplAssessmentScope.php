<?php

namespace App\Services;

use App\Models\Kurikulum;
use App\Models\MataKuliah;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Menentukan mata kuliah yang dihitung sebagai asesmen CPL.
 *
 * Model baru: matkul asesmen ditetapkan per kurikulum lewat flag
 * `mata_kuliah.isAsesmen`. Tidak ada fallback — bila sebuah kurikulum
 * belum punya matkul asesmen, tidak ada yang dihitung.
 */
class CplAssessmentScope
{
    /**
     * Mata kuliah IDs yang dihitung, opsional disaring per kurikulum.
     */
    public function assessedMataKuliahIds(?int $kurikulumId = null): Collection
    {
        return MataKuliah::query()
            ->where('isAsesmen', true)
            ->when($kurikulumId, fn ($q) => $q->where('kurikulumId', $kurikulumId))
            ->orderBy('id')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values();
    }

    /**
     * Map kurikulumId => Collection of assessed mata kuliah IDs.
     *
     * @param  iterable<int>|null  $kurikulumIds
     */
    public function assessedMataKuliahIdsByKurikulum(?iterable $kurikulumIds = null): Collection
    {
        return MataKuliah::query()
            ->where('isAsesmen', true)
            ->when($kurikulumIds !== null, fn ($q) => $q->whereIn('kurikulumId', collect($kurikulumIds)->all()))
            ->get(['id', 'kurikulumId'])
            ->groupBy('kurikulumId')
            ->map(fn ($rows) => $rows->pluck('id')->map(fn ($id) => (int) $id)->values());
    }

    /**
     * Apakah kurikulum ini (atau kurikulum mana pun bila null) punya matkul asesmen.
     */
    public function hasExplicitAssessment(?int $kurikulumId = null): bool
    {
        return MataKuliah::query()
            ->where('isAsesmen', true)
            ->when($kurikulumId, fn ($q) => $q->where('kurikulumId', $kurikulumId))
            ->exists();
    }

    /**
     * Daftar kurikulum untuk dropdown filter laporan.
     */
    public function kurikulumList(): Collection
    {
        return Kurikulum::query()->orderBy('kode')->get(['id', 'kode', 'nama']);
    }

    /**
     * SQL constraint: mata kuliah pada TAM yang ditandai asesmen (opsional satu kurikulum).
     * Expects a joined `tahun_ajaran_matkul` table exposing `mataKuliahId`.
     */
    public function applyAssessedMatkulConstraint($query, ?int $kurikulumId = null, string $tamTable = 'tahun_ajaran_matkul')
    {
        return $query->whereExists(function ($sub) use ($kurikulumId, $tamTable) {
            $sub->select(DB::raw(1))
                ->from('mata_kuliah as mk_asesmen')
                ->whereColumn('mk_asesmen.id', "{$tamTable}.mataKuliahId")
                ->where('mk_asesmen.isAsesmen', true);

            if ($kurikulumId) {
                $sub->where('mk_asesmen.kurikulumId', $kurikulumId);
            }
        });
    }
}
