<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Controller as BaseController;
use App\Models\CutiRequest;
use App\Models\Divisi;
use Illuminate\Http\Request;

class CutiKaryawanController extends Controller
{
    public function pengajuan()
    {
        $daftar = CutiRequest::where('status', 'menunggu')
            ->with(['user.divisi', 'disetujuiOleh'])
            ->orderBy('tanggal_mulai')
            ->get();

        return view('management.cuti-karyawan.pengajuan', ['daftar' => $daftar]);
    }

    public function riwayat(Request $request)
    {
        $query = CutiRequest::whereIn('status', ['disetujui', 'ditolak'])
            ->with(['user.divisi']);

        if ($request->filled('divisi_id')) {
            $query->whereHas('user', fn ($q) => $q->where('divisi_id', $request->divisi_id));
        }

        $riwayat = $query->orderByDesc('tanggal_mulai')->get()->map(function ($c) {
            $terpakaiTahunIni = CutiRequest::where('user_id', $c->user_id)
                ->where('status', 'disetujui')
                ->whereYear('tanggal_mulai', now()->year)
                ->sum('jumlah_hari');

            $c->sisa_cuti = $c->user->jatah_cuti_tahunan - $terpakaiTahunIni;
            return $c;
        });

        return view('management.cuti-karyawan.riwayat', [
            'riwayat' => $riwayat,
            'divisiList' => Divisi::orderBy('nama')->get(),
        ]);
    }
}