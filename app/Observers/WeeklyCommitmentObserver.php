<?php

namespace App\Observers;

use App\Models\WeeklyCommitment;
use App\Models\NotificationWpm;

class WeeklyCommitmentObserver
{
    public function created(WeeklyCommitment $commitment): void
    {
        $user = $commitment->user;
        $atasan = $user->atasan;

        if ($atasan) {
            NotificationWpm::create([
                'user_id' => $atasan->id,
                'type' => 'commitment_submitted',
                'message' => "{$user->nama} ({$user->divisi->nama}) telah mengisi Weekly Commitment.",
                'is_read' => false,
                'sent_at' => now(),
            ]);
        }

        // Juga beri tahu semua HRD
        \App\Models\User::whereHas('role', fn ($q) => $q->where('nama', 'HRD'))
            ->get()
            ->each(function ($hrd) use ($user) {
                NotificationWpm::create([
                    'user_id' => $hrd->id,
                    'type' => 'commitment_submitted',
                    'message' => "{$user->nama} ({$user->divisi->nama}) telah mengisi Weekly Commitment.",
                    'is_read' => false,
                    'sent_at' => now(),
                ]);
            });
    }
}