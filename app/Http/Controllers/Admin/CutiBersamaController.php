<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CutiRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CutiBersamaController extends Controller
{
    protected const ROLE_TERSEDIA = ['Karyawan', 'Atasan', 'HRD'];

    protected const JENIS_CUTI_BERSAMA = 'Cuti Bersama';

    public function create()
    {
        $jumlahPerRole = User::whereHas('role', fn ($q) => $q->whereIn('nama', self::ROLE_TERSEDIA))
            ->get()
            ->groupBy(fn ($u) => $u->role->nama)
            ->map->count();

        return view('admin.cuti-bersama.create', [
            'roleTersedia' => self::ROLE_TERSEDIA,
            'jumlahPerRole' => $jumlahPerRole,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jumlah_hari' => 'required|numeric|min:0.5',
            'keterangan' => 'required|string',
            'roles' => 'required|array|min:1',
            'roles.*' => 'in:' . implode(',', self::ROLE_TERSEDIA),
        ]);

        $users = User::whereHas('role', fn ($q) => $q->whereIn('nama', $data['roles']))->get();

        $dibuat = 0;
        $dilewati = 0;

        foreach ($users as $user) {
            $existing = CutiRequest::where('user_id', $user->id)
                ->where('jenis_cuti', self::JENIS_CUTI_BERSAMA)
                ->where('tanggal_mulai', $data['tanggal_mulai'])
                ->first();

            if ($existing) {
                $dilewati++;
                continue;
            }

            CutiRequest::create([
                'user_id' => $user->id,
                'jenis_cuti' => self::JENIS_CUTI_BERSAMA,
                'tanggal_mulai' => $data['tanggal_mulai'],
                'tanggal_selesai' => $data['tanggal_selesai'],
                'jumlah_hari' => $data['jumlah_hari'],
                'keterangan' => $data['keterangan'],
                'status' => 'disetujui',
                'disetujui_oleh' => Auth::id(),
            ]);

            $dibuat++;
        }

        $pesan = "Cuti Bersama diterapkan ke {$dibuat} orang.";
        if ($dilewati > 0) {
            $pesan .= " {$dilewati} orang dilewati (sudah pernah diterapkan untuk periode ini).";
        }

        return redirect()->route('admin.cuti-bersama.create')->with('status', $pesan);
    }
}