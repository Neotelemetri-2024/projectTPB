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
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas'); // Nama kelas, misal 'A', 'B', 'Reguler'

            // Foreign Key untuk id_tahun_ajaran_matkul
            $table->unsignedBigInteger('id_tahun_ajaran_matkul');
            $table->foreign('id_tahun_ajaran_matkul')
                ->references('id')
                ->on('tahun_ajaran_matkuls')
                ->onDelete('cascade'); // Jika TahunAjaranMatkul dihapus, kelas terkait juga dihapus
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
