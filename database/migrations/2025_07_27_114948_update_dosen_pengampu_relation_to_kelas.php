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
        Schema::table('dosen_pengampu', function (Blueprint $table) {
            // Add new kelasId column
            $table->foreignId('kelasId')->nullable()->after('tahunAjaranMatkulId')->constrained('kelas')->onDelete('cascade');
        });

        // Migrate existing data
        $dosenPengampu = \DB::table('dosen_pengampu')->get();
        
        foreach ($dosenPengampu as $dp) {
            // Find the kelas that belongs to this tahunAjaranMatkul
            $kelas = \DB::table('kelas')
                ->where('tahunAjaranMatkulId', $dp->tahunAjaranMatkulId)
                ->first();
            
            if ($kelas) {
                \DB::table('dosen_pengampu')
                    ->where('id', $dp->id)
                    ->update(['kelasId' => $kelas->id]);
            }
        }

        // Drop old foreign key and column
        Schema::table('dosen_pengampu', function (Blueprint $table) {
            $table->dropForeign(['tahunAjaranMatkulId']);
            $table->dropColumn('tahunAjaranMatkulId');
        });

        // Make kelasId not nullable
        Schema::table('dosen_pengampu', function (Blueprint $table) {
            $table->foreignId('kelasId')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dosen_pengampu', function (Blueprint $table) {
            // Add back tahunAjaranMatkulId
            $table->foreignId('tahunAjaranMatkulId')->nullable()->after('dosenId')->constrained('tahun_ajaran_matkul')->onDelete('cascade');
        });

        // Migrate data back
        $dosenPengampu = \DB::table('dosen_pengampu')->get();
        
        foreach ($dosenPengampu as $dp) {
            $kelas = \DB::table('kelas')->find($dp->kelasId);
            if ($kelas) {
                \DB::table('dosen_pengampu')
                    ->where('id', $dp->id)
                    ->update(['tahunAjaranMatkulId' => $kelas->tahunAjaranMatkulId]);
            }
        }

        // Make tahunAjaranMatkulId not nullable and drop kelasId
        Schema::table('dosen_pengampu', function (Blueprint $table) {
            $table->foreignId('tahunAjaranMatkulId')->nullable(false)->change();
            $table->dropForeign(['kelasId']);
            $table->dropColumn('kelasId');
        });
    }
};
