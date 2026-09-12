<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\KpiValueCalculator;
use Illuminate\Support\Carbon;

class PicDetailController extends Controller
{
    public function show(User $user)
    {
        $periode = now();

        $ringkasan = KpiValueCalculator::calculate($user, $periode);

        $riwayatMingguan = $user->weeklyCommitment()
            ->with(['weeklyProgress', 'selfReview', 'actualMingguan', 'targetMingguan.targetBulanan.kpi'])
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        return view('hrd.pic-detail', [
            'pic' => $user,
            'ringkasan' => $ringkasan,
            'riwayatMingguan' => $riwayatMingguan,
            'periode' => $periode,
        ]);
    }
}