<?php

require_once 'vendor/autoload.php';

use App\Imports\DosenImport;
use Maatwebsite\Excel\Facades\Excel;

// Test data sample
$testData = [
    [
        'NAMA' => 'Prof.Dr. Ir. Rusnam, MS',
        'NIP' => '196309041989031002'
    ],
    [
        'NAMA' => 'Dr. Renny Eka Putri, S.TP, MP',
        'NIP' => '198006212006042016'
    ],
    [
        'NAMA' => 'Ir. Ayendra Asmuti, M.Si',
        'NIP' => '196504051990101001'
    ]
];

echo "=== TEST IMPORT SAMPLE ===\n\n";

foreach ($testData as $row) {
    echo "Testing row: " . json_encode($row) . "\n";
    
    // Test generate email
    $import = new DosenImport();
    $reflection = new ReflectionClass($import);
    $method = $reflection->getMethod('generateEmailFromName');
    $method->setAccessible(true);
    
    $email = $method->invoke($import, $row['NAMA']);
    echo "Generated email: {$email}\n\n";
}

echo "=== SELESAI ===\n"; 