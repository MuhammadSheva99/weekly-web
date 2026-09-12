<?php

namespace App\Services;

use App\Models\KpiPerformance;
use App\Models\TargetBulanan;
use App\Models\User;
use Carbon\Carbon;

class KpiValueCalculator
{
    public static function calculate(User $user, Carbon $periode): array
    {
        $performances = KpiPerformance::where('user_id', $user->id)
            ->whereMonth('periode', $periode->month)
            ->whereYear('periode', $periode->year)
            ->with('kpi')
            ->get();

        $items = collect();
        $totalWeighted = 0;
        $totalBobot = 0;

        foreach ($performances as $perf) {
            $targetBulanan = TargetBulanan::where('kpi_id', $perf->kpi_id)
                ->where('user_id', $user->id)
                ->whereMonth('periode', $periode->month)
                ->whereYear('periode', $periode->year)
                ->first();

            $bobot = (float) ($targetBulanan->bobot ?? 0);
            $achievement = (float) $perf->achievement_pct;

            $totalWeighted += ($bobot / 100) * $achievement;
            $totalBobot += $bobot;

            $items->push([
                'nama_kpi' => $perf->kpi->nama_kpi,
                'achievement' => $achievement,
                'bobot' => $bobot,
                'score' => $perf->score,
            ]);
        }

        $kpiValue = round($totalWeighted, 2);
        $grade = match (true) {
            $kpiValue >= 90 => 'A',
            $kpiValue >= 80 => 'B',
            $kpiValue >= 70 => 'C',
            default => 'D',
        };

        return [
            'items' => $items,
            'total_bobot' => $totalBobot,
            'kpi_value' => $kpiValue,
            'grade' => $grade,
        ];
    }
}