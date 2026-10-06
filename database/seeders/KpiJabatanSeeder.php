<?php

namespace Database\Seeders;

use App\Models\Divisi;
use App\Models\KpiMaster;
use App\Models\TargetBulanan;
use App\Models\TargetMingguan;
use App\Models\User;
use Illuminate\Database\Seeder;

class KpiJabatanSeeder extends Seeder
{
    // Role yang bisa kena auto-assign KPI divisi-wide.
    // 'HRD' ditambahkan supaya divisi HR-GA (Irwan) ikut ter-assign.
    protected const ROLE_DIVISI_WIDE = ['Karyawan', 'Atasan', 'HRD'];

    // KPI bertarget fleksibel: target mengikuti realisasi (hanya dapat 1 -> target 1).
    protected const KPI_FLEKSIBEL = [
        'HR-GA' => [
            'Hiring Fulfillment Rate',
            'Asset Maintenance Completion Rate',
            'Performance Review Completion',
            'Training Plan Execution Rate',
            'Disciplinary Violations',
        ],
    ];

    public function run(): void
    {
        // 1. Buat divisi baru yang belum ada (aman kalau sudah ada, tidak duplikat)
        $divisiBaru = ['Team Leader Digital Marketing', 'Research and Development'];
        foreach ($divisiBaru as $nama) {
            Divisi::firstOrCreate(['nama' => $nama]);
        }

        // 2. Definisi KPI per divisi: [nama_kpi, satuan, pola, bobot, target]
        //    Target diambil dari dokumen acuan KPI.xlsx (target per-divisi, berlaku
        //    sama untuk semua orang di divisi itu, kecuali dicatat lain di komentar).
        $kpiPerDivisi = [
            'Admin Sales' => [
                ['Sales Order Accuracy Rate', '%', 'Maximize', 30, 1],
                ['Average Lead Time Order', 'Menit', 'Maximize', 30, 20],
                ['Stock Information Accuracy', '%', 'Minimize', 40, 1],
            ],
            'Telesales' => [
                ['Sales Revenue', 'Rupiah', 'Maximize', 45, 180000000],
                ['Partner Acquisition', 'Angka', 'Maximize', 20, 36],
                // Target awal di dokumen masih 0% (belum diisi) — dikonfirmasi jadi 20%.
                ['Lead Conversion Rate', '%', 'Maximize', 35, 0.2],
            ],
            // Target KPI Supervisor belum ada di dokumen acuan (tidak ada sheet-nya).
            // Semua nilai_target di-set 0 sampai ada data asli dari lapangan.
            'Supervisor' => [
                ['Sales Revenue', 'Rupiah', 'Maximize', 30, 0],
                ['Partner Acquisition', 'Angka', 'Maximize', 25, 0],
                ['Active Partner Rate', '%', 'Maximize', 20, 0],
                ['Zero Complaint', 'Angka', 'Minimize', 15, 0],
                ['Performance Tim', '%', 'Maximize', 10, 0],
            ],
            'Team Leader Digital Marketing' => [
                // Bobot & target mengikuti sheet "Marketing Supervisor" versi terbaru
                // di KPI.xlsx (total bobot 100%). Harga Pokok Produksi sudah dihapus.
                ['Average Revenue per Product', 'Rupiah', 'Maximize', 25, 1000000000],
                ['ROAS', 'Angka', 'Maximize', 25, 7],
                ['Lead Generation', 'Angka', 'Maximize', 20, 5000],
                ['Performance Tim', '%', 'Maximize', 30, 0.8],
            ],
            'Content Creator' => $this->socialMediaKpi(),
            'Product Executive' => [
                ['Harga Pokok Produksi', '%', 'Maximize', 25, 0.6],
                ['Recipe Development Lead Time', 'Hari', 'Maximize', 25, 7],
                ['Product Life Cycle', 'Bulan', 'Maximize', 50, 6],
            ],
            'Research and Development' => [
                // Target fleksibel: ikut realisasi launching bulan itu (misal dapat 1
                // produk baru, ya targetnya dianggap 1). 1 dipakai sebagai baseline,
                // wajar disesuaikan manual tiap bulan, bukan kuota tetap.
                ['New Product Development', 'Angka', 'Maximize', 35, 1],
                ['Product Development Lead Time', 'Hari', 'Maximize', 40, 14],
                ['Product Life Cycle', 'Bulan', 'Maximize', 25, 6],
            ],
            'Admin Stock Warehouse' => [
                ['Delivery Delay', 'Angka', 'Minimize', 25, 5],
                ['Delivery Accuracy', 'Angka', 'Minimize', 20, 0],
                ['Expired Product', 'Angka', 'Minimize', 20, 10],
                ['Loss or Damage Goods', 'Angka', 'Minimize', 20, 0],
                ['Unnoticed Stockout Rate', '%', 'Minimize', 15, 0.1],
            ],
            'Asisten Purchasing' => [
                ['On Time Delivery', '%', 'Maximize', 30, 0.9],
                ['Order Accuracy Rate', '%', 'Maximize', 30, 1],
                ['Cost Percentage', '%', 'Maximize', 40, 0.1],
            ],
            // Target KPI Head Purchasing belum ada di dokumen acuan (tidak ada sheet-nya).
            // Semua nilai_target di-set 0 sampai ada data asli dari lapangan.
            'Head Purchasing' => [
                ['Cost Percentage', '%', 'Maximize', 20, 0],
                ['Inventory Shrinkage Cost', 'Rupiah', 'Minimize', 25, 0],
                ['Order Fulfillment Rate', '%', 'Maximize', 20, 0],
                ['Zero Complaint', 'Angka', 'Minimize', 25, 0],
                ['Performance Tim', 'Angka', 'Maximize', 10, 0],
            ],
            // KPI resmi HR-GA — dari dokumen "KEY PERFORMANCE INDICATOR - SUPPORT -
            // HUMAN RESOURCE GENERAL AFFAIR" (No Dokumen: KPI/GAS/013/VII/2026,
            // Tanggal Efektif 1/9/2026). Bobot total 100%.
            // Catatan: "Asset Maintenance Completion Rate" ditandai Minimize di
            // dokumen aslinya (bukan salah ketik saya) — diikuti persis apa adanya.
            // Nama Divisi: HR-GA (dicek langsung dari tabel divisi — bukan "HRD",
            // itu nama Role-nya, beda hal sama nama Divisi).
            // 4 dari 5 target masih 0% di dokumen -> dikosongkan (0) sampai ada angka asli.
            'HR-GA' => [
                ['Hiring Fulfillment Rate', '%', 'Maximize', 20, 0],
                ['Disciplinary Violations', 'Angka', 'Minimize', 25, 2],
                ['Asset Maintenance Completion Rate', '%', 'Minimize', 15, 0],
                ['Performance Review Completion', '%', 'Maximize', 30, 0],
                ['Training Plan Execution Rate', '%', 'Maximize', 10, 0],
            ],
            // Divisi khusus milik Siti Maysaroh (lihat kpi-assignment.json).
            'Customer Relation' => [
                ['Revenue Retention', 'Rupiah', 'Maximize', 40, 50000000],
                ['Partnert Repeat Order Rate', '%', 'Maximize', 30, 0.2],
                // Skor kepuasan mitra, skala per 10 (target 8 dari 10).
                ['Partner Satisfaction Score', 'Angka', 'Maximize', 15, 8],
                ['Handling Complaint Rate', '%', 'Maximize', 15, 1],
            ],
        ];

        // 3. Divisi yang KPI-nya berlaku untuk SEMUA karyawan di divisi itu
        $divisiWide = [
            'Admin Sales', 'Telesales', 'Supervisor', 'Team Leader Digital Marketing',
            'Content Creator',
            'HR-GA',
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
            foreach ($daftarKpi as [$nama, $satuan, $pola, $bobot, $target]) {
                $kpi = KpiMaster::firstOrCreate(
                    ['nama_kpi' => $nama, 'divisi_id' => $divisi->id],
                    ['satuan' => $satuan, 'pola' => $pola, 'is_active' => true]
                );
                if (in_array($nama, self::KPI_FLEKSIBEL[$namaDivisi] ?? [], true)) {
                    KpiMaster::whereKey($kpi->id)->update(['target_fleksibel' => true]);
                }
                $kpiIds[] = ['kpi' => $kpi, 'bobot' => $bobot, 'target' => $target];
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
                    ->whereHas('role', fn ($q) => $q->whereIn('nama', self::ROLE_DIVISI_WIDE))
                    ->get();
            } else {
                $users = collect();
            }

            if ($users->isEmpty()) {
                $this->command->warn("Divisi '{$namaDivisi}': KPI master dibuat, tapi TIDAK ADA user yang di-assign (cek kpi-assignment.json atau role user di divisi ini).");
            }

            foreach ($users as $user) {
                foreach ($kpiIds as $item) {
                    $targetBulanan = TargetBulanan::firstOrCreate(
                        ['kpi_id' => $item['kpi']->id, 'user_id' => $user->id, 'periode' => $periode],
                        ['nilai_target' => $item['target'], 'bobot' => $item['bobot']]
                    );

                    // firstOrCreate tidak mengubah baris yang sudah ada, jadi bobot
                    // disinkronkan di sini. nilai_target sengaja TIDAK ditimpa supaya
                    // target yang sudah diedit manual tidak hilang.
                    if ((float) $targetBulanan->bobot !== (float) $item['bobot']) {
                        $targetBulanan->update(['bobot' => $item['bobot']]);
                    }

                    for ($minggu = 1; $minggu <= 4; $minggu++) {
                        TargetMingguan::firstOrCreate(
                            ['target_bulanan_id' => $targetBulanan->id, 'minggu_ke' => $minggu],
                            ['nilai_target' => round($targetBulanan->nilai_target / 4, 4)]
                        );
                    }
                }
            }

            $this->command->info("Divisi '{$namaDivisi}': ".count($kpiIds)." KPI, ".$users->count()." user di-assign.");
        }
    }

    protected function socialMediaKpi(): array
    {
        return [
            // Bobot mengikuti sheet "Social Media Specialist" di KPI.xlsx (total 100%).
            ['Content Fulfilment', 'Angka', 'Maximize', 15, 800],
            ['High Performing Content', '%', 'Maximize', 40, 0.2],
            ['Lead Generation', 'Angka', 'Maximize', 20, 1000],
            ['Engagement Rate', '%', 'Maximize', 15, 0.2],
            ['Followers Growth', '%', 'Maximize', 10, 0.2],
        ];
    }
}