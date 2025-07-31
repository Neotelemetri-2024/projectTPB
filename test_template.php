<?php

require_once 'vendor/autoload.php';

use App\Exports\DosenTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

// Test export template
try {
    $export = new DosenTemplateExport();
    $fileName = 'test_template_dosen.xlsx';
    
    // Simulasi download
    Excel::store($export, $fileName, 'local');
    
    echo "Template berhasil dibuat: {$fileName}\n";
    echo "File disimpan di: " . storage_path('app/' . $fileName) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 