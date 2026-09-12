<?php

namespace Database\Seeders;

use App\Models\TargetBulanan;
use App\Models\TargetMingguan;
use Illuminate\Database\Seeder;

class IsiTargetDummySeeder extends Seeder
{
    public function run(): void
    {
        $targets = TargetBulanan::where('nilai_target', 0)->with('kpi')->get();

        foreach ($targets as $target) {
            $nilai = $this->dummyValueFor($target->kpi->satuan, $target->kpi->pola);

            $target->update(['nilai_target' => $nilai]);

            // Pecah jadi 4 minggu (kalau belum ada)
            if ($target->targetMingguan()->count() === 0) {
                for ($minggu = 1; $minggu <= 4; $minggu++) {
                    TargetMingguan::create([
                        'target_bulanan_id' => $target->id,
                        'minggu_ke' => $minggu,
                        'nilai_target' => round($nilai / 4, 2),
                    ]);
                }
            }
        }

        $this->command->info($targets->count().' Target Bulanan diisi nilai dummy + dipecah jadi Target Mingguan.');
    }

    protected function dummyValueFor(string $satuan, string $pola): float
    {
        return match ($satuan) {
            'Rupiah' => rand(30, 70) * 1_000_000,
            '%' => $pola === 'minimize' ? rand(1, 5) : rand(80, 100),
            'Angka' => $pola === 'minimize' ? rand(0, 3) : rand(5, 20),
            'Hari' => rand(3, 10),
            'Bulan' => rand(6, 24),
            'Menit' => rand(10, 60),
            default => rand(10, 100),
        };
    }
}