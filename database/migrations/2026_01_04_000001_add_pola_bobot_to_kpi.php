<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kpi_master', function (Blueprint $table) {
            $table->enum('pola', ['maximize', 'minimize'])->default('maximize')->after('satuan');
        });

        Schema::table('target_bulanan', function (Blueprint $table) {
            $table->decimal('bobot', 5, 2)->default(100)->after('nilai_target')
                ->comment('Persentase kontribusi KPI ini terhadap skor total jabatan, total semua KPI aktif user = 100');
        });
    }

    public function down(): void
    {
        Schema::table('kpi_master', function (Blueprint $table) {
            $table->dropColumn('pola');
        });
        Schema::table('target_bulanan', function (Blueprint $table) {
            $table->dropColumn('bobot');
        });
    }
};