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
        Schema::create('dosen_pengampu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosenId')->constrained('dosen')->onDelete('cascade');
            $table->foreignId('tahunAjaranMatkulId')->constrained('tahun_ajaran_matkul')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosen_pengampu');
    }
};
