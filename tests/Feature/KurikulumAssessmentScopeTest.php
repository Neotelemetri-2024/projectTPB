<?php

namespace Tests\Feature;

use App\Models\Cpl;
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

    private function makeKurikulum(string $kode): Kurikulum
    {
        return Kurikulum::create(['kode' => $kode, 'nama' => 'Kurikulum ' . $kode, 'isAktif' => true]);
    }

    private function makeMatkul(Kurikulum $kurikulum, string $kode): MataKuliah
    {
        return MataKuliah::create([
            'kodeMatkul' => $kode,
            'kurikulumId' => $kurikulum->id,
            'namaMatkul' => 'MK ' . $kode,
            'jenis' => 'wajib',
            'sks' => 3,
        ]);
    }

    private function makeCpl(string $kode): Cpl
    {
        return Cpl::create([
            'kodeCpl' => $kode,
            'deskripsi' => 'CPL ' . $kode,
            'nilaiMinimal' => 60,
            'targetPersen' => 60,
        ]);
    }

    /**
     * Satu matkul bisa diases untuk sebagian CPL saja, dan satu CPL
     * bisa diases oleh sebagian matkul saja.
     */
    public function test_only_selected_cpl_matkul_pairs_are_assessed(): void
    {
        $kurikulum = $this->makeKurikulum('2020');
        $cpl1 = $this->makeCpl('CPL1');
        $cpl2 = $this->makeCpl('CPL2');
        $ekonomi = $this->makeMatkul($kurikulum, 'A1');
        $lainnya = $this->makeMatkul($kurikulum, 'A2');

        // ekonomi diases hanya untuk CPL1; lainnya diases hanya untuk CPL2.
        DB::table('cpl_mata_kuliah_asesmen')->insert([
            ['cplId' => $cpl1->id, 'mataKuliahId' => $ekonomi->id, 'created_at' => now(), 'updated_at' => now()],
            ['cplId' => $cpl2->id, 'mataKuliahId' => $lainnya->id, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $scope = new CplAssessmentScope();
        $pairs = $scope->assessedPairs();

        $this->assertTrue(isset($pairs[$cpl1->id][$ekonomi->id]), 'ekonomi harus diases untuk CPL1');
        $this->assertFalse(isset($pairs[$cpl1->id][$lainnya->id]), 'lainnya tidak diases untuk CPL1');
        $this->assertTrue(isset($pairs[$cpl2->id][$lainnya->id]), 'lainnya harus diases untuk CPL2');
        $this->assertFalse(isset($pairs[$cpl2->id][$ekonomi->id]), 'ekonomi tidak diases untuk CPL2');
    }

    public function test_pairs_are_scoped_to_selected_kurikulum(): void
    {
        $k2020 = $this->makeKurikulum('2020');
        $k2024 = $this->makeKurikulum('2024');
        $cpl = $this->makeCpl('CPL1');

        $lama = $this->makeMatkul($k2020, 'A1');
        $baru = $this->makeMatkul($k2024, 'B1');

        DB::table('cpl_mata_kuliah_asesmen')->insert([
            ['cplId' => $cpl->id, 'mataKuliahId' => $lama->id, 'created_at' => now(), 'updated_at' => now()],
            ['cplId' => $cpl->id, 'mataKuliahId' => $baru->id, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $scope = new CplAssessmentScope();

        $this->assertCount(2, $scope->assessedPairKeys());
        $this->assertCount(1, $scope->assessedPairKeys($k2020->id));
        $this->assertTrue(isset($scope->assessedPairKeys($k2020->id)[$cpl->id . ':' . $lama->id]));
        $this->assertFalse(isset($scope->assessedPairKeys($k2020->id)[$cpl->id . ':' . $baru->id]));
    }

    public function test_curriculum_without_pairs_has_no_assessment(): void
    {
        $kurikulum = $this->makeKurikulum('2024');
        $this->makeMatkul($kurikulum, 'B1');
        $this->makeCpl('CPL1');

        $scope = new CplAssessmentScope();

        $this->assertFalse($scope->hasExplicitAssessment($kurikulum->id));
        $this->assertFalse($scope->hasExplicitAssessment());
        $this->assertCount(0, $scope->assessedPairKeys($kurikulum->id));
    }

    public function test_assessed_constraint_only_matches_selected_pairs(): void
    {
        $kurikulum = $this->makeKurikulum('2020');
        $cpl1 = $this->makeCpl('CPL1');
        $cpl2 = $this->makeCpl('CPL2');
        $asesmen = $this->makeMatkul($kurikulum, 'A1');
        $nonAsesmen = $this->makeMatkul($kurikulum, 'A2');

        DB::table('cpl_mata_kuliah_asesmen')->insert([
            ['cplId' => $cpl1->id, 'mataKuliahId' => $asesmen->id, 'created_at' => now(), 'updated_at' => now()],
        ]);

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

        // CPL1 hanya mengases matkul A1, jadi hanya TAM A1 yang cocok.
        $queryCpl1 = DB::table('cpl')
            ->crossJoin('tahun_ajaran_matkul as tam')
            ->select('cpl.id as cpl_id', 'tam.id as tam_id');
        $scope->applyAssessedPairConstraint($queryCpl1, 'cpl.id', 'tam.mataKuliahId', $kurikulum->id);
        $idsCpl1 = $queryCpl1->pluck('tam_id')->all();
        $this->assertContains($tamAsesmen->id, $idsCpl1);
        $this->assertNotContains($tamNonAsesmen->id, $idsCpl1);

        // CPL2 tidak mengases matkul mana pun.
        $queryCpl2 = DB::table('cpl')
            ->crossJoin('tahun_ajaran_matkul as tam')
            ->where('cpl.id', $cpl2->id)
            ->select('tam.id as tam_id');
        $scope->applyAssessedPairConstraint($queryCpl2, 'cpl.id', 'tam.mataKuliahId', $kurikulum->id);
        $this->assertSame([], $queryCpl2->pluck('tam_id')->all());
    }

    public function test_kurikulum_accessor_returns_kode_for_legacy_views(): void
    {
        $kurikulum = $this->makeKurikulum('2020');
        $mk = $this->makeMatkul($kurikulum, 'A1');

        $this->assertSame('2020', $mk->kurikulum);
        $this->assertSame('2020', $mk->fresh()->kurikulum);
    }
}
