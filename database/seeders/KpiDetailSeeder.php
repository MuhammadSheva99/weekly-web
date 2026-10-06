<?php

namespace Database\Seeders;

use App\Models\Divisi;
use App\Models\KpiMaster;
use Illuminate\Database\Seeder;

/**
 * Mengisi detail KPI (Key Result Area, deskripsi, sumber data, rumus) dari
 * data/kpi-detail.json (diambil dari dokumen KPI.xlsx).
 * Hanya mengisi kolom yang masih kosong, jadi hasil edit manual tidak tertimpa.
 */
class KpiDetailSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/kpi-detail.json');

        if (! file_exists($path)) {
            $this->command->warn('File data/kpi-detail.json tidak ditemukan.');
            return;
        }

        $kolom = ['key_result_area', 'deskripsi', 'sumber_data', 'rumus_realisasi', 'rumus_target'];
        $terisi = 0;

        foreach (json_decode(file_get_contents($path), true) as $item) {
            $divisi = Divisi::where('nama', $item['divisi'])->first();
            if (! $divisi) {
                $this->command->warn("Divisi '{$item['divisi']}' tidak ditemukan, dilewati.");
                continue;
            }

            $kpiList = KpiMaster::where('divisi_id', $divisi->id)->where('nama_kpi', $item['nama_kpi'])->get();
            if ($kpiList->isEmpty()) {
                $this->command->warn("KPI '{$item['nama_kpi']}' di divisi '{$item['divisi']}' tidak ditemukan, dilewati.");
                continue;
            }

            foreach ($kpiList as $kpi) {
                $isi = [];
                foreach ($kolom as $k) {
                    if (blank($kpi->{$k}) && filled($item[$k] ?? null)) {
                        $isi[$k] = $item[$k];
                    }
                }
                if ($isi) {
                    $kpi->update($isi);
                    $terisi++;
                }
            }
        }

        $this->command->info("Detail KPI terisi untuk {$terisi} KPI.");
    }
}