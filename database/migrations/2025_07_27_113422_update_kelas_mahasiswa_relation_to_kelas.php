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
        Schema::table('kelas_mahasiswa', function (Blueprint $table) {
            // Hapus foreign key lama
            $table->dropForeign(['tahunAjaranMatkulId']);
            $table->dropColumn('tahunAjaranMatkulId');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas_mahasiswa', function (Blueprint $table) {
            // Tambahkan kembali kolom lama
            $table->foreignId('tahunAjaranMatkulId')->nullable()->constrained('tahun_ajaran_matkul')->onDelete('cascade');
        });
    }
};
