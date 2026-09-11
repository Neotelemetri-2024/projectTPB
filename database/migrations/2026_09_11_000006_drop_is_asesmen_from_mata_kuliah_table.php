<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('mata_kuliah', 'isAsesmen')) {
            return;
        }

        Schema::table('mata_kuliah', function (Blueprint $table) {
            $table->dropColumn('isAsesmen');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('mata_kuliah', 'isAsesmen')) {
            return;
        }

        Schema::table('mata_kuliah', function (Blueprint $table) {
            $table->boolean('isAsesmen')->default(false)->after('jenis');
        });
    }
};
