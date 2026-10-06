<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Support\KpiTabel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KpiController extends Controller
{
    public function index(Request $request)
    {
        $periode = KpiTabel::periode($request->get('periode'));
        $data = KpiTabel::bangunBanyak(collect([Auth::user()]), $periode)->first();

        return view('karyawan.kpi.index', [
            'data' => $data,
            'periode' => $periode->format('Y-m'),
        ]);
    }
}