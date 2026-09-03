<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('target_mingguan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('target_bulanan_id')->constrained('target_bulanan')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedTinyInteger('minggu_ke');
            $table->decimal('nilai_target', 15, 2);
            $table->timestamps();

            $table->unique(['target_bulanan_id', 'minggu_ke'], 'uq_target_mingguan');
        });

        // Constraint minggu_ke harus 1-4 (MySQL 8.0.16+)
        DB::statement('ALTER TABLE target_mingguan ADD CONSTRAINT chk_target_mingguan_minggu_ke CHECK (minggu_ke BETWEEN 1 AND 4)');
    }

    public function down(): void
    {
        Schema::dropIfExists('target_mingguan');
    }
};