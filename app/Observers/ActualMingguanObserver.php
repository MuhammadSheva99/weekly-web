<?php

namespace App\Observers;

use App\Models\ActualMingguan;
use App\Models\KpiPerformance;

class ActualMingguanObserver
{
    public function saved(ActualMingguan $actual): void
    {
        $targetMingguan = $actual->targetMingguan()->with('targetBulanan.kpi')->first();

        if (! $targetMingguan || ! $targetMingguan->targetBulanan) {
            return;
        }

        $targetBulanan = $targetMingguan->targetBulanan;
        $kpi = $targetBulanan->kpi;

        // Jumlahkan semua Actual Mingguan yang sudah terkunci untuk Target Bulanan ini
        $totalActual = ActualMingguan::whereHas('targetMingguan', function ($q) use ($targetBulanan) {
                $q->where('target_bulanan_id', $targetBulanan->id);
            })
            ->sum('nilai_actual_final');

        $target = (float) $targetBulanan->nilai_target;
        $pola = strtolower((string) $kpi->pola);
        $achievementPct = 0;

        if ($kpi->target_fleksibel) {
            // Target fleksibel: target mengikuti realisasi.
            // Minimize selalu 100%; Maximize 100% selama ada realisasi.
            $achievementPct = $pola === 'minimize' ? 100 : ($totalActual > 0 ? 100 : 0);
        } elseif ($target > 0) {
            $achievementPct = $pola === 'minimize'
                ? min(($target / max($totalActual, 0.0001)) * 100, 999)
                : ($totalActual / $target) * 100;
        }

        $score = match (true) {
            $achievementPct >= 90 => 'A',
            $achievementPct >= 80 => 'B',
            $achievementPct >= 70 => 'C',
            default => 'D',
        };

        KpiPerformance::updateOrCreate(
            [
                'kpi_id' => $kpi->id,
                'user_id' => $targetBulanan->user_id,
                'periode' => $targetBulanan->periode,
            ],
            [
                'actual_bulanan' => $totalActual,
                'achievement_pct' => round($achievementPct, 2),
                'score' => $score,
            ]
        );
    }
}