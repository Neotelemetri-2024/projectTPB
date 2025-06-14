<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\DirectorController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
});

// Mahasiswa
Route::middleware(['auth', 'student'])->group(function () {
    Route::get('/student', [StudentController::class, 'index'])->name('student.dashboard');
});

// Dosen
Route::middleware(['auth', 'lecturer'])->group(function () {
    Route::get('/lecturer', [LecturerController::class, 'index'])->name('lecturer.dashboard');
});

// Pimpinan
Route::middleware(['auth', 'director'])->group(function () {
    Route::get('/director', [DirectorController::class, 'index'])->name('director.dashboard');
});


require __DIR__ . '/auth.php';
