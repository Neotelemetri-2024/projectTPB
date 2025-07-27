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
        Schema::create('dosen_pengampu_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosenId')->constrained('dosen')->onDelete('cascade');
            $table->foreignId('kelasId')->constrained('kelas')->onDelete('cascade');
            $table->timestamps();
            
            // Ensure unique combination
            $table->unique(['dosenId', 'kelasId']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosen_pengampu_kelas');
    }
};
