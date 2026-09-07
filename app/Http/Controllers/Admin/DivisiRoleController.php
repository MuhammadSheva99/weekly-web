<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use Illuminate\Http\Request;

class DivisiRoleController extends Controller
{
    public function index()
    {
        $divisiList = Divisi::withCount('users')
            ->with(['users' => fn ($q) => $q->whereHas('role', fn ($r) => $r->where('nama', 'Atasan'))])
            ->orderBy('nama')
            ->get()
            ->map(function ($divisi) {
                $divisi->head_count = $divisi->users->count();
                return $divisi;
            });

        $roleDescriptions = [
            'Karyawan' => 'Mengisi weekly, melihat performa sendiri',
            'Atasan' => 'Memantau tim, memberi feedback, mengisi weekly untuk KPI tim',
            'HRD' => 'Monitoring lintas divisi, underperform alert, report',
            'Management' => 'Ringkasan performa perusahaan dan tren',
            'Admin' => 'Mengelola user, divisi, KPI, dan target',
        ];

        return view('admin.divisi-role.index', [
            'divisiList' => $divisiList,
            'roleDescriptions' => $roleDescriptions,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:divisi,nama',
        ]);

        Divisi::create(['nama' => $request->nama]);

        return back()->with('status', 'Divisi berhasil ditambahkan.');
    }

    public function show(Divisi $divisi)
    {
        $divisi->load(['users.role', 'users.atasan.divisi']);

        $atasanList = $divisi->users
            ->pluck('atasan')
            ->filter()
            ->unique('id')
            ->values();

        return view('admin.divisi-role.show', [
            'divisi' => $divisi,
            'atasanList' => $atasanList,
        ]);
    }

    public function update(Request $request, Divisi $divisi)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:divisi,nama,'.$divisi->id,
        ]);

        $divisi->update(['nama' => $request->nama]);

        return back()->with('status', 'Divisi berhasil diperbarui.');
    }

    public function destroy(Divisi $divisi)
    {
        if ($divisi->users()->exists()) {
            return back()->with('error', 'Divisi tidak bisa dihapus karena masih punya anggota. Pindahkan dulu anggotanya ke divisi lain.');
        }

        $divisi->delete();

        return redirect()->route('admin.divisi-role.index')->with('status', 'Divisi berhasil dihapus.');
    }
}