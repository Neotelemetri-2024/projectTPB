<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mata_kuliah', function (Blueprint $table) {
            $table->unsignedBigInteger('kurikulumId')->nullable()->after('kodeMatkul');
        });

        // Backfill kurikulumId dari kolom string kurikulum.
        if (Schema::hasColumn('mata_kuliah', 'kurikulum')) {
            DB::statement('
                UPDATE mata_kuliah mk
                JOIN kurikulum k ON k.kode = mk.kurikulum
                SET mk.kurikulumId = k.id
            ');
        }

        // Sisa baris (mis. kurikulum kosong) diarahkan ke kurikulum pertama yang tersedia.
        if (DB::table('mata_kuliah')->whereNull('kurikulumId')->exists()) {
            $fallbackId = DB::table('kurikulum')->orderBy('kode')->value('kode');
            if ($fallbackId === null) {
                DB::table('kurikulum')->insert([
                    'kode' => 'default',
                    'nama' => 'Kurikulum Default',
                    'tahun' => null,
                    'deskripsi' => 'Dibuat otomatis saat migrasi kurikulumId.',
                    'isAktif' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $fallbackId = 'default';
            }
            DB::statement('
                UPDATE mata_kuliah mk
                JOIN kurikulum k ON k.kode = ?
                SET mk.kurikulumId = k.id
                WHERE mk.kurikulumId IS NULL
            ', [$fallbackId]);
        }

        Schema::table('mata_kuliah', function (Blueprint $table) {
            $table->dropUnique('mata_kuliah_kode_kurikulum_unique');
            $table->dropColumn('kurikulum');
        });

        DB::statement('ALTER TABLE mata_kuliah MODIFY kurikulumId BIGINT UNSIGNED NOT NULL');

        Schema::table('mata_kuliah', function (Blueprint $table) {
            $table->foreign('kurikulumId')->references('id')->on('kurikulum')->onDelete('restrict');
            $table->unique(['kodeMatkul', 'kurikulumId'], 'mata_kuliah_kode_kurikulum_unique');
        });
    }

    public function down(): void
    {
        Schema::table('mata_kuliah', function (Blueprint $table) {
            $table->dropUnique('mata_kuliah_kode_kurikulum_unique');
            $table->dropForeign(['kurikulumId']);
            $table->string('kurikulum')->after('kodeMatkul')->nullable();
        });

        DB::statement('
            UPDATE mata_kuliah mk
            JOIN kurikulum k ON k.id = mk.kurikulumId
            SET mk.kurikulum = k.kode
        ');

        Schema::table('mata_kuliah', function (Blueprint $table) {
            $table->dropColumn('kurikulumId');
            $table->unique(['kodeMatkul', 'kurikulum'], 'mata_kuliah_kode_kurikulum_unique');
        });
    }
};
