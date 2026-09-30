<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\IzinRequest;
use Illuminate\Http\Request;

class IzinKaryawanController extends Controller
{
    public function index(Request $request)
    {
        $periode = $request->get('periode', now()->format('Y-m'));
        $bulan = \Carbon\Carbon::createFromFormat('Y-m', $periode);

        $query = IzinRequest::whereMonth('tanggal', $bulan->month)
            ->whereYear('tanggal', $bulan->year)
            ->with(['user.divisi', 'disetujuiOleh']);

        if ($request->filled('divisi_id')) {
            $query->whereHas('user', fn ($q) => $q->where('divisi_id', $request->divisi_id));
        }

        $daftar = $query->orderBy('tanggal')->get();

        return view('management.izin-karyawan.index', [
            'daftar' => $daftar,
            'periode' => $periode,
            'divisiList' => Divisi::orderBy('nama')->get(),
        ]);
    }
}