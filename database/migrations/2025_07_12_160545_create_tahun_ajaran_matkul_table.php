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
        Schema::create('tahun_ajaran_matkul', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahunAjaranId')->constrained('tahun_ajaran')->onDelete('cascade');
            $table->foreignId('mataKuliahId')->constrained('mata_kuliah')->onDelete('cascade');
            $table->integer('kelas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahun_ajaran_matkul');
    }
};
