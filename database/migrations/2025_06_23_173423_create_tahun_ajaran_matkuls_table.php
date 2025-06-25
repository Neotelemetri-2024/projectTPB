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
        Schema::create('tahun_ajaran_matkuls', function (Blueprint $table) {
            $table->id(); // Primary Key untuk tabel pivot ini

            // Foreign Key untuk id_matkul (dari tabel matkuls)
            $table->unsignedBigInteger('matkul_id');
            $table->foreign('matkul_id')->references('id')->on('matkuls')->onDelete('cascade');

            // Foreign Key untuk id_tahun_ajaran (dari tabel tahun_ajarans)
            $table->unsignedBigInteger('tahun_ajaran_id');
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajarans')->onDelete('cascade');

            // kolom tambahan di tabel pivot ini
            $table->integer('semester_studi'); // Semester untuk matkul di tahun ajaran ini
            $table->integer('sks'); // Jumlah SKS untuk matkul di tahun ajaran ini


            // Ini mencegah satu mata kuliah terdaftar dua kali di tahun ajaran yang sama
            $table->unique(['matkul_id', 'tahun_ajaran_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahun_ajaran_matkuls');
    }
};
