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
}