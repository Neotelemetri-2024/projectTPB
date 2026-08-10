<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = Illuminate\Support\Facades\DB::class;

echo "=== Verifikasi 1: Tidak ada duplikat ===\n";
$dupes = App\Models\TahunAjaranMatkul::select("mataKuliahId", "tahunAjaranId", $db::raw("COUNT(*) as count"))
    ->groupBy("mataKuliahId", "tahunAjaranId")
    ->having("count", ">", 1)
    ->get();
if ($dupes->isEmpty()) {
    echo "OK: Tidak ada duplikat ditemukan.\n\n";
} else {
    echo "MASIH ADA DUP!: " . $dupes->count() . " grup\n\n";
}

echo "=== Verifikasi 2: Data masih utuh ===\n";
$tam92 = App\Models\TahunAjaranMatkul::find(92);
echo "TAM 92 (Kewirausahaan): semester={$tam92->semester}\n";
echo "  Kelas: [" . $tam92->kelas->pluck("namaKelas")->implode(",") . "]\n";
echo "  Bobot: " . App\Models\Bobot::where("tahunAjaranMatkulId", 92)->count() . "\n";
echo "  CPMK: " . App\Models\CpmkMatKul::where("tahunAjaranMatkulId", 92)->count() . "\n";

$tam93 = App\Models\TahunAjaranMatkul::find(93);
echo "\nTAM 93 (Analisis Sistem): semester={$tam93->semester}\n";
echo "  Kelas: [" . $tam93->kelas->pluck("namaKelas")->implode(",") . "]\n";
echo "  Bobot: " . App\Models\Bobot::where("tahunAjaranMatkulId", 93)->count() . "\n";
echo "  CPMK: " . App\Models\CpmkMatKul::where("tahunAjaranMatkulId", 93)->count() . "\n";

echo "\n=== Verifikasi 3: Duplikat lama sudah dihapus ===\n";
foreach ([166, 170, 272, 275] as $id) {
    $tam = App\Models\TahunAjaranMatkul::find($id);
    echo "TAM {$id}: " . ($tam ? "MASIH ADA!" : "OK (sudah dihapus)") . "\n";
}
