<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\DivisiRoleController;
use App\Http\Controllers\Hrd\DashboardController as HrdDashboardController;
use App\Http\Controllers\Hrd\MonitoringPicController;
use App\Http\Controllers\Hrd\UnderperformController;
use App\Http\Controllers\Hrd\TrendPerformanceController;
use App\Http\Controllers\Hrd\ReportController;
use App\Http\Controllers\Hrd\NotificationController;
use App\Http\Controllers\Hrd\CutiController;
use App\Http\Controllers\Hrd\CutiKaryawanController;
use App\Http\Controllers\Hrd\DivisiPerformanceController;
use App\Http\Controllers\Management\DashboardController as ManagementDashboardController;
use App\Http\Controllers\Management\TrendImprovementController;
use App\Http\Controllers\Management\CutiKaryawanController as ManagementCutiKaryawanController;
use App\Http\Controllers\Atasan\DashboardController as AtasanDashboardController;
use App\Http\Controllers\Atasan\WeeklyCommitmentController;
use App\Http\Controllers\Atasan\WeeklyProgressController;
use App\Http\Controllers\Atasan\SelfReviewController;
use App\Http\Controllers\Hrd\PicDetailController;
use App\Http\Controllers\Karyawan\DashboardController as KaryawanDashboardController;
use App\Http\Controllers\Karyawan\WeeklyCommitmentController as KaryawanWeeklyCommitmentController;
use App\Http\Controllers\Karyawan\WeeklyProgressController as KaryawanWeeklyProgressController;
use App\Http\Controllers\Karyawan\SelfReviewController as KaryawanSelfReviewController;




Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('admin.users.index'))->name('dashboard');

    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

    Route::get('/divisi-role', [DivisiRoleController::class, 'index'])->name('divisi-role.index');
    Route::post('/divisi-role', [DivisiRoleController::class, 'store'])->name('divisi-role.store');
    Route::get('/divisi-role/{divisi}', [DivisiRoleController::class, 'show'])->name('divisi-role.show');
    Route::put('/divisi-role/{divisi}', [DivisiRoleController::class, 'update'])->name('divisi-role.update');
    Route::delete('/divisi-role/{divisi}', [DivisiRoleController::class, 'destroy'])->name('divisi-role.destroy');
});


Route::middleware(['auth', 'role:Karyawan'])->prefix('karyawan')->name('karyawan.')->group(function () {
    Route::get('/dashboard', [KaryawanDashboardController::class, 'index'])->name('dashboard');

    Route::prefix('weekly-commitment')->name('weekly-commitment.')->group(function () {
        Route::get('/', [KaryawanWeeklyCommitmentController::class, 'create'])->name('create');
        Route::post('/', [KaryawanWeeklyCommitmentController::class, 'store'])->name('store');
        Route::put('/{commitment}', [KaryawanWeeklyCommitmentController::class, 'update'])->name('update');
    });

    Route::prefix('weekly-progress')->name('weekly-progress.')->group(function () {
        Route::get('/', [KaryawanWeeklyProgressController::class, 'create'])->name('create');
        Route::post('/', [KaryawanWeeklyProgressController::class, 'store'])->name('store');
        Route::put('/{progress}', [KaryawanWeeklyProgressController::class, 'update'])->name('update');
    });

    Route::prefix('self-review')->name('self-review.')->group(function () {
        Route::get('/', [KaryawanSelfReviewController::class, 'create'])->name('create');
        Route::post('/', [KaryawanSelfReviewController::class, 'store'])->name('store');
        Route::put('/{review}', [KaryawanSelfReviewController::class, 'update'])->name('update');
    });
});


