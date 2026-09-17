<?php

namespace App\Console\Commands;

use App\Models\KpiPerformance;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendAlertUnderperformTim extends Command
{
    protected $signature = 'alert:underperform-tim';
    protected $description = 'Kirim alert ke Atasan kalau anggota tim repeatedly underperform';

    public function handle(): void
    {
        $atasanList = User::whereHas('role', fn ($q) => $q->where('nama', 'Atasan'))->get();

        $count = 0;
        foreach ($atasanList as $atasan) {
            $anggotaTim = User::where('atasan_id', $atasan->id)->get();

            foreach ($anggotaTim as $anggota) {
                $recent = KpiPerformance::where('user_id', $anggota->id)
                    ->orderByDesc('periode')
                    ->limit(4)
                    ->pluck('achievement_pct');

                $isDeclining = $recent->count() >= 4 && $recent->values()
                    ->every(fn ($v, $i) => $i === 0 || $v < $recent[$i - 1]);

                if ($isDeclining) {
                    NotificationService::send(
                        $atasan, 'alert_underperform_anggota',
                        "{$anggota->nama} repeatedly underperform. Achievement menurun 4 minggu berturut-turut. Pertimbangkan sesi coaching."
                    );
                    $count++;
                }
            }
        }

        $this->info("Alert underperform tim terkirim ke {$count} kasus.");
    }
}