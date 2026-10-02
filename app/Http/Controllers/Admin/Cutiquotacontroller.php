<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CutiRequest;
use App\Models\User;
use Illuminate\Http\Request;

class CutiQuotaController extends Controller
{
    // Role yang kuota cutinya diatur lewat halaman ini.
    protected const ROLE_DIATUR = ['Karyawan', 'Atasan', 'HRD'];

    public function index(Request $request)
    {
        $tahun = (int) $request->get('tahun', now()->year);

        $users = User::whereHas('role', fn ($q) => $q->whereIn('nama', self::ROLE_DIATUR))
            ->with(['role', 'divisi'])
            ->orderBy('nama')
            ->get()
            ->map(function ($user) use ($tahun) {
                $terpakaiSistem = CutiRequest::where('user_id', $user->id)
                    ->memotongKuota()
                    ->where('status', 'disetujui')
                    ->whereYear('tanggal_mulai', $tahun)
                    ->sum('jumlah_hari');

                $terpakaiTotal = $user->cuti_terpakai_manual + $terpakaiSistem;

                $user->terpakai_sistem = (float) $terpakaiSistem;
                $user->terpakai_total = (float) $terpakaiTotal;
                $user->sisa_cuti = $user->jatah_cuti_tahunan + $user->sisa_cuti_tahun_lalu - $terpakaiTotal;

                return $user;
            });

        return view('admin.cuti.index', [
            'users' => $users,
            'tahun' => $tahun,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'jatah_cuti_tahunan' => 'required|numeric|min:0',
            'sisa_cuti_tahun_lalu' => 'required|numeric|min:0',
            'cuti_terpakai_manual' => 'required|numeric|min:0',
        ]);

        $user->update($data);

        return back()->with('status', "Kuota cuti {$user->nama} berhasil diperbarui.");
    }
}