Route::middleware(['auth', 'role:HRD'])->prefix('hrd')->name('hrd.')->group(function () {
    Route::get('/dashboard', [HrdDashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/monitoring-pic', [MonitoringPicController::class, 'index'])->name('monitoring-pic.index');
    Route::get('/underperform', [UnderperformController::class, 'index'])->name('underperform.index');
    Route::get('/trend-performance', [TrendPerformanceController::class, 'index'])->name('trend-performance.index');
    Route::get('/report', [ReportController::class, 'index'])->name('report.index');
    Route::get('/report/export-excel', [ReportController::class, 'exportExcel'])->name('report.export-excel');
    Route::get('/report/export-pdf', [ReportController::class, 'exportPdf'])->name('report.export-pdf');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/divisi/{divisi}/show', [DivisiPerformanceController::class, 'show'])->name('divisi.show');

    Route::prefix('cuti')->name('cuti.')->group(function () {
        Route::get('/', [CutiController::class, 'dashboard'])->name('dashboard');
        Route::get('/ajukan', [CutiController::class, 'createForm'])->name('ajukan');
        Route::post('/ajukan', [CutiController::class, 'store'])->name('store');
        Route::get('/riwayat', [CutiController::class, 'riwayat'])->name('riwayat');
        Route::get('/notifikasi', [CutiController::class, 'notifikasi'])->name('notifikasi');
    });
    
    Route::prefix('cuti-karyawan')->name('cuti-karyawan.')->group(function () {
        Route::get('/pengajuan', [CutiKaryawanController::class, 'pengajuan'])->name('pengajuan');
        Route::post('/{cuti}/approve', [CutiKaryawanController::class, 'approve'])->name('approve');
        Route::post('/{cuti}/reject', [CutiKaryawanController::class, 'reject'])->name('reject');
        Route::get('/riwayat', [CutiKaryawanController::class, 'riwayat'])->name('riwayat');
    });


    Route::get('/monitoring-pic/{user}', [PicDetailController::class, 'show'])->name('monitoring-pic.show');
});

Route::middleware(['auth', 'role:Management'])->prefix('management')->name('management.')->group(function () {
    Route::get('/dashboard', [ManagementDashboardController::class, 'index'])->name('dashboard');
    Route::get('/divisi/{divisi}', [ManagementDashboardController::class, 'divisiShow'])->name('divisi.show');
    Route::get('/underperform', [ManagementDashboardController::class, 'underperform'])->name('underperform');
    Route::get('/trend', [TrendImprovementController::class, 'index'])->name('trend.index');

    Route::prefix('cuti-karyawan')->name('cuti-karyawan.')->group(function () {
        Route::get('/', [ManagementCutiKaryawanController::class, 'pengajuan'])->name('pengajuan');
        Route::get('/riwayat', [ManagementCutiKaryawanController::class, 'riwayat'])->name('riwayat');
    });
});

Route::middleware(['auth', 'role:Atasan'])->prefix('atasan')->name('atasan.')->group(function () {
    Route::get('/dashboard', [AtasanDashboardController::class, 'index'])->name('dashboard');

    Route::prefix('weekly-commitment')->name('weekly-commitment.')->group(function () {
        Route::get('/', [WeeklyCommitmentController::class, 'create'])->name('create');
        Route::post('/', [WeeklyCommitmentController::class, 'store'])->name('store');
        Route::put('/{commitment}', [WeeklyCommitmentController::class, 'update'])->name('update');
    });

    Route::prefix('weekly-progress')->name('weekly-progress.')->group(function () {
        Route::get('/', [WeeklyProgressController::class, 'create'])->name('create');
        Route::post('/', [WeeklyProgressController::class, 'store'])->name('store');
        Route::put('/{progress}', [WeeklyProgressController::class, 'update'])->name('update');
    });

    Route::prefix('self-review')->name('self-review.')->group(function () {
        Route::get('/', [SelfReviewController::class, 'create'])->name('create');
        Route::post('/', [SelfReviewController::class, 'store'])->name('store');
        Route::put('/{review}', [SelfReviewController::class, 'update'])->name('update'); 
    });
});

require __DIR__.'/auth.php';