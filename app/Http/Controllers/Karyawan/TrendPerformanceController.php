<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\KpiPerformance;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TrendPerformanceController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $bulanList = collect();

        for ($i = 3; $i >= 0; $i--) {
            $bulanList->push(Carbon::now()->subMonths($i));
        }

        $trend = $bulanList->map(function ($bulan) use ($userId) {
            $avg = KpiPerformance::where('user_id', $userId)
                ->whereMonth('periode', $bulan->month)
                ->whereYear('periode', $bulan->year)
                ->avg('achievement_pct');

            return [
                'label' => $bulan->translatedFormat('M'),
                'value' => $avg ? round($avg) : null,
            ];
        });

        $validValues = $trend->pluck('value')->filter(fn ($v) => $v !== null)->values();
        $isDeclining = $validValues->count() >= 3 && $validValues->values()
            ->every(fn ($v, $i) => $i === 0 || $v <= $validValues[$i - 1]);

        $insightText = null;
        if ($isDeclining) {
            $insightText = 'Achievement bulanan menurun '.$validValues->count().' bulan berturut-turut. Perhatikan Action Plan dan Self Review minggu-minggu terakhir untuk mengidentifikasi akar penyebabnya.';
        }

        return view('karyawan.trend-performance', [
            'trend' => $trend,
            'insightText' => $insightText,
        ]);
    }
}