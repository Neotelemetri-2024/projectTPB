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
        Schema::create('nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cpmkId')->constrained('cpmk')->onDelete('cascade');
            $table->foreignId('mahasiswaId')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('dosenPengampuId')->constrained('dosen_pengampu')->onDelete('cascade');
            $table->foreignId('tahunAjaranMatkulId')->constrained('tahun_ajaran_matkul')->onDelete('cascade');
            $table->foreignId('bobotId')->constrained('bobot')->onDelete('cascade');
            $table->float('nilai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai');
    }
};
