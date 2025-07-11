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
        Schema::create('matkul_cpl_cpmk', function (Blueprint $table) {
            $table->id(); // Menambahkan kolom id
            $table->unsignedBigInteger('matkul_id'); // Kolom untuk matkul_id
            $table->unsignedBigInteger('cpl_id'); // Kolom untuk cpl_id
            $table->unsignedBigInteger('cpmk_id'); // Kolom untuk cpmk_id
            $table->timestamps(); // Kolom created_at dan updated_at

            // Menambahkan foreign key constraints
            $table->foreign('matkul_id')->references('id')->on('matkuls')->onDelete('cascade');
            $table->foreign('cpl_id')->references('id')->on('cpl')->onDelete('cascade');
            $table->foreign('cpmk_id')->references('id')->on('cpmk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matkul_cpl_cpmk');
    }
};
