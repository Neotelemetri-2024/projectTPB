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
        Schema::table('nilai', function (Blueprint $table) {
            // Add new column
            $table->foreignId('dosenPengampuKelasId')->nullable()->after('dosenPengampuId')->constrained('dosen_pengampu_kelas')->onDelete('cascade');
        });

        // Migrate existing data
        $nilaiRecords = \DB::table('nilai')->get();
        
        foreach ($nilaiRecords as $nilai) {
            // Find corresponding dosenPengampuKelas record
            $dosenPengampuKelas = \DB::table('dosen_pengampu_kelas')
                ->where('dosenId', function($query) use ($nilai) {
                    $query->select('dosenId')
                          ->from('dosen_pengampu')
                          ->where('id', $nilai->dosenPengampuId);
                })
                ->where('kelasId', function($query) use ($nilai) {
                    $query->select('kelasId')
                          ->from('dosen_pengampu')
                          ->where('id', $nilai->dosenPengampuId);
                })
                ->first();
            
            if ($dosenPengampuKelas) {
                \DB::table('nilai')
                    ->where('id', $nilai->id)
                    ->update(['dosenPengampuKelasId' => $dosenPengampuKelas->id]);
            }
        }

        // Drop old foreign key and column
        Schema::table('nilai', function (Blueprint $table) {
            $table->dropForeign(['dosenPengampuId']);
            $table->dropColumn('dosenPengampuId');
        });

        // Make new column not nullable
        Schema::table('nilai', function (Blueprint $table) {
            $table->foreignId('dosenPengampuKelasId')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            // Add back dosenPengampuId
            $table->foreignId('dosenPengampuId')->nullable()->after('mahasiswaId')->constrained('dosen_pengampu')->onDelete('cascade');
        });

        // Migrate data back
        $nilaiRecords = \DB::table('nilai')->get();
        
        foreach ($nilaiRecords as $nilai) {
            $dosenPengampu = \DB::table('dosen_pengampu')
                ->where('dosenId', function($query) use ($nilai) {
                    $query->select('dosenId')
                          ->from('dosen_pengampu_kelas')
                          ->where('id', $nilai->dosenPengampuKelasId);
                })
                ->where('kelasId', function($query) use ($nilai) {
                    $query->select('kelasId')
                          ->from('dosen_pengampu_kelas')
                          ->where('id', $nilai->dosenPengampuKelasId);
                })
                ->first();
            
            if ($dosenPengampu) {
                \DB::table('nilai')
                    ->where('id', $nilai->id)
                    ->update(['dosenPengampuId' => $dosenPengampu->id]);
            }
        }

        // Make dosenPengampuId not nullable and drop dosenPengampuKelasId
        Schema::table('nilai', function (Blueprint $table) {
            $table->foreignId('dosenPengampuId')->nullable(false)->change();
            $table->dropForeign(['dosenPengampuKelasId']);
            $table->dropColumn('dosenPengampuKelasId');
        });
    }
};
