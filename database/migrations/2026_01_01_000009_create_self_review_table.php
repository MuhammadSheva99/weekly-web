<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('self_review', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('weekly_commitment_id')->unique()->constrained('weekly_commitment')->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('target_minggu', 15, 2);
            $table->decimal('actual', 15, 2);
            $table->decimal('achievement_pct', 5, 2);
            $table->text('apa_berhasil')->nullable();
            $table->text('apa_gagal')->nullable();
            $table->text('kenapa_gagal')->nullable();
            $table->text('apa_beda')->nullable();
            $table->text('improvement_depan')->nullable();
            $table->text('kritik_diri')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('self_review');
    }
};