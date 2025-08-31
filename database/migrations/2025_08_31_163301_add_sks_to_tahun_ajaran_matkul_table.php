<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tahun_ajaran_matkul', function (Blueprint $table) {
            $table->integer('sks')->after('mataKuliahId')->default(0);
        });

        // Update existing records dengan SKS dari mata kuliah
        DB::statement('
            UPDATE tahun_ajaran_matkul tam 
            JOIN mata_kuliah mk ON tam.mataKuliahId = mk.id 
            SET tam.sks = mk.sks
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tahun_ajaran_matkul', function (Blueprint $table) {
            $table->dropColumn('sks');
        });
    }
};
