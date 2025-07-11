<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
//admin 
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CplController;
use App\Http\Controllers\Admin\TahunAjaranController;
use App\Http\Controllers\Admin\MatkulController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\CpmkController;
//dosen 

//dashboard
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\DirectorController;
use App\Models\TahunAjaran;

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

    // ───── USER MANAGEMENT LECTURER AND DIRECTOR─────
    Route::get('/users/lecturers', [UserController::class, 'indexLecturers'])->name('users.lecturers');
    Route::post('/users/lecturers', [UserController::class, 'storeLecturer'])->name('users.lecturers.store');
    Route::get('/users/lecturers/{id}/edit', [UserController::class, 'editLecturer'])->name('users.lecturers.edit');
    Route::put('/users/lecturers/{id}', [UserController::class, 'updateLecturer'])->name('users.lecturers.update');
    Route::delete('/users/lecturers/{id}', [UserController::class, 'destroyLecturer'])->name('users.lecturers.destroy');
    Route::post('/users/lecturers/import', [UserController::class, 'importLecturers'])->name('users.lecturers.import');

    // ───── CPL MANAGEMENT─────
    Route::get('/cpl', [CplController::class, 'indexCpl'])->name('cpl');
    Route::post('/cpl/storeCpl', [CplController::class, 'storeCpl'])->name('cpl.storeCpl');
    Route::get('/cpl/{id}/edit', [CplController::class, 'editCpl'])->name('cpl.edit');
    Route::put('/cpl/{id}', [CplController::class, 'updateCpl'])->name('cpl.update');
    Route::delete('/cpl/{id}', [CplController::class, 'destroyCpl'])->name('cpl.destroy');

    // ───── CPMK MANAGEMENT─────
    Route::get('/cpmk', [CpmkController::class, 'index'])->name('cpmk');
    Route::post('/cpmk/storeCpmk', [CpmkController::class, 'storeCpmk'])->name('cpmk.store');
    Route::get('/cpmk/{id}/edit', [CpmkController::class, 'editCpmk'])->name('cpmk.edit');
    Route::put('/cpmk/{id}', [CpmkController::class, 'updateCpmk'])->name('cpmk.update');
    Route::delete('/cpmk/{id}', [CpmkController::class, 'destroyCpmk'])->name('cpmk.destroy');

    // ───── TAHUN AJARAN MANAGEMENT─────
    Route::get('/tahunajaran', [TahunAjaranController::class, 'indexTahunAjaran'])->name('tahunajaran');
    Route::post('/tahunajaran/storeTahunAjaran', [TahunAjaranController::class, 'storeTahunAjaran'])->name('admin.tahunajaran.storeTahunAjaran');
    Route::get('/tahunajaran/{id}/get', [TahunAjaranController::class, 'getTahunAjaran'])->name('tahunajaran.get');
    Route::put('/tahunajaran/{id}', [TahunAjaranController::class, 'updateTahunAjaran'])->name('tahunajaran.update');
    Route::delete('/tahunajaran/{id}', [TahunAjaranController::class, 'destroyTahunAjaran'])->name('tahunajaran.destroy');

    // ───── MATKUL MANAGEMENT─────
    Route::get('/matakuliah', [MatkulController::class, 'indexMatkul'])->name('matakuliah');
    Route::post('/matakuliah/storematkul', [MatkulController::class, 'store'])->name('matakuliah.storematkul');
    Route::get('/matakuliah/{id}/get', [MatkulController::class, 'getMatkul'])->name('matakuliah.get');
    Route::put('/matakuliah/{id}', [MatkulController::class, 'updateMatkul'])->name('matakuliah.update');
    Route::delete('/matakuliah/{id}', [MatkulController::class, 'destroyMatkul'])->name('matakuliah.destroy');

    // ───── KELAS MANAGEMENT─────
    Route::get('/kelas/matakuliah/{matkulId}', [KelasController::class, 'indexKelas'])->name('kelas');
    Route::get('/kelas/matakuliah/{matkulId}/storekelas', [KelasController::class, 'store'])->name('kelas.storekelas');
    // Route::get('/kelas', [KelasController::class, 'indexKelas'])->name('kelas');
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
