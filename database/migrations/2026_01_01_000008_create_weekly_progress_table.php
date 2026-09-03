<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_progress', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('weekly_commitment_id')->unique()->constrained('weekly_commitment')->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('actual_sementara', 15, 2);
            $table->decimal('achievement_pct', 5, 2);
            $table->text('problem')->nullable();
            $table->text('analysis')->nullable();
            $table->text('solution')->nullable();
            $table->text('action_plan')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_progress');
    }
};