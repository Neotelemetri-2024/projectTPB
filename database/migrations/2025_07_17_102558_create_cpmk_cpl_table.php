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
        Schema::create('cpmk_cpl', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cplId')->constrained('cpl')->onDelete('cascade');
            $table->foreignId('cpmkId')->constrained('cpmk')->onDelete('cascade');
            $table->timestamps();

            // Add unique constraint to prevent duplicate relationships
            $table->unique(['cplId', 'cpmkId']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cpmk_cpl');
    }
};
