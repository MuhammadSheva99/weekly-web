<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\KpiPerformance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TrendPerformanceController extends Controller
{
    public function index(Request $request)
    {
        $divisiList = Divisi::orderBy('nama')->get();

        $selectedDivisiId = $request->get('divisi_id', $divisiList->first()?->id);
        $selectedDivisi = $divisiList->firstWhere('id', $selectedDivisiId);

        $rangeBulan = (int) $request->get('range', 3);

        $avgAchievement = null;
        $monthlyTrend = collect();

        if ($selectedDivisi) {
            $startPeriode = Carbon::now()->subMonths($rangeBulan - 1)->startOfMonth();

            $avgAchievement = KpiPerformance::whereHas('user', fn ($q) => $q->where('divisi_id', $selectedDivisi->id))
                ->where('periode', '>=', $startPeriode)
                ->avg('achievement_pct');
            $avgAchievement = $avgAchievement ? round($avgAchievement) : null;

            for ($i = $rangeBulan - 1; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $avg = KpiPerformance::whereHas('user', fn ($q) => $q->where('divisi_id', $selectedDivisi->id))
                    ->whereMonth('periode', $month->month)
                    ->whereYear('periode', $month->year)
                    ->avg('achievement_pct');

                $monthlyTrend->push([
                    'label' => $month->translatedFormat('M Y'),
                    'value' => $avg ? round($avg) : null,
                ]);
            }
        }

        return view('hrd.trend-performance', [
            'divisiList' => $divisiList,
            'selectedDivisi' => $selectedDivisi,
            'selectedDivisiId' => $selectedDivisiId,
            'rangeBulan' => $rangeBulan,
            'avgAchievement' => $avgAchievement,
            'monthlyTrend' => $monthlyTrend,
        ]);
    }
}