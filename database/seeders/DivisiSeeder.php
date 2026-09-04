<?php

namespace Database\Seeders;

use App\Models\Divisi;
use Illuminate\Database\Seeder;

class DivisiSeeder extends Seeder
{
    public function run(): void
    {
        $divisi = ['Supervisor', 'Admin Order', 'Advertiser', 'Product Executive', 'Social Media', 
        'Marketing Many Platform', 'HR-GA', 'Creative Design', 'Content Creator TikTok', 'Admin Purchasing',
        'Content Creator', 'Kepala Gudang', 'Telesales', 'Content Creator Outlet', 'AI Support', 
        'Head Purchasing', 'Asisten Purchasing', 'Admin Stock Warehouse', 'Product Executive Outlet', 
        'Admin Sales'];

        foreach ($divisi as $nama) {
            Divisi::firstOrCreate(['nama' => $nama]);
        }
    }
}
