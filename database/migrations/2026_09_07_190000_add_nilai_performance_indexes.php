<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            $table->index(['tahunAjaranMatkulId', 'nilai'], 'nilai_tam_nilai_index');
            $table->index(['cpmkId', 'nilai'], 'nilai_cpmk_nilai_index');
            $table->index(['mahasiswaId', 'cpmkId'], 'nilai_mahasiswa_cpmk_index');
            $table->index(['mahasiswaId', 'tahunAjaranMatkulId', 'nilai'], 'nilai_mhs_tam_nilai_index');
        });

        Schema::table('tahun_ajaran_matkul', function (Blueprint $table) {
            $table->index(['tahunAjaranId', 'mataKuliahId'], 'tam_tahun_matkul_index');
        });
    }

    public function down(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            $table->dropIndex('nilai_tam_nilai_index');
            $table->dropIndex('nilai_cpmk_nilai_index');
            $table->dropIndex('nilai_mahasiswa_cpmk_index');
            $table->dropIndex('nilai_mhs_tam_nilai_index');
        });

        Schema::table('tahun_ajaran_matkul', function (Blueprint $table) {
            $table->dropIndex('tam_tahun_matkul_index');
        });
    }
};
