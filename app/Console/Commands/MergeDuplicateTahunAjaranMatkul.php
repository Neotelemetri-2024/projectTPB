<?php

namespace App\Console\Commands;

use App\Models\TahunAjaranMatkul;
use App\Models\Kelas;
use App\Models\CpmkMatKul;
use App\Models\Bobot;
use App\Models\Nilai;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MergeDuplicateTahunAjaranMatkul extends Command
{
    protected $signature = 'merge:duplicate-tahun-ajaran-matkul';
    protected $description = 'Merge duplicate TahunAjaranMatkul records (same mataKuliahId + tahunAjaranId) into a single record.';

    public function handle()
    {
        $this->info('Mencari duplikat TahunAjaranMatkul...');

        $duplicateGroups = TahunAjaranMatkul::select(
            'mataKuliahId',
            'tahunAjaranId',
            DB::raw('COUNT(*) as count'),
            DB::raw('GROUP_CONCAT(id ORDER BY id ASC) as ids')
        )
            ->groupBy('mataKuliahId', 'tahunAjaranId')
            ->having('count', '>', 1)
            ->get();

        if ($duplicateGroups->isEmpty()) {
            $this->info('Tidak ada duplikat TahunAjaranMatkul ditemukan.');
            return 0;
        }

        $this->warn("Ditemukan {$duplicateGroups->count()} grup duplikat:");
        foreach ($duplicateGroups as $group) {
            $this->line("  - mataKuliahId={$group->mataKuliahId}, tahunAjaranId={$group->tahunAjaranId}: {$group->count} records ({$group->ids})");
        }

        if (!$this->confirm('Lanjutkan merge duplikat? Data akan dipindahkan ke TAM dengan ID terkecil.', true)) {
            $this->info('Dibatalkan.');
            return 0;
        }

        $totalMerged = 0;

        foreach ($duplicateGroups as $group) {
            $allDuplicates = TahunAjaranMatkul::where('mataKuliahId', $group->mataKuliahId)
                ->where('tahunAjaranId', $group->tahunAjaranId)
                ->orderBy('id', 'asc')
                ->get();

            $primary = $allDuplicates->shift(); // TAM dengan ID terkecil
            $duplicates = $allDuplicates;

            $this->warn("\nMemproses grup: mataKuliahId={$group->mataKuliahId}, tahunAjaranId={$group->tahunAjaranId}");
            $this->info("  Primary TAM ID: {$primary->id} (semester={$primary->semester})");

            foreach ($duplicates as $dup) {
                DB::beginTransaction();
                try {
                    $this->info("  Menggabungkan TAM ID {$dup->id} (semester={$dup->semester}) ke TAM ID {$primary->id}...");

                    // 1. Update kelas
                    $kelasUpdated = Kelas::where('tahunAjaranMatkulId', $dup->id)
                        ->update(['tahunAjaranMatkulId' => $primary->id]);
                    $this->line("    - {$kelasUpdated} kelas dipindahkan");

                    // 2. Update cpmk_mat_kul
                    $cpmkUpdated = CpmkMatKul::where('tahunAjaranMatkulId', $dup->id)
                        ->update(['tahunAjaranMatkulId' => $primary->id]);
                    $this->line("    - {$cpmkUpdated} CPMK dipindahkan");

                    // 3. Update bobot
                    $bobotUpdated = Bobot::where('tahunAjaranMatkulId', $dup->id)
                        ->update(['tahunAjaranMatkulId' => $primary->id]);
                    $this->line("    - {$bobotUpdated} bobot dipindahkan");

                    // 4. Update nilai
                    $nilaiUpdated = Nilai::where('tahunAjaranMatkulId', $dup->id)
                        ->update(['tahunAjaranMatkulId' => $primary->id]);
                    $this->line("    - {$nilaiUpdated} nilai dipindahkan");

                    // 5. Update semester primary jika null
                    if (is_null($primary->semester) && !is_null($dup->semester)) {
                        $primary->update(['semester' => $dup->semester]);
                        $this->line("    - Semester primary diupdate ke {$dup->semester}");
                    }

                    // 6. Hapus duplikat
                    $dup->delete();
                    $this->info("  TAM ID {$dup->id} berhasil dihapus.");

                    DB::commit();
                    $totalMerged++;
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->error("  Gagal memproses TAM ID {$dup->id}: " . $e->getMessage());
                    Log::error("MergeDuplicateTAM: Gagal merge TAM {$dup->id} ke {$primary->id}: " . $e->getMessage());
                }
            }
        }

        $this->info("\nSelesai. {$totalMerged} duplikat berhasil digabungkan.");
        return 0;
    }
}
