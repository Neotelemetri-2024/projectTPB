<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            $table->index(
                ['tahunAjaranMatkulId', 'mahasiswaId', 'cpmkId'],
                'nilai_tam_mahasiswa_cpmk_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            $table->dropIndex('nilai_tam_mahasiswa_cpmk_index');
        });
    }
};
