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
        Schema::create('cpmk_mat_kul', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mataKuliahId')->constrained('mata_kuliah')->onDelete('cascade');
            $table->foreignId('cpmkId')->constrained('cpmk')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cpmk_mat_kul');
    }
};
