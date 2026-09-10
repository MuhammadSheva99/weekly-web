<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\User;
use App\Models\KpiPerformance;

class DashboardController extends Controller
{
    public function index()
    {
        $totalKaryawan = User::whereHas('role', fn ($q) => $q->where('nama', 'Karyawan'))->count();
        $totalDivisi = Divisi::count();

        $performances = KpiPerformance::whereMonth('periode', now()->month)
            ->whereYear('periode', now()->year)
            ->with('user.divisi')
            ->get();

        $companyAchievement = $performances->avg('achievement_pct');

        $achievementPerDivisi = Divisi::withCount(['users' => fn ($q) => $q->whereHas('role', fn ($r) => $r->where('nama', 'Karyawan'))])
            ->orderBy('nama')
            ->get()
            ->map(function ($divisi) {
                $avg = KpiPerformance::whereHas('user', fn ($q) => $q->where('divisi_id', $divisi->id))
                    ->whereMonth('periode', now()->month)
                    ->whereYear('periode', now()->year)
                    ->avg('achievement_pct');

                $divisi->avg_achievement = $avg ? round($avg) : null;
                return $divisi;
            });

        $topPerformer = $performances->sortByDesc('achievement_pct')->first();
        $lowestPerformer = $performances->sortBy('achievement_pct')->first();

        $perluPerhatian = User::whereHas('role', fn ($q) => $q->where('nama', 'Karyawan'))
            ->get()
            ->filter(function ($user) {
                $recent = KpiPerformance::where('user_id', $user->id)
                    ->orderByDesc('periode')
                    ->limit(3)
                    ->pluck('achievement_pct');

                return $recent->count() >= 3 && $recent->every(fn ($v) => $v < 80);
            });

        return view('management.dashboard', [
            'companyAchievement' => $companyAchievement ? round($companyAchievement) : null,
            'totalKaryawan' => $totalKaryawan,
            'totalDivisi' => $totalDivisi,
            'perluPerhatianCount' => $perluPerhatian->count(),
            'achievementPerDivisi' => $achievementPerDivisi,
            'topPerformer' => $topPerformer,
            'lowestPerformer' => $lowestPerformer,
        ]);
    }

    public function divisiShow(Divisi $divisi)
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

        return view('management.divisi-show', [
            'divisi' => $divisi,
            'picList' => $picList,
        ]);
    }

    public function underperform()
    {
        $karyawan = User::whereHas('role', fn ($q) => $q->where('nama', 'Karyawan'))
            ->with('divisi')
            ->get();

        $result = $karyawan->map(function ($user) {
            $riwayat = collect();

            $weeklyCommitments = $user->weeklyCommitment()
                ->with(['actualMingguan', 'weeklyProgress', 'targetMingguan.targetBulanan.kpi'])
                ->orderBy('created_at')
                ->get();

            foreach ($weeklyCommitments as $wc) {
                $achievement = $wc->actualMingguan->achievement_pct
                    ?? $wc->weeklyProgress->achievement_pct
                    ?? null;

                if ($achievement !== null) {
                    $riwayat->push([
                        'achievement' => (float) $achievement,
                        'kpi' => $wc->targetMingguan?->targetBulanan?->kpi?->nama_kpi,
                    ]);
                }
            }

            $riwayat = $riwayat->take(-4)->values();

            $isDeclining = $riwayat->count() >= 2 && $riwayat->values()
                ->every(fn ($item, $i) => $i === 0 || $item['achievement'] < $riwayat[$i - 1]['achievement']);

            if (! $isDeclining) {
                return null;
            }

            return (object) [
                'user' => $user,
                'kpi' => $riwayat->last()['kpi'] ?? '-',
                'weeks' => $riwayat->pluck('achievement'),
            ];
        })->filter()->values();

        return view('management.underperform', ['data' => $result]);
    }
}