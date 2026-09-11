<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cpl_mata_kuliah_asesmen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cplId')->constrained('cpl')->cascadeOnDelete();
            $table->foreignId('mataKuliahId')->constrained('mata_kuliah')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['cplId', 'mataKuliahId'], 'cpl_mk_asesmen_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cpl_mata_kuliah_asesmen');
    }
};
