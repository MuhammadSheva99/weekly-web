<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendReminderProgress extends Command
{
    protected $signature = 'reminder:progress';
    protected $description = 'Kirim reminder ke user yang sudah isi Commitment tapi belum isi Weekly Progress';

    public function handle(): void
    {
        $users = User::whereHas('role', fn ($q) => $q->whereIn('nama', ['Karyawan', 'Atasan']))->get();

        $count = 0;
        foreach ($users as $user) {
            $commitment = $user->weeklyCommitment()
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->with('weeklyProgress')
                ->latest()
                ->first();

            if ($commitment && ! $commitment->weeklyProgress) {
                NotificationService::send(
                    $user, 'reminder_progress',
                    'Saatnya update weekly progress. Reminder rutin. Setiap hari Rabu pagi.'
                );
                $count++;
            }
        }

        $this->info("Reminder progress terkirim ke {$count} user.");
    }
}