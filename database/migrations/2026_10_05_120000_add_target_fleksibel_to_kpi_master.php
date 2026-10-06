<?php

use App\Models\KpiMaster;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tabel = (new KpiMaster)->getTable();

        if (! Schema::hasColumn($tabel, 'target_fleksibel')) {
            Schema::table($tabel, function (Blueprint $t) {
                $t->boolean('target_fleksibel')->default(false);
            });
        }
    }

    public function down(): void
    {
        $tabel = (new KpiMaster)->getTable();

        if (Schema::hasColumn($tabel, 'target_fleksibel')) {
            Schema::table($tabel, function (Blueprint $t) {
                $t->dropColumn('target_fleksibel');
            });
        }
    }
};