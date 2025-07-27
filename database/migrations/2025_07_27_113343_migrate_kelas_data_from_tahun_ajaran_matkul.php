<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pindahkan data kelas dari tahun_ajaran_matkul ke tabel kelas
        $tahunAjaranMatkuls = DB::table('tahun_ajaran_matkul')->get();
        
        foreach ($tahunAjaranMatkuls as $tam) {
            // Buat kelas berdasarkan data yang ada
            DB::table('kelas')->insert([
                'namaKelas' => $tam->kelas,
                'tahunAjaranMatkulId' => $tam->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Hapus kolom kelas dari tahun_ajaran_matkul
        Schema::table('tahun_ajaran_matkul', function (Blueprint $table) {
            $table->dropColumn('kelas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tambahkan kembali kolom kelas ke tahun_ajaran_matkul
        Schema::table('tahun_ajaran_matkul', function (Blueprint $table) {
            $table->string('kelas')->nullable();
        });

        // Pindahkan data kembali
        $kelas = DB::table('kelas')->get();
        foreach ($kelas as $k) {
            DB::table('tahun_ajaran_matkul')
                ->where('id', $k->tahunAjaranMatkulId)
                ->update(['kelas' => $k->namaKelas]);
        }
    }
};
