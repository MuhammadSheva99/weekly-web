<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\User;

class UnderperformController extends Controller
{
    public function index()
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

            // Cek apakah achievement turun terus tiap minggu (strictly menurun)
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

        return view('hrd.underperform', ['data' => $result]);
    }
}