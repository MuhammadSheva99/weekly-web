<?php

namespace App\Support;

class KpiAchievement
{
    /**
     * Hitung capaian (%) satu komitmen mingguan.
     * - KPI fleksibel: target mengikuti realisasi. Maximize: 100% kalau ada realisasi.
     *   Minimize: selalu 100%.
     * - KPI biasa: aktual / target * 100 (0 kalau target 0).
     */
    public static function hitung($commitment, $actual): float
    {
        $kpi = $commitment->targetMingguan->targetBulanan->kpi;

        if ($kpi && $kpi->target_fleksibel) {
            // Minimize: target mengikuti realisasi, jadi selalu 100% (0 pelanggaran = bagus).
            if ($kpi->pola === 'Minimize') {
                return 100.0;
            }

            return $actual > 0 ? 100.0 : 0.0;
        }

        return $commitment->target > 0
            ? ($actual / $commitment->target) * 100
            : 0.0;
    }
}