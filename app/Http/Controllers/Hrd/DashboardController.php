<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\User;
use App\Models\WeeklyCommitment;
use App\Models\KpiPerformance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $divisiId = $request->get('divisi_id');

        $picQuery = User::whereHas('role', fn ($q) => $q->where('nama', 'Karyawan'));
        if ($divisiId) {
            $picQuery->where('divisi_id', $divisiId);
        }
        $totalPic = $picQuery->count();

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $sudahSubmitQuery = WeeklyCommitment::whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->when($divisiId, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('divisi_id', $divisiId)));
        $sudahSubmit = $sudahSubmitQuery->distinct('user_id')->count('user_id');

        $belumSubmit = max($totalPic - $sudahSubmit, 0);

        $rataRataQuery = KpiPerformance::whereMonth('periode', now()->month)
            ->whereYear('periode', now()->year)
            ->when($divisiId, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('divisi_id', $divisiId)));
        $rataRataAchievement = $rataRataQuery->avg('achievement_pct');

        $divisiListForFilter = Divisi::orderBy('nama')->get();

        $achievementQuery = Divisi::withCount(['users' => fn ($q) => $q->whereHas('role', fn ($r) => $r->where('nama', 'Karyawan'))]);
        if ($divisiId) {
            $achievementQuery->where('id', $divisiId);
        }

        $achievementPerDivisi = $achievementQuery->orderBy('nama')
            ->with(['users' => fn ($q) => $q->whereHas('role', fn ($r) => $r->where('nama', 'Karyawan'))->select('id', 'nama', 'divisi_id')])
            ->get()
            ->map(function ($divisi) {
                $avg = KpiPerformance::whereHas('user', fn ($q) => $q->where('divisi_id', $divisi->id))
                    ->whereMonth('periode', now()->month)
                    ->whereYear('periode', now()->year)
                    ->avg('achievement_pct');

                $divisi->avg_achievement = $avg ? round($avg) : null;
                return $divisi;
        });

        $underperformQuery = User::whereHas('role', fn ($q) => $q->where('nama', 'Karyawan'))->with('divisi');
        if ($divisiId) {
            $underperformQuery->where('divisi_id', $divisiId);
        }

        $repeatedUnderperform = $underperformQuery->get()->filter(function ($user) {
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
            'divisiList' => $divisiListForFilter,
            'divisiId' => $divisiId,
        ]);
    }
}