<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_performance', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('kpi_id')->constrained('kpi_master')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignUuid('user_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->date('periode')->comment('Bulan berjalan');
            $table->decimal('actual_bulanan', 15, 2);
            $table->decimal('achievement_pct', 5, 2);
            $table->string('score', 5);
            $table->timestamps();

            $table->unique(['kpi_id', 'user_id', 'periode'], 'uq_kpi_performance');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_performance');
    }
};