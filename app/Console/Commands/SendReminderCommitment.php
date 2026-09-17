<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendReminderCommitment extends Command
{
    protected $signature = 'reminder:commitment';
    protected $description = 'Kirim reminder ke semua Karyawan/Atasan untuk mengisi Weekly Commitment';

    public function handle(): void
    {
        $users = User::whereHas('role', fn ($q) => $q->whereIn('nama', ['Karyawan', 'Atasan']))->get();

        $count = 0;
        foreach ($users as $user) {
            NotificationService::send(
                $user, 'reminder_commitment',
                'Saatnya mengisi weekly commitment. Reminder rutin. Setiap hari Senin pagi.'
            );
            $count++;
        }

        $this->info("Reminder commitment terkirim ke {$count} user.");
    }
}