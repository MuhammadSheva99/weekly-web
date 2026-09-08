<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE notifications_wpm MODIFY type ENUM(
            'reminder_commitment', 'reminder_progress', 'reminder_review',
            'alert_underperform', 'alert_belum_submit', 'commitment_submitted'
        ) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE notifications_wpm MODIFY type ENUM(
            'reminder_commitment', 'reminder_progress', 'reminder_review',
            'alert_underperform', 'alert_belum_submit'
        ) NOT NULL");
    }
};