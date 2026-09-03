<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_commitment', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('target_mingguan_id')->unique()->constrained('target_mingguan')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignUuid('user_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->text('big_goal');
            $table->json('prioritas')->comment('Array 3 string prioritas');
            $table->json('metric')->comment('Array 5 objek {nama_metric, satuan}');
            $table->decimal('target', 15, 2);
            $table->text('output_deliverable');
            $table->enum('status', ['draft', 'submitted', 'locked'])->default('draft');
            $table->dateTime('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_commitment');
    }
};