<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\ActualMingguan;
use App\Models\WeeklyCommitment;
use App\Models\SelfReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SelfReviewController extends Controller
{
    protected function commitmentMingguIni()
    {
        return WeeklyCommitment::where('user_id', Auth::id())
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->with(['weeklyProgress', 'selfReview', 'targetMingguan'])
            ->latest()
            ->first();
    }

    public function create()
    {
        $commitment = $this->commitmentMingguIni();

        if (! $commitment || ! $commitment->weeklyProgress) {
            return view('karyawan.self-review.no-progress');
        }

        if ($commitment->selfReview) {
            return view('karyawan.self-review.edit', [
                'commitment' => $commitment,
                'review' => $commitment->selfReview,
            ]);
        }

        return view('karyawan.self-review.create', ['commitment' => $commitment]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'weekly_commitment_id' => 'required|exists:weekly_commitment,id',
            'actual' => 'required|numeric|min:0',
            'apa_berhasil' => 'nullable|string',
            'apa_gagal' => 'nullable|string',
            'kenapa_gagal' => 'nullable|string',
            'apa_beda' => 'nullable|string',
            'improvement_depan' => 'nullable|string',
            'kritik_diri' => 'nullable|string',
        ]);

        $commitment = WeeklyCommitment::with('targetMingguan')->findOrFail($data['weekly_commitment_id']);
        $achievement = $commitment->target > 0 ? ($data['actual'] / $commitment->target) * 100 : 0;

        SelfReview::create([
            'weekly_commitment_id' => $commitment->id,
            'target_minggu' => $commitment->target,
            'actual' => $data['actual'],
            'achievement_pct' => round($achievement, 2),
            'apa_berhasil' => $data['apa_berhasil'] ?? null,
            'apa_gagal' => $data['apa_gagal'] ?? null,
            'kenapa_gagal' => $data['kenapa_gagal'] ?? null,
            'apa_beda' => $data['apa_beda'] ?? null,
            'improvement_depan' => $data['improvement_depan'] ?? null,
            'kritik_diri' => $data['kritik_diri'] ?? null,
            'submitted_at' => now(),
        ]);

        ActualMingguan::create([
            'weekly_commitment_id' => $commitment->id,
            'target_mingguan_id' => $commitment->target_mingguan_id,
            'nilai_actual_final' => $data['actual'],
            'achievement_pct' => round($achievement, 2),
            'locked_at' => now(),
        ]);

        return redirect()->route('karyawan.dashboard')->with('status', 'Self Review berhasil disimpan. Data actual mingguan sudah terkunci.');
    }

    public function update(Request $request, SelfReview $review)
    {
        $data = $request->validate([
            'actual' => 'required|numeric|min:0',
            'apa_berhasil' => 'nullable|string',
            'apa_gagal' => 'nullable|string',
            'kenapa_gagal' => 'nullable|string',
            'apa_beda' => 'nullable|string',
            'improvement_depan' => 'nullable|string',
            'kritik_diri' => 'nullable|string',
        ]);

        $commitment = $review->weeklyCommitment;
        $achievement = $commitment->target > 0 ? ($data['actual'] / $commitment->target) * 100 : 0;

        $review->update([
            'actual' => $data['actual'],
            'achievement_pct' => round($achievement, 2),
            'apa_berhasil' => $data['apa_berhasil'] ?? null,
            'apa_gagal' => $data['apa_gagal'] ?? null,
            'kenapa_gagal' => $data['kenapa_gagal'] ?? null,
            'apa_beda' => $data['apa_beda'] ?? null,
            'improvement_depan' => $data['improvement_depan'] ?? null,
            'kritik_diri' => $data['kritik_diri'] ?? null,
        ]);

        $commitment->actualMingguan?->update([
            'nilai_actual_final' => $data['actual'],
            'achievement_pct' => round($achievement, 2),
        ]);

        return redirect()->route('karyawan.self-review.create')->with('status', 'Self Review berhasil diperbarui.');
    }
}