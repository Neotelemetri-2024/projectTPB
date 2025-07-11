<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('komponen_per_cpmk', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('matkul_cpl_cpmk_id');
            $table->unsignedBigInteger('komponen_id');
            $table->float('bobot');
            $table->timestamps();

            $table->foreign('matkul_cpl_cpmk_id')->references('id')->on('matkul_cpl_cpmk')->onDelete('cascade');
            $table->foreign('komponen_id')->references('id')->on('komponen')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komponen_per_cpmk');
    }
};
