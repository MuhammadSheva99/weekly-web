<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_comment', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('weekly_commitment_id')->constrained('weekly_commitment')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignUuid('commented_by')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->text('comment');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_comment');
    }
};