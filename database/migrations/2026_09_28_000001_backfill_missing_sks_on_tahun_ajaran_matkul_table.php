<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(<<<'SQL'
            UPDATE tahun_ajaran_matkul tam
            INNER JOIN mata_kuliah mk ON mk.id = tam.mataKuliahId
            SET tam.sks = mk.sks
            WHERE (tam.sks IS NULL OR tam.sks = 0)
              AND mk.sks IS NOT NULL
              AND mk.sks > 0
        SQL);
    }

    public function down(): void
    {
        // The backfill intentionally preserves valid historical TAM values.
    }
};
