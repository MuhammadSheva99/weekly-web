<?php

namespace Database\Seeders;

use App\Models\Divisi;
use App\Models\KpiMaster;
use App\Models\TargetBulanan;
use App\Models\User;
use Illuminate\Database\Seeder;

class KpiJabatanSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat divisi baru yang belum ada (aman kalau sudah ada, tidak duplikat)
        $divisiBaru = ['Team Leader Digital Marketing', 'Research and Development'];
        foreach ($divisiBaru as $nama) {
            Divisi::firstOrCreate(['nama' => $nama]);
        }

        // 2. Definisi KPI per divisi: [nama_kpi, satuan, pola, bobot]
        $kpiPerDivisi = [
            'Admin Sales' => [
                ['Sales Order Accuracy Rate', '%', 'maximize', 30],
                ['Average Lead Time Order', 'Menit', 'maximize', 30],
                ['Stock Information Accuracy', '%', 'minimize', 40],
            ],
            'Telesales' => [
                ['Sales Revenue', 'Rupiah', 'maximize', 30],
                ['Partner Acquisition', 'Angka', 'maximize', 20],
                ['Lead Conversion Rate', '%', 'maximize', 25],
            ],
            'Supervisor' => [
                ['Sales Revenue', 'Rupiah', 'maximize', 30],
                ['Partner Acquisition', 'Angka', 'maximize', 30],
                ['Active Partner Rate', '%', 'maximize', 25],
                ['Zero Complaint', 'Angka', 'minimize', 15],
                ['Performance Tim', '%', 'maximize', 10],
            ],
            'Team Leader Digital Marketing' => [
                ['Average Revenue per Product', 'Rupiah', 'maximize', 15],
                ['Harga Pokok Produksi', '%', 'maximize', 30],
                ['ROAS', 'Angka', 'maximize', 25],
                ['Lead Generation', 'Angka', 'maximize', 20],
                ['Performance Tim', '%', 'maximize', 10],
            ],
            'Content Creator TikTok' => $this->socialMediaKpi(),
            'Content Creator' => $this->socialMediaKpi(),
            'Marketing Many Platform' => $this->socialMediaKpi(),
            'Product Executive' => [
                ['Harga Pokok Produksi', '%', 'maximize', 25],
                ['Recipe Development Lead Time', 'Hari', 'maximize', 25],
                ['Product Life Cycle', 'Bulan', 'maximize', 50],
            ],
            'Research and Development' => [
                ['New Product Development', 'Angka', 'maximize', 35],
                ['Product Development Lead Time', 'Hari', 'maximize', 40],
                ['Product Life Cycle', 'Bulan', 'maximize', 25],
            ],
            'Admin Stock Warehouse' => [
                ['Delivery Delay', 'Angka', 'minimize', 25],
                ['Delivery Accuracy', 'Angka', 'minimize', 20],
                ['Expired Product', 'Angka', 'minimize', 20],
                ['Loss or Damage Goods', 'Angka', 'minimize', 20],
                ['Unnoticed Stockout Rate', '%', 'minimize', 15],
            ],
            'Asisten Purchasing' => [
                ['On Time Delivery', '%', 'maximize', 30],
                ['Order Accuracy Rate', '%', 'maximize', 30],
                ['Cost Percentage', '%', 'maximize', 40],
            ],
            'Head Purchasing' => [
                ['Cost Percentage', '%', 'maximize', 20],
                ['Inventory Shrinkage Cost', 'Rupiah', 'minimize', 25],
                ['Order Fulfillment Rate', '%', 'maximize', 20],
                ['Zero Complaint', 'Angka', 'minimize', 25],
                ['Performance Tim', 'Angka', 'maximize', 10],
            ],
        ];

        // 3. Divisi yang KPI-nya berlaku untuk SEMUA karyawan di divisi itu
        $divisiWide = [
            'Admin Sales', 'Telesales', 'Supervisor', 'Team Leader Digital Marketing',
            'Content Creator TikTok', 'Content Creator', 'Marketing Many Platform',
        ];

        // 4. Divisi yang KPI-nya cuma untuk orang tertentu — dibaca dari file terpisah (tidak di-commit)
        $path = database_path('seeders/data/kpi-assignment.json');

        if (! file_exists($path)) {
            $this->command->warn('File data/kpi-assignment.json tidak ditemukan. Salin dari kpi-assignment.example.json lalu isi data asli.');
            $orangSpesifik = [];
        } else {
            $orangSpesifik = json_decode(file_get_contents($path), true);
        }

        $periode = now()->startOfMonth();

        foreach ($kpiPerDivisi as $namaDivisi => $daftarKpi) {
            $divisi = Divisi::where('nama', $namaDivisi)->first();

            if (! $divisi) {
                $this->command->warn("Divisi '{$namaDivisi}' tidak ditemukan, dilewati.");
                continue;
            }

            $kpiIds = [];
            foreach ($daftarKpi as [$nama, $satuan, $pola, $bobot]) {
                $kpi = KpiMaster::firstOrCreate(
                    ['nama_kpi' => $nama, 'divisi_id' => $divisi->id],
                    ['satuan' => $satuan, 'pola' => $pola, 'is_active' => true]
                );
                $kpiIds[] = ['kpi' => $kpi, 'bobot' => $bobot];
            }

            if (isset($orangSpesifik[$namaDivisi])) {
                $namaOrangList = $orangSpesifik[$namaDivisi];
                $users = User::where('divisi_id', $divisi->id)
                    ->where(function ($q) use ($namaOrangList) {
                        foreach ($namaOrangList as $nama) {
                            $q->orWhere('nama', 'like', "%{$nama}%");
                        }
                    })
                    ->get();
            } elseif (in_array($namaDivisi, $divisiWide)) {
                $users = User::where('divisi_id', $divisi->id)
                    ->whereHas('role', fn ($q) => $q->whereIn('nama', ['Karyawan', 'Atasan']))
                    ->get();
            } else {
                $users = collect();
            }

            foreach ($users as $user) {
                foreach ($kpiIds as $item) {
                    TargetBulanan::firstOrCreate(
                        ['kpi_id' => $item['kpi']->id, 'user_id' => $user->id, 'periode' => $periode],
                        ['nilai_target' => 0, 'bobot' => $item['bobot']]
                    );
                }
            }

            $this->command->info("Divisi '{$namaDivisi}': ".count($kpiIds)." KPI, ".$users->count()." user di-assign.");
        }
    }

    protected function socialMediaKpi(): array
    {
        return [
            ['Content Fulfilment', 'Angka', 'maximize', 15],
            ['High Performing Content', '%', 'maximize', 25],
            ['Lead Generation', 'Angka', 'maximize', 25],
            ['Engagement Rate', '%', 'maximize', 20],
            ['Followers Growth', '%', 'maximize', 15],
        ];
    }
}