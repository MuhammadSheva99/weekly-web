<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WeeklyCommitment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class MonitoringTimController extends Controller
{
    public function index(Request $request)
    {
        $anggotaTim = User::where('atasan_id', Auth::id())->orderBy('nama')->get();

        // Tentukan rentang minggu yang dipilih (default: minggu berjalan)
        $mingguSelected = $request->get('minggu', now()->format('Y-\WW'));
        [$tahun, $mingguKe] = $this->parseMinggu($mingguSelected);

        $awalMinggu = Carbon::now()->startOfMonth()->addWeeks($mingguKe - 1)->startOfWeek();
        $akhirMinggu = $awalMinggu->copy()->endOfWeek();

        $status = $request->get('status');

        $data = $anggotaTim->map(function ($user) use ($awalMinggu, $akhirMinggu) {
            $commitment = $user->weeklyCommitment()
                ->whereBetween('created_at', [$awalMinggu, $akhirMinggu])
                ->with(['weeklyProgress', 'selfReview', 'actualMingguan'])
                ->latest()
                ->first();

            $target = $commitment?->target;
            $actual = $commitment?->actualMingguan?->nilai_actual_final ?? $commitment?->weeklyProgress?->actual_sementara;
            $achievement = $commitment?->actualMingguan?->achievement_pct ?? $commitment?->weeklyProgress?->achievement_pct;

            if (! $commitment) {
                $statusSubmit = 'Belum Submit';
            } elseif ($commitment->selfReview) {
                $statusSubmit = 'Lengkap';
            } else {
                $statusSubmit = 'Progress saja';
            }

            return (object) [
                'user' => $user,
                'big_goal' => $commitment?->big_goal,
                'target' => $target,
                'actual' => $actual,
                'achievement' => $achievement !== null ? round($achievement) : null,
                'status_submit' => $statusSubmit,
            ];
        });

        if ($status) {
            $data = $data->filter(fn ($d) => $d->status_submit === $status);
        }

        return view('atasan.monitoring-tim', [
            'data' => $data->values(),
            'mingguLabel' => 'Minggu '.$mingguKe.' - '.$awalMinggu->translatedFormat('F Y'),
            'status' => $status,
        ]);
    }

    protected function parseMinggu(string $value): array
    {
        // Sederhana: pakai minggu berjalan berdasarkan tanggal hari ini
        $mingguKe = min((int) ceil(now()->day / 7), 4);
        return [now()->year, $mingguKe];
    }
}