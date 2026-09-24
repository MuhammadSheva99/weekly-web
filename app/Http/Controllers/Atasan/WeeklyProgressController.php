<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\WeeklyCommitment;
use App\Models\WeeklyProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WeeklyProgressController extends Controller
{
    protected function commitmentBatchMingguIni()
    {
        $latest = WeeklyCommitment::where('user_id', Auth::id())
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->latest()
            ->first();

        if (! $latest) {
            return collect();
        }

        return $latest->batchSiblings();
    }

    public function create()
    {
        $commitments = $this->commitmentBatchMingguIni();

        if ($commitments->isEmpty()) {
            return view('atasan.weekly-progress.no-commitment');
        }

        $sudahProgress = $commitments->first()->weeklyProgress;

        if ($sudahProgress) {
            return view('atasan.weekly-progress.edit-multi', ['commitments' => $commitments]);
        }

        return view('atasan.weekly-progress.create-multi', ['commitments' => $commitments]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'weekly_commitment_ids' => 'required|array|min:1',
            'weekly_commitment_ids.*' => 'exists:weekly_commitment,id',
            'actual' => 'required|array',
            'actual.*' => 'required|numeric|min:0',
            'problem' => 'nullable|string',
            'analysis' => 'nullable|string',
            'solution' => 'nullable|string',
            'action_plan' => 'nullable|string',
        ]);

        foreach ($data['weekly_commitment_ids'] as $commitmentId) {
            $commitment = WeeklyCommitment::findOrFail($commitmentId);
            $actualValue = $data['actual'][$commitmentId] ?? 0;
            $achievement = $commitment->target > 0 ? ($actualValue / $commitment->target) * 100 : 0;

            WeeklyProgress::create([
                'weekly_commitment_id' => $commitment->id,
                'actual_sementara' => $actualValue,
                'achievement_pct' => round($achievement, 2),
                'problem' => $data['problem'] ?? null,
                'analysis' => $data['analysis'] ?? null,
                'solution' => $data['solution'] ?? null,
                'action_plan' => $data['action_plan'] ?? null,
                'submitted_at' => now(),
            ]);
        }

        return redirect()->route('atasan.dashboard')->with('status', 'Weekly Progress berhasil disimpan.');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'weekly_progress_ids' => 'required|array|min:1',
            'weekly_progress_ids.*' => 'exists:weekly_progress,id',
            'actual' => 'required|array',
            'actual.*' => 'required|numeric|min:0',
            'problem' => 'nullable|string',
            'analysis' => 'nullable|string',
            'solution' => 'nullable|string',
            'action_plan' => 'nullable|string',
        ]);

        foreach ($data['weekly_progress_ids'] as $progressId) {
            $progress = WeeklyProgress::findOrFail($progressId);
            $commitment = $progress->weeklyCommitment;
            $actualValue = $data['actual'][$progressId] ?? 0;
            $achievement = $commitment->target > 0 ? ($actualValue / $commitment->target) * 100 : 0;

            $progress->update([
                'actual_sementara' => $actualValue,
                'achievement_pct' => round($achievement, 2),
                'problem' => $data['problem'] ?? null,
                'analysis' => $data['analysis'] ?? null,
                'solution' => $data['solution'] ?? null,
                'action_plan' => $data['action_plan'] ?? null,
            ]);
        }

        return redirect()->route('atasan.weekly-progress.create')->with('status', 'Weekly Progress berhasil diperbarui.');
    }
}