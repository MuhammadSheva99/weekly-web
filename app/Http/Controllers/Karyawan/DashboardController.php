<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\WeeklyCommitment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $mingguKe = \App\Support\MingguIni::mingguKe();

        // Semua KPI dari submit mingguan terbaru minggu ini (satu baris per KPI).
        $commitments = WeeklyCommitment::where('user_id', Auth::id())
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->with(['targetMingguan.targetBulanan.kpi', 'weeklyProgress', 'selfReview', 'actualMingguan'])
            ->orderByDesc('created_at')
            ->get();

        $batchTerbaru = $commitments->first()?->batch_id;
        $commitments = ($batchTerbaru ? $commitments->where('batch_id', $batchTerbaru) : $commitments->take(1))
            ->sortBy('id')
            ->values();

        // History: satu baris per MINGGU (minggu ke-N di bulan tertentu), walau ada beberapa submit.
        // Satu submit = satu batch_id (satu baris per KPI); data lama tanpa batch_id pakai id.
        $batchKeys = WeeklyCommitment::where('user_id', Auth::id())
            ->selectRaw('COALESCE(batch_id, id) as bkey, MAX(created_at) as terakhir')
            ->groupBy('bkey')
            ->orderByDesc('terakhir')
            ->limit(16)
            ->pluck('bkey');

        $historyWeekly = WeeklyCommitment::where('user_id', Auth::id())
            ->where(function ($q) use ($batchKeys) {
                $q->whereIn('batch_id', $batchKeys)->orWhereIn('id', $batchKeys);
            })
            ->with(['targetMingguan', 'selfReview'])
            ->get()
            ->groupBy(function ($wc) {
                $minggu = $wc->targetMingguan->minggu_ke ?? '?';

                return $minggu.'|'.$wc->created_at->format('Y-m');
            })
            ->map(function ($group) {
                $terbaru = $group->sortByDesc('created_at')->first();
                $mingguKe = $terbaru->targetMingguan->minggu_ke ?? null;

                // Final hanya kalau semua KPI di minggu ini sudah di-self review.
                $semuaFinal = $group->every(fn ($wc) => $wc->selfReview);
                $status = $semuaFinal
                    ? round($group->avg(fn ($wc) => $wc->selfReview->achievement_pct)).'%'
                    : 'Belum final';

                return (object) [
                    'minggu_ke' => $mingguKe ?? '-',
                    'label' => $terbaru->created_at->translatedFormat('\M\i\n\g\g\u '.($mingguKe ?? '?').' - F'),
                    'status' => $status,
                    // detail dibuka untuk submit terbaru di minggu ini
                    'key' => $terbaru->batch_id ?? $terbaru->id,
                    'terakhir' => $group->max('created_at'),
                ];
            })
            ->sortByDesc('terakhir')
            ->take(4)
            ->values();

        return view('karyawan.dashboard', [
            'mingguKe' => $mingguKe,
            'commitments' => $commitments,
            'historyWeekly' => $historyWeekly,
        ]);
    }
}