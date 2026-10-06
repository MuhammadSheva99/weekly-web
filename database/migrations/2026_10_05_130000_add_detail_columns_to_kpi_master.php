<?php

use App\Models\KpiMaster;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kolom detail KPI seperti di dokumen KPI.xlsx:
 * Key Result Area, deskripsi indikator, sumber data, rumus realisasi, rumus target.
 */
return new class extends Migration
{
    private array $kolom = [
        'key_result_area' => 255,
        'sumber_data' => 255,
        'rumus_realisasi' => 500,
        'rumus_target' => 500,
    ];

    public function up(): void
    {
        $tabel = (new KpiMaster)->getTable();

        Schema::table($tabel, function (Blueprint $t) use ($tabel) {
            foreach ($this->kolom as $nama => $panjang) {
                if (! Schema::hasColumn($tabel, $nama)) {
                    $t->string($nama, $panjang)->nullable();
                }
            }
            if (! Schema::hasColumn($tabel, 'deskripsi')) {
                $t->text('deskripsi')->nullable();
            }
        });
    }

    public function down(): void
    {
        $tabel = (new KpiMaster)->getTable();

        Schema::table($tabel, function (Blueprint $t) use ($tabel) {
            foreach (array_merge(array_keys($this->kolom), ['deskripsi']) as $nama) {
                if (Schema::hasColumn($tabel, $nama)) {
                    $t->dropColumn($nama);
                }
            }
        });
    }
};