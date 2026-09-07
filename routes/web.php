<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\DivisiRoleController;
use App\Http\Controllers\Hrd\DashboardController as HrdDashboardController;
use App\Http\Controllers\Hrd\MonitoringPicController;
use App\Http\Controllers\Hrd\UnderperformController;
use App\Http\Controllers\Hrd\TrendPerformanceController;




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

Route::middleware(['auth', 'role:HRD'])->prefix('hrd')->name('hrd.')->group(function () {
    Route::get('/dashboard', [HrdDashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/monitoring-pic', [MonitoringPicController::class, 'index'])->name('monitoring-pic.index');
    Route::get('/underperform', [UnderperformController::class, 'index'])->name('underperform.index');
    Route::get('/trend-performance', [TrendPerformanceController::class, 'index'])->name('trend-performance.index');
});

require __DIR__.'/auth.php';