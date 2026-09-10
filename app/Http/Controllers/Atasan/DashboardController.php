<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\KpiPerformance;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $atasan = Auth::user();
        $anggotaTim = User::where('atasan_id', $atasan->id)->orderBy('nama')->get();

        $totalAnggota = $anggotaTim->count();

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $statusTim = $anggotaTim->map(function ($user) use ($startOfWeek, $endOfWeek) {
            $commitment = $user->weeklyCommitment()
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->with(['targetMingguan.targetBulanan.kpi', 'weeklyProgress', 'selfReview', 'actualMingguan'])
                ->latest()
                ->first();

            $target = $commitment?->target;
            $actual = $commitment?->actualMingguan?->nilai_actual_final
                ?? $commitment?->weeklyProgress?->actual_sementara;
            $achievement = $commitment?->actualMingguan?->achievement_pct
                ?? $commitment?->weeklyProgress?->achievement_pct;

            return (object) [
                'user' => $user,
                'kpi' => $commitment?->targetMingguan?->targetBulanan?->kpi?->nama_kpi,
                'target' => $target,
                'actual' => $actual,
                'achievement' => $achievement !== null ? round($achievement) : null,
                'submitSenin' => (bool) $commitment,
                'submitRabu' => (bool) $commitment?->weeklyProgress,
                'submitJumat' => (bool) $commitment?->selfReview,
            ];
        });

        $sudahSubmit = $statusTim->filter(fn ($s) => $s->submitSenin)->count();

        $rataRataAchievement = $statusTim->pluck('achievement')->filter(fn ($v) => $v !== null)->avg();

        // Deteksi penurunan 3 minggu berturut
        $anggotaMenurun = $anggotaTim->filter(function ($user) {
            $recent = KpiPerformance::where('user_id', $user->id)
                ->orderByDesc('periode')
                ->limit(3)
                ->pluck('achievement_pct');

            return $recent->count() >= 3 && $recent->every(fn ($v, $i) => $i === 0 || $v < $recent[$i - 1]);
        });

        return view('atasan.dashboard', [
            'totalAnggota' => $totalAnggota,
            'sudahSubmit' => $sudahSubmit,
            'rataRataAchievement' => $rataRataAchievement !== null ? round($rataRataAchievement) : null,
            'statusTim' => $statusTim,
            'anggotaMenurun' => $anggotaMenurun,
        ]);
    }
}