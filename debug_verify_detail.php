<?php
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TAM 92 (Kewirausahaan) - Detail Kelas ===\n";
$kelas92 = App\Models\Kelas::where("tahunAjaranMatkulId", 92)->get();
foreach ($kelas92 as $k) {
    echo "  Kelas ID {$k->id}: {$k->namaKelas}\n";
}

echo "\n=== TAM 92 - Bobot ===\n";
$bobot92 = App\Models\Bobot::where("tahunAjaranMatkulId", 92)->get();
echo "  Total: " . $bobot92->count() . "\n";

echo "\n=== TAM 92 - CPMK ===\n";
$cpmk92 = App\Models\CpmkMatKul::where("tahunAjaranMatkulId", 92)->get();
echo "  Total: " . $cpmk92->count() . "\n";

// Check if bobot/cpmk were under TAM 166
echo "\n=== Cek bobot/CPMK dengan tahunAjaranMatkulId 166 ===\n";
echo "  Bobot: " . App\Models\Bobot::where("tahunAjaranMatkulId", 166)->count() . "\n";
echo "  CPMK: " . App\Models\CpmkMatKul::where("tahunAjaranMatkulId", 166)->count() . "\n";

echo "\n=== TAM 93 (Analisis Sistem) - Detail Kelas ===\n";
$kelas93 = App\Models\Kelas::where("tahunAjaranMatkulId", 93)->get();
foreach ($kelas93 as $k) {
    $dpCount = $k->dosenPengampuKelas()->count();
    $kmCount = $k->kelasMahasiswa()->count();
    echo "  Kelas ID {$k->id}: {$k->namaKelas} (dosenPengampu={$dpCount}, kelasMhs={$kmCount})\n";
}
