<?php

namespace Tests\Feature;

use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\TahunAjaran;
use App\Models\TahunAjaranMatkul;
use App\Services\CplAssessmentScope;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class KurikulumAssessmentScopeTest extends TestCase
{
    use RefreshDatabase;

    private function makeMatkul(Kurikulum $kurikulum, string $kode, bool $asesmen): MataKuliah
    {
        return MataKuliah::create([
            'kodeMatkul' => $kode,
            'kurikulumId' => $kurikulum->id,
            'namaMatkul' => 'MK ' . $kode,
            'jenis' => 'wajib',
            'sks' => 3,
            'isAsesmen' => $asesmen,
        ]);
    }

    public function test_scope_only_returns_matkul_flagged_asesmen(): void
    {
        $k2020 = Kurikulum::create(['kode' => '2020', 'nama' => 'Kurikulum 2020', 'isAktif' => true]);
        $k2024 = Kurikulum::create(['kode' => '2024', 'nama' => 'Kurikulum 2024', 'isAktif' => true]);

        $asesmen = $this->makeMatkul($k2020, 'A1', true);
        $this->makeMatkul($k2020, 'A2', false);
        $this->makeMatkul($k2024, 'B1', false);

        $scope = new CplAssessmentScope();

        $this->assertSame([$asesmen->id], $scope->assessedMataKuliahIds()->all());
        $this->assertSame([$asesmen->id], $scope->assessedMataKuliahIds($k2020->id)->all());
        $this->assertSame([], $scope->assessedMataKuliahIds($k2024->id)->all());
    }

    public function test_curriculum_without_asesmen_has_no_assessment(): void
    {
        $kosong = Kurikulum::create(['kode' => '2024', 'nama' => 'Kurikulum 2024', 'isAktif' => true]);
        $this->makeMatkul($kosong, 'B1', false);

        $scope = new CplAssessmentScope();

        $this->assertFalse($scope->hasExplicitAssessment($kosong->id));
        $this->assertSame(0, $scope->assessedMataKuliahIds($kosong->id)->count());
    }

    public function test_assessed_constraint_excludes_non_asesmen_from_report_query(): void
    {
        $kurikulum = Kurikulum::create(['kode' => '2020', 'nama' => 'Kurikulum 2020', 'isAktif' => true]);
        $asesmen = $this->makeMatkul($kurikulum, 'A1', true);
        $nonAsesmen = $this->makeMatkul($kurikulum, 'A2', false);

        $tahunAjaran = TahunAjaran::create(['tahun' => '2024/2025', 'periode' => 'ganjil']);

        $tamAsesmen = TahunAjaranMatkul::create([
            'mataKuliahId' => $asesmen->id,
            'tahunAjaranId' => $tahunAjaran->id,
            'semester' => 1,
        ]);
        $tamNonAsesmen = TahunAjaranMatkul::create([
            'mataKuliahId' => $nonAsesmen->id,
            'tahunAjaranId' => $tahunAjaran->id,
            'semester' => 1,
        ]);

        $scope = new CplAssessmentScope();
        $query = DB::table('tahun_ajaran_matkul as tam')->select('tam.id');
        $scope->applyAssessedMatkulConstraint($query, $kurikulum->id, 'tam');

        $ids = $query->pluck('id')->all();

        $this->assertContains($tamAsesmen->id, $ids);
        $this->assertNotContains($tamNonAsesmen->id, $ids);
    }

    public function test_kurikulum_accessor_returns_kode_for_legacy_views(): void
    {
        $kurikulum = Kurikulum::create(['kode' => '2020', 'nama' => 'Kurikulum 2020', 'isAktif' => true]);
        $mk = $this->makeMatkul($kurikulum, 'A1', false);

        $this->assertSame('2020', $mk->kurikulum);
        $this->assertSame('2020', $mk->fresh()->kurikulum);
    }
}
