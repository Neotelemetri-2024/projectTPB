<?php

use App\Models\Mahasiswa;
use App\Models\Cpl;
use App\Models\Bobot;
use App\Models\Nilai;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Auth;

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$mahasiswa = Mahasiswa::query()->has('nilai')->first() ?? Mahasiswa::first();
if (!$mahasiswa) {
    fwrite(STDERR, "No mahasiswa found\n");
    exit(1);
}

$controller = app(App\Http\Controllers\CapaianController::class);
$ref = new ReflectionClass($controller);

$buildCpl = $ref->getMethod('buildCplData');
$buildCpl->setAccessible(true);
$buildAkademik = $ref->getMethod('buildAcademicSummary');
$buildAkademik->setAccessible(true);
$formatTgl = $ref->getMethod('formatTanggalIndonesia');
$formatTgl->setAccessible(true);

$cplData = $buildCpl->invoke($controller, $mahasiswa->id, null, null, false);
$akademik = $buildAkademik->invoke($controller, $mahasiswa);
$institution = config('institution');
$logoPath = public_path('images/logo-unand.png');
$logoBase64 = is_file($logoPath)
    ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
    : null;

$html = view('exports.capaian-pdf', [
    'mahasiswa' => $mahasiswa,
    'cplData' => $cplData,
    'akademik' => $akademik,
    'institution' => $institution,
    'logoBase64' => $logoBase64,
    'tanggalCetak' => $formatTgl->invoke($controller, now()),
])->render();

$options = new Options();
$options->set('defaultFont', 'DejaVu Sans');
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$out = storage_path('app/sample-surat-capaian.pdf');
file_put_contents($out, $dompdf->output());

echo "OK mahasiswa={$mahasiswa->nama} nim={$mahasiswa->nim}\n";
echo "CPL rows=" . count($cplData) . " IPK=" . ($akademik['ipk'] ?? '-') . "\n";
echo "PDF: {$out} size=" . filesize($out) . " bytes\n";
