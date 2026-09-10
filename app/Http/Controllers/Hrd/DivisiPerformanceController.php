<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\KpiPerformance;

class DivisiPerformanceController extends Controller
{
    public function show(Divisi $divisi)
    {
        $picList = $divisi->users()
            ->whereHas('role', fn ($q) => $q->where('nama', 'Karyawan'))
            ->orderBy('nama')
            ->get()
            ->map(function ($user) {
                $performance = KpiPerformance::where('user_id', $user->id)
                    ->whereMonth('periode', now()->month)
                    ->whereYear('periode', now()->year)
                    ->first();

                $user->achievement = $performance?->achievement_pct !== null
                    ? round($performance->achievement_pct)
                    : null;

                return $user;
            });

        return view('hrd.divisi-show', [
            'divisi' => $divisi,
            'picList' => $picList,
        ]);
    }
}