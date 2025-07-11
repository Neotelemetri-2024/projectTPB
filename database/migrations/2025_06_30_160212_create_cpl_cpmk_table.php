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
        Schema::create('cpl_cpmk', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cpl_id');
            $table->unsignedBigInteger('cpmk_id');
            $table->timestamps();

            $table->foreign('cpl_id')->references('id')->on('cpl')->onDelete('cascade');
            $table->foreign('cpmk_id')->references('id')->on('cpmk')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cpl_cpmk');
    }
};
