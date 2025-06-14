<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DirectorController extends Controller
{
    public function index()
    {
        return view('director.dashboard');
    }
}
