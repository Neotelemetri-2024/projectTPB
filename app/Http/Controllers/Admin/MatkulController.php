<?php

namespace App\Http\Controllers\Admin;

use App\Models\Matkul;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MatkulController extends Controller
{
    public function indexMatkul()
    {
        $matkuls = Matkul::Paginate(10);
        return view('admin.matakuliah', compact('matkuls'));
    }
}
