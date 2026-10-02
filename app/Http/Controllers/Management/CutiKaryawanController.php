<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\CutiRequest;
use App\Models\Divisi;
use Illuminate\Http\Request;

class CutiKaryawanController extends Controller
{
    public function index(Request $request)
    {
        $periode = $request->get('periode', now()->format('Y-m'));
        $bulan = \Carbon\Carbon::createFromFormat('Y-m', $periode);

        $query = CutiRequest::whereMonth('tanggal_mulai', $bulan->month)
            ->whereYear('tanggal_mulai', $bulan->year)
            ->with(['user.divisi', 'disetujuiOleh']);

        if ($request->filled('divisi_id')) {
            $query->whereHas('user', fn ($q) => $q->where('divisi_id', $request->divisi_id));
        }

        $daftar = $query->orderBy('tanggal_mulai')->get()->map(function ($c) {
            $terpakaiTahunIni = CutiRequest::where('user_id', $c->user_id)
                ->where('status', 'disetujui')
                ->whereYear('tanggal_mulai', now()->year)
                ->memotongKuota()
                ->sum('jumlah_hari');

            $c->sisa_cuti = $c->user->jatah_cuti_tahunan - $terpakaiTahunIni;
            return $c;
        });

        return view('management.cuti-karyawan.index', [
            'daftar' => $daftar,
            'periode' => $periode,
            'divisiList' => Divisi::orderBy('nama')->get(),
        ]);
    }
}