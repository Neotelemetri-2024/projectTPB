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
        Schema::create('kelas_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswaId')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('tahunAjaranMatkulId')->constrained('tahun_ajaran_matkul')->onDelete('cascade');
            $table->float('totalNilai')->nullable()->after('tahunAjaranMatkulId');
            $table->enum('grade', ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'D', 'E'])->nullable()->after('totalNilai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas_mahasiswa');
    }
};
