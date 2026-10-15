<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WeeklyCommitment;
use App\Support\WeeklyHistory; // Import class WeeklyHistory
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HistoryWeeklyController extends Controller
{
    /** Management melihat Karyawan, Atasan, HRD, dan dirinya sendiri */
    private function orangQuery()
    {
        return User::whereHas('role', function ($r) {
            $r->whereIn('nama', ['Karyawan', 'Atasan', 'HRD']);
        })->orWhere('id', auth()->id());
    }

    public function index(Request $request)
    {
        $periode = $request->get('periode', now()->format('Y-m'));
        $status = $request->get('status');
        $userId = $request->get('user');
        $bulan = Carbon::createFromFormat('Y-m', $periode);

        $daftarUser = $this->orangQuery()->orderBy('nama')->get();

        $commitments = WeeklyCommitment::whereIn('user_id', $daftarUser->pluck('id'))
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->whereMonth('created_at', $bulan->month)
            ->whereYear('created_at', $bulan->year)
            ->with(WeeklyHistory::relasi())
            ->get();

        $riwayat = WeeklyHistory::riwayat($commitments, $daftarUser->keyBy('id'));

        if ($status === 'selesai') {
            $riwayat = $riwayat->filter(fn ($r) => $r->is_final)->values();
        } elseif ($status === 'berjalan') {
            $riwayat = $riwayat->filter(fn ($r) => ! $r->is_final)->values();
        }

        return view('management.history-weekly.index', [
            'riwayat' => $riwayat,
            'daftarUser' => $daftarUser,
            'periode' => $periode,
            'status' => $status,
            'userId' => $userId,
        ]);
    }

    public function show(User $user, string $batch)
    {
        abort_unless($this->orangQuery()->whereKey($user->id)->exists(), 404);

        $commitments = WeeklyCommitment::where('user_id', $user->id)
            ->where(fn ($q) => $q->where('batch_id', $batch)->orWhere('id', $batch))
            ->with(WeeklyHistory::relasi())
            ->get()
            ->sortBy('id')
            ->values();

        abort_if($commitments->isEmpty(), 404);

        return view('management.history-weekly.index', WeeklyHistory::detail($commitments) + [
            'mode' => 'detail',
            'pic' => $user,
            'kembali' => route('management.history-weekly.index', ['user' => $user->id]),
            'urlFeedback' => route('management.monitoring-karyawan.index'),
        ]);
    }
}