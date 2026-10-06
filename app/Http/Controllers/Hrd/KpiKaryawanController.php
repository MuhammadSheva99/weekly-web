<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\User;
use App\Support\KpiExcel;
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

        return view('hrd.kpi-karyawan.index', [
            'daftar' => $daftar,
            'periode' => $periode->format('Y-m'),
            'divisiList' => Divisi::orderBy('nama')->get(),
            'divisiId' => $divisiId,
            'cari' => $cari,
        ]);
    }

    /** Halaman detail KPI satu karyawan. */
    public function show(Request $request, User $user)
    {
        $periode = KpiTabel::periode($request->get('periode'));
        $data = KpiTabel::bangunBanyak(collect([$user->load('divisi')]), $periode)->first();

        return view('hrd.kpi-karyawan.show', [
            'data' => $data,
            'periode' => $periode->format('Y-m'),
            'kembali' => route('hrd.kpi-karyawan.index', ['periode' => $periode->format('Y-m')]),
            'excelUrl' => route('hrd.kpi-karyawan.excel', ['user' => $user->id, 'periode' => $periode->format('Y-m')]),
        ]);
    }

    /** Download tabel KPI satu karyawan sebagai file Excel. */
    public function excel(Request $request, User $user)
    {
        $periode = KpiTabel::periode($request->get('periode'));
        $data = KpiTabel::bangunBanyak(collect([$user->load('divisi')]), $periode)->first();

        return response()
            ->download(KpiExcel::buat($data, $periode), KpiExcel::namaFile($data, $periode))
            ->deleteFileAfterSend(true);
    }
}