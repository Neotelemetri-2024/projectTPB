<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
//admin 
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
//dashboard
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\DirectorController;



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==============================
// Admin Routes
// ==============================

Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    // ───── USER MANAGEMENT STUDENT─────
    Route::get('/users/students', [UserController::class, 'indexStudents'])->name('users.students');
    Route::post('/users/students', [UserController::class, 'storeStudent'])->name('users.students.store');
    Route::post('/users/students/import', [UserController::class, 'import'])->name('users.students.import');
    Route::get('/users/students/{id}/edit', [UserController::class, 'editStudent'])->name('users.students.edit');
    Route::put('/users/students/{id}', [UserController::class, 'updateStudent'])->name('users.students.update');
    Route::delete('/users/students/{id}', [UserController::class, 'destroyStudent'])->name('users.students.destroy');

    // ───── USER MANAGEMENT LECTURE─────
    Route::get('/users/lecturers', [UserController::class, 'indexLecturers'])->name('users.lecturers');
    Route::post('/users/lecturers', [UserController::class, 'storeLecturer'])->name('users.lecturers.store');
    Route::put('/users/lecturers/{id}', [UserController::class, 'updateLecturer'])->name('users.lecturers.update');
    Route::delete('/users/lecturers/{id}', [UserController::class, 'destroyLecturer'])->name('users.lecturers.destroy');
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
