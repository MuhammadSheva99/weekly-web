<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actual_mingguan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('weekly_commitment_id')->unique()->constrained('weekly_commitment')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignUuid('target_mingguan_id')->unique()->constrained('target_mingguan')->onUpdate('cascade')->onDelete('restrict');
            $table->decimal('nilai_actual_final', 15, 2);
            $table->decimal('achievement_pct', 5, 2);
            $table->dateTime('locked_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actual_mingguan');
    }
};