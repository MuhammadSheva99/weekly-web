<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\KpiMaster;
use App\Models\TargetBulanan;
use App\Models\TargetMingguan;
use App\Models\User;
use Illuminate\Http\Request;

class AssignKpiController extends Controller
{
    public function index(Request $request)
    {
        $divisiList = Divisi::orderBy('nama')->get();
        $selectedDivisiId = $request->get('divisi_id');

        $kpiDivisi = collect();
        $userDivisi = collect();

        $periode = $request->get('periode', now()->format('Y-m'));
        $periodeDate = \Carbon\Carbon::createFromFormat('Y-m', $periode)->startOfMonth();

        if ($selectedDivisiId) {
            $kpiDivisi = KpiMaster::where('divisi_id', $selectedDivisiId)->where('is_active', true)->get();
            $userDivisi = User::where('divisi_id', $selectedDivisiId)
                ->whereHas('role', fn ($q) => $q->whereIn('nama', ['Karyawan', 'Atasan']))
                ->orderBy('nama')
                ->get();

            $sampleUserId = $userDivisi->first()?->id;

            $kpiDivisi = $kpiDivisi->map(function ($kpi) use ($sampleUserId, $periodeDate) {
                $existing = $sampleUserId
                    ? TargetBulanan::where('kpi_id', $kpi->id)
                        ->where('user_id', $sampleUserId)
                        ->whereMonth('periode', $periodeDate->month)
                        ->whereYear('periode', $periodeDate->year)
                        ->first()
                    : null;

                if (! $existing && $sampleUserId) {
                    $existing = TargetBulanan::where('kpi_id', $kpi->id)
                        ->where('user_id', $sampleUserId)
                        ->latest('periode')
                        ->first();
                }

                $kpi->prefill_bobot = $existing->bobot ?? '';
                $kpi->prefill_target = $existing->nilai_target ?? '';

                return $kpi;
            });
        }

        $allUsers = User::whereHas('role', fn ($q) => $q->whereIn('nama', ['Karyawan', 'Atasan']))
            ->with('divisi')
            ->orderBy('nama')
            ->get();

        return view('admin.assign-kpi.index', [
            'divisiList' => $divisiList,
            'selectedDivisiId' => $selectedDivisiId,
            'kpiDivisi' => $kpiDivisi,
            'userDivisi' => $userDivisi,
            'periode' => $periode,
            'allUsers' => $allUsers,
            'allKpi' => KpiMaster::where('is_active', true)->with('divisi')->orderBy('nama_kpi')->get(),
        ]);
    }

    public function storeDivisi(Request $request)
    {
        $data = $request->validate([
            'periode' => 'required|date_format:Y-m',
            'divisi_id' => 'required|exists:divisi,id',
            'kpi' => 'required|array',
            'kpi.*.kpi_id' => 'required|exists:kpi_master,id',
            'kpi.*.bobot' => 'required|numeric|min:0|max:100',
            'kpi.*.target' => 'required|numeric|min:0',
        ]);

        $periode = \Carbon\Carbon::createFromFormat('Y-m', $data['periode'])->startOfMonth();

        $users = User::where('divisi_id', $data['divisi_id'])
            ->whereHas('role', fn ($q) => $q->whereIn('nama', ['Karyawan', 'Atasan']))
            ->get();

        $count = 0;
        foreach ($users as $user) {
            foreach ($data['kpi'] as $item) {
                $targetBulanan = TargetBulanan::updateOrCreate(
                    ['kpi_id' => $item['kpi_id'], 'user_id' => $user->id, 'periode' => $periode],
                    ['nilai_target' => $item['target'], 'bobot' => $item['bobot']]
                );

                for ($minggu = 1; $minggu <= 4; $minggu++) {
                    TargetMingguan::updateOrCreate(
                        ['target_bulanan_id' => $targetBulanan->id, 'minggu_ke' => $minggu],
                        ['nilai_target' => round($item['target'] / 4, 2)]
                    );
                }

                $count++;
            }
        }

        return back()->with('status', "Target berhasil di-assign ke {$users->count()} karyawan ({$count} baris target, otomatis dipecah 4 minggu).");
    }

    public function storeIndividu(Request $request)
    {
        $data = $request->validate([
            'periode' => 'required|date_format:Y-m',
            'user_id' => 'required|exists:users,id',
            'kpi_id' => 'required|exists:kpi_master,id',
            'bobot' => 'required|numeric|min:0|max:100',
            'target' => 'required|numeric|min:0',
        ]);

        $periode = \Carbon\Carbon::createFromFormat('Y-m', $data['periode'])->startOfMonth();

        $targetBulanan = TargetBulanan::updateOrCreate(
            ['kpi_id' => $data['kpi_id'], 'user_id' => $data['user_id'], 'periode' => $periode],
            ['nilai_target' => $data['target'], 'bobot' => $data['bobot']]
        );

        for ($minggu = 1; $minggu <= 4; $minggu++) {
            TargetMingguan::updateOrCreate(
                ['target_bulanan_id' => $targetBulanan->id, 'minggu_ke' => $minggu],
                ['nilai_target' => round($data['target'] / 4, 2)]
            );
        }

        return back()->with('status', 'Target berhasil di-assign, otomatis dipecah 4 minggu.');
    }
}