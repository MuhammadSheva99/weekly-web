<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $kolom = [
        'target_bulanan'    => ['nilai_target'],
        'target_mingguan'   => ['nilai_target'],
        'weekly_commitment' => ['target'],
        'weekly_progress'   => ['actual_sementara'],
        'self_review'       => ['target_minggu', 'actual'],
        'actual_mingguan'   => ['nilai_actual_final'],
    ];

    public function up(): void
    {
        foreach ($this->kolom as $tabel => $daftarKolom) {
            if (! Schema::hasTable($tabel)) {
                continue;
            }

            $info = collect(Schema::getColumns($tabel))->keyBy('name');

            foreach ($daftarKolom as $nama) {
                $col = $info->get($nama);

                if (! $col || ! str_starts_with($col['type'], 'decimal')) {
                    continue; // kolom tidak ada / bukan decimal: jangan disentuh
                }

                $null = $col['nullable'] ? 'NULL' : 'NOT NULL';
                $default = '';
                if ($col['default'] !== null) {
                    $default = ' DEFAULT '.DB::getPdo()->quote($col['default']);
                } elseif ($col['nullable']) {
                    $default = ' DEFAULT NULL';
                }

                DB::statement("ALTER TABLE `{$tabel}` MODIFY `{$nama}` DECIMAL(20,4) {$null}{$default}");
            }
        }
    }

    public function down(): void
    {
        // Sengaja kosong: mempersempit kembali ke 2 desimal akan membulatkan data persen.
    }
};