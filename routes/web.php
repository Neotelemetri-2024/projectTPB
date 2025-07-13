<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\DosenController;
use App\Http\Controllers\Admin\TahunAjaranController;
use App\Http\Controllers\Admin\MataKuliahController;
use Illuminate\Support\Facades\Route;

// Default route
Route::get('/', function () {
    return view('welcome');
});

// Dashboard Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Admin Dashboard
    Route::middleware('admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
        
        // Mahasiswa CRUD Routes
        Route::resource('admin/mahasiswa', MahasiswaController::class)->names([
            'index' => 'admin.mahasiswa.index',
            'create' => 'admin.mahasiswa.create',
            'store' => 'admin.mahasiswa.store',
            'show' => 'admin.mahasiswa.show',
            'edit' => 'admin.mahasiswa.edit',
            'update' => 'admin.mahasiswa.update',
            'destroy' => 'admin.mahasiswa.destroy',
        ]);
        
        // Dosen CRUD Routes
        Route::resource('admin/dosen', DosenController::class)->names([
            'index' => 'admin.dosen.index',
            'create' => 'admin.dosen.create',
            'store' => 'admin.dosen.store',
            'show' => 'admin.dosen.show',
            'edit' => 'admin.dosen.edit',
            'update' => 'admin.dosen.update',
            'destroy' => 'admin.dosen.destroy',
        ]);
        
        // Data Master Routes
        Route::resource('admin/tahun-ajaran', TahunAjaranController::class)->names([
            'index' => 'admin.tahun-ajaran.index',
            'store' => 'admin.tahun-ajaran.store',
            'show' => 'admin.tahun-ajaran.show',
            'update' => 'admin.tahun-ajaran.update',
            'destroy' => 'admin.tahun-ajaran.destroy',
        ]);
        
        Route::resource('admin/mata-kuliah', MataKuliahController::class)->names([
            'index' => 'admin.mata-kuliah.index',
            'store' => 'admin.mata-kuliah.store',
            'show' => 'admin.mata-kuliah.show',
            'update' => 'admin.mata-kuliah.update',
            'destroy' => 'admin.mata-kuliah.destroy',
        ]);
    });
    
    // Dosen Dashboard
    Route::middleware('dosen')->group(function () {
        Route::get('/dosen/dashboard', [DashboardController::class, 'dosenDashboard'])->name('dosen.dashboard');
    });
    
    // Mahasiswa Dashboard
    Route::middleware('mahasiswa')->group(function () {
        Route::get('/mahasiswa/dashboard', [DashboardController::class, 'mahasiswaDashboard'])->name('mahasiswa.dashboard');
    });
    
    // Pimpinan Dashboard
    Route::middleware('pimpinan')->group(function () {
        Route::get('/pimpinan/dashboard', [DashboardController::class, 'pimpinanDashboard'])->name('pimpinan.dashboard');
    });
});

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
