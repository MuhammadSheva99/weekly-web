<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('kpi_performance')) {
            return;
        }

        $col = collect(Schema::getColumns('kpi_performance'))->firstWhere('name', 'actual_bulanan');

        if (! $col || ! str_starts_with($col['type'], 'decimal')) {
            return;
        }

        $null = $col['nullable'] ? 'NULL' : 'NOT NULL';
        $default = '';
        if ($col['default'] !== null) {
            $default = ' DEFAULT '.DB::getPdo()->quote($col['default']);
        } elseif ($col['nullable']) {
            $default = ' DEFAULT NULL';
        }

        DB::statement("ALTER TABLE `kpi_performance` MODIFY `actual_bulanan` DECIMAL(20,4) {$null}{$default}");
    }

    public function down(): void
    {
        // Sengaja kosong: mempersempit kembali akan membulatkan data persen.
    }
};