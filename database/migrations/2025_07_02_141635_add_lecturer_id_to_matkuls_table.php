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
        Schema::table('matkuls', function (Blueprint $table) {
            $table->unsignedBigInteger('lecturer_id')->nullable()->after('id');

            $table->foreign('lecturer_id')
                ->references('id')
                ->on('lecturers')
                ->onDelete('set null'); // Supaya kalau dosen dihapus, matkul tetap ada
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matkuls', function (Blueprint $table) {
            $table->dropForeign(['lecturer_id']);
            $table->dropColumn('lecturer_id');
        });
    }
};
