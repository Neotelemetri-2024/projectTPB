<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cpmk_parents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_cpmk_id');
            $table->unsignedBigInteger('child_cpmk_id');
            $table->timestamps();

            $table->foreign('parent_cpmk_id')->references('id')->on('cpmk')->onDelete('cascade');
            $table->foreign('child_cpmk_id')->references('id')->on('cpmk')->onDelete('cascade');

            $table->unique(['parent_cpmk_id', 'child_cpmk_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cpmk_parents');
    }
};
