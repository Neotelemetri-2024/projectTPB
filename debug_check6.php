<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pairs = [
    ['Kewirausahaan', 92, 166],
    ['Analisis Sistem', 93, 170],
    ['Pengetahuan Bahan Teknik', 258, 272],
    ['Manajemen Agroindustri', 274, 275],
];

foreach ($pairs as $p) {
    list($nama, $a, $b) = $p;
    echo "=== {$nama} (TAM {$a} vs TAM {$b}) ===\n";
    foreach ([$a, $b] as $id) {
        $tam = App\Models\TahunAjaranMatkul::find($id);
        if (!$tam) {
            echo "  TAM {$id}: NOT FOUND\n";
            continue;
        }
        $kelas = $tam->kelas;
        $kelasMhsCount = 0;
        $dosenCount = 0;
        foreach ($kelas as $k) {
            $kelasMhsCount += $k->kelasMahasiswa()->count();
            $dosenCount += $k->dosenPengampuKelas()->count();
        }
        $bobotCount = App\Models\Bobot::where('tahunAjaranMatkulId', $id)->count();
        $cpmkCount = App\Models\CpmkMatKul::where('tahunAjaranMatkulId', $id)->count();
        $nilaiCount = App\Models\Nilai::where('tahunAjaranMatkulId', $id)->count();
        echo "  TAM {$id}: semester={$tam->semester}, kelas=[" . $kelas->pluck('namaKelas')->implode(',') . "], kelasMhs={$kelasMhsCount}, dosenPengampu={$dosenCount}, bobot={$bobotCount}, cpmkMatKul={$cpmkCount}, nilai={$nilaiCount}\n";
    }
}
