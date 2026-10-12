<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ai_chats', function (Blueprint $table) {
            if (!Schema::hasColumn('ai_chats', 'weekly_commitment_id')) {
                $table->uuid('weekly_commitment_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('ai_chats', 'title')) {
                $table->string('title')->nullable()->after('weekly_commitment_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_chats', function (Blueprint $table) {
            if (Schema::hasColumn('ai_chats', 'title')) {
                $table->dropColumn('title');
            }
            if (Schema::hasColumn('ai_chats', 'weekly_commitment_id')) {
                $table->dropColumn('weekly_commitment_id');
            }
        });
    }
};