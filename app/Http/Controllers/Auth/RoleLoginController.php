<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class RoleLoginController extends Controller
{
    /** Peta slug URL => nama role persis di tabel `role` */
    protected array $roleMap = [
        'karyawan'   => 'Karyawan',
        'atasan'     => 'Atasan',
        'hrd'        => 'HRD',
        'management' => 'Management',
        'admin'      => 'Admin',
    ];

    public function create(string $role)
    {
        abort_unless(isset($this->roleMap[$role]), 404);

        return view("auth.login-{$role}", [
            'roleSlug' => $role,
            'roleLabel' => $this->roleMap[$role],
        ]);
    }

    public function store(Request $request, string $role)
    {
        abort_unless(isset($this->roleMap[$role]), 404);
        $roleName = $this->roleMap[$role];

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        $user = Auth::user();

        // Cek: akun ini beneran role yang sesuai halaman login-nya?
        if ($user->role->nama !== $roleName) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => "Akun ini bukan akun {$roleName}. Silakan login lewat halaman {$roleName} yang sesuai.",
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended($this->redirectPathFor($roleName));
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.karyawan');
    }

    protected function redirectPathFor(string $roleName): string
    {
        return match ($roleName) {
            'Admin' => route('admin.users.index'),
            'HRD' => '/hrd/dashboard',
            'Management' => '/management/dashboard',
            'Atasan' => '/atasan/dashboard',
            default => '/dashboard', // Karyawan
        };
    }
}