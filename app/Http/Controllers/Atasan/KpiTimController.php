<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\User;
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
}