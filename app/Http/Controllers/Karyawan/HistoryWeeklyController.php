<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\WeeklyCommitment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class HistoryWeeklyController extends Controller
{
    public function index(Request $request)
    {
        $periode = $request->get('periode', now()->format('Y-m'));
        $status = $request->get('status');

        $bulan = Carbon::createFromFormat('Y-m', $periode);

        $query = WeeklyCommitment::where('user_id', Auth::id())
            ->whereMonth('created_at', $bulan->month)
            ->whereYear('created_at', $bulan->year)
            ->with(['targetMingguan', 'weeklyProgress', 'selfReview', 'actualMingguan']);

        $riwayat = $query->orderBy('created_at')->get()->map(function ($wc) {
            $achievement = $wc->actualMingguan?->achievement_pct;
            $actual = $wc->actualMingguan?->nilai_actual_final ?? $wc->weeklyProgress?->actual_sementara;

            $statusLabel = $wc->selfReview ? round($achievement).'%' : 'Berjalan';

            return (object) [
                'minggu_label' => 'Minggu '.($wc->targetMingguan->minggu_ke ?? '-').' - '.$wc->created_at->translatedFormat('M'),
                'big_goal' => $wc->big_goal,
                'target' => $wc->target,
                'actual' => $actual,
                'status_label' => $statusLabel,
                'is_final' => (bool) $wc->selfReview,
                'achievement' => $achievement,
            ];
        });

        // Filter status setelah dihitung, karena "Berjalan" vs persentase itu logic turunan
        if ($status === 'selesai') {
            $riwayat = $riwayat->filter(fn ($r) => $r->is_final);
        } elseif ($status === 'berjalan') {
            $riwayat = $riwayat->filter(fn ($r) => ! $r->is_final);
        }

        return view('karyawan.history-weekly', [
            'riwayat' => $riwayat->values(),
            'periode' => $periode,
            'status' => $status,
        ]);
    }
}