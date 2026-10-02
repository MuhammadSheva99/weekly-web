<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\KpiMaster;
use App\Models\KpiPerformance;
use App\Models\TargetBulanan;
use App\Models\User;

class MonitoringDivisiController extends Controller
{
    // Role yang dihitung sebagai "karyawan" divisi untuk achievement & total karyawan.
    protected const ROLE_DIHITUNG = ['Karyawan', 'Atasan', 'HRD'];

    // Sama persis dengan Monitoring Karyawan.
    protected function statusDari(float $achievement): string
    {
        return match (true) {
            $achievement >= 90 => 'Sangat Baik',
            $achievement >= 80 => 'Baik',
            $achievement >= 70 => 'Cukup',
            default => 'Buruk',
        };
    }

    public function index()
    {
        $periode = now()->startOfMonth();

        // Bobot per (user_id, kpi_id) dari TargetBulanan bulan berjalan.
        $bobotMap = TargetBulanan::whereMonth('periode', $periode->month)
            ->whereYear('periode', $periode->year)
            ->get()
            ->keyBy(fn ($t) => $t->user_id.'-'.$t->kpi_id);

        // Semua KpiPerformance bulan berjalan, langsung diambil sekali untuk semua divisi.
        $performances = KpiPerformance::whereMonth('periode', $periode->month)
            ->whereYear('periode', $periode->year)
            ->get();

        // Total nilai_target per KPI (dijumlah dari semua user) untuk "Target Bulanan".
        $targetPerKpi = TargetBulanan::whereMonth('periode', $periode->month)
            ->whereYear('periode', $periode->year)
            ->get()
            ->groupBy('kpi_id')
            ->map(fn ($items) => $items->sum('nilai_target'));

        $divisiList = Divisi::orderBy('nama')->get()->map(function ($divisi) use ($bobotMap, $performances, $targetPerKpi) {
            $users = User::where('divisi_id', $divisi->id)
                ->whereHas('role', fn ($q) => $q->whereIn('nama', self::ROLE_DIHITUNG))
                ->get();

            $head = User::where('divisi_id', $divisi->id)
                ->whereHas('role', fn ($q) => $q->where('nama', 'Atasan'))
                ->first();

            // Achievement per karyawan (weighted bobot), lalu dirata-rata se-divisi.
            // Sama persis logikanya dengan Monitoring Karyawan.
            $achievementPerUser = $users->map(function ($user) use ($bobotMap, $performances) {
                $items = $performances->where('user_id', $user->id);
                if ($items->isEmpty()) {
                    return null;
                }

                $kontribusi = $items->map(function ($p) use ($bobotMap) {
                    $bobot = optional($bobotMap->get($p->user_id.'-'.$p->kpi_id))->bobot;
                    if ($bobot === null) {
                        return null;
                    }
                    return (float) $p->achievement_pct * (float) $bobot / 100;
                });

                // Kalau ada KPI yang bobotnya nggak ketemu, data user ini dianggap
                // tidak lengkap dan tidak ikut dirata-rata (daripada salah hitung).
                if ($kontribusi->contains(null)) {
                    return null;
                }

                return $kontribusi->sum();
            })->filter(fn ($v) => $v !== null);

            $achievementDivisi = $achievementPerUser->isNotEmpty()
                ? round($achievementPerUser->avg(), 1)
                : null;

            // Daftar KPI divisi ini + total target bulanan (dijumlah semua user di divisi).
            $kpiList = KpiMaster::where('divisi_id', $divisi->id)
                ->where('is_active', true)
                ->get()
                ->map(fn ($kpi) => (object) [
                    'nama_kpi' => $kpi->nama_kpi,
                    'satuan' => $kpi->satuan,
                    'total_target' => $targetPerKpi->get($kpi->id, 0),
                ]);

            return (object) [
                'divisi' => $divisi,
                'head' => $head->nama ?? null,
                'total_karyawan' => $users->count(),
                'achievement' => $achievementDivisi,
                'status_label' => $achievementDivisi !== null ? $this->statusDari($achievementDivisi) : null,
                'kpiList' => $kpiList,
            ];
        });

        return view('management.monitoring-divisi.index', [
            'divisiList' => $divisiList,
        ]);
    }
}