<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\KpiExcel;
use App\Support\KpiTabel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KpiTimController extends Controller
{
    public function index(Request $request)
    {
        $periode = KpiTabel::periode($request->get('periode'));
        $cari = $request->get('q');

        // Bawahan = user yang atasan_id-nya adalah atasan yang login.
        $daftar = KpiTabel::daftar(User::where('atasan_id', Auth::id()), $periode, $cari);

        return view('atasan.kpi-tim.index', [
            'daftar' => $daftar,
            'periode' => $periode->format('Y-m'),
            'cari' => $cari,
        ]);
    }

    /** Halaman detail KPI satu bawahan. */
    public function show(Request $request, User $user)
    {
        $this->pastikanBawahan($user);

        $periode = KpiTabel::periode($request->get('periode'));
        $data = KpiTabel::bangunBanyak(collect([$user->load('divisi')]), $periode)->first();

        return view('atasan.kpi-tim.show', [
            'data' => $data,
            'periode' => $periode->format('Y-m'),
            'kembali' => route('atasan.kpi-tim.index', ['periode' => $periode->format('Y-m')]),
            'excelUrl' => route('atasan.kpi-tim.excel', ['user' => $user->id, 'periode' => $periode->format('Y-m')]),
        ]);
    }

    /** Download tabel KPI satu bawahan sebagai file Excel. */
    public function excel(Request $request, User $user)
    {
        $this->pastikanBawahan($user);

        $periode = KpiTabel::periode($request->get('periode'));
        $data = KpiTabel::bangunBanyak(collect([$user->load('divisi')]), $periode)->first();

        return response()
            ->download(KpiExcel::buat($data, $periode), KpiExcel::namaFile($data, $periode))
            ->deleteFileAfterSend(true);
    }

    /** Atasan hanya boleh membuka KPI bawahannya sendiri. */
    private function pastikanBawahan(User $user): void
    {
        abort_unless((string) $user->atasan_id === (string) Auth::id(), 403);
    }
}