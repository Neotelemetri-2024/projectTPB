<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cpl', function (Blueprint $table) {
            $table->unsignedTinyInteger('nilaiMinimal')->default(60)->after('deskripsi');
            $table->unsignedTinyInteger('targetPersen')->default(60)->after('nilaiMinimal');
        });
    }

    public function down(): void
    {
        Schema::table('cpl', function (Blueprint $table) {
            $table->dropColumn(['nilaiMinimal', 'targetPersen']);
        });
    }
};
