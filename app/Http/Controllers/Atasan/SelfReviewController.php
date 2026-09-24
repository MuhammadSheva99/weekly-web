<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\ActualMingguan;
use App\Models\WeeklyCommitment;
use App\Models\SelfReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SelfReviewController extends Controller
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

        if ($commitments->isEmpty() || ! $commitments->first()->weeklyProgress) {
            return view('atasan.self-review.no-progress');
        }

        $sudahReview = $commitments->first()->selfReview;

        if ($sudahReview) {
            return view('atasan.self-review.edit-multi', ['commitments' => $commitments]);
        }

        return view('atasan.self-review.create-multi', ['commitments' => $commitments]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'weekly_commitment_ids' => 'required|array|min:1',
            'weekly_commitment_ids.*' => 'exists:weekly_commitment,id',
            'actual' => 'required|array',
            'actual.*' => 'required|numeric|min:0',
            'apa_berhasil' => 'nullable|string',
            'apa_gagal' => 'nullable|string',
            'kenapa_gagal' => 'nullable|string',
            'apa_beda' => 'nullable|string',
            'improvement_depan' => 'nullable|string',
            'kritik_diri' => 'nullable|string',
        ]);

        foreach ($data['weekly_commitment_ids'] as $commitmentId) {
            $commitment = WeeklyCommitment::with('targetMingguan')->findOrFail($commitmentId);
            $actualValue = $data['actual'][$commitmentId] ?? 0;
            $achievement = $commitment->target > 0 ? ($actualValue / $commitment->target) * 100 : 0;

            SelfReview::create([
                'weekly_commitment_id' => $commitment->id,
                'target_minggu' => $commitment->target,
                'actual' => $actualValue,
                'achievement_pct' => round($achievement, 2),
                'apa_berhasil' => $data['apa_berhasil'] ?? null,
                'apa_gagal' => $data['apa_gagal'] ?? null,
                'kenapa_gagal' => $data['kenapa_gagal'] ?? null,
                'apa_beda' => $data['apa_beda'] ?? null,
                'improvement_depan' => $data['improvement_depan'] ?? null,
                'kritik_diri' => $data['kritik_diri'] ?? null,
                'submitted_at' => now(),
            ]);

            if ($commitment->target_mingguan_id) {
                ActualMingguan::create([
                    'weekly_commitment_id' => $commitment->id,
                    'target_mingguan_id' => $commitment->target_mingguan_id,
                    'nilai_actual_final' => $actualValue,
                    'achievement_pct' => round($achievement, 2),
                    'locked_at' => now(),
                ]);
            }
        }

        return redirect()->route('atasan.dashboard')->with('status', 'Self Review berhasil disimpan untuk '.count($data['weekly_commitment_ids']).' KPI. Data actual mingguan sudah terkunci.');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'self_review_ids' => 'required|array|min:1',
            'self_review_ids.*' => 'exists:self_review,id',
            'actual' => 'required|array',
            'actual.*' => 'required|numeric|min:0',
            'apa_berhasil' => 'nullable|string',
            'apa_gagal' => 'nullable|string',
            'kenapa_gagal' => 'nullable|string',
            'apa_beda' => 'nullable|string',
            'improvement_depan' => 'nullable|string',
            'kritik_diri' => 'nullable|string',
        ]);

        foreach ($data['self_review_ids'] as $reviewId) {
            $review = SelfReview::findOrFail($reviewId);
            $commitment = $review->weeklyCommitment;
            $actualValue = $data['actual'][$reviewId] ?? 0;
            $achievement = $commitment->target > 0 ? ($actualValue / $commitment->target) * 100 : 0;

            $review->update([
                'actual' => $actualValue,
                'achievement_pct' => round($achievement, 2),
                'apa_berhasil' => $data['apa_berhasil'] ?? null,
                'apa_gagal' => $data['apa_gagal'] ?? null,
                'kenapa_gagal' => $data['kenapa_gagal'] ?? null,
                'apa_beda' => $data['apa_beda'] ?? null,
                'improvement_depan' => $data['improvement_depan'] ?? null,
                'kritik_diri' => $data['kritik_diri'] ?? null,
            ]);

            $commitment->actualMingguan?->update([
                'nilai_actual_final' => $actualValue,
                'achievement_pct' => round($achievement, 2),
            ]);
        }

        return redirect()->route('atasan.self-review.create')->with('status', 'Self Review berhasil diperbarui.');
    }
}