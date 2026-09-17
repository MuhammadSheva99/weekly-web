<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendAlertBelumSubmitTim extends Command
{
    protected $signature = 'alert:belum-submit-tim';
    protected $description = 'Kirim alert ke Atasan kalau ada anggota tim belum submit Weekly Commitment';

    public function handle(): void
    {
        $atasanList = User::whereHas('role', fn ($q) => $q->where('nama', 'Atasan'))->get();

        $count = 0;
        foreach ($atasanList as $atasan) {
            $anggotaTim = User::where('atasan_id', $atasan->id)->get();

            foreach ($anggotaTim as $anggota) {
                $sudahSubmit = $anggota->weeklyCommitment()
                    ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                    ->exists();

                if (! $sudahSubmit) {
                    NotificationService::send(
                        $atasan, 'alert_belum_submit_anggota',
                        "{$anggota->nama} belum submit weekly commitment. Deadline Senin 23:59 sudah lewat."
                    );
                    $count++;
                }
            }
        }

        $this->info("Alert belum submit tim terkirim ke {$count} kasus.");
    }
}