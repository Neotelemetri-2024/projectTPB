<?php

namespace Tests\Feature;

use App\Http\Controllers\CapaianController;
use App\Models\Kelas;
use App\Models\KelasMahasiswa;
use App\Models\Kurikulum;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\TahunAjaran;
use App\Models\TahunAjaranMatkul;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use ReflectionMethod;
use Tests\TestCase;

class StudentKurikulumSelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_rapor_only_offers_curricula_of_courses_taken_by_the_student(): void
    {
        // Migrasi lama menghapus tahunAjaranMatkulId tanpa menambah kelasId.
        Schema::table('kelas_mahasiswa', function (Blueprint $table) {
            $table->foreignId('kelasId')->nullable()->constrained('kelas');
        });

        $student = Mahasiswa::create([
            'userId' => User::factory()->create()->id,
            'nama' => 'Mahasiswa A',
            'nim' => '123456',
            'tahunMasuk' => 2025,
        ]);
        $year = TahunAjaran::create(['tahun' => '2025/2026', 'periode' => 'ganjil']);

        $taken = Kurikulum::create(['kode' => '2025', 'nama' => 'Kurikulum 2025', 'isAktif' => true]);
        $other = Kurikulum::create(['kode' => '2026', 'nama' => 'Kurikulum 2026', 'isAktif' => true]);

        foreach ([$taken, $other] as $index => $curriculum) {
            $course = MataKuliah::create([
                'kodeMatkul' => 'MK' . $index,
                'kurikulumId' => $curriculum->id,
                'namaMatkul' => 'Mata Kuliah ' . $index,
                'jenis' => 'wajib',
                'sks' => 3,
            ]);
            $offering = TahunAjaranMatkul::create([
                'tahunAjaranId' => $year->id,
                'mataKuliahId' => $course->id,
                'sks' => 3,
                'semester' => 1,
            ]);
            $class = Kelas::create(['namaKelas' => 'A', 'tahunAjaranMatkulId' => $offering->id]);

            if ($curriculum->is($taken)) {
                KelasMahasiswa::create(['mahasiswaId' => $student->id, 'kelasId' => $class->id]);
            }
        }

        $controller = new CapaianController();
        $list = (new ReflectionMethod($controller, 'studentKurikulumList'))->invoke($controller, $student->id);
        $select = new ReflectionMethod($controller, 'selectedKurikulumId');

        $this->assertSame([$taken->id], $list->pluck('id')->all());
        $this->assertSame($taken->id, $select->invoke($controller, $list, null));
        $this->assertSame($taken->id, $select->invoke($controller, $list, $other->id));
    }
}
