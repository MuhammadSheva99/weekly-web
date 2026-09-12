<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\WeeklyCommitment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $mingguKe = min((int) ceil(now()->day / 7), 4);

        $commitment = WeeklyCommitment::where('user_id', Auth::id())
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->with(['targetMingguan.targetBulanan.kpi', 'weeklyProgress', 'selfReview', 'actualMingguan'])
            ->latest()
            ->first();

        $historyWeekly = WeeklyCommitment::where('user_id', Auth::id())
            ->with(['targetMingguan', 'selfReview', 'weeklyProgress'])
            ->orderByDesc('created_at')
            ->limit(4)
            ->get()
            ->map(function ($wc) {
                $status = $wc->selfReview
                    ? round($wc->selfReview->achievement_pct).'%'
                    : 'Belum final';

                return (object) [
                    'minggu_ke' => $wc->targetMingguan->minggu_ke ?? '-',
                    'label' => $wc->created_at->translatedFormat('\M\i\n\g\g\u '.($wc->targetMingguan->minggu_ke ?? '?').' - F'),
                    'status' => $status,
                ];
            });

        return view('karyawan.dashboard', [
            'mingguKe' => $mingguKe,
            'commitment' => $commitment,
            'historyWeekly' => $historyWeekly,
        ]);
    }
}