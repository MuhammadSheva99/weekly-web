<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PasswordOtpController extends Controller
{
    // --- STEP 1: Form input email ---
    public function showEmailForm()
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'Email tidak terdaftar di sistem.',
            ]);
        }

        // Hapus OTP lama untuk email ini
        DB::table('password_reset_otps')->where('email', $request->email)->delete();

        $otpCode = (string) random_int(100000, 999999);

        DB::table('password_reset_otps')->insert([
            'email' => $request->email,
            'otp_code' => $otpCode,
            'expires_at' => now()->addMinutes(10),
            'is_verified' => false,
            'created_at' => now(),
        ]);

        Mail::to($request->email)->send(new OtpMail($otpCode, $user->nama));

        // Simpan email di session buat step berikutnya
        session(['otp_email' => $request->email]);

        return redirect()->route('password.otp.verify.form');
    }

    // --- STEP 2: Form input kode OTP ---
    public function showVerifyForm()
    {
        if (! session('otp_email')) {
            return redirect()->route('password.request');
        }

        return view('auth.verify-otp', ['email' => session('otp_email')]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp_code' => 'required|digits:6']);

        $email = session('otp_email');

        if (! $email) {
            return redirect()->route('password.request');
        }

        $record = DB::table('password_reset_otps')
            ->where('email', $email)
            ->where('otp_code', $request->otp_code)
            ->first();

        if (! $record) {
            throw ValidationException::withMessages([
                'otp_code' => 'Kode OTP salah.',
            ]);
        }

        if (now()->greaterThan($record->expires_at)) {
            throw ValidationException::withMessages([
                'otp_code' => 'Kode OTP sudah kedaluwarsa. Silakan minta kode baru.',
            ]);
        }

        DB::table('password_reset_otps')->where('email', $email)->update(['is_verified' => true]);

        session(['otp_verified' => true]);

        return redirect()->route('password.otp.reset.form');
    }

    // --- STEP 3: Form password baru ---
    public function showResetForm()
    {
        if (! session('otp_email') || ! session('otp_verified')) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password-otp');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $email = session('otp_email');

        if (! $email || ! session('otp_verified')) {
            return redirect()->route('password.request');
        }

        $user = User::where('email', $email)->first();
        $user->update(['password' => Hash::make($request->password)]);

        // Bersihkan OTP & session
        DB::table('password_reset_otps')->where('email', $email)->delete();
        $request->session()->forget(['otp_email', 'otp_verified']);

        return redirect()->route('login.karyawan')->with('status', 'Password berhasil diubah. Silakan login.');
    }
}