<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tahun_ajaran', function (Blueprint $table) {
            // Ubah kolom tahun dari year ke varchar(9) untuk format "2024/2025"
            $table->string('tahun', 9)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tahun_ajaran', function (Blueprint $table) {
            // Kembalikan ke year jika diperlukan
            $table->year('tahun')->change();
        });
    }
};
