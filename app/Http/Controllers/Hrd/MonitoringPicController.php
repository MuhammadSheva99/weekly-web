<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\User;
use Illuminate\Http\Request;

class MonitoringPicController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereHas('role', fn ($q) => $q->where('nama', 'Karyawan'))
            ->with([
                'divisi',
                'weeklyCommitment' => fn ($q) => $q->latest()->limit(1)->with([
                    'targetMingguan.targetBulanan.kpi',
                    'weeklyProgress',
                    'selfReview',
                    'actualMingguan',
                ]),
            ]);

        if ($request->filled('divisi_id')) {
            $query->where('divisi_id', $request->divisi_id);
        }

        $karyawan = $query->orderBy('nama')->get()->map(function ($user) {
            $commitment = $user->weeklyCommitment->first();

            $kpiNama = $commitment?->targetMingguan?->targetBulanan?->kpi?->nama_kpi;
            $target = $commitment?->target;
            $actual = $commitment?->actualMingguan?->nilai_actual_final
                ?? $commitment?->weeklyProgress?->actual_sementara;
            $achievement = $commitment?->actualMingguan?->achievement_pct
                ?? $commitment?->weeklyProgress?->achievement_pct;

            if (! $commitment) {
                $status = 'Belum Submit';
            } elseif ($commitment->actualMingguan) {
                $status = 'Lengkap';
            } else {
                $status = 'Progress saja';
            }

            return (object) [
                'user' => $user,
                'kpi' => $kpiNama,
                'target' => $target,
                'actual' => $actual,
                'achievement' => $achievement,
                'status' => $status,
            ];
        });

        return view('hrd.monitoring-pic', [
            'karyawan' => $karyawan,
            'divisiList' => Divisi::orderBy('nama')->get(),
        ]);
    }
}