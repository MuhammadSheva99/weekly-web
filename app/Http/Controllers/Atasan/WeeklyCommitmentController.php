<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\TargetMingguan;
use App\Models\WeeklyCommitment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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

        $targetMingguanList = TargetMingguan::whereHas('targetBulanan', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->whereMonth('periode', now()->month)
                  ->whereYear('periode', now()->year);
            })
            ->where('minggu_ke', $mingguKe)
            ->with('targetBulanan.kpi')
            ->get();

        $existing = WeeklyCommitment::whereIn('target_mingguan_id', $targetMingguanList->pluck('id'))->first();

        if ($targetMingguanList->isEmpty()) {
            $existingManual = WeeklyCommitment::where('user_id', $user->id)
                ->whereNull('target_mingguan_id')
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->latest()
                ->first();

            if ($existingManual) {
                return view('atasan.weekly-commitment.edit', [
                    'targetMingguan' => null,
                    'mingguKe' => $mingguKe,
                    'commitment' => $existingManual,
                ]);
            }

            return view('atasan.weekly-commitment.create', [
                'targetMingguan' => null,
                'mingguKe' => $mingguKe,
            ]);
        }

        if ($existing) {
            return view('atasan.weekly-commitment.edit-multi', [
                'mingguKe' => $mingguKe,
                'commitments' => $existing->batchSiblings(),
                'batchId' => $existing->batch_id,
            ]);
        }

        return view('atasan.weekly-commitment.create-multi', [
            'targetMingguanList' => $targetMingguanList,
            'mingguKe' => $mingguKe,
        ]);
    }

    public function store(Request $request)
    {
        if ($request->has('mode') && $request->mode === 'manual') {
            return $this->storeManual($request);
        }

        $data = $request->validate([
            'big_goal' => 'required|string',
            'prioritas' => 'required|array|size:3',
            'prioritas.*' => 'required|string',
            'output_deliverable' => 'required|string',
            'target_mingguan_ids' => 'required|array|min:1',
            'target_mingguan_ids.*' => 'exists:target_mingguan,id',
        ]);

        $batchId = Str::uuid();

        foreach ($data['target_mingguan_ids'] as $targetMingguanId) {
            $targetMingguan = TargetMingguan::with('targetBulanan.kpi')->findOrFail($targetMingguanId);

            WeeklyCommitment::create([
                'target_mingguan_id' => $targetMingguan->id,
                'user_id' => Auth::id(),
                'batch_id' => $batchId,
                'big_goal' => $data['big_goal'],
                'prioritas' => array_values($data['prioritas']),
                'metric' => [[
                    'nama_metric' => $targetMingguan->targetBulanan->kpi->nama_kpi,
                    'target' => $targetMingguan->nilai_target,
                ]],
                'target' => $targetMingguan->nilai_target,
                'output_deliverable' => $data['output_deliverable'],
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);
        }

        return redirect()->route('atasan.dashboard')->with('status', 'Weekly Commitment berhasil disimpan untuk '.count($data['target_mingguan_ids']).' KPI.');
    }

    protected function storeManual(Request $request)
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

        WeeklyCommitment::create([
            'target_mingguan_id' => null,
            'user_id' => Auth::id(),
            'batch_id' => Str::uuid(),
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
        if (! $commitment->target_mingguan_id) {
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

        $data = $request->validate([
            'big_goal' => 'required|string',
            'prioritas' => 'required|array|size:3',
            'prioritas.*' => 'required|string',
            'output_deliverable' => 'required|string',
        ]);

        WeeklyCommitment::where('batch_id', $commitment->batch_id)->update([
            'big_goal' => $data['big_goal'],
            'prioritas' => json_encode(array_values($data['prioritas'])),
            'output_deliverable' => $data['output_deliverable'],
        ]);

        return redirect()->route('atasan.weekly-commitment.create')->with('status', 'Weekly Commitment berhasil diperbarui.');
    }
}