<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendReminderReview extends Command
{
    protected $signature = 'reminder:review';
    protected $description = 'Kirim reminder ke user yang sudah isi Progress tapi belum isi Self Review';

    public function handle(): void
    {
        $users = User::whereHas('role', fn ($q) => $q->whereIn('nama', ['Karyawan', 'Atasan']))->get();

        $count = 0;
        foreach ($users as $user) {
            $commitment = $user->weeklyCommitment()
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->with(['weeklyProgress', 'selfReview'])
                ->latest()
                ->first();

            if ($commitment && $commitment->weeklyProgress && ! $commitment->selfReview) {
                NotificationService::send(
                    $user, 'reminder_review',
                    'Saatnya melakukan self review. Reminder rutin. Setiap hari Jumat pagi.'
                );
                $count++;
            }
        }

        $this->info("Reminder review terkirim ke {$count} user.");
    }
}