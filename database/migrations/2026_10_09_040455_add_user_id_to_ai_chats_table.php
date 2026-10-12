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
            if (!Schema::hasColumn('ai_chats', 'user_id')) {
                $table->foreignUuid('user_id')->nullable()->after('id')->constrained('users')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('ai_chats', 'weekly_commitment_id')) {
                $table->foreignUuid('weekly_commitment_id')->nullable()->after('user_id')->constrained('weekly_commitments')->cascadeOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_chats', function (Blueprint $table) {
            if (Schema::hasColumn('ai_chats', 'weekly_commitment_id')) {
                $table->dropForeign(['weekly_commitment_id']);
                $table->dropColumn('weekly_commitment_id');
            }
            if (Schema::hasColumn('ai_chats', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};