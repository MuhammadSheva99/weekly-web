<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\KpiPerformance;
use App\Models\TargetBulanan;
use Illuminate\Http\Request;

class MonitoringKaryawanController extends Controller
{
    // Status penilaian berdasarkan Achievement gabungan (weighted by bobot).
    // Sangat Baik  : >= 90
    // Baik         : 80 - 89.99
    // Cukup        : 70 - 79.99
    // Buruk        : < 70
    protected function statusDari(float $achievement): string
    {
        return match (true) {
            $achievement >= 90 => 'Sangat Baik',
            $achievement >= 80 => 'Baik',
            $achievement >= 70 => 'Cukup',
            default => 'Buruk',
        };
    }

    public function index(Request $request)
    {
        $divisiList = Divisi::orderBy('nama')->get();

        $divisiId = $request->get('divisi_id', $divisiList->first()->id ?? null);

        $periode = now()->startOfMonth();

        $performances = KpiPerformance::with(['user.divisi', 'kpi'])
            ->whereHas('user', function ($q) use ($divisiId) {
                if ($divisiId) {
                    $q->where('divisi_id', $divisiId);
                }
            })
            ->whereMonth('periode', $periode->month)
            ->whereYear('periode', $periode->year)
            ->get();

        // Bobot tiap KPI disimpan di TargetBulanan (kpi_id + user_id + periode),
        // bukan di kpi_master / kpi_performance. Diambil sekali lalu di-map
        // supaya tidak query berulang di dalam loop.
        $bobotMap = TargetBulanan::whereMonth('periode', $periode->month)
            ->whereYear('periode', $periode->year)
            ->get()
            ->keyBy(fn ($t) => $t->user_id.'-'.$t->kpi_id);

        // Satu baris per karyawan. Achievement gabungan = jumlah
        // (achievement_pct KPI x bobot KPI / 100), bukan rata-rata biasa,
        // supaya ikut bobot resmi dari KpiJabatanSeeder / Assign KPI.
        $karyawan = $performances
            ->groupBy('user_id')
            ->map(function ($items) use ($bobotMap) {
                $user = $items->first()->user;

                $kpiList = $items->map(function ($p) use ($bobotMap) {
                    $bobot = optional($bobotMap->get($p->user_id.'-'.$p->kpi_id))->bobot;
                    $bobot = $bobot !== null ? (float) $bobot : null;
                    $achievementPct = (float) $p->achievement_pct;

                    return (object) [
                        'nama_kpi' => $p->kpi->nama_kpi ?? '-',
                        'achievement_pct' => $achievementPct,
                        'bobot' => $bobot,
                        'kontribusi' => $bobot !== null ? round($achievementPct * $bobot / 100, 2) : null,
                    ];
                })->values();

                // Kalau ada KPI yang bobotnya tidak ketemu di TargetBulanan periode ini,
                // Achievement gabungan ditandai tidak lengkap (null) daripada diam-diam salah.
                $bobotLengkap = $kpiList->every(fn ($k) => $k->bobot !== null);
                $achievementTotal = $bobotLengkap ? (float) $kpiList->sum('kontribusi') : null;

                return (object) [
                    'user' => $user,
                    'achievement_total' => $achievementTotal,
                    'status_label' => $achievementTotal !== null ? $this->statusDari($achievementTotal) : null,
                    'kpiList' => $kpiList,
                ];
            })
            ->sortBy(fn ($k) => $k->user->nama ?? '')
            ->values();

        return view('management.monitoring-karyawan.index', [
            'divisiList' => $divisiList,
            'divisiId' => $divisiId,
            'karyawan' => $karyawan,
        ]);
    }
}