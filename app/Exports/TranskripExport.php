<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;

class TranskripExport
{
    protected $mahasiswa;
    protected $matkulDiambil;
    protected $totalSks;
    protected $ipk;

    public function __construct($mahasiswa, $matkulDiambil, $totalSks, $ipk)
    {
        $this->mahasiswa = $mahasiswa;
        $this->matkulDiambil = $matkulDiambil;
        $this->totalSks = $totalSks;
        $this->ipk = $ipk;
    }

    public function view(): View
    {
        return view('exports.transkrip-pdf', [
            'mahasiswa' => $this->mahasiswa,
            'matkulDiambil' => $this->matkulDiambil,
            'totalSks' => $this->totalSks,
            'ipk' => $this->ipk,
        ]);
    }
}
