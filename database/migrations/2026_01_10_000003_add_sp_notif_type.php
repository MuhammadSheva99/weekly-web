<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE notifications_wpm MODIFY type ENUM(
            'reminder_commitment', 'reminder_progress', 'reminder_review',
            'alert_underperform', 'alert_belum_submit', 'commitment_submitted',
            'cuti_menunggu', 'cuti_disetujui', 'cuti_ditolak', 'feedback_atasan',
            'progress_submitted', 'review_submitted',
            'alert_underperform_anggota', 'alert_belum_submit_anggota',
            'izin_menunggu', 'izin_disetujui', 'izin_ditolak',
            'sp_diterbitkan'
        ) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE notifications_wpm MODIFY type ENUM(
            'reminder_commitment', 'reminder_progress', 'reminder_review',
            'alert_underperform', 'alert_belum_submit', 'commitment_submitted',
            'cuti_menunggu', 'cuti_disetujui', 'cuti_ditolak', 'feedback_atasan',
            'progress_submitted', 'review_submitted',
            'alert_underperform_anggota', 'alert_belum_submit_anggota',
            'izin_menunggu', 'izin_disetujui', 'izin_ditolak'
        ) NOT NULL");
    }
};