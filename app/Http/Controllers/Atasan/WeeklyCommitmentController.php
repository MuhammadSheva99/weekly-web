<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\TargetMingguan;
use App\Models\WeeklyCommitment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class WeeklyCommitmentController extends Controller
{
    protected function mingguKeSekarang(): int
    {
        return min((int) ceil(now()->day / 7), 4);
    }

    public function create()
    {
        $user = Auth::user();
        $mingguKe = $this->mingguKeSekarang();

        $targetMingguan = TargetMingguan::whereHas('targetBulanan', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->whereMonth('periode', now()->month)
                  ->whereYear('periode', now()->year);
            })
            ->where('minggu_ke', $mingguKe)
            ->with('targetBulanan.kpi')
            ->first();

        if (! $targetMingguan) {
            return view('atasan.weekly-commitment.no-target');
        }

        $existing = WeeklyCommitment::where('target_mingguan_id', $targetMingguan->id)->first();

        if ($existing) {
            return view('atasan.weekly-commitment.edit', [
                'targetMingguan' => $targetMingguan,
                'mingguKe' => $mingguKe,
                'commitment' => $existing,
            ]);
        }

        return view('atasan.weekly-commitment.create', [
            'targetMingguan' => $targetMingguan,
            'mingguKe' => $mingguKe,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'target_mingguan_id' => 'required|exists:target_mingguan,id',
            'big_goal' => 'required|string',
            'prioritas' => 'required|array|size:3',
            'prioritas.*' => 'required|string',
            'metric_nama' => 'required|array|size:5',
            'metric_nama.*' => 'required|string',
            'metric_target' => 'required|array|size:5',
            'metric_target.*' => 'nullable|string',
            'target' => 'required|numeric|min:0',
            'output_deliverable' => 'required|string',
        ]);

        $metric = collect($data['metric_nama'])->map(function ($nama, $i) use ($data) {
            return ['nama_metric' => $nama, 'target' => $data['metric_target'][$i] ?? null];
        })->values()->toArray();

        WeeklyCommitment::create([
            'target_mingguan_id' => $data['target_mingguan_id'],
            'user_id' => Auth::id(),
            'big_goal' => $data['big_goal'],
            'prioritas' => array_values($data['prioritas']),
            'metric' => $metric,
            'target' => $data['target'],
            'output_deliverable' => $data['output_deliverable'],
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('atasan.dashboard')->with('status', 'Weekly Commitment berhasil disimpan.');
    }

    public function update(Request $request, WeeklyCommitment $commitment)
    {
        $data = $request->validate([
            'big_goal' => 'required|string',
            'prioritas' => 'required|array|size:3',
            'prioritas.*' => 'required|string',
            'metric_nama' => 'required|array|size:5',
            'metric_nama.*' => 'required|string',
            'metric_target' => 'required|array|size:5',
            'metric_target.*' => 'nullable|string',
            'target' => 'required|numeric|min:0',
            'output_deliverable' => 'required|string',
        ]);

        $metric = collect($data['metric_nama'])->map(function ($nama, $i) use ($data) {
            return ['nama_metric' => $nama, 'target' => $data['metric_target'][$i] ?? null];
        })->values()->toArray();

        $commitment->update([
            'big_goal' => $data['big_goal'],
            'prioritas' => array_values($data['prioritas']),
            'metric' => $metric,
            'target' => $data['target'],
            'output_deliverable' => $data['output_deliverable'],
        ]);

        return redirect()->route('atasan.weekly-commitment.create')->with('status', 'Weekly Commitment berhasil diperbarui.');
    }
}