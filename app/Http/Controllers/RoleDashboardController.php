<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleDashboardController extends Controller
{
    public function adminDashboard()
    {
        $user = Auth::user();
        return view('dashboard.admin', compact('user'));
    }

    public function mahasiswaDashboard()
    {
        $user = Auth::user();
        return view('dashboard.mahasiswa', compact('user'));
    }

    public function dosenDashboard()
    {
        $user = Auth::user();
        return view('dashboard.dosen', compact('user'));
    }

    public function pimpinanDashboard()
    {
        $user = Auth::user();
        return view('dashboard.pimpinan', compact('user'));
    }
}
