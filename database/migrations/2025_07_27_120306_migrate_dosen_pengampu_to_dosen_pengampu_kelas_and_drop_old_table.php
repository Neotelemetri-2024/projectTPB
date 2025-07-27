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
        // Migrate existing data from dosen_pengampu to dosen_pengampu_kelas
        $dosenPengampu = \DB::table('dosen_pengampu')->get();
        
        foreach ($dosenPengampu as $dp) {
            // Check if combination already exists
            $existing = \DB::table('dosen_pengampu_kelas')
                ->where('dosenId', $dp->dosenId)
                ->where('kelasId', $dp->kelasId)
                ->first();
            
            if (!$existing) {
                \DB::table('dosen_pengampu_kelas')->insert([
                    'dosenId' => $dp->dosenId,
                    'kelasId' => $dp->kelasId,
                    'created_at' => $dp->created_at,
                    'updated_at' => $dp->updated_at
                ]);
            }
        }

        // Drop the old dosen_pengampu table
        Schema::dropIfExists('dosen_pengampu');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate dosen_pengampu table
        Schema::create('dosen_pengampu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosenId')->constrained('dosen')->onDelete('cascade');
            $table->foreignId('kelasId')->constrained('kelas')->onDelete('cascade');
            $table->timestamps();
        });

        // Migrate data back
        $dosenPengampuKelas = \DB::table('dosen_pengampu_kelas')->get();
        
        foreach ($dosenPengampuKelas as $dpk) {
            \DB::table('dosen_pengampu')->insert([
                'dosenId' => $dpk->dosenId,
                'kelasId' => $dpk->kelasId,
                'created_at' => $dpk->created_at,
                'updated_at' => $dpk->updated_at
            ]);
        }

        // Drop the new table
        Schema::dropIfExists('dosen_pengampu_kelas');
    }
};
