<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weekly_commitment', function (Blueprint $table) {
            $table->dropForeign(['target_mingguan_id']);
            $table->uuid('target_mingguan_id')->nullable()->change();
            $table->foreign('target_mingguan_id')->references('id')->on('target_mingguan')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('weekly_commitment', function (Blueprint $table) {
            $table->dropForeign(['target_mingguan_id']);
            $table->uuid('target_mingguan_id')->nullable(false)->change();
            $table->foreign('target_mingguan_id')->references('id')->on('target_mingguan')->onDelete('restrict');
        });
    }
};