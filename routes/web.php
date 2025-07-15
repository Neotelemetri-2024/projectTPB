<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\DosenController;
use App\Http\Controllers\Admin\TahunAjaranController;
use App\Http\Controllers\Admin\MataKuliahController;
use App\Http\Controllers\Admin\CplController;
use App\Http\Controllers\Admin\KomponenController;
use App\Http\Controllers\Admin\TahunAjaranMatkulController;
use App\Http\Controllers\Dosen\MataKuliahController as DosenMataKuliahController;
use App\Http\Controllers\Dosen\CpmkController as DosenCpmkController;
use App\Http\Controllers\Dosen\KomponenPenilaianController as DosenKomponenPenilaianController;
use App\Http\Controllers\Dosen\BobotKomponenController;
use App\Http\Controllers\Dosen\NilaiController as DosenNilaiController;
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

        Route::resource('admin/cpl', CplController::class)->except(['show'])->names([
            'index' => 'admin.cpl.index',
            'create' => 'admin.cpl.create',
            'store' => 'admin.cpl.store',
            'edit' => 'admin.cpl.edit',
            'update' => 'admin.cpl.update',
            'destroy' => 'admin.cpl.destroy',
        ]);
        Route::resource('admin/komponen', KomponenController::class)->names([
            'index' => 'admin.komponen.index',
            'store' => 'admin.komponen.store',
            'show' => 'admin.komponen.show',
            'update' => 'admin.komponen.update',
            'destroy' => 'admin.komponen.destroy',
        ]);

        // Tahun Ajaran Matkul Routes
        Route::resource('admin/tahun-ajaran-matkul', TahunAjaranMatkulController::class)->names([
            'index' => 'admin.tahun-ajaran-matkul.index',
            'create' => 'admin.tahun-ajaran-matkul.create',
            'store' => 'admin.tahun-ajaran-matkul.store',
            'show' => 'admin.tahun-ajaran-matkul.show',
            'edit' => 'admin.tahun-ajaran-matkul.edit',
            'update' => 'admin.tahun-ajaran-matkul.update',
            'destroy' => 'admin.tahun-ajaran-matkul.destroy',
        ]);

        // Additional routes for managing students and lecturers
        Route::get('admin/tahun-ajaran-matkul/{id}/manage-mahasiswa', [TahunAjaranMatkulController::class, 'manageMahasiswa'])->name('admin.tahun-ajaran-matkul.manage-mahasiswa');
        Route::post('admin/tahun-ajaran-matkul/{id}/bulk-add-mahasiswa', [TahunAjaranMatkulController::class, 'bulkAddMahasiswa'])->name('admin.tahun-ajaran-matkul.bulk-add-mahasiswa');
        Route::post('admin/tahun-ajaran-matkul/{id}/add-mahasiswa', [TahunAjaranMatkulController::class, 'addMahasiswa'])->name('admin.tahun-ajaran-matkul.add-mahasiswa');
        Route::delete('admin/tahun-ajaran-matkul/{id}/remove-mahasiswa/{mahasiswaId}', [TahunAjaranMatkulController::class, 'removeMahasiswa'])->name('admin.tahun-ajaran-matkul.remove-mahasiswa');
        Route::post('admin/tahun-ajaran-matkul/{id}/add-dosen', [TahunAjaranMatkulController::class, 'addDosen'])->name('admin.tahun-ajaran-matkul.add-dosen');
        Route::delete('admin/tahun-ajaran-matkul/{id}/remove-dosen/{dosenId}', [TahunAjaranMatkulController::class, 'removeDosen'])->name('admin.tahun-ajaran-matkul.remove-dosen');
    });

    // Dosen Dashboard
    Route::middleware('dosen')->group(function () {
        Route::get('/dosen/dashboard', [DashboardController::class, 'dosenDashboard'])->name('dosen.dashboard');

        // Kelola CPMK - List Mata Kuliah
        Route::get('/dosen/mata-kuliah/cpmk', [DosenCpmkController::class, 'index'])->name('dosen.cpmk.index');
        Route::get('/dosen/mata-kuliah/{tahunAjaranMatkulId}/cpmk', [DosenCpmkController::class, 'showMataKuliah'])->name('dosen.cpmk.show');
        Route::get('/dosen/mata-kuliah/{tahunAjaranMatkulId}/cpmk/create', [DosenCpmkController::class, 'create'])->name('dosen.cpmk.create');
        Route::post('/dosen/mata-kuliah/{tahunAjaranMatkulId}/cpmk', [DosenCpmkController::class, 'store'])->name('dosen.cpmk.store');
        Route::get('/dosen/mata-kuliah/{tahunAjaranMatkulId}/cpmk/{id}', [DosenCpmkController::class, 'showDetail'])->name('dosen.cpmk.detail');
        Route::get('/dosen/mata-kuliah/{tahunAjaranMatkulId}/cpmk/{id}/edit', [DosenCpmkController::class, 'edit'])->name('dosen.cpmk.edit');
        Route::put('/dosen/mata-kuliah/{tahunAjaranMatkulId}/cpmk/{id}', [DosenCpmkController::class, 'update'])->name('dosen.cpmk.update');
        Route::delete('/dosen/mata-kuliah/{tahunAjaranMatkulId}/cpmk/{id}', [DosenCpmkController::class, 'destroy'])->name('dosen.cpmk.destroy');
        Route::post('/dosen/mata-kuliah/{tahunAjaranMatkulId}/cpmk/bulk-action', [DosenCpmkController::class, 'bulkAction'])->name('dosen.cpmk.bulk-action');
        // Bobot Komponen Penilaian Management untuk CPMK
        Route::get('/dosen/mata-kuliah/{tahunAjaranMatkulId}/cpmk/bobot-komponen/bulk/create', [BobotKomponenController::class, 'bulkCreate'])->name('dosen.bobot-komponen.bulk-create');
        Route::post('/dosen/mata-kuliah/{tahunAjaranMatkulId}/cpmk/bobot-komponen/bulk', [BobotKomponenController::class, 'bulkStore'])->name('dosen.bobot-komponen.bulk-store');

        // Kelola Nilai Mahasiswa - List Mata Kuliah
        Route::get('/dosen/nilai', [DosenNilaiController::class, 'index'])->name('dosen.nilai.index');
        Route::get('/dosen/nilai/{id}', [DosenNilaiController::class, 'show'])->name('dosen.nilai.show');
        Route::get('/dosen/nilai/{matkul}/mahasiswa/{mahasiswa}/input', [DosenNilaiController::class, 'input'])->name('dosen.nilai.input');
        Route::post('/dosen/nilai/{matkul}/mahasiswa/{mahasiswa}/store', [DosenNilaiController::class, 'store'])->name('dosen.nilai.store');
        Route::post('/dosen/nilai/{id}/bulk-store', [DosenNilaiController::class, 'bulkStore'])->name('dosen.nilai.bulk-store');
        Route::post('/dosen/nilai/{id}/individual-store', [DosenNilaiController::class, 'storeIndividual'])->name('dosen.nilai.individual-store');
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
