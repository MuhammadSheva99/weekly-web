<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_log', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('table_name', 50);
            $table->uuid('record_id');
            $table->enum('action', ['created', 'updated', 'deleted']);
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->foreignUuid('changed_by')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->dateTime('changed_at');

            $table->index(['table_name', 'record_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
};