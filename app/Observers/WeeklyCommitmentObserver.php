<?php

namespace App\Observers;

use App\Models\NotificationWpm;
use App\Models\User;
use App\Models\WeeklyCommitment;

class WeeklyCommitmentObserver
{
    public function created(WeeklyCommitment $commitment): void
    {
        // Satu batch multi-KPI menghasilkan banyak baris, notifikasi cukup dikirim sekali
        if ($commitment->batch_id && WeeklyCommitment::where('batch_id', $commitment->batch_id)->count() > 1) {
            return;
        }

        $user = $commitment->user;
        $namaDivisi = $user->divisi->nama ?? '-';
        $pesan = "{$user->nama} ({$namaDivisi}) telah mengisi Weekly Commitment.";

        if ($user->atasan) {
            NotificationWpm::create([
                'user_id' => $user->atasan->id,
                'type' => 'commitment_submitted',
                'message' => $pesan,
                'is_read' => false,
                'sent_at' => now(),
            ]);
        }

        // Semua HRD, kecuali kalau yang mengisi adalah HRD itu sendiri
        User::whereHas('role', fn ($q) => $q->where('nama', 'HRD'))
            ->where('id', '!=', $user->id)
            ->get()
            ->each(function ($hrd) use ($pesan) {
                NotificationWpm::create([
                    'user_id' => $hrd->id,
                    'type' => 'commitment_submitted',
                    'message' => $pesan,
                    'is_read' => false,
                    'sent_at' => now(),
                ]);
            });
    }
}