<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['role', 'divisi', 'atasan']);

        if ($request->filled('divisi_id')) {
            $query->where('divisi_id', $request->divisi_id);
        }
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        return view('admin.users.index', [
            'users' => $query->orderBy('nama')->get(),
            'divisiList' => Divisi::orderBy('nama')->get(),
            'roleList' => Role::orderBy('nama')->get(),
            'atasanOptions' => User::whereHas('role', fn ($q) => $q->where('nama', 'Atasan'))
                ->orderBy('nama')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|exists:role,id',
            'divisi_id' => 'nullable|exists:divisi,id',
            'atasan_id' => 'nullable|exists:users,id',
            'jabatan' => 'nullable|string|max:100',
        ]);

        $data['password'] = Hash::make('password123');
        $data['is_active'] = true;

        User::create($data);

        return back()->with('status', 'User berhasil ditambahkan. Password default: password123');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role_id' => 'required|exists:role,id',
            'divisi_id' => 'nullable|exists:divisi,id',
            'atasan_id' => 'nullable|exists:users,id',
        ]);

        $user->update($data);

        return back()->with('status', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return back()->with('status', 'User berhasil dihapus.');
    }
}