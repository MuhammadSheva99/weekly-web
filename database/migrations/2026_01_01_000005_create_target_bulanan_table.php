<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('target_bulanan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('kpi_id')->constrained('kpi_master')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignUuid('user_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->date('periode')->comment('Tanggal awal bulan, cth 2026-01-01');
            $table->decimal('nilai_target', 15, 2);
            $table->timestamps();

            $table->unique(['kpi_id', 'user_id', 'periode'], 'uq_target_bulanan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('target_bulanan');
    }
};