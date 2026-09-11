<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kurikulum', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique();
            $table->string('nama', 100);
            $table->year('tahun')->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('isAktif')->default(true);
            $table->timestamps();
        });

        // Backfill: setiap nilai unik pada mata_kuliah.kurikulum menjadi satu baris kurikulum.
        if (Schema::hasColumn('mata_kuliah', 'kurikulum')) {
            $existing = DB::table('mata_kuliah')
                ->whereNotNull('kurikulum')
                ->where('kurikulum', '!=', '')
                ->distinct()
                ->orderBy('kurikulum')
                ->pluck('kurikulum');

            $now = now();
            $rows = $existing->map(function ($kode) use ($now) {
                return [
                    'kode' => $kode,
                    'nama' => 'Kurikulum ' . $kode,
                    'tahun' => is_numeric($kode) ? (int) $kode : null,
                    'deskripsi' => null,
                    'isAktif' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })->all();

            if (!empty($rows)) {
                DB::table('kurikulum')->insert($rows);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kurikulum');
    }
};
