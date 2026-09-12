<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\WeeklyCommitment;
use App\Models\WeeklyProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WeeklyProgressController extends Controller
{
    protected function commitmentMingguIni()
    {
        return WeeklyCommitment::where('user_id', Auth::id())
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->with('weeklyProgress')
            ->latest()
            ->first();
    }

    public function create()
    {
        $commitment = $this->commitmentMingguIni();

        if (! $commitment) {
            return view('karyawan.weekly-progress.no-commitment');
        }

        if ($commitment->weeklyProgress) {
            return view('karyawan.weekly-progress.edit', [
                'commitment' => $commitment,
                'progress' => $commitment->weeklyProgress,
            ]);
        }

        return view('karyawan.weekly-progress.create', ['commitment' => $commitment]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'weekly_commitment_id' => 'required|exists:weekly_commitment,id',
            'actual_sementara' => 'required|numeric|min:0',
            'problem' => 'nullable|string',
            'analysis' => 'nullable|string',
            'solution' => 'nullable|string',
            'action_plan' => 'nullable|string',
        ]);

        $commitment = WeeklyCommitment::findOrFail($data['weekly_commitment_id']);
        $achievement = $commitment->target > 0 ? ($data['actual_sementara'] / $commitment->target) * 100 : 0;

        WeeklyProgress::create([
            'weekly_commitment_id' => $commitment->id,
            'actual_sementara' => $data['actual_sementara'],
            'achievement_pct' => round($achievement, 2),
            'problem' => $data['problem'] ?? null,
            'analysis' => $data['analysis'] ?? null,
            'solution' => $data['solution'] ?? null,
            'action_plan' => $data['action_plan'] ?? null,
            'submitted_at' => now(),
        ]);

        return redirect()->route('karyawan.dashboard')->with('status', 'Weekly Progress berhasil disimpan.');
    }

    public function update(Request $request, WeeklyProgress $progress)
    {
        $data = $request->validate([
            'actual_sementara' => 'required|numeric|min:0',
            'problem' => 'nullable|string',
            'analysis' => 'nullable|string',
            'solution' => 'nullable|string',
            'action_plan' => 'nullable|string',
        ]);

        $commitment = $progress->weeklyCommitment;
        $achievement = $commitment->target > 0 ? ($data['actual_sementara'] / $commitment->target) * 100 : 0;

        $progress->update([
            'actual_sementara' => $data['actual_sementara'],
            'achievement_pct' => round($achievement, 2),
            'problem' => $data['problem'] ?? null,
            'analysis' => $data['analysis'] ?? null,
            'solution' => $data['solution'] ?? null,
            'action_plan' => $data['action_plan'] ?? null,
        ]);

        return redirect()->route('karyawan.weekly-progress.create')->with('status', 'Weekly Progress berhasil diperbarui.');
    }
}