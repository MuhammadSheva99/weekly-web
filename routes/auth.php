<?php

use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordOtpController;
use App\Http\Controllers\Auth\RoleLoginController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Form login per role — tiap role punya URL & nama route sendiri
    foreach (['karyawan', 'atasan', 'hrd', 'management', 'admin'] as $role) {
        Route::get("/login/{$role}", [RoleLoginController::class, 'create'])
            ->defaults('role', $role)
            ->name("login.{$role}");

        Route::post("/login/{$role}", [RoleLoginController::class, 'store'])
            ->defaults('role', $role);
    }

    // Lupa password via OTP — 3 tahap
    Route::get('forgot-password', [PasswordOtpController::class, 'showEmailForm'])
        ->name('password.request');
    Route::post('forgot-password', [PasswordOtpController::class, 'sendOtp'])
        ->name('password.email');

    Route::get('forgot-password/verify', [PasswordOtpController::class, 'showVerifyForm'])
        ->name('password.otp.verify.form');
    Route::post('forgot-password/verify', [PasswordOtpController::class, 'verifyOtp'])
        ->name('password.otp.verify');

    Route::get('forgot-password/reset', [PasswordOtpController::class, 'showResetForm'])
        ->name('password.otp.reset.form');
    Route::post('forgot-password/reset', [PasswordOtpController::class, 'resetPassword'])
        ->name('password.otp.reset');
});

Route::middleware('auth')->group(function () {
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [RoleLoginController::class, 'destroy'])
        ->name('logout');
});