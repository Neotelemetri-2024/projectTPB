<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            // Speeds up MAX(updated_at) lookups used by grading screens.
            $table->index(['tahunAjaranMatkulId', 'updated_at'], 'nilai_tam_updated_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            $table->dropIndex('nilai_tam_updated_at_index');
        });
    }
};
