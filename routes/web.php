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
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Dosen\MataKuliahController as DosenMataKuliahController;
use App\Http\Controllers\Dosen\CpmkController as DosenCpmkController;
use App\Http\Controllers\Dosen\KomponenPenilaianController as DosenKomponenPenilaianController;
use App\Http\Controllers\Dosen\BobotKomponenController;
use App\Http\Controllers\Dosen\NilaiController as DosenNilaiController;
use App\Http\Controllers\KHSController as KHSController;
use Illuminate\Support\Facades\Route;

// Default route - redirect to dashboard based on user role
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'dosen':
                return redirect()->route('dosen.dashboard');
            case 'mahasiswa':
                return redirect()->route('mahasiswa.dashboard');
            case 'pimpinan':
                return redirect()->route('pimpinan.dashboard');
            default:
                return redirect()->route('dashboard');
        }
    }
    return redirect()->route('login');
});

// Dashboard Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'dosen':
                return redirect()->route('dosen.dashboard');
            case 'mahasiswa':
                return redirect()->route('mahasiswa.dashboard');
            case 'pimpinan':
                return redirect()->route('pimpinan.dashboard');
            default:
                return redirect()->route('admin.dashboard');
        }
    })->name('dashboard');

    // Admin Dashboard
    Route::middleware('admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

        // Mahasiswa CRUD Routes
        Route::delete('/admin/mahasiswa/bulk-destroy', [MahasiswaController::class, 'bulkDestroy'])->name('admin.mahasiswa.bulk-destroy');
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
        Route::delete('/admin/dosen/bulk-destroy', [DosenController::class, 'bulkDestroy'])->name('admin.dosen.bulk-destroy');
        Route::resource('admin/dosen', DosenController::class)->names([
            'index' => 'admin.dosen.index',
            'create' => 'admin.dosen.create',
            'store' => 'admin.dosen.store',
            'show' => 'admin.dosen.show',
            'edit' => 'admin.dosen.edit',
            'update' => 'admin.dosen.update',
            'destroy' => 'admin.dosen.destroy',
        ]);

        // Import/Export routes for dosen
        Route::get('/admin/dosen/export/template', [DosenController::class, 'exportTemplate'])->name('admin.dosen.export-template');
        Route::post('/admin/dosen/import', [DosenController::class, 'importDosen'])->name('admin.dosen.import');

        // Data Master Routes
        Route::resource('admin/tahun-ajaran', TahunAjaranController::class)->names([
            'index' => 'admin.tahun-ajaran.index',
            'store' => 'admin.tahun-ajaran.store',
            'show' => 'admin.tahun-ajaran.show',
            'update' => 'admin.tahun-ajaran.update',
            'destroy' => 'admin.tahun-ajaran.destroy',
        ]);

        Route::delete('/admin/mata-kuliah/bulk-destroy', [MataKuliahController::class, 'bulkDestroy'])->name('admin.mata-kuliah.bulk-destroy');
        Route::resource('admin/mata-kuliah', MataKuliahController::class)->names([
            'index' => 'admin.mata-kuliah.index',
            'store' => 'admin.mata-kuliah.store',
            'show' => 'admin.mata-kuliah.show',
            'update' => 'admin.mata-kuliah.update',
            'destroy' => 'admin.mata-kuliah.destroy',
        ]);

        // Import/Export routes for mata kuliah
        Route::get('/admin/mata-kuliah/export/template', [MataKuliahController::class, 'exportTemplate'])->name('admin.mata-kuliah.export-template');
        Route::post('/admin/mata-kuliah/import', [MataKuliahController::class, 'import'])->name('admin.mata-kuliah.import');

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
        // IMPORTANT: These specific routes MUST be before the resource route,
        // otherwise the resource's {id} wildcard will catch them first.
        Route::delete('/admin/tahun-ajaran-matkul/bulk-destroy', [TahunAjaranMatkulController::class, 'bulkDestroy'])->name('admin.tahun-ajaran-matkul.bulk-destroy');
        Route::get('/admin/tahun-ajaran-matkul/export/template', [TahunAjaranMatkulController::class, 'exportTemplate'])->name('admin.tahun-ajaran-matkul.export-template');
        Route::post('/admin/tahun-ajaran-matkul/import', [TahunAjaranMatkulController::class, 'import'])->name('admin.tahun-ajaran-matkul.import');
        Route::get('/admin/tahun-ajaran-matkul/import-status', [TahunAjaranMatkulController::class, 'importStatus'])->name('admin.tahun-ajaran-matkul.import-status');
        Route::post('/admin/tahun-ajaran-matkul/duplicate', [TahunAjaranMatkulController::class, 'duplicateFromPreviousYear'])->name('admin.tahun-ajaran-matkul.duplicate');

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
        Route::delete('admin/tahun-ajaran-matkul/{id}/remove-mahasiswa/{mahasiswaId}/{kelasId}', [TahunAjaranMatkulController::class, 'removeMahasiswa'])->name('admin.tahun-ajaran-matkul.remove-mahasiswa');
        Route::post('admin/tahun-ajaran-matkul/{id}/add-dosen', [TahunAjaranMatkulController::class, 'addDosen'])->name('admin.tahun-ajaran-matkul.add-dosen');
        Route::delete('admin/tahun-ajaran-matkul/{id}/remove-dosen/{dosenId}/{kelasId}', [TahunAjaranMatkulController::class, 'removeDosen'])->name('admin.tahun-ajaran-matkul.remove-dosen');
        Route::post('admin/tahun-ajaran-matkul/{id}/add-kelas', [TahunAjaranMatkulController::class, 'addKelas'])->name('admin.tahun-ajaran-matkul.add-kelas');

        // Kelas routes
        Route::get('admin/kelas/{id}', [KelasController::class, 'show'])->name('admin.kelas.show');
        Route::get('admin/kelas/{id}/manage-mahasiswa', [KelasController::class, 'manageMahasiswa'])->name('admin.kelas.manage-mahasiswa');
        Route::get('admin/kelas/{id}/manage-dosen', [KelasController::class, 'manageDosen'])->name('admin.kelas.manage-dosen');
        Route::post('admin/kelas/{id}/add-mahasiswa', [KelasController::class, 'addMahasiswa'])->name('admin.kelas.add-mahasiswa');
        Route::delete('admin/kelas/{id}/remove-mahasiswa/{mahasiswaId}', [KelasController::class, 'removeMahasiswa'])->name('admin.kelas.remove-mahasiswa');
        Route::post('admin/kelas/{id}/add-dosen', [KelasController::class, 'addDosen'])->name('admin.kelas.add-dosen');
        Route::delete('admin/kelas/{id}/remove-dosen/{dosenId}', [KelasController::class, 'removeDosen'])->name('admin.kelas.remove-dosen');
        Route::post('admin/kelas/{id}/bulk-add-mahasiswa', [KelasController::class, 'bulkAddMahasiswa'])->name('admin.kelas.bulk-add-mahasiswa');
        Route::delete('admin/kelas/{id}/bulk-remove-mahasiswa', [KelasController::class, 'bulkRemoveMahasiswa'])->name('admin.kelas.bulk-remove-mahasiswa');
    });

    // Dosen Dashboard
    Route::middleware('dosen')->group(function () {
        Route::get('/dosen/dashboard', [DashboardController::class, 'dosenDashboard'])->name('dosen.dashboard');

        // Kelola CPMK - List Mata Kuliah
        Route::get('/dosen/mata-kuliah/cpmk', [DosenCpmkController::class, 'index'])->name('dosen.cpmk.index');
        Route::get('/dosen/mata-kuliah/{tahunAjaranMatkulId}/cpmk', [DosenCpmkController::class, 'showMataKuliah'])->name('dosen.cpmk.show');
        Route::get('/dosen/mata-kuliah/{tahunAjaranMatkulId}/cpmk/create', [DosenCpmkController::class, 'create'])->name('dosen.cpmk.create');
        Route::post('/dosen/mata-kuliah/{tahunAjaranMatkulId}/cpmk', [DosenCpmkController::class, 'store'])->name('dosen.cpmk.store');

        // Sub-CPMK routes
        Route::get('/dosen/mata-kuliah/{tahunAjaranMatkulId}/cpmk/{parentId}/sub-cpmk/create', [DosenCpmkController::class, 'createSubCpmk'])->name('dosen.cpmk.sub-cpmk.create');
        Route::post('/dosen/mata-kuliah/{tahunAjaranMatkulId}/cpmk/{parentId}/sub-cpmk', [DosenCpmkController::class, 'storeSubCpmk'])->name('dosen.cpmk.sub-cpmk.store');
        Route::get('/dosen/mata-kuliah/{tahunAjaranMatkulId}/sub-cpmk/{cpmkId}/edit', [DosenCpmkController::class, 'editSubCpmk'])->name('dosen.cpmk.sub-cpmk.edit');
        Route::put('/dosen/mata-kuliah/{tahunAjaranMatkulId}/sub-cpmk/{cpmkId}', [DosenCpmkController::class, 'updateSubCpmk'])->name('dosen.cpmk.sub-cpmk.update');

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
        Route::delete('/dosen/nilai/{id}/reset', [DosenNilaiController::class, 'resetNilai'])->name('dosen.nilai.reset');
        Route::get('/dosen/nilai/{id}/export-template', [DosenNilaiController::class, 'exportTemplate'])->name('dosen.nilai.export-template');
        Route::post('/dosen/nilai/{id}/import', [DosenNilaiController::class, 'importNilai'])->name('dosen.nilai.import');
        Route::get('/dosen/nilai/{id}/import-status', [DosenNilaiController::class, 'checkImportStatus'])->name('dosen.nilai.import-status');
        Route::get('/dosen/nilai/{id}/detail', [DosenNilaiController::class, 'detailNilai'])->name('dosen.nilai.detail');

        // Laporan CPMK
        Route::get('/dosen/cpmk-laporan', [\App\Http\Controllers\Dosen\CpmkLaporanController::class, 'index'])->name('dosen.cpmk-laporan.index');
        Route::get('/dosen/cpmk-laporan/{tahunAjaranMatkulId}', [\App\Http\Controllers\Dosen\CpmkLaporanController::class, 'show'])->name('dosen.cpmk-laporan.show');
        Route::get('/dosen/cpmk-laporan/{tahunAjaranMatkulId}/export-pdf', [\App\Http\Controllers\Dosen\CpmkLaporanController::class, 'exportPdf'])->name('dosen.cpmk-laporan.export-pdf');
    });

    // Mahasiswa Dashboard
    Route::middleware('mahasiswa')->group(function () {
        Route::get('/mahasiswa/dashboard', [DashboardController::class, 'mahasiswaDashboard'])->name('mahasiswa.dashboard');
        Route::get('/mahasiswa/transkrip', [KHSController::class,'index'])->name('mahasiswa.transkrip');
        Route::get('/mahasiswa/transkrip/export-pdf', [KHSController::class,'exportPDF'])->name('mahasiswa.transkrip.export-pdf');
        Route::get('/mahasiswa/capaian', [\App\Http\Controllers\CapaianController::class, 'index'])->name('mahasiswa.capaian');
        Route::get('/mahasiswa/capaian/export-pdf', [\App\Http\Controllers\CapaianController::class, 'exportPDF'])->name('mahasiswa.capaian.export-pdf');
    });

    // Pimpinan Dashboard
    Route::middleware('pimpinan')->group(function () {
        Route::get('/pimpinan/dashboard', [DashboardController::class, 'pimpinanDashboard'])->name('pimpinan.dashboard');

        // Laporan CPMK Pimpinan
        Route::get('/pimpinan/cpmk-report', [\App\Http\Controllers\Pimpinan\CpmkReportController::class, 'index'])->name('pimpinan.cpmk-report.index');
        Route::get('/pimpinan/cpmk-report/{tahunAjaranMatkulId}', [\App\Http\Controllers\Pimpinan\CpmkReportController::class, 'show'])->name('pimpinan.cpmk-report.show');
        Route::get('/pimpinan/cpmk-report/{tahunAjaranMatkulId}/export-pdf', [\App\Http\Controllers\Pimpinan\CpmkReportController::class, 'exportPdf'])->name('pimpinan.cpmk-report.export-pdf');

        // Ketercapaian CPL
        Route::get('/pimpinan/cpl-achievement', [\App\Http\Controllers\Pimpinan\CplAchievementController::class, 'index'])->name('pimpinan.cpl-achievement.index');
    });
});

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
