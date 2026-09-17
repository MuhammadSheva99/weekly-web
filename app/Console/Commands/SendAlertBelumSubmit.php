<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendAlertBelumSubmit extends Command
{
    protected $signature = 'alert:belum-submit';
    protected $description = 'Kirim alert ke user yang belum submit Weekly Commitment menjelang deadline Senin';

    public function handle(): void
    {
        $users = User::whereHas('role', fn ($q) => $q->whereIn('nama', ['Karyawan', 'Atasan']))->get();

        $count = 0;
        foreach ($users as $user) {
            $sudahSubmit = $user->weeklyCommitment()
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->exists();

            if (! $sudahSubmit) {
                NotificationService::send(
                    $user, 'alert_belum_submit',
                    'Weekly commitment belum disubmit. Deadline Senin 23:59. Kurang dari 2 jam lagi.'
                );
                $count++;
            }
        }

        $this->info("Alert belum submit terkirim ke {$count} user.");
    }
}