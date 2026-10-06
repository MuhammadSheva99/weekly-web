<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\User;
use App\Support\KpiTabel;
use Illuminate\Http\Request;

class KpiKaryawanController extends Controller
{
    public function index(Request $request)
    {
        $periode = KpiTabel::periode($request->get('periode'));
        $divisiId = $request->get('divisi_id');
        $cari = $request->get('q');

        $daftar = KpiTabel::daftar(
            User::query()->when($divisiId, fn ($q) => $q->where('divisi_id', $divisiId)),
            $periode,
            $cari
        );

        return view('management.kpi-karyawan.index', [
            'daftar' => $daftar,
            'periode' => $periode->format('Y-m'),
            'divisiList' => Divisi::orderBy('nama')->get(),
            'divisiId' => $divisiId,
            'cari' => $cari,
        ]);
    }
}