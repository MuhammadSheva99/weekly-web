<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\User;
use App\Models\WeeklyCommitment;
use App\Models\KpiPerformance;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPic = User::whereHas('role', fn ($q) => $q->where('nama', 'Karyawan'))->count();

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $sudahSubmit = WeeklyCommitment::whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->distinct('user_id')
            ->count('user_id');

        $belumSubmit = max($totalPic - $sudahSubmit, 0);

        $rataRataAchievement = KpiPerformance::whereMonth('periode', now()->month)
            ->whereYear('periode', now()->year)
            ->avg('achievement_pct');

        $achievementPerDivisi = Divisi::withCount(['users' => fn ($q) => $q->whereHas('role', fn ($r) => $r->where('nama', 'Karyawan'))])
            ->get()
            ->map(function ($divisi) {
                $avg = KpiPerformance::whereHas('user', fn ($q) => $q->where('divisi_id', $divisi->id))
                    ->whereMonth('periode', now()->month)
                    ->whereYear('periode', now()->year)
                    ->avg('achievement_pct');

                $divisi->avg_achievement = $avg ? round($avg) : null;
                return $divisi;
            });

        // Karyawan dengan achievement < 80% selama 3 periode terakhir berturut-turut
        $repeatedUnderperform = User::whereHas('role', fn ($q) => $q->where('nama', 'Karyawan'))
            ->with('divisi')
            ->get()
            ->filter(function ($user) {
                $recent = KpiPerformance::where('user_id', $user->id)
                    ->orderByDesc('periode')
                    ->limit(3)
                    ->pluck('achievement_pct');

                return $recent->count() >= 3 && $recent->every(fn ($v) => $v < 80);
            });

        return view('hrd.dashboard', [
            'totalPic' => $totalPic,
            'sudahSubmit' => $sudahSubmit,
            'belumSubmit' => $belumSubmit,
            'rataRataAchievement' => $rataRataAchievement ? round($rataRataAchievement) : null,
            'achievementPerDivisi' => $achievementPerDivisi,
            'repeatedUnderperform' => $repeatedUnderperform,
        ]);
    }
}