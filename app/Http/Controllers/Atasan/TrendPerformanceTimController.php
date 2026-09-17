<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\KpiPerformance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrendPerformanceTimController extends Controller
{
    public function index(Request $request)
    {
        $anggotaTim = User::where('atasan_id', Auth::id())->orderBy('nama')->get();

        $rataRata = $anggotaTim->map(function ($user) {
            $avg = KpiPerformance::where('user_id', $user->id)
                ->whereMonth('periode', now()->month)
                ->whereYear('periode', now()->year)
                ->avg('achievement_pct');

            return (object) [
                'user' => $user,
                'achievement' => $avg !== null ? round($avg) : null,
            ];
        });

        $selectedId = $request->get('user', $anggotaTim->first()?->id);
        $selectedUser = $anggotaTim->firstWhere('id', $selectedId);

        $riwayat = collect();
        if ($selectedUser) {
            $riwayat = $selectedUser->weeklyCommitment()
                ->with(['actualMingguan', 'weeklyProgress', 'targetMingguan'])
                ->orderBy('created_at')
                ->get()
                ->take(-4)
                ->values()
                ->map(function ($wc, $i) {
                    $achievement = $wc->actualMingguan?->achievement_pct ?? $wc->weeklyProgress?->achievement_pct;
                    return (object) [
                        'label' => 'Minggu '.($wc->targetMingguan->minggu_ke ?? $i + 1),
                        'value' => $achievement !== null ? round($achievement) : null,
                    ];
                });
        }

        return view('atasan.trend-performance', [
            'rataRata' => $rataRata,
            'selectedUser' => $selectedUser,
            'riwayat' => $riwayat,
        ]);
    }
